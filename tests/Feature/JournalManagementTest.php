<?php

namespace Tests\Feature;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Your Journal');
    }

    public function test_user_can_create_a_journal_entry(): void
    {
        $response = $this->actingAs($this->user)->post('/journals', [
            'title' => 'My First Entry',
            'content' => 'This is a reflective journal entry written in markdown format.',
            'mood' => 'Grateful',
            'is_favorite' => 1,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('journals', [
            'user_id' => $this->user->id,
            'title' => 'My First Entry',
            'mood' => 'Grateful',
            'is_favorite' => 1,
        ]);
    }

    public function test_journal_creation_requires_title_and_content(): void
    {
        $response = $this->actingAs($this->user)->post('/journals', [
            'title' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'content']);
    }

    public function test_user_can_view_their_journal(): void
    {
        $journal = Journal::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Entry to View',
            'content' => 'Full text content here',
        ]);

        $response = $this->actingAs($this->user)->get("/journals/{$journal->id}/show");
        $response->assertStatus(200);
        $response->assertSee('Entry to View');
    }

    public function test_user_can_update_their_journal(): void
    {
        $journal = Journal::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Old Title',
            'content' => 'Old content',
            'mood' => 'Calm',
        ]);

        $response = $this->actingAs($this->user)->put("/journals/{$journal->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated content here',
            'mood' => 'Happy',
            'is_favorite' => 1,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'title' => 'Updated Title',
            'mood' => 'Happy',
        ]);
    }

    public function test_user_can_toggle_favorite_status(): void
    {
        $journal = Journal::factory()->create([
            'user_id' => $this->user->id,
            'is_favorite' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/journals/{$journal->id}/favorite");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_favorite' => true,
        ]);

        $this->assertTrue($journal->fresh()->is_favorite);
    }

    public function test_user_can_soft_delete_and_restore_journal(): void
    {
        $journal = Journal::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Soft delete
        $deleteResponse = $this->actingAs($this->user)->delete("/journals/{$journal->id}");
        $deleteResponse->assertRedirect('/dashboard');
        $this->assertSoftDeleted('journals', ['id' => $journal->id]);

        // Restore
        $restoreResponse = $this->actingAs($this->user)->post("/journals/{$journal->id}/restore");
        $restoreResponse->assertRedirect('/recently-deleted');
        $this->assertNotSoftDeleted('journals', ['id' => $journal->id]);
    }

    public function test_user_cannot_access_or_modify_another_users_journal(): void
    {
        $otherUser = User::factory()->create();
        $otherJournal = Journal::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Private Other User Entry',
        ]);

        // Try viewing
        $viewResponse = $this->actingAs($this->user)->get("/journals/{$otherJournal->id}/show");
        $viewResponse->assertStatus(403);

        // Try editing
        $editResponse = $this->actingAs($this->user)->get("/journals/{$otherJournal->id}/edit");
        $editResponse->assertStatus(403);

        // Try updating
        $updateResponse = $this->actingAs($this->user)->put("/journals/{$otherJournal->id}", [
            'title' => 'Hacked Title',
            'content' => 'Hacked content',
        ]);
        $updateResponse->assertStatus(403);

        // Try deleting
        $deleteResponse = $this->actingAs($this->user)->delete("/journals/{$otherJournal->id}");
        $deleteResponse->assertStatus(403);
    }

    public function test_dashboard_search_filter_returns_matching_entries(): void
    {
        Journal::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Scripture Study in Romans',
            'content' => 'Deep reflection on grace and faith.',
        ]);

        Journal::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Grocery List',
            'content' => 'Milk, bread, apples.',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard?search=Romans');
        $response->assertStatus(200);
        $response->assertSee('Scripture Study in Romans');
        $response->assertDontSee('Grocery List');
    }
}
