<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Models\ParkingSpot;

class ReservationController extends Controller
{
    public function createReservation(ReservationRequest $request)
    {
        $userId = $request->user()->id;

        $parkingSpot = ParkingSpot::find($request->parking_spot_id);
        if ($parkingSpot->status !== 'available') {
            return response()->json(['error' => 'Parking spot is not available'], 400);
        }

        $reservation = Reservation::create([
            'user_id' => $userId,
            'parking_spot_id' => $request->parking_spot_id,
            'reservation_time' => $request->reservation_time,
        ]);

        $parkingSpot->update(['status' => 'reserved']);

        return response()->json(['message' => 'Reservation created successfully', 'reservation' => $reservation], 201);
    }
}
