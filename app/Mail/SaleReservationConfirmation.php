<?php

namespace App\Mail;

use App\Models\PropertyReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SaleReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    /**
     * Create a new message instance.
     */
    public function __construct(PropertyReservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $property = $this->reservation->property;
        $category = $property->category ?? 'Property';
        $location = $property->location ?? 'Unknown Location';
        
        return new Envelope(
            subject: "Sale Reservation Confirmed - {$category} in {$location}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.sale_reservation_confirmation',
            with: [
                'reservation' => $this->reservation,
                'property' => $this->reservation->property,
                'user' => $this->reservation->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
