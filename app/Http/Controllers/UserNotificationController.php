<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UserNotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function go(UserNotification $notification): RedirectResponse
    {
        $authUser = auth()->user();

        if (!$authUser || $notification->user_id !== $authUser->id) {
            abort(403);
        }

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        if (!empty($notification->action_url)) {
            return redirect()->to($notification->action_url);
        }

        return redirect()->route('profile.show', ['user' => $authUser->id]);
    }

    public function markRead(UserNotification $notification): RedirectResponse
    {
        $authUser = auth()->user();

        if (!$authUser || $notification->user_id !== $authUser->id) {
            abort(403);
        }

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return back()->with('success', 'Bildirim okundu olarak işaretlendi.');
    }

    public function destroy(UserNotification $notification): RedirectResponse
    {
        $authUser = auth()->user();

        if (!$authUser || $notification->user_id !== $authUser->id) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Bildirim silindi.');
    }

    public function markAllRead(): RedirectResponse
    {
        $authUser = auth()->user();

        if (!$authUser) {
            abort(403);
        }

        $authUser->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Tüm bildirimler okundu olarak işaretlendi.');
    }
}