<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetComplete extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = 
            "Your password has been successfully reset.\n\n" .
            "If you did not request a password reset, please contact our support team immediately.\n\n" .
            "Thank you for using our application!";

        return (new MailMessage)
            ->subject('Password Reset Successful')
            ->view('vendor.property', [
                'content' => nl2br(e($message)),
                'notifiable' => $notifiable,
            ]);
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Password reset.',
            'message' => 'Your password has been successfully reset.',
        ];
    }
}
