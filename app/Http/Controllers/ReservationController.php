<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\ParkingSpot;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;
use App\Http\Requests\ReservationRequest;

class ReservationController extends Controller
{
    private Helper $helper;
    private ActivityLogService $activityLogService;
    public  function __construct(Helper $helper,ActivityLogService $activityLogService) {
        $this->helper = $helper;
        $this->activityLogService = $activityLogService;
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

        $correctSignature = $this -> activityLogService->verifySignature($request->except('signature'),$request->signature);

        if(!$correctSignature){
            return response()->json(['error' => 'Wrong data'], 400);
        }

        dd($correctSignature);
        //decryption
        $r =$this->helper->decryptArray($key,$iv, $inputs['encryptedTexts']);

        //decode json inputs
        $data = json_decode($r, true);

        dd($data);

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


        $this -> activityLogService->logReservation($inputs);

        
        return response()->json(['message' => 'Reservation created successfully', 'reservation' => $reservation], 201);
    }

    
}
