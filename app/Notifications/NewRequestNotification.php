<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ServiceRequest;
use App\Models\Order;

class NewRequestNotification extends Notification
{
    use Queueable;

    protected $serviceRequest, $customer;

    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        fcm(
            "New Service Request", 
            "You've received a new service request. Check your dashboard for details.", 
            $notifiable->device_id
        );

        $message = 
            "Hello Artisan!\n\n" .
            "A new service request has been submitted that matches your skills:\n" .
            "Please review the details and submit your quote if interested.\n\n" .
            "Thank you for using our service platform!";

        return (new MailMessage)
            ->subject('New Service Request Received')
            ->view('vendor.email', [
                'content' => nl2br(e($message)),
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Service Request',
            'message' => 'You have received a new service request. Please check your dashboard.',
        ];
    }
}