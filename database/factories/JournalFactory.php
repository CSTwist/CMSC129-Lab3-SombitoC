<?php

namespace Database\Factories;

use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalFactory extends Factory
{
    protected $model = Journal::class;

    public function definition(): array
    {
        return [
            'title' => fake()->catchPhrase(),
            'content' => fake()->paragraphs(3, true),

            'mood' => fake()->randomElement(['Happy', 'Sad', 'Excited', 'Calm', 'Anxious', 'Productive']),
            'is_favorite' => fake()->boolean(20),

            'created_at' => fake()->dateTimeBetween('-4 months', 'now'),
        ];
    }
}
