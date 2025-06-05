<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class ServiceProviderActionNotification extends Notification
{
    use Queueable;

    protected $action;
    protected $data;

    public function __construct($action, $data)
    {
        $this->action = $action;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', FcmChannel::class];
    }

    public function toMail($notifiable)
    {
        $message = 
            "Action: " . $this->action . "\n\n" .
            "Data: " . json_encode($this->data);

        return (new MailMessage)
            ->subject('Notification')
            ->view('vendor.property', [
                'content' => nl2br(e($message)),
                'notifiable' => $notifiable,
            ]);
    }


    public function toDatabase($notifiable)
    {
        return [
            'action' => $this->action,
            'data' => $this->data,
            'title' => $this->action,
            'message' => 'Please check your service logs for new changes'
        ];
    }

    public function toFcm($notifiable)
    {
        return fcm($notifiable, $this->action, $this->data);
    }
}
