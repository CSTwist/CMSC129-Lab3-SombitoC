<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Journal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create your main test user (using the email from your mockups)
        $user = User::factory()->create([
            'name' => 'Iska',
            'email' => 'iska@up.edu.ph',
            'password' => Hash::make('password123'), // Default password
        ]);

        // 2. Generate 15 fake journal entries and assign them to your user
        Journal::factory(15)->create([
            'user_id' => $user->id
        ]);

        // 3. (Optional) Generate a few trashed entries to populate the "Recently Deleted" page
        Journal::factory(5)->create([
            'user_id' => $user->id,
            'deleted_at' => now() // Instantly soft-deletes them
        ]);
    }
}
