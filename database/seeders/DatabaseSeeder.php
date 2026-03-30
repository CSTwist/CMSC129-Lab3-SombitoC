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
        $user = User::factory()->create([
            'name' => 'Iska',
            'email' => 'iska@up.edu.ph',
            'password' => Hash::make('password123'), // Default password
        ]);

        Journal::factory(15)->create([
            'user_id' => $user->id
        ]);

        Journal::factory(5)->create([
            'user_id' => $user->id,
            'deleted_at' => now() // Instantly soft-deletes them
        ]);
    }
}
