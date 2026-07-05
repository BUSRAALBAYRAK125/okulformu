<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Password;



class ProfileController extends Controller
{
   public function show(User $user, Request $request): View
{
    $activeTab = $request->get('tab', 'overview');

    if (!in_array($activeTab, ['overview', 'posts', 'comments'])) {
        $activeTab = 'overview';
    }

    $posts = $user->posts()
        ->with(['category', 'course', 'attachments'])
        ->withCount(['comments', 'savedPosts'])
        ->where('status', 'approved')
        ->where('is_active', true)
        ->orderByDesc('is_pinned')
        ->orderByDesc('published_at')
        ->latest()
        ->get();

    return view('profiles.show', compact('user', 'posts', 'activeTab'));
}

   public function edit(Request $request): View
{
    $activeEditTab = $request->get('tab', 'general');

    if (!in_array($activeEditTab, ['general', 'media', 'privacy', 'notifications'])) {
        $activeEditTab = 'general';
    }

    $user = auth()->user()->load([
        'profile',
        'privacySetting',
        'notificationSetting',
        'items' => function ($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        },
    ]);

    return view('profiles.edit', compact('user', 'activeEditTab'));
}

   public function update(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'headline' => ['nullable', 'string', 'max:160'],
        'bio' => ['nullable', 'string'],

        'linkedin_url' => ['nullable', 'url', 'max:255'],
        'github_url' => ['nullable', 'url', 'max:255'],
        'website_url' => ['nullable', 'url', 'max:255'],

        'interests' => ['nullable', 'string'],

        'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        'cover_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],

        'profile_visibility' => ['required', 'in:public,connections,private'],
        'email_visibility' => ['required', 'in:public,connections,private'],
        'clubs_visibility' => ['required', 'in:public,connections,private'],
        'connections_visibility' => ['required', 'in:public,connections,private'],
        'social_links_visibility' => ['required', 'in:public,connections,private'],

        'connection_request_enabled' => ['nullable'],
        'comment_enabled' => ['nullable'],
        'event_enabled' => ['nullable'],
        'announcement_enabled' => ['nullable'],

        'email_connection_request_enabled' => ['nullable'],
        'email_comment_enabled' => ['nullable'],
        'email_event_enabled' => ['nullable'],
        'email_announcement_enabled' => ['nullable'],
    ]);

    $user = auth()->user()->load([
        'profile',
        'privacySetting',
        'notificationSetting',
        'items',
    ]);

    DB::transaction(function () use ($user, $validated, $request) {
        $profile = $user->profile()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'headline' => null,
                'bio' => null,
                'photo' => null,
                'cover_photo' => null,
                'city' => null,
                'linkedin_url' => null,
                'github_url' => null,
                'website_url' => null,
            ]
        );

        $privacy = $user->privacySetting()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'profile_visibility' => 'public',
                'email_visibility' => 'private',
                'clubs_visibility' => 'public',
                'connections_visibility' => 'private',
                'social_links_visibility' => 'public',
            ]
        );

        $notificationSetting = $user->notificationSetting()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'connection_request_enabled' => true,
                'comment_enabled' => true,
                'event_enabled' => true,
                'announcement_enabled' => true,
                'email_connection_request_enabled' => false,
                'email_comment_enabled' => false,
                'email_event_enabled' => false,
                'email_announcement_enabled' => false,
            ]
        );

        if ($request->hasFile('photo')) {
            $this->deleteStoredFile($profile->photo);
            $profile->photo = $this->storePublicUpload($request->file('photo'), $user->id, 'photos');
        }

        if ($request->hasFile('cover_photo')) {
            $this->deleteStoredFile($profile->cover_photo);
            $profile->cover_photo = $this->storePublicUpload($request->file('cover_photo'), $user->id, 'covers');
        }

        $profile->headline = $validated['headline'] ?? null;
        $profile->bio = $validated['bio'] ?? null;
        $profile->linkedin_url = $validated['linkedin_url'] ?? null;
        $profile->github_url = $validated['github_url'] ?? null;
        $profile->website_url = $validated['website_url'] ?? null;
        $profile->save();

        $privacy->update([
            'profile_visibility' => $validated['profile_visibility'],
            'email_visibility' => $validated['email_visibility'],
            'clubs_visibility' => $validated['clubs_visibility'],
            'connections_visibility' => $validated['connections_visibility'],
            'social_links_visibility' => $validated['social_links_visibility'],
        ]);

        $notificationSetting->update([
            'connection_request_enabled' => $request->has('connection_request_enabled') && $request->input('connection_request_enabled') == '1',
            'comment_enabled' => $request->has('comment_enabled') && $request->input('comment_enabled') == '1',
            'event_enabled' => $request->has('event_enabled') && $request->input('event_enabled') == '1',
            'announcement_enabled' => $request->has('announcement_enabled') && $request->input('announcement_enabled') == '1',

            'email_connection_request_enabled' => $request->has('email_connection_request_enabled') && $request->input('email_connection_request_enabled') == '1',
            'email_comment_enabled' => $request->has('email_comment_enabled') && $request->input('email_comment_enabled') == '1',
            'email_event_enabled' => $request->has('email_event_enabled') && $request->input('email_event_enabled') == '1',
            'email_announcement_enabled' => $request->has('email_announcement_enabled') && $request->input('email_announcement_enabled') == '1',
        ]);

        $user->items()
            ->where('type', 'interest')
            ->delete();

        $interests = collect(explode(',', $validated['interests'] ?? ''))
            ->map(fn ($item) => trim($item))
            ->map(fn ($item) => ltrim($item, '#'))
            ->filter()
            ->unique()
            ->take(12)
            ->values();

        foreach ($interests as $index => $interest) {
            $user->items()->create([
                'type' => 'interest',
                'title' => null,
                'value' => $interest,
                'url' => null,
                'visibility' => 'public',
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }
    });

    return redirect()
        ->route('profile.edit')
        ->with('success', 'Profil ayarları güncellendi.');
}
   public function sendPasswordResetLink(): RedirectResponse
{
    $user = auth()->user();

    $status = Password::sendResetLink([
        'email' => $user->email,
    ]);

    if ($status !== Password::RESET_LINK_SENT) {
        return redirect()
            ->route('profile.edit')
            ->withErrors([
                'password_reset' => 'Şifre sıfırlama bağlantısı gönderilemedi.',
            ]);
    }

    return redirect()
        ->route('profile.edit')
        ->with('success', 'Şifre sıfırlama bağlantısı e-posta adresine gönderildi.');
}

    private function storePublicUpload(UploadedFile $file, int $userId, string $type): string
    {
        $relativeDirectory = "uploads/users/{$userId}/profile/{$type}";
        $absoluteDirectory = public_path($relativeDirectory);

        if (!File::exists($absoluteDirectory)) {
            File::makeDirectory($absoluteDirectory, 0755, true);
        }

        $filename = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();

        $file->move($absoluteDirectory, $filename);

        return $relativeDirectory . '/' . $filename;
    }

    private function deleteStoredFile(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        if (str_starts_with($path, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $path));
            return;
        }

        $fullPath = public_path($path);

        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}