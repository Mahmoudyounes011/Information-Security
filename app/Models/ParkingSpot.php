<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class ParkingSpot extends Model
{

    use HasFactory;

    protected $fillable = ['spot_number', 'status'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
