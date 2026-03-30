<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // 1. Imported the trait

class Journal extends Model
{
    // 2. Added HasFactory here (This fixes your BadMethodCallException!)
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'mood',
        'is_favorite',
    ];
}
