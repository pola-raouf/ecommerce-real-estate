<?php

namespace App\Observers;

use App\Models\PropertyReservation;
use App\Mail\RentReservationConfirmation;
use App\Mail\SaleReservationConfirmation;
use App\Mail\AdminReservationNotification;
use App\Notifications\ReservationCreatedNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\Logger;
use App\Models\User;

class ReservationObserver
{
    /**
     * Handle the PropertyReservation "created" event.
     * 
     * This method is automatically triggered when a new reservation is created.
     * It sends different emails based on whether the property is for rent or sale.
     */
    public function created(PropertyReservation $reservation): void
    {
        $logger = Logger::getInstance();
        
        try {
            // Load relationships
            $reservation->load(['property', 'user']);
            
            $property = $reservation->property;
            $user = $reservation->user;
            
            if (!$property || !$user) {
                $logger->error('Reservation created but property or user not found', [
                    'reservation_id' => $reservation->id,
                ]);
                return;
            }
            
            $logger->info('Reservation created - sending notifications', [
                'reservation_id' => $reservation->id,
                'property_id' => $property->id,
                'user_id' => $user->id,
                'transaction_type' => $property->transaction_type,
            ]);
            
            // Send confirmation email to user based on property type
            if ($property->transaction_type === 'rent') {
                Mail::to($user->email)->send(new RentReservationConfirmation($reservation));
                $logger->info('Rent confirmation email sent to user', ['user_email' => $user->email]);
            } else {
                Mail::to($user->email)->send(new SaleReservationConfirmation($reservation));
                $logger->info('Sale confirmation email sent to user', ['user_email' => $user->email]);
            }
            
            // Send notification to admin and property owner
            $this->sendAdminNotifications($reservation, $logger);
            
            // Create database notification for user
            $user->notify(new ReservationCreatedNotification($reservation));
            
            // Create notification for property owner if different from user
            if ($property->user_id && $property->user_id !== $user->id) {
                $owner = User::find($property->user_id);
                if ($owner) {
                    $owner->notify(new ReservationCreatedNotification($reservation));
                }
            }
            
            $logger->info('Reservation notifications completed successfully', [
                'reservation_id' => $reservation->id,
            ]);
            
        } catch (\Exception $e) {
            $logger->error('Reservation observer failed', [
                'reservation_id' => $reservation->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
    
    /**
     * Send notification emails to admins and property owner
     */
    protected function sendAdminNotifications(PropertyReservation $reservation, $logger): void
    {
        try {
            // Get all admin users
            $admins = User::where('role', 'admin')->get();
            
            foreach ($admins as $admin) {
                // Send email notification
                Mail::to($admin->email)->send(new AdminReservationNotification($reservation));
                
                // Create database notification for admin
                $admin->notify(new ReservationCreatedNotification($reservation));
            }
            
            $logger->info('Admin notifications sent (email + database)', [
                'admin_count' => $admins->count(),
            ]);
            
            // Send to property owner if they're not an admin
            $property = $reservation->property;
            if ($property->user_id) {
                $owner = User::find($property->user_id);
                if ($owner && $owner->role !== 'admin') {
                    // Send email notification
                    Mail::to($owner->email)->send(new AdminReservationNotification($reservation));
                    
                    // Create database notification for owner (already done in created method, but ensure it's there)
                    $logger->info('Property owner notification sent', ['owner_email' => $owner->email]);
                }
            }
            
        } catch (\Exception $e) {
            $logger->error('Failed to send admin notifications', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
