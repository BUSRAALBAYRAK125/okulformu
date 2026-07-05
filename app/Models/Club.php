<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    protected $table = 'clubs';

    protected $fillable = [
        'created_by',
        'name',
        'slug',
        'short_description',
        'description',
        'logo',
        'cover_image',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'published_at',
        'is_active',
        'founded_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'founded_at' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(ClubMember::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_members')
            ->withPivot([
                'role',
                'status',
                'approved_by',
                'joined_at',
                'approved_at',
                'rejection_reason',
            ])
            ->withTimestamps();
    }
    public function posts(): HasMany
{
    return $this->hasMany(Post::class);
}
}