<?php
namespace App\Notifications\ServiceRequest;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestSuccessful extends Notification
{
    use Queueable;

    protected $order, $serviceRequest, $assignedProviders, $property;

    /**
     * Create a new notification instance.
     */
    public function __construct($serviceRequest, $assignedProviders, $property)
    {
        $this->serviceRequest = $serviceRequest;
        $this->assignedProviders = $assignedProviders;
        $this->property = $property;
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
            "New Service Request",
            "Your Service Request has been placed successfully.",
            auth()->user()->device_id
        );

        // Example placeholders — you should replace these with actual variables
        $requestTitle      = $this->serviceRequest->problem_title ?? '[Request Title]';
        $propertyAddress   = $this->property->property_address ?? '[Property Address]';
        $requestedDateTime = $this->serviceRequest->created_at ?? '[Date & Time]';
        $providers         = $this->assignedProviders ?? []; // Array of ['name' => '', 'contact' => '']

        $message =
            "Your service request has been successfully submitted to the open market. Our system has assigned up to 5 of the closest service providers to handle your request.\n\n" .
            "Request Details:\n" .
            "\t• Service Requested: {$requestTitle}\n" .
            "\t• Location: {$propertyAddress}\n" .
            "\t• Requested Date & Time: {$requestedDateTime}\n\n" .
            "Assigned Providers:\n";

        // Loop through providers and append each one
        foreach ($providers as $index => $provider) {
            $service_provider = User::find($provider);
            $providerNumber  = $index + 1;
            $providerName    = $service_provider['first_name']." ".$service_provider['last_name'] ?? '[Provider\'s Name]';
            $providerContact = $service_provider->phone ?? '[Contact Number]';
            $message .= "\t• Provider {$index}: {$providerName} - {$providerContact}\n";
        }

        $message .= "\nEach provider will contact you soon to confirm the details and arrange the service. You can also reach out to any of them directly through the in-app messaging/chat feature for immediate updates or questions.\n\n" .
            "If you need to make any adjustments or have questions, please contact us at 0701-530-0138 or reply to this email.";

        return (new MailMessage)
            ->subject('Service Request Placed Successfully')
            ->view('vendor.email', [
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
            'title'   => 'Service Request Placed Successfully',
            'message' => 'You have place a new Service Request',
        ];
    }
}
