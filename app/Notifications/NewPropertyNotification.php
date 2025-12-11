<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPropertyNotification extends Notification
{
    use Queueable;

    protected $property;

    /**
     * Create a new notification instance.
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'property_id' => $this->property->id,
            'category' => $this->property->category,
            'location' => $this->property->location,
            'price' => $this->property->price,
            'transaction_type' => $this->property->transaction_type,
            'image' => $this->property->image,
            'message' => 'New ' . $this->property->category . ' available in ' . $this->property->location,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
