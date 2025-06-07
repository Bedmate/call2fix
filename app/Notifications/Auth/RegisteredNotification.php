<?php
namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
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
            "Thank you for joining Call2Fix!\n\n" .
            "To ensure the security of your account and to give you the best experience possible, we need to verify your email address.\n\n" .
            "Please click the link below to confirm your email:\n" .
            "[Insert Verification Link]\n\n" .
            "Once your email is verified, you’ll be able to:\n" .
            "\t• Access a wide range of trusted maintenance and repair services\n" .
            "\t• Manage all your service requests and appointments efficiently\n" .
            "\t• Buy, rent, or lease household materials and equipment with confidence\n" .
            "\t• Receive transparent pricing and detailed service quotes upfront\n" .
            "\t• Track your service history and favorite providers in the app\n" .
            "\t• Enjoy seamless, secure payments directly through the app\n" .
            "\t• Benefit from exclusive offers and discounts\n\n" .
            "If you have any questions or need assistance, our support team is here to help. Simply reply to this email or call us at 0701-530-0138.";

        return (new MailMessage)
            ->subject('Please Verify Your Email - Call2Fix')
            ->view('vendor.property', [
                'content'    => nl2br(e($message)),
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
            'title'   => 'Welcome to ' . config('app.name'),
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
