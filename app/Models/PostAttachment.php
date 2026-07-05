<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostAttachment extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}