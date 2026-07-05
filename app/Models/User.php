<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserNotification;
use App\Models\UserNotificationSetting;



class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'user_type',
        'student_no',
        'graduation_year',
        'department',
        'approval_status',
        'approved_by',
        'approved_at',
        'reject_reason',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'last_login_at' => 'datetime',
        'graduation_year' => 'integer',
        'password' => 'hashed',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function reviewedPosts(): HasMany
    {
        return $this->hasMany(Post::class, 'reviewed_by');
    }

    public function pinnedPosts(): HasMany
    {
        return $this->hasMany(Post::class, 'pinned_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function commentLikes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    public function savedPosts(): HasMany
    {
        return $this->hasMany(SavedPost::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function privacySetting(): HasOne
    {
        return $this->hasOne(UserPrivacySetting::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(UserItem::class)->orderBy('sort_order');
    }

    public function sentConnections(): HasMany
    {
        return $this->hasMany(UserConnection::class, 'sender_user_id');
    }

    public function receivedConnections(): HasMany
    {
        return $this->hasMany(UserConnection::class, 'receiver_user_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot([
                'assigned_at',
                'assigned_by',
                'is_active',
            ])
            ->withTimestamps();
    }

    public function activeRoles(): BelongsToMany
    {
        return $this->roles()
            ->wherePivot('is_active', true)
            ->where('roles.is_active', true);
    }

    public function hasRole(string $slug): bool
    {
        return $this->activeRoles()->where('slug', $slug)->exists();
    }

    public function hasAnyRole(array $slugs): bool
    {
        return $this->activeRoles()->whereIn('slug', $slugs)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->user_type === 'admin' || $this->hasRole('admin');
    }

    public function isAcademic(): bool
    {
        return $this->user_type === 'academic' || $this->hasRole('academic');
    }

    public function isStudent(): bool
    {
        return $this->user_type === 'student' || $this->hasRole('student');
    }

    public function isGraduate(): bool
    {
        return $this->user_type === 'graduate' || $this->hasRole('graduate');
    }
    public function notifications(): HasMany
{
    return $this->hasMany(UserNotification::class)->latest();
}

public function unreadNotifications(): HasMany
{
    return $this->hasMany(UserNotification::class)
        ->where('is_read', false)
        ->latest();
}
public function notificationSetting(): HasOne
{
    return $this->hasOne(UserNotificationSetting::class);
}
public function createdClubs(): HasMany
{
    return $this->hasMany(Club::class, 'created_by');
}

public function reviewedClubs(): HasMany
{
    return $this->hasMany(Club::class, 'reviewed_by');
}

public function clubMemberships(): HasMany
{
    return $this->hasMany(ClubMember::class);
}

public function clubs(): BelongsToMany
{
    return $this->belongsToMany(Club::class, 'club_members')
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
}