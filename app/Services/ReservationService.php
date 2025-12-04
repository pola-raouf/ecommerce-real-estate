<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyReservation;
use App\Models\User;
use App\Services\Logger;

class ReservationService
{
    protected Logger $logger;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    // Reserve a property
    public function reserve(Property $property, User $user): void
    {
        if ($property->isReserved() || $property->status === 'sold') {
            $this->logger->warning('Property cannot be reserved', ['property_id' => $property->id]);
            throw new \Exception('Property cannot be reserved.');
        }

        // Create reservation
        PropertyReservation::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'reserved_at' => now(),
        ]);

        // Update property status
        $property->update(['status' => 'reserved']);

        $this->logger->info('Property reserved', [
            'property_id' => $property->id,
            'user_id' => $user->id,
        ]);
    }

    // Cancel a reservation
    public function cancel(Property $property, User $user): void
    {
        $reservation = $property->reservation;

        if (!$reservation) {
            throw new \Exception('No reservation found.');
        }

        if ($reservation->user_id !== $user->id && $user->role !== 'admin') {
            throw new \Exception('Unauthorized to cancel this reservation.');
        }

        $reservation->delete();

        // Update property status
        $property->update(['status' => 'available']);

        $this->logger->info('Reservation cancelled', [
            'property_id' => $property->id,
            'user_id' => $user->id,
        ]);
    }
}

