<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Models\ParkingSpot;

class ReservationController extends Controller
{
    private Helper $helper;
    public  function __construct(Helper $helper) {
        $this->helper = $helper;
    }
    public function createReservation(Request $request)
    {
        //auth user for get userInfo
        $userId = $request->user();
        $inputs = $request->all();

    
        //get the key and iv for decryption
        $key = $userId->session_key;
        $iv = $userId->iv;

        //get inputs
        $inputs = $request->all();

        $spot_number = $this->helper->decrypt($key,$iv,$inputs['encryptedTexts'][0]);
        $time = $this->helper->decrypt($key,$iv,$inputs['encryptedTexts'][1]);

    
        //get object ParkingSpot
        $parkingSpot = ParkingSpot::where('spot_number','=',$spot_number)->first();
        
        
        //get status of ParkingSpot
        if ($parkingSpot->status !== 'available') {
            return response()->json(['error' => 'Parking spot is not available'], 400);
        }

        //create reservation
        $reservation = Reservation::create([
            'user_id' => $userId->id,
            'parking_spot_id' => $parkingSpot->id,
            'reservation_time' => $time['decryptedText'],
        ]);

        //update status 
        $parkingSpot->update(['status' => 'reserved']);

        return response()->json(['message' => 'Reservation created successfully', 'reservation' => $reservation], 201);
    }

    public function calculateAmount(Request $request)
    {
        $request->validate([
            'spot_number' => 'required|exists:parking_spots,spot_number',
        ]);
    
        $parkingSpot = ParkingSpot::where('spot_number', $request->spot_number)->first();
    
        $reservation = Reservation::where('parking_spot_id', $parkingSpot->id)
            ->where('reservation_time', '>', now())
            ->first();
    
        if (!$reservation) {
            return response()->json(['message' => 'No active reservation found for this parking spot.'], 404);
        }
    
        $hours = $reservation->created_at->diffInHours($reservation->reservation_time);
    
    
        $amount = $hours * 500;
    
        return response()->json([
            'spot_number' => $parkingSpot->spot_number,
            'amount' => $amount,
            'start_time' => $reservation->created_at,
            'current_time' => $reservation->reservation_time,
        ]);
    }

    public function updateExpiredReservations()
{
    $expiredReservations = Reservation::where('reservation_time', '<=', now())
        ->whereHas('parkingSpot', function ($query) {
            $query->where('status', 'reserved'); 
        })
        ->get();

    foreach ($expiredReservations as $reservation) {
        $reservation->parkingSpot->update(['status' => 'available']);
    }

    return response()->json(['message' => 'Expired reservations have been updated successfully.']);
}

    
}
