<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * DEPRECATED: This mailable is no longer used.
 * Use RentReservationConfirmation or SaleReservationConfirmation instead.
 * 
 * Kept for backward compatibility but should not be used in new code.
 */
class PropertyReserved extends Mailable
{
    use Queueable, SerializesModels;

    public $property;
    public $appointmentDate;

    /**
     * Create a new message instance.
     */
    public function __construct($property)
    {
        $this->property = $property;

        // Generate a random date within the next 7 days
        $date = now()->addDays(rand(1, 7));

        // If it's Saturday or Sunday, move to Monday
        if ($date->isWeekend()) {
            $date->addDays($date->isSaturday() ? 2 : 1);
        }

        // Random hour between 10 and 16 (office hours)
        $hour = rand(10, 16);

        // Random minute rounded to 15-minute increments
        $minutes = [0, 15, 30, 45];
        $minute = $minutes[array_rand($minutes)];

        $date->setHour($hour)->setMinute($minute)->setSecond(0);

        $this->appointmentDate = $date->format('l, d F Y \a\t H:i');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Property Reservation Confirmed [DEPRECATED]',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.property_reserved',
            with: [
                'property' => $this->property,
                'appointmentDate' => $this->appointmentDate,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
