<?php

namespace App\Notifications;

use App\Models\PropertyReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationCreatedNotification extends Notification
{
    use Queueable;

    protected $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct(PropertyReservation $reservation)
    {
        $this->reservation = $reservation;
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
        $property = $this->reservation->property;
        $user = $this->reservation->user;
        $type = $property->transaction_type === 'rent' ? 'rental' : 'sale';
        
        $data = [
            'reservation_id' => $this->reservation->id,
            'property_id' => $property->id,
            'property_category' => $property->category,
            'property_location' => $property->location,
            'property_price' => $property->price,
            'property_image' => $property->image,
            'transaction_type' => $property->transaction_type,
            'meeting_datetime' => $this->reservation->meeting_datetime?->format('Y-m-d H:i:s'),
            'meeting_datetime_formatted' => $this->reservation->getMeetingDateFormatted(),
            'user_name' => $user->name,
            'user_email' => $user->email,
        ];
        
        // Add rental-specific details
        if ($property->transaction_type === 'rent' && $this->reservation->duration_value) {
            $data['duration'] = $this->reservation->getDurationText();
            $data['start_date'] = $this->reservation->start_date?->format('F j, Y');
        }
        
        // Add notes if present
        if ($this->reservation->notes) {
            $data['notes'] = $this->reservation->notes;
        }
        
        // Create appropriate message
        if ($notifiable->id === $user->id) {
            // Message for the user who made the reservation
            $data['message'] = "Your {$type} reservation for {$property->category} in {$property->location} has been confirmed";
        } else {
            // Message for admin/property owner
            $data['message'] = "New {$type} reservation by {$user->name} for {$property->category} in {$property->location}";
        }
        
        return $data;
    }
}
