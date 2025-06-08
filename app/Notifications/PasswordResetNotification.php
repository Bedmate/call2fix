<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
            "We received a request to reset your Call2Fix password. No worries—we’ve got you covered!\n\n" .
            "To reset your password, please use the code below:\n" .
            "$this->resetCode \n\n" .
            "For security reasons, this link will expire in 24 hours. If you didn’t request a password reset, please ignore this email. Your account will remain secure.\n\n" .
            "If you need any further assistance, feel free to reach out to our support team by replying to this email or calling us at 0701-530-0138.";

        return (new MailMessage)
            ->subject('Reset Your Call2Fix Password')
            ->view('vendor.email', [
                'content'    => nl2br(e($message)),
                'notifiable' => $notifiable,
            ]);

    }

    // public function toNexmo($notifiable)
    // {
    //     return (new NexmoMessage)
    //                 ->content('Your password reset code is: ' . $this->resetCode);
    // }
}
