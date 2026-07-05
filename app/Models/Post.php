<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Post extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'body',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'published_at',
        'is_pinned',
        'pinned_at',
        'pinned_by',
        'allow_comments',
        'is_active',
        'view_count',
        'club_id',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
        'is_pinned' => 'boolean',
        'pinned_at' => 'datetime',
        'allow_comments' => 'boolean',
        'is_active' => 'boolean',
        'view_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function pinner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pinned_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    public function course(): BelongsTo
{
    return $this->belongsTo(Course::class);
}
public function attachments(): HasMany
{
    return $this->hasMany(PostAttachment::class)->orderBy('sort_order');
}
public function savedPosts(): HasMany
{
    return $this->hasMany(SavedPost::class);
}
public function club(): BelongsTo
{
    return $this->belongsTo(Club::class);
}
}