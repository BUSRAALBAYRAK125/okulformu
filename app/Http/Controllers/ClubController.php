<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\ClubPageService;
use App\Services\PostFeedService;

class ClubController extends Controller
{
    public function __construct(
    private ClubPageService $clubPageService,
    private PostFeedService $postFeedService
) {
}
    public function create()
    {
        abort_unless(auth()->check(), 403);

        $users = User::query()
            ->orderBy('name')
            ->get();

        return view('clubs.create', compact('users'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->check(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'founded_at' => ['nullable', 'date'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['integer', 'exists:users,id'],
            'president_ids' => ['nullable', 'array'],
            'president_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $memberIds = collect($validated['member_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $presidentIds = collect($validated['president_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($presidentIds->diff($memberIds)->isNotEmpty()) {
            return back()
                ->withErrors([
                    'president_ids' => 'Başkan olarak seçilen kullanıcılar, üye listesinde de bulunmalıdır.',
                ])
                ->withInput();
        }

        $slug = $this->makeUniqueSlug($validated['name']);

        $club = DB::transaction(function () use ($validated, $slug, $memberIds, $presidentIds) {
            $club = Club::create([
                'created_by' => auth()->id(),
                'name' => $validated['name'],
                'founded_at' => $validated['founded_at'] ?? null,
                'slug' => $slug,
                'short_description' => $validated['short_description'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'rejection_reason' => null,
                'published_at' => null,
                'is_active' => true,
            ]);

            $this->syncMembers(
                $club,
                auth()->id(),
                $memberIds,
                $presidentIds
            );

            return $club;
        });

        return redirect()
            ->route('clubs.show', $club)
            ->with('success', 'Kulüp başvurusu başarıyla oluşturuldu.');
    }
public function show(Club $club)
{
    $user = auth()->user();

    $pageData = $this->clubPageService->buildShowData($club, $user);

    $canViewPage = ($club->status === 'approved' && $club->is_active)
        || $pageData['canManageClub'];

    abort_unless($canViewPage, 403);

    $clubPosts = null;

    if ($pageData['canViewClubPosts']) {
        $clubPosts = $this->postFeedService->getClubFeed($club, $user, 10);
    }

    return view('clubs.show', array_merge($pageData, [
        'clubPosts' => $clubPosts,
    ]));
}
    public function edit(Club $club)
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->canManageClub(auth()->user(), $club), 403);

        $club->load('memberships');

        $users = User::query()
            ->orderBy('name')
            ->get();

        $selectedMemberIds = $club->memberships
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $selectedPresidentIds = $club->memberships
            ->whereIn('role', ['president'])
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        return view('clubs.edit', compact(
            'club',
            'users',
            'selectedMemberIds',
            'selectedPresidentIds'
        ));
    }

    public function update(Request $request, Club $club)
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->canManageClub(auth()->user(), $club), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'founded_at' => ['nullable', 'date'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['integer', 'exists:users,id'],
            'president_ids' => ['nullable', 'array'],
            'president_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $memberIds = collect($validated['member_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $presidentIds = collect($validated['president_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($presidentIds->diff($memberIds)->isNotEmpty()) {
            return back()
                ->withErrors([
                    'president_ids' => 'Başkan olarak seçilen kullanıcılar, üye listesinde de bulunmalıdır.',
                ])
                ->withInput();
        }

        $slug = $this->makeUniqueSlug($validated['name'], $club->id);

        DB::transaction(function () use ($club, $validated, $slug, $memberIds, $presidentIds) {
            $updateData = [
                'name' => $validated['name'],
                'founded_at' => $validated['founded_at'] ?? null,
                'slug' => $slug,
                'short_description' => $validated['short_description'] ?? null,
                'description' => $validated['description'] ?? null,
            ];

            if ($club->status !== 'approved') {
                $updateData['status'] = 'pending';
                $updateData['reviewed_by'] = null;
                $updateData['reviewed_at'] = null;
                $updateData['rejection_reason'] = null;
                $updateData['published_at'] = null;
            }

            $club->update($updateData);

            $club->memberships()->delete();

            $this->syncMembers(
                $club,
                $club->created_by,
                $memberIds,
                $presidentIds
            );
        });

        return redirect()
            ->route('clubs.show', $club)
            ->with('success', 'Kulüp başarıyla güncellendi.');
    }

    public function destroy(Club $club)
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->canManageClub(auth()->user(), $club), 403);

        $club->delete();

        return redirect()
            ->route('home')
            ->with('success', 'Kulüp başarıyla silindi.');
    }

    private function canManageClub(User $user, Club $club): bool
    {
        if ($club->created_by === $user->id) {
            return true;
        }

        return ClubMember::query()
            ->where('club_id', $club->id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereIn('role', ['founder', 'president', 'manager'])
            ->exists();
    }

    private function syncMembers(
        Club $club,
        int $founderUserId,
        Collection $memberIds,
        Collection $presidentIds
    ): void {
        $memberIds = $memberIds
            ->reject(fn ($id) => (int) $id === (int) $founderUserId)
            ->values();

        $presidentIds = $presidentIds
            ->reject(fn ($id) => (int) $id === (int) $founderUserId)
            ->values();

        ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $founderUserId,
            'role' => 'founder',
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'joined_at' => now(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        foreach ($memberIds as $userId) {
            ClubMember::create([
                'club_id' => $club->id,
                'user_id' => $userId,
                'role' => $presidentIds->contains((int) $userId) ? 'president' : 'member',
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'joined_at' => now(),
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);
        }
    }

    private function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'kulup';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            Club::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
    public function joinRequest(Club $club)
{
    abort_unless(auth()->check(), 403);

    if ($club->status !== 'approved' || !$club->is_active) {
        return back()->with('error', 'Bu kulübe şu anda katılım isteği gönderilemez.');
    }

    $existingMembership = ClubMember::query()
        ->where('club_id', $club->id)
        ->where('user_id', auth()->id())
        ->first();

    if ($existingMembership) {
        if ($existingMembership->status === 'approved') {
            return back()->with('error', 'Zaten bu kulübün üyesisin.');
        }

        if ($existingMembership->status === 'pending') {
            return back()->with('error', 'Bu kulüp için zaten bekleyen bir isteğin var.');
        }

        $existingMembership->update([
            'role' => 'member',
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
            'joined_at' => null,
        ]);

        return back()->with('success', 'Kulübe katılma isteğin tekrar gönderildi.');
    }

    ClubMember::create([
        'club_id' => $club->id,
        'user_id' => auth()->id(),
        'role' => 'member',
        'status' => 'pending',
        'approved_by' => null,
        'joined_at' => null,
        'approved_at' => null,
        'rejection_reason' => null,
    ]);

    return back()->with('success', 'Kulübe katılma isteğin gönderildi.');
}

public function approveMember(Club $club, ClubMember $clubMember)
{
    abort_unless(auth()->check(), 403);
    abort_unless($this->canManageClub(auth()->user(), $club), 403);

    if ((int) $clubMember->club_id !== (int) $club->id) {
        abort(404);
    }

    $clubMember->update([
        'status' => 'approved',
        'approved_by' => auth()->id(),
        'approved_at' => now(),
        'joined_at' => now(),
        'rejection_reason' => null,
    ]);

    return back()->with('success', 'Üyelik isteği onaylandı.');
}

public function rejectMember(Request $request, Club $club, ClubMember $clubMember)
{
    abort_unless(auth()->check(), 403);
    abort_unless($this->canManageClub(auth()->user(), $club), 403);

    if ((int) $clubMember->club_id !== (int) $club->id) {
        abort(404);
    }

    $clubMember->update([
        'status' => 'rejected',
        'approved_by' => auth()->id(),
        'approved_at' => null,
        'joined_at' => null,
        'rejection_reason' => $request->input('rejection_reason'),
    ]);

    return back()->with('success', 'Üyelik isteği reddedildi.');
}
}