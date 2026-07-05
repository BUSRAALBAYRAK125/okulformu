<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPrivacySetting extends Model
{
    protected $table = 'user_privacy_settings';

    protected $fillable = [
        'user_id',
        'profile_visibility',
        'email_visibility',
        'clubs_visibility',
        'connections_visibility',
        'social_links_visibility',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}