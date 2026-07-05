<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserConnection;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Mail\ConnectionRequestNotificationMail;
use Illuminate\Support\Facades\Mail;

class UserConnectionController extends Controller
{
  public function send(User $user): RedirectResponse
{
    $authUser = auth()->user();

    if (!$authUser || $authUser->id === $user->id) {
        return redirect()
            ->back()
            ->withErrors([
                'connection' => 'Kendine bağlantı isteği gönderemezsin.',
            ]);
    }

    $existingConnection = UserConnection::query()
        ->where(function ($query) use ($authUser, $user) {
            $query->where('sender_user_id', $authUser->id)
                ->where('receiver_user_id', $user->id);
        })
        ->orWhere(function ($query) use ($authUser, $user) {
            $query->where('sender_user_id', $user->id)
                ->where('receiver_user_id', $authUser->id);
        })
        ->first();

    if ($existingConnection && in_array($existingConnection->status, ['pending', 'accepted', 'blocked'])) {
        return redirect()
            ->back()
            ->withErrors([
                'connection' => 'Bu kullanıcıyla zaten bir bağlantı kaydı var.',
            ]);
    }

    DB::transaction(function () use ($authUser, $user, $existingConnection) {
        if ($existingConnection && $existingConnection->status === 'rejected') {
            $existingConnection->update([
                'sender_user_id' => $authUser->id,
                'receiver_user_id' => $user->id,
                'status' => 'pending',
                'requested_at' => now(),
                'responded_at' => null,
            ]);

            $connection = $existingConnection;

            UserNotification::query()
                ->where('related_type', 'user_connection')
                ->where('related_id', $connection->id)
                ->delete();
        } else {
            $connection = UserConnection::create([
                'sender_user_id' => $authUser->id,
                'receiver_user_id' => $user->id,
                'status' => 'pending',
                'requested_at' => now(),
            ]);
        }

        $receiverNotificationSetting = $user->notificationSetting()->firstOrCreate(
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

        if ($receiverNotificationSetting->connection_request_enabled) {
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'connection_request',
                'title' => 'Yeni bağlantı isteği',
                'body' => $authUser->name . ' ' . $authUser->surname . ' sana bağlantı isteği gönderdi.',
                'related_type' => 'user_connection',
                'related_id' => $connection->id,
                'action_url' => route('profile.show', ['user' => $authUser->id]),
                'is_read' => false,
                'read_at' => null,
            ]);
        }

        if ($receiverNotificationSetting->email_connection_request_enabled) {
            Mail::to($user->email)->send(
                new ConnectionRequestNotificationMail(
                    $authUser,
                    $user,
                    route('profile.show', ['user' => $authUser->id])
                )
            );
        }
    });

    return redirect()
        ->back()
        ->with('success', 'Bağlantı isteği gönderildi.');
}
    public function cancel(UserConnection $connection): RedirectResponse
{
    $authUser = auth()->user();

    if (!$authUser || $connection->sender_user_id !== $authUser->id) {
        abort(403);
    }

    if ($connection->status !== 'pending') {
        return redirect()
            ->back()
            ->withErrors([
                'connection' => 'Sadece bekleyen bağlantı isteği geri çekilebilir.',
            ]);
    }

    DB::transaction(function () use ($connection) {
        UserNotification::query()
            ->where('type', 'connection_request')
            ->where('related_type', 'user_connection')
            ->where('related_id', $connection->id)
            ->delete();

        $connection->delete();
    });

    return redirect()
        ->back()
        ->with('success', 'Bağlantı isteği geri çekildi.');
}

public function disconnect(UserConnection $connection): RedirectResponse
{
    $authUser = auth()->user();

    if (
        !$authUser ||
        (
            $connection->sender_user_id !== $authUser->id &&
            $connection->receiver_user_id !== $authUser->id
        )
    ) {
        abort(403);
    }

    if ($connection->status !== 'accepted') {
        return redirect()
            ->back()
            ->withErrors([
                'connection' => 'Sadece kabul edilmiş bağlantılar kesilebilir.',
            ]);
    }

    DB::transaction(function () use ($connection, $authUser) {
        $otherUserId = $connection->sender_user_id === $authUser->id
            ? $connection->receiver_user_id
            : $connection->sender_user_id;

        UserNotification::create([
            'user_id' => $otherUserId,
            'type' => 'connection_removed',
            'title' => 'Bağlantı kaldırıldı',
            'body' => $authUser->name . ' ' . $authUser->surname . ' seninle olan bağlantıyı kaldırdı.',
            'related_type' => 'user_connection',
            'related_id' => $connection->id,
            'action_url' => route('profile.show', ['user' => $authUser->id]),
            'is_read' => false,
            'read_at' => null,
        ]);

        UserNotification::query()
            ->where('related_type', 'user_connection')
            ->where('related_id', $connection->id)
            ->delete();

        $connection->delete();
    });

    return redirect()
        ->back()
        ->with('success', 'Bağlantı kaldırıldı.');
}

   public function accept(UserConnection $connection): RedirectResponse
{
    $authUser = auth()->user();

    if (!$authUser || $connection->receiver_user_id !== $authUser->id) {
        abort(403);
    }

    if ($connection->status !== 'pending') {
        return redirect()
            ->back()
            ->withErrors([
                'connection' => 'Bu bağlantı isteği artık işlem yapılabilir durumda değil.',
            ]);
    }

    \Illuminate\Support\Facades\DB::transaction(function () use ($connection, $authUser) {
        $connection->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);

        \App\Models\UserNotification::query()
            ->where('user_id', $authUser->id)
            ->where('type', 'connection_request')
            ->where('related_type', 'user_connection')
            ->where('related_id', $connection->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        \App\Models\UserNotification::create([
            'user_id' => $connection->sender_user_id,
            'type' => 'connection_accepted',
            'title' => 'Bağlantı isteğin kabul edildi',
            'body' => $authUser->name . ' ' . $authUser->surname . ' bağlantı isteğini kabul etti.',
            'related_type' => 'user_connection',
            'related_id' => $connection->id,
            'action_url' => route('profile.show', ['user' => $authUser->id]),
            'is_read' => false,
            'read_at' => null,
        ]);
    });

    return redirect()
        ->back()
        ->with('success', 'Bağlantı isteği kabul edildi.');
}

public function reject(UserConnection $connection): RedirectResponse
{
    $authUser = auth()->user();

    if (!$authUser || $connection->receiver_user_id !== $authUser->id) {
        abort(403);
    }

    if ($connection->status !== 'pending') {
        return redirect()
            ->back()
            ->withErrors([
                'connection' => 'Bu bağlantı isteği artık işlem yapılabilir durumda değil.',
            ]);
    }

    \Illuminate\Support\Facades\DB::transaction(function () use ($connection, $authUser) {
        $connection->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);

        \App\Models\UserNotification::query()
            ->where('user_id', $authUser->id)
            ->where('type', 'connection_request')
            ->where('related_type', 'user_connection')
            ->where('related_id', $connection->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        \App\Models\UserNotification::create([
            'user_id' => $connection->sender_user_id,
            'type' => 'connection_rejected',
            'title' => 'Bağlantı isteğin reddedildi',
            'body' => $authUser->name . ' ' . $authUser->surname . ' bağlantı isteğini reddetti.',
            'related_type' => 'user_connection',
            'related_id' => $connection->id,
            'action_url' => route('profile.show', ['user' => $authUser->id]),
            'is_read' => false,
            'read_at' => null,
        ]);
    });

    return redirect()
        ->back()
        ->with('success', 'Bağlantı isteği reddedildi.');
}
}