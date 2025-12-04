<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyReservation extends Model
{
    protected $fillable = ['property_id', 'user_id', 'reserved_at'];
}
