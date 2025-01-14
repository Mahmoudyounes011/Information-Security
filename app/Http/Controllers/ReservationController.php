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

        //get the key and iv for decryption
        $key = $userId->session_key;
        $iv = $userId->iv;

        //get inputs
        $inputs = $request->all();

        //decryption
        $r =$this->helper->decryptArray($key,$iv, $inputs['encryptedTexts']);

        //decode json inputs
        $data = json_decode($r, true);

        //spot_number
        $spot_number = $data['decryptedTexts'][0]; 
        
        //time for reservation 
        $time = $data['decryptedTexts'][1]; 
    
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
            'reservation_time' => $time,
        ]);

        //update status 
        $parkingSpot->update(['status' => 'reserved']);

        return response()->json(['message' => 'Reservation created successfully', 'reservation' => $reservation], 201);
    }

    
}
