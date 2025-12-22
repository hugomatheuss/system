<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'content',
        'metadata',
        'ai_result',
        'processed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'ai_result' => 'array',
        'processed_at' => 'datetime',
    ];
}
