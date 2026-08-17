<?php

namespace Tests\Unit;

use App\Models\Journal;
use Tests\TestCase;

class JournalModelTest extends TestCase
{
    public function test_word_count_calculation(): void
    {
        $journal = new Journal([
            'content' => 'One two three four five six seven eight nine ten.',
        ]);

        $this->assertEquals(10, $journal->word_count);
    }

    public function test_reading_time_estimation(): void
    {
        // 450 words should be ~3 minutes at 200 wpm
        $words = implode(' ', array_fill(0, 450, 'word'));
        $journal = new Journal(['content' => $words]);

        $this->assertEquals(3, $journal->reading_time);
    }

    public function test_mood_emoji_mapping(): void
    {
        $happyJournal = new Journal(['mood' => 'Happy']);
        $this->assertEquals('😊', $happyJournal->mood_emoji);

        $peacefulJournal = new Journal(['mood' => 'Peaceful']);
        $this->assertEquals('🕊️', $peacefulJournal->mood_emoji);

        $gratefulJournal = new Journal(['mood' => 'Grateful']);
        $this->assertEquals('🙏', $gratefulJournal->mood_emoji);

        $customJournal = new Journal(['mood' => 'OtherMood']);
        $this->assertEquals('📝', $customJournal->mood_emoji);
    }
}
