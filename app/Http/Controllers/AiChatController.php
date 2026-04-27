<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    /**
     * Send a message to the Gemini AI API and return the response.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $apiKey = config('services.gemini.key');
        $primaryEndpoint = config('services.gemini.endpoint');
        $fallbackEndpoint = config('services.gemini.fallback_endpoint');

        if (!$apiKey) {
            return response()->json([
                'error' => 'Gemini API key is not configured.'
            ], 500);
        }

        $endpoints = array_filter([$primaryEndpoint, $fallbackEndpoint]);
        $lastResponse = null;

        foreach ($endpoints as $index => $endpoint) {
            try {
                $response = $this->callGemini($endpoint, $apiKey, $request->message);

                if ($response->successful()) {
                    $data = $response->json();
                    $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not understand that.';

                    return response()->json([
                        'response' => $aiResponse
                    ]);
                }

                $lastResponse = $response;
                Log::warning("Gemini API Error (Attempt " . ($index + 1) . ")", [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

            } catch (\Exception $e) {
                Log::warning("Gemini API Exception (Attempt " . ($index + 1) . ")", [
                    'endpoint' => $endpoint,
                    'message' => $e->getMessage()
                ]);
            }
        }

        if ($lastResponse) {
            return response()->json([
                'error' => 'Failed to get response from AI after trying all available models.'
            ], $lastResponse->status());
        }

        return response()->json([
            'error' => 'An unexpected error occurred or the AI service is unavailable.'
        ], 500);
    }

    /**
     * Helper to call Gemini API.
     */
    private function callGemini($endpoint, $apiKey, $message)
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => $apiKey,
        ])->post($endpoint, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $message]
                    ]
                ]
            ]
        ]);
    }
}
