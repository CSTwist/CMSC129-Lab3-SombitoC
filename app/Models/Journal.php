<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    // Enables moving to trash instead of permanent deletion
    use SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];
}
