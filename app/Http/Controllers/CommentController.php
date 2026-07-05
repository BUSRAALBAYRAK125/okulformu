<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\CommentNotificationMail;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        DB::transaction(function () use ($validated, $post) {
            $comment = Comment::create([
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'parent_id' => $validated['parent_id'] ?? null,
                'body' => $validated['body'],
                'is_active' => true,
            ]);

            $postOwnerId = $post->user_id;
            $authUser = auth()->user();

            if ($postOwnerId && $postOwnerId !== auth()->id()) {
                $postOwner = $post->user ?? $post->user()->first();

                if ($postOwner) {
                    $notificationSetting = $postOwner->notificationSetting()->firstOrCreate(
                        ['user_id' => $postOwner->id],
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

                    if ($notificationSetting->comment_enabled) {
                        UserNotification::create([
                            'user_id' => $postOwner->id,
                            'type' => 'comment',
                            'title' => 'Gönderine yeni yorum geldi',
                            'body' => $authUser->name . ' ' . $authUser->surname . ' gönderine yorum yaptı.',
                            'related_type' => 'comment',
                            'related_id' => $comment->id,
                            'action_url' => route('posts.show', $post->slug),
                            'is_read' => false,
                            'read_at' => null,
                        ]);
                    }
                    if ($notificationSetting->email_comment_enabled) {
    Mail::to($postOwner->email)->send(
        new CommentNotificationMail(
            $authUser,
            $postOwner,
            $post,
            route('posts.show', $post->slug)
        )
    );
}
                }
            }
        });

        return back()->with('success', 'Yorum eklendi.');
    }
}