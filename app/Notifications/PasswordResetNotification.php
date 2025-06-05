<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;

class PasswordResetNotification extends Notification
{
    use Queueable;

    protected $resetCode;

    public function __construct($resetCode)
    {
        $this->resetCode = $resetCode;
    }

    public function via($notifiable)
    {
        return $notifiable->preferredChannel();
    }

    public function toMail($notifiable)
    {
        $message = 
            "Your password reset code is: " . $this->resetCode . "\n\n" .
            "If you didn't request a password reset, please ignore this message.";

        return (new MailMessage)
            ->subject('Password Reset Code')
            ->view('vendor.property', [
                'content' => nl2br(e($message)),
                'notifiable' => $notifiable,
            ]);
    }


    // public function toNexmo($notifiable)
    // {
    //     return (new NexmoMessage)
    //                 ->content('Your password reset code is: ' . $this->resetCode);
    // }
}
