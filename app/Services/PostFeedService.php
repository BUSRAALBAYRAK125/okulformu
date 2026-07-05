<?php

namespace App\Services;

use App\Models\Club;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PostFeedService
{
    public function getHomeFeed(?User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->baseVisiblePostsQuery($user)
            ->whereNull('club_id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getSavedFeed(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->baseVisiblePostsQuery($user)
            ->whereHas('savedPosts', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getMyPosts(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyListQueryDefaults(
            Post::query()
                ->where('user_id', $user->id),
            $user
        )
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getClubFeed(Club $club, ?User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyListQueryDefaults(
            Post::query()
                ->where('club_id', $club->id)
                ->where('status', 'approved')
                ->where('is_active', true),
            $user
        )
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->orderByDesc('published_at')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getAvailableClubsForPosting(?User $user): Collection
    {
        if (! $user) {
            return collect();
        }

        if ($user->user_type === 'admin') {
            return Club::query()
                ->orderBy('name')
                ->get();
        }

        return Club::query()
            ->where('status', 'approved')
            ->where('is_active', true)
            ->whereHas('memberships', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->whereIn('role', ['member', 'president', 'founder', 'manager']);
            })
            ->orderBy('name')
            ->get();
    }

    public function canUserPostInClub(?User $user, int $clubId): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->user_type === 'admin') {
            return Club::query()
                ->where('id', $clubId)
                ->exists();
        }

        return Club::query()
            ->where('id', $clubId)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->whereHas('memberships', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->whereIn('role', ['member', 'president', 'founder', 'manager']);
            })
            ->exists();
    }

    public function canUserViewClubPosts(?User $user, Club $club): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->user_type === 'admin') {
            return true;
        }

        return $club->memberships()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereIn('role', ['member', 'president', 'founder', 'manager'])
            ->exists();
    }

    public function canUserViewPost(?User $user, Post $post): bool
    {
        if (is_null($post->club_id)) {
            return true;
        }

        if (! $user) {
            return false;
        }

        if ($user->user_type === 'admin' || $user->id === $post->user_id) {
            return true;
        }

        return Club::query()
            ->where('id', $post->club_id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->whereHas('memberships', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->whereIn('role', ['member', 'president', 'founder', 'manager']);
            })
            ->exists();
    }

    public function buildPostDetailQuery(?User $user, string $slug): Builder
    {
        return Post::query()
            ->with([
                'user',
                'reviewer',
                'category',
                'course',
                'club',
                'attachments',
                'comments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->where('is_active', true)
                        ->with([
                            'user',
                            'replies' => function ($replyQuery) {
                                $replyQuery->where('is_active', true)
                                    ->with(['user', 'likes'])
                                    ->withCount('likes')
                                    ->latest();
                            },
                            'likes',
                        ])
                        ->withCount(['likes', 'replies'])
                        ->latest();
                },
            ])
            ->withCount([
                'comments',
                'savedPosts',
                'savedPosts as is_saved' => function ($query) use ($user) {
                    if ($user) {
                        $query->where('user_id', $user->id);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->where('slug', $slug)
            ->where('status', 'approved')
            ->where('is_active', true);
    }

    private function baseVisiblePostsQuery(?User $user): Builder
    {
        $query = $this->applyListQueryDefaults(
            Post::query()
                ->where('status', 'approved')
                ->where('is_active', true),
            $user
        );

        if (! $user) {
            $query->whereNull('club_id');

            return $this->applyDefaultOrdering($query);
        }

        if ($user->user_type !== 'admin') {
            $query->where(function ($visibilityQuery) use ($user) {
                $visibilityQuery->whereNull('club_id')
                    ->orWhere('user_id', $user->id)
                    ->orWhereHas('club', function ($clubQuery) use ($user) {
                        $clubQuery->where('status', 'approved')
                            ->where('is_active', true)
                            ->whereHas('memberships', function ($membershipQuery) use ($user) {
                                $membershipQuery->where('user_id', $user->id)
                                    ->where('status', 'approved')
                                    ->whereIn('role', ['member', 'president', 'founder', 'manager']);
                            });
                    });
            });
        }

        return $this->applyDefaultOrdering($query);
    }

    private function applyListQueryDefaults(Builder $query, ?User $user): Builder
    {
        $query->with([
            'user',
            'category',
            'course',
            'club',
            'attachments',
            'reviewer',
        ])->withCount([
            'comments',
            'savedPosts',
        ]);

        if ($user) {
            $query->withCount([
                'savedPosts as is_saved' => function ($savedQuery) use ($user) {
                    $savedQuery->where('user_id', $user->id);
                },
            ]);
        }

        return $query;
    }

    private function applyDefaultOrdering(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->orderByRaw("
                CASE
                    WHEN EXISTS (
                        SELECT 1 FROM users
                        WHERE users.id = posts.user_id
                        AND users.user_type IN ('admin', 'academic')
                    ) THEN 1
                    ELSE 0
                END DESC
            ")
            ->orderByDesc('published_at')
            ->latest();
    }
}