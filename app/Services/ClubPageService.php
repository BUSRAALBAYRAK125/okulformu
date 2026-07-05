<?php

namespace App\Services;

use App\Models\Club;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ClubPageService
{
    public function buildShowData(Club $club, ?User $user): array
    {
        $club->load([
            'creator',
            'reviewer',
            'memberships.user',
        ]);

        $approvedMemberships = $club->memberships
            ->where('status', 'approved')
            ->values();

        $pendingMemberships = $club->memberships
            ->where('status', 'pending')
            ->values();

        $founders = $approvedMemberships
            ->where('role', 'founder')
            ->values();

        $presidents = $approvedMemberships
            ->where('role', 'president')
            ->values();

        $managers = $approvedMemberships
            ->where('role', 'manager')
            ->values();

        $authMembership = $user
            ? $club->memberships->firstWhere('user_id', $user->id)
            : null;

        $canManageClub = $this->canManageClub($club, $user);
        $canPostInClub = $this->canPostInClub($authMembership, $user);
        $canViewClubPosts = $this->canViewClubPosts($authMembership, $user);

        return [
            'club' => $club,
            'clubInitial' => $this->getClubInitial($club),
            'clubStatusLabel' => $this->getClubStatusLabel($club),
            'clubStatusBadgeClass' => $this->getClubStatusBadgeClass($club),
            'creatorDisplayName' => $this->displayUserName($club->creator),
            'approvedMemberships' => $this->mapMembershipUsers($approvedMemberships),
            'pendingMemberships' => $this->mapMembershipUsers($pendingMemberships),
            'founders' => $this->mapMembershipUsers($founders),
            'presidents' => $this->mapMembershipUsers($presidents),
            'managers' => $this->mapMembershipUsers($managers),
            'authMembership' => $authMembership,
            'canManageClub' => $canManageClub,
            'canPostInClub' => $canPostInClub,
            'canViewClubPosts' => $canViewClubPosts,
            'joinAction' => $this->resolveJoinAction($club, $user, $authMembership, $canManageClub),
            'memberCount' => $approvedMemberships->count(),
            'pendingCount' => $pendingMemberships->count(),
            'foundedAtLabel' => $this->formatDate($club->founded_at),
            'createdAtLabel' => $this->formatDateTime($club->created_at),
            'reviewedAtLabel' => $this->formatDateTime($club->reviewed_at),
            'publishedAtLabel' => $this->formatDateTime($club->published_at),
        ];
    }

    public function transformClubPostsPaginator(LengthAwarePaginator $paginator, Collection $approvedMemberships): LengthAwarePaginator
    {
        $membershipMap = $approvedMemberships->keyBy('user_id');

        $items = collect($paginator->items())
            ->map(function (Post $post) use ($membershipMap) {
                $authorMembership = $membershipMap->get($post->user_id);
                $badgeText = $this->resolvePostBadgeText($post, $authorMembership);

                return [
                    'id' => $post->id,
                    'slug' => $post->slug,
                    'title' => $post->title,
                    'excerpt' => Str::limit(strip_tags((string) $post->body), 320),
                    'author_name' => $this->displayUserName($post->user),
                    'published_at_label' => $this->formatPostDateTime($post),
                    'is_pinned' => (bool) $post->is_pinned,
                    'attachments_count' => $post->attachments->count(),
                    'comments_count' => $post->comments_count,
                    'saved_posts_count' => $post->saved_posts_count,
                    'is_saved' => (bool) ($post->is_saved ?? false),
                    'badge_text' => $badgeText,
                    'has_highlight' => !is_null($badgeText),
                ];
            })
            ->values();

        $paginator->setCollection($items);

        return $paginator;
    }

    public function canManageClub(Club $club, ?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->user_type === 'admin') {
            return true;
        }

        if ((int) $club->created_by === (int) $user->id) {
            return true;
        }

        return $club->memberships()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereIn('role', ['founder', 'president', 'manager'])
            ->exists();
    }

    public function canPostInClub($authMembership, ?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->user_type === 'admin') {
            return true;
        }

        return $authMembership
            && $authMembership->status === 'approved'
            && in_array($authMembership->role, ['member', 'founder', 'president', 'manager']);
    }

    public function canViewClubPosts($authMembership, ?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->user_type === 'admin') {
            return true;
        }

        return $authMembership
            && $authMembership->status === 'approved'
            && in_array($authMembership->role, ['member', 'founder', 'president', 'manager']);
    }

    public function displayUserName(?User $user): string
    {
        if (! $user) {
            return 'Kullanıcı';
        }

        $fullName = trim(($user->name ?? '') . ' ' . ($user->surname ?? ''));

        if ($fullName !== '') {
            return $fullName;
        }

        return $user->username ?? $user->email ?? 'Kullanıcı';
    }

    public function mapMembershipUsers(Collection $memberships): Collection
    {
        return $memberships->map(function ($membership) {
            return [
                'id' => $membership->id,
                'user_id' => $membership->user_id,
                'name' => $this->displayUserName($membership->user),
                'role' => $membership->role,
                'role_label' => $this->getRoleLabel($membership->role),
                'status' => $membership->status,
            ];
        })->values();
    }

    private function getRoleLabel(?string $role): string
    {
        return match ($role) {
            'founder' => 'Kurucu',
            'president' => 'Kulüp Başkanı',
            'manager' => 'Yönetici',
            default => 'Üye',
        };
    }

    private function getClubInitial(Club $club): string
    {
        $name = trim((string) $club->name);

        if ($name === '') {
            return 'K';
        }

        return mb_strtoupper(mb_substr($name, 0, 1));
    }

    private function getClubStatusLabel(Club $club): string
    {
        if (! $club->is_active) {
            return 'Pasif';
        }

        return match ($club->status) {
            'approved' => 'Onaylandı',
            'pending' => 'Onay Bekliyor',
            'rejected' => 'Reddedildi',
            default => 'Bilinmiyor',
        };
    }

    private function getClubStatusBadgeClass(Club $club): string
    {
        if (! $club->is_active) {
            return 'secondary';
        }

        return match ($club->status) {
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    private function resolveJoinAction(Club $club, ?User $user, $authMembership, bool $canManageClub): array
    {
        if (! $user) {
            if ($club->status === 'approved' && $club->is_active) {
                return [
                    'type' => 'login',
                    'label' => 'Katılmak İçin Giriş Yap',
                    'disabled' => false,
                ];
            }

            return [
                'type' => 'none',
                'label' => null,
                'disabled' => true,
            ];
        }

        if ($canManageClub) {
            return [
                'type' => 'manage',
                'label' => null,
                'disabled' => true,
            ];
        }

        if ($club->status !== 'approved' || ! $club->is_active) {
            return [
                'type' => 'none',
                'label' => null,
                'disabled' => true,
            ];
        }

        if (! $authMembership) {
            return [
                'type' => 'join',
                'label' => 'Kulübe Katıl',
                'disabled' => false,
            ];
        }

        if ($authMembership->status === 'pending') {
            return [
                'type' => 'pending',
                'label' => 'Katılım İsteği Gönderildi',
                'disabled' => true,
            ];
        }

        if ($authMembership->status === 'approved') {
            return [
                'type' => 'approved',
                'label' => 'Zaten Üyesin',
                'disabled' => true,
            ];
        }

        return [
            'type' => 'rejoin',
            'label' => 'Tekrar Katılım İsteği Gönder',
            'disabled' => false,
        ];
    }

    private function resolvePostBadgeText(Post $post, $authorMembership): ?string
    {
        if ($authorMembership && $authorMembership->role) {
            return match ($authorMembership->role) {
                'founder' => 'Kurucu Paylaşımı',
                'president' => 'Kulüp Başkanı Paylaşımı',
                'manager' => 'Kulüp Yöneticisi Paylaşımı',
                default => null,
            };
        }

        return match ($post->user->user_type ?? null) {
            'admin' => 'Admin Paylaşımı',
            'academic' => 'Akademik Paylaşım',
            default => null,
        };
    }

    private function formatDate($date): string
    {
        return $date ? $date->format('d.m.Y') : '—';
    }

    private function formatDateTime($dateTime): string
    {
        return $dateTime ? $dateTime->format('d.m.Y H:i') : '—';
    }

    private function formatPostDateTime(Post $post): string
    {
        return $this->formatDateTime($post->published_at ?? $post->created_at);
    }
}