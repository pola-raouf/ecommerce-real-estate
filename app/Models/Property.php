<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    
    protected $fillable = [
    'category',
    'location',
    'price',
    'status',
    'image',
    'user_id',
    'description',
    'installment_years',
    'transaction_type',
];



    public function user() {
        return $this->belongsTo(User::class);
    }

    public function transactions() {
        return $this->hasOne(Transaction::class);
    }

    // For multiple images
    public function images() {
        return $this->hasMany(PropertyImage::class);
    }
    public function reservation()
{
    return $this->hasOne(PropertyReservation::class);
}

// Check if reserved
public function isReserved(): bool
{
    return $this->reservation !== null;
}


// User who reserved
public function reservedByUser()
{
    return $this->reservation?->user;
}

// Can be reserved by user
public function canBeReservedBy(User $user): bool
{
    return $this->status === 'available' && !$this->reservation;
}

// Can reservation be cancelled
public function canReservationBeCancelledBy(User $user): bool
{
    return $this->reservation && $this->reservation->user_id === $user->id;
}


public function releaseReservation(): void
{
    if ($this->reservation) {
        $this->reservation->delete();
    }
    // removed: $this->update(['status' => 'available']);
}


}

