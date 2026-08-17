<?php

namespace Tests\Feature;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class AiChatTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_send_chat_message(): void
    {
        $response = $this->postJson('/chat/send', [
            'message' => 'Hello',
        ]);

        $response->assertStatus(401);
    }

    public function test_message_validation_fails_when_message_is_empty(): void
    {
        $response = $this->actingAs($this->user)->postJson('/chat/send', [
            'message' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    public function test_user_can_clear_ai_chat_history(): void
    {
        $this->actingAs($this->user);
        Session::put('ai_chat_history', [
            ['role' => 'user', 'parts' => [['text' => 'Hello']]],
            ['role' => 'model', 'parts' => [['text' => 'Hi there!']]],
        ]);

        $response = $this->postJson('/chat/clear');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEmpty(Session::get('ai_chat_history'));
    }

    public function test_journal_model_scopes_and_accessors(): void
    {
        $journal = Journal::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Daily Walk and Prayer',
            'content' => 'Walked by the beach today and spent time in prayer and gratitude for family and friends.',
            'mood' => 'Grateful',
            'is_favorite' => true,
        ]);

        // Word count and reading time
        $this->assertGreaterThan(5, $journal->word_count);
        $this->assertEquals(1, $journal->reading_time);
        $this->assertEquals('🙏', $journal->mood_emoji);

        // Search scope
        $this->assertTrue(Journal::where('user_id', $this->user->id)->search('Prayer')->exists());
        $this->assertFalse(Journal::where('user_id', $this->user->id)->search('NonExistentKeyword')->exists());

        // Favorite scope
        $this->assertEquals(1, Journal::where('user_id', $this->user->id)->favorites()->count());

        // Mood scope
        $this->assertEquals(1, Journal::where('user_id', $this->user->id)->mood('Grateful')->count());
    }
}
