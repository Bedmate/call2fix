<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReworkIssuedNotification extends Notification
{
    use Queueable;

    protected $serviceRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct($serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
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
        fcm(
            "Rework issued", 
            "A Rework has been issued for a task you (or your artisan) worked on", 
            $notifiable->device_id
        );

        $message = 
            "A rework has been issued for your service request.\n\n" .
            "Service Request ID: " . $this->serviceRequest->id . "\n" .
            "Description: " . $this->serviceRequest->description . "\n\n" .
            "Please review the rework details and take necessary action.";

        return (new MailMessage)
            ->subject('Rework Issued for Service Request #' . $this->serviceRequest->id)
            ->view('vendor.email', [
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
            'title' => "Rework issued",
            'message' => "A rework has been issued for your service request. Service Title: {$this->serviceRequest->title} and Service Description: {$this->serviceRequest->description}",
        ];
    }
}
