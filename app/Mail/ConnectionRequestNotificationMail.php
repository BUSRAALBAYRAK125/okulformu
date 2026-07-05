<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConnectionRequestNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $sender;
    public User $receiver;
    public string $profileUrl;

    public function __construct(User $sender, User $receiver, string $profileUrl)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->profileUrl = $profileUrl;
    }

    public function build(): self
    {
        return $this->subject('Yeni bağlantı isteği aldın')
            ->view('emails.connection-request-notification');
    }
}