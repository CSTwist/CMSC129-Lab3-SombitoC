<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'mood',
        'is_favorite',
    ];

    protected function casts(): array
    {
        return [
            'is_favorite' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * User relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for searching journals by title or content.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('content', 'like', "%{$term}%");
        });
    }

    /**
     * Scope for favorite journals.
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope for filtering by mood.
     */
    public function scopeMood($query, ?string $mood)
    {
        if (blank($mood)) {
            return $query;
        }

        return $query->where('mood', $mood);
    }

    /**
     * Calculate word count of content.
     */
    public function getWordCountAttribute(): int
    {
        return str_word_count(strip_tags((string) $this->content));
    }

    /**
     * Calculate estimated reading time in minutes (avg 200 wpm).
     */
    public function getReadingTimeAttribute(): int
    {
        return max(1, (int) ceil($this->word_count / 200));
    }

    /**
     * Return mood emoji representation.
     */
    public function getMoodEmojiAttribute(): string
    {
        return match (strtolower((string) $this->mood)) {
            'happy', 'joyful', 'excited' => '😊',
            'grateful', 'thankful', 'blessed' => '🙏',
            'peaceful', 'calm', 'serene' => '🕊️',
            'reflective', 'thoughtful' => '📖',
            'sad', 'down', 'grieving' => '😔',
            'anxious', 'worried', 'stressed' => '😟',
            'tired', 'exhausted' => '😴',
            'inspired', 'motivated' => '✨',
            default => '📝',
        };
    }
}

