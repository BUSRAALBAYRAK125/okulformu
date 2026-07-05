<?php

namespace App\Mail;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $sender;
    public User $receiver;
    public Post $post;
    public string $postUrl;

    public function __construct(User $sender, User $receiver, Post $post, string $postUrl)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->post = $post;
        $this->postUrl = $postUrl;
    }

    public function build(): self
    {
        return $this->subject('Gönderine yeni yorum geldi')
            ->view('emails.comment-notification');
    }
}