<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyReservation extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'property_id',
        'user_id',
        'reserved_at',
        'meeting_datetime',
        'start_date',
        'duration_value',
        'duration_unit',
        'notes'
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'meeting_datetime' => 'datetime',
        'start_date' => 'date',
    ];

    /**
     * Get the property that was reserved
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user who made the reservation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this is a rental reservation
     */
    public function isRental(): bool
    {
        return $this->property && $this->property->transaction_type === 'rent';
    }

    /**
     * Check if this is a sale reservation
     */
    public function isSale(): bool
    {
        return $this->property && $this->property->transaction_type === 'sale';
    }

    /**
     * Get formatted duration text (e.g., "6 months")
     */
    public function getDurationText(): ?string
    {
        if (!$this->duration_value || !$this->duration_unit) {
            return null;
        }

        $value = $this->duration_value;
        $unit = $this->duration_unit;
        
        // Make singular if value is 1
        if ($value == 1) {
            $unit = rtrim($unit, 's');
        }

        return "{$value} {$unit}";
    }

    /**
     * Get formatted meeting date
     */
    public function getMeetingDateFormatted(): string
    {
        return $this->meeting_datetime 
            ? $this->meeting_datetime->format('l, F j, Y \a\t g:i A')
            : 'Not scheduled';
    }
}
