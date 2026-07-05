<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubMember extends Model
{
    protected $table = 'club_members';

    protected $fillable = [
        'club_id',
        'user_id',
        'role',
        'status',
        'approved_by',
        'joined_at',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}