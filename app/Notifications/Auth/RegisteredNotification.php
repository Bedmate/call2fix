<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmMessage;

class RegisteredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

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
            'Thank you for registering with ' . config('app.name') . "!\n" .
            'Time: ' . now()->toDateTimeString() . "\n" .
            'IP Address: ' . request()->ip() . "\n" .
            "We are excited to have you on board.\n\n" .
            "If you have any questions, feel free to contact our support team.";

        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name'))
            ->view('vendor.property', [
                'content' => nl2br(e($message)),
                'notifiable' => $notifiable
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
            'title' => 'Welcome to ' . config('app.name'),
            'message' => 'Thank you for registering. We are excited to have you on board!',
        ];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setData(['action' => 'Welcome to ' . config('app.name'), 'data' => 'Thank you for registering. We are excited to have you on board!'])
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->title('Welcome to ' . config('app.name'))
                ->body('Thank you for registering. We are excited to have you on board!'));
    }
}
