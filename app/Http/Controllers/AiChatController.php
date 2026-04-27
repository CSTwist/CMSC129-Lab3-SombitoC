<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AiChatController extends Controller
{
    private const HISTORY_LIMIT = 10;

    /**
     * Send a message to the Gemini AI API and return the response.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            return response()->json(['error' => 'Gemini API key is not configured.'], 500);
        }

        $userMessage = $request->message;
        
        // Add user message to history
        $this->pushHistory('user', [['text' => $userMessage]]);

        try {
            Session::forget('ai_chat_refreshed'); // Reset flag
            $aiResponse = $this->processConversation(0);
            
            $shouldRefresh = Session::get('ai_chat_refreshed', false);
            
            return response()->json([
                'response' => $aiResponse,
                'refresh' => $shouldRefresh
            ]);
        } catch (\Exception $e) {
            Log::error('AiChatController Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Recursive function to handle conversation including tool calls.
     */
    private function processConversation($depth = 0)
    {
        if ($depth > 10) {
            return "I'm sorry, I reached my limit for complex operations. Could you try rephrasing?";
        }

        $apiKey = config('services.gemini.key');
        $endpoint = config('services.gemini.endpoint');
        $history = $this->getHistory();

        $payload = [
            'contents' => $this->prepareHistoryForApi($history),
            'tools' => [
                ['function_declarations' => $this->getTools()]
            ],
            'system_instruction' => [
                'parts' => [
                    ['text' => $this->getSystemPrompt()]
                ]
            ],
            'tool_config' => [
                'function_calling_config' => [
                    'mode' => 'AUTO'
                ]
            ]
        ];

        $response = Http::timeout(90)->withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => $apiKey,
        ])->post($endpoint, $payload);

        // Fallback logic
        if ($response->failed()) {
            $fallback = config('services.gemini.fallback_endpoint');
            if ($fallback && $endpoint !== $fallback) {
                $response = Http::timeout(90)->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $apiKey,
                ])->post($fallback, $payload);
            }
        }

        if ($response->failed()) {
            Log::error('Gemini API Failure', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload
            ]);
            throw new \Exception("Gemini API Error: " . $response->status());
        }

        $data = $response->json();
        $candidate = $data['candidates'][0] ?? null;

        if (!$candidate) {
            return "I couldn't generate a response. Please try again.";
        }

        $parts = $candidate['content']['parts'] ?? [];
        $aiResponseText = "";
        $toolCalls = [];

        foreach ($parts as $part) {
            if (isset($part['text'])) {
                $aiResponseText .= $part['text'];
            }
            $fCall = $part['functionCall'] ?? $part['function_call'] ?? null;
            if ($fCall) {
                $toolCalls[] = $fCall;
            }
        }

        // Add model turn to history
        $this->pushHistory('model', $parts);

        if (empty($toolCalls)) {
            return $aiResponseText ?: "I'm not sure how to respond to that.";
        }

        // Handle Tool Calls
        $toolResponses = [];
        foreach ($toolCalls as $call) {
            $result = $this->executeTool($call['name'], $call['args'] ?? []);
            $toolResponses[] = [
                'functionResponse' => [
                    'name' => $call['name'],
                    'response' => ['result' => $result]
                ]
            ];
        }

        // Add function responses to history
        $this->pushHistory('function', $toolResponses);

        // Call AI again with tool results
        return $this->processConversation($depth + 1);
    }

    /**
     * Clean and format history specifically for the Google API.
     */
    private function prepareHistoryForApi($history)
    {
        // 1. Ensure history starts with 'user'
        while (!empty($history) && $history[0]['role'] !== 'user') {
            array_shift($history);
        }

        if (empty($history)) return [];

        // 2. Format parts and roles
        $formattedHistory = [];
        foreach ($history as $entry) {
            $role = (string)$entry['role'];
            
            $parts = array_map(function($part) {
                if (isset($part['text'])) {
                    return ['text' => (string)$part['text']];
                }
                
                $fCall = $part['functionCall'] ?? $part['function_call'] ?? null;
                if ($fCall) {
                    $newPart = [];
                    // Preserve original fields like thoughtSignature
                    foreach ($part as $key => $value) {
                        $camelKey = str_replace('_', '', lcfirst(ucwords($key, '_')));
                        $newPart[$camelKey] = $value;
                    }
                    
                    $newPart['functionCall'] = [
                        'name' => (string)$fCall['name'],
                        'args' => (object)($fCall['args'] ?? [])
                    ];
                    return $newPart;
                }

                $fResp = $part['functionResponse'] ?? $part['function_response'] ?? null;
                if ($fResp) {
                    return [
                        'functionResponse' => [
                            'name' => (string)$fResp['name'],
                            'response' => (object)($fResp['response'] ?? [])
                        ]
                    ];
                }

                return $part;
            }, $entry['parts']);

            $formattedHistory[] = [
                'role' => $role,
                'parts' => $parts
            ];
        }

        return $formattedHistory;
    }

    /**
     * Execute the requested tool and return the result.
     */
    private function executeTool($name, $args)
    {
        $userId = Auth::id();
        
        try {
            switch ($name) {
                case 'list_journals':
                    return Journal::where('user_id', $userId)
                        ->latest()
                        ->limit(20)
                        ->get(['id', 'title', 'mood', 'is_favorite', 'created_at'])
                        ->toArray();
                
                case 'get_journal':
                    $id = $args['id'] ?? null;
                    $journal = Journal::where('user_id', $userId)->find($id);
                    return $journal ? $journal->toArray() : "Journal entry not found.";

                case 'create_journal':
                    $journal = Journal::create([
                        'user_id' => $userId,
                        'title' => $args['title'] ?? 'Untitled',
                        'content' => $args['content'] ?? '',
                        'mood' => $args['mood'] ?? null,
                        'is_favorite' => $args['is_favorite'] ?? false,
                    ]);
                    Session::put('ai_chat_refreshed', true);
                    return ['success' => true, 'journal' => $journal->toArray()];

                case 'update_journal':
                    $id = $args['id'] ?? null;
                    $journal = Journal::where('user_id', $userId)->find($id);
                    if (!$journal) return "Journal entry not found.";
                    
                    $data = array_filter([
                        'title' => $args['title'] ?? null,
                        'content' => $args['content'] ?? null,
                        'mood' => $args['mood'] ?? null,
                        'is_favorite' => $args['is_favorite'] ?? null,
                    ], fn($v) => !is_null($v));

                    $journal->update($data);
                    Session::put('ai_chat_refreshed', true);
                    return ['success' => true, 'journal' => $journal->fresh()->toArray()];

                case 'delete_journal':
                    $id = $args['id'] ?? null;
                    $journal = Journal::where('user_id', $userId)->find($id);
                    if (!$journal) return "Journal entry not found.";
                    $journal->delete();
                    Session::put('ai_chat_refreshed', true);
                    return "Journal entry moved to trash.";

                case 'list_trashed_journals':
                    return Journal::onlyTrashed()
                        ->where('user_id', $userId)
                        ->latest('deleted_at')
                        ->get(['id', 'title', 'deleted_at'])
                        ->toArray();

                case 'restore_journal':
                    $id = $args['id'] ?? null;
                    $journal = Journal::onlyTrashed()->where('user_id', $userId)->find($id);
                    if (!$journal) return "Journal entry not found in trash.";
                    $journal->restore();
                    Session::put('ai_chat_refreshed', true);
                    return "Journal entry restored successfully.";

                default:
                    return "Error: Unknown tool '$name'.";
            }
        } catch (\Exception $e) {
            return "Error executing operation: " . $e->getMessage();
        }
    }

    /**
     * Define the tools available to the AI.
     */
    private function getTools()
    {
        return [
            [
                'name' => 'list_journals',
                'description' => 'List the titles and basic info of the user\'s active journal entries.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => (object)[]
                ]
            ],
            [
                'name' => 'get_journal',
                'description' => 'Get the full details and content of a specific journal entry by its ID.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'id' => ['type' => 'INTEGER', 'description' => 'The unique ID of the journal entry.']
                    ],
                    'required' => ['id']
                ]
            ],
            [
                'name' => 'create_journal',
                'description' => 'Create a new journal entry.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'title' => ['type' => 'STRING', 'description' => 'The title of the entry.'],
                        'content' => ['type' => 'STRING', 'description' => 'The text content of the entry.'],
                        'mood' => ['type' => 'STRING', 'description' => 'The mood associated with the entry.'],
                        'is_favorite' => ['type' => 'BOOLEAN', 'description' => 'Whether to mark it as a favorite.'],
                    ],
                    'required' => ['title', 'content']
                ]
            ],
            [
                'name' => 'update_journal',
                'description' => 'Update an existing journal entry. ALWAYS ask for confirmation first.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'id' => ['type' => 'INTEGER', 'description' => 'The ID of the entry to update.'],
                        'title' => ['type' => 'STRING'],
                        'content' => ['type' => 'STRING'],
                        'mood' => ['type' => 'STRING'],
                        'is_favorite' => ['type' => 'BOOLEAN'],
                    ],
                    'required' => ['id']
                ]
            ],
            [
                'name' => 'delete_journal',
                'description' => 'Move a journal entry to the trash. ALWAYS ask for confirmation first.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'id' => ['type' => 'INTEGER', 'description' => 'The ID of the entry to delete.']
                    ],
                    'required' => ['id']
                ]
            ],
            [
                'name' => 'list_trashed_journals',
                'description' => 'List entries that have been moved to the trash.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => (object)[]
                ]
            ],
            [
                'name' => 'restore_journal',
                'description' => 'Restore a journal entry from the trash.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'id' => ['type' => 'INTEGER', 'description' => 'The ID of the entry to restore.']
                    ],
                    'required' => ['id']
                ]
            ],
        ];
    }

    /**
     * Get the system prompt.
     */
    private function getSystemPrompt()
    {
        $user = Auth::user();
        $date = now()->toDayDateTimeString();
        
        return "You are 'The Journal Assistant', a friendly and helpful AI companion integrated into a personal journaling application.
Current Date: $date.
User: {$user->name} ({$user->email}).

Your Goal:
- Help the user manage their journals via natural language.
- Answer questions about their thoughts and experiences recorded in their journals.
- Perform CRUD operations efficiently.

Behavioral Rules:
1. CONTEXT: You have access to conversation history. Roles MUST alternate strictly (user -> model -> function -> model -> user).
2. INQUIRIES: If a user asks 'What did I write about lunch?', use 'list_journals' first, then 'get_journal' for relevant IDs to find the answer.
3. CRUD:
   - To CREATE: Use 'create_journal'.
   - To UPDATE/DELETE: Use 'update_journal' or 'delete_journal'.
   - SAFETY: You MUST ask for confirmation (e.g., 'Are you sure you want to delete this entry?') before performing destructive actions (Update or Delete), unless the user has already explicitly confirmed it in the current context.
4. FEEDBACK: Always confirm success or explain failure after a tool call.
5. TONE: Be warm, empathetic, and concise. Don't be too robotic.";
    }

    /**
     * Get chat history from session.
     */
    private function getHistory()
    {
        return Session::get('ai_chat_history', []);
    }

    /**
     * Push a new entry into history and maintain the limit.
     */
    private function pushHistory($role, $parts)
    {
        $history = $this->getHistory();
        
        // Enforce alternating roles: user, model, function, model, user...
        // Note: consecutive 'user' turns are not allowed.
        // If the new role is the same as the last role, merge the parts.
        $lastEntry = end($history);
        
        if ($lastEntry && $lastEntry['role'] === $role) {
            $lastIndex = count($history) - 1;
            $history[$lastIndex]['parts'] = array_merge($history[$lastIndex]['parts'], $parts);
        } else {
            $history[] = [
                'role' => $role,
                'parts' => $parts
            ];
        }

        // Limit history to 20 entries
        if (count($history) > self::HISTORY_LIMIT * 2) {
            $history = array_slice($history, -self::HISTORY_LIMIT * 2);
        }

        Session::put('ai_chat_history', $history);
    }
}
