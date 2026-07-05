<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationSetting extends Model
{
    protected $table = 'user_notification_settings';

    protected $fillable = [
        'user_id',
        'connection_request_enabled',
        'comment_enabled',
        'event_enabled',
        'announcement_enabled',
        'email_connection_request_enabled',
        'email_comment_enabled',
        'email_event_enabled',
        'email_announcement_enabled',
    ];

    protected $casts = [
        'connection_request_enabled' => 'boolean',
        'comment_enabled' => 'boolean',
        'event_enabled' => 'boolean',
        'announcement_enabled' => 'boolean',
        'email_connection_request_enabled' => 'boolean',
        'email_comment_enabled' => 'boolean',
        'email_event_enabled' => 'boolean',
        'email_announcement_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}