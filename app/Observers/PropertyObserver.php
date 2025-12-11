<?php

namespace App\Observers;

use App\Models\Property;
use App\Models\User;
use App\Mail\NewPropertyAdded;
use App\Notifications\NewPropertyNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\Logger;

class PropertyObserver
{
    /**
     * Handle the Property "created" event.
     * 
     * This method is automatically triggered when a new property is created.
     * It sends email notifications to all users and creates database notifications.
     */
    public function created(Property $property): void
    {
        $logger = Logger::getInstance();
        
        try {
            // Get all users from the database
            $users = User::all();
            
            $logger->info('Property created - sending notifications to all users', [
                'property_id' => $property->id,
                'property_category' => $property->category,
                'property_location' => $property->location,
                'total_users' => $users->count(),
            ]);
            
            foreach ($users as $user) {
                try {
                    // Send email notification to each user
                    Mail::to($user->email)->send(new NewPropertyAdded($property));
                    
                    // Create database notification for each user
                    $user->notify(new NewPropertyNotification($property));
                    
                } catch (\Exception $e) {
                    // Log individual user notification failures but continue with others
                    $logger->error('Failed to send notification to user', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'property_id' => $property->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            $logger->info('Property notifications sent successfully', [
                'property_id' => $property->id,
                'users_notified' => $users->count(),
            ]);
            
        } catch (\Exception $e) {
            $logger->error('Property observer failed', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
