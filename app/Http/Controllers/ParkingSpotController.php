<?php

namespace App\Http\Controllers;

use App\Models\ParkingSpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ParkingSpotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parkings = ParkingSpot::where('status','=','available')->get();
        return response(['Available' => $parkings],201);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if($user->type_user === 'visitor')
        {
            return response()->json(['visito cannot create or update']);
        }
        $validatedData = $request->validate([
            'spot_number' => 'required|string|max:255',
            'status' => 'nullable|in:available,reserved', // Optional status
        ]);

        // Attempt to find an existing spot by its number
        $parkingSpot = ParkingSpot::where('spot_number', $validatedData['spot_number'])->first();

        if ($parkingSpot) {
            // Update the existing spot's status if provided
            $parkingSpot->status = $validatedData['status'] ?? $parkingSpot->status;
            $parkingSpot->save();

            return response()->json([
                'message' => 'Parking spot updated successfully.',
                'parking_spot' => $parkingSpot,
            ], 200);
        }

        // Create a new parking spot if it doesn't exist
        $newParkingSpot = ParkingSpot::create([
            'spot_number' => $validatedData['spot_number'],
            'status' => $validatedData['status'] ?? 'available', // Default to 'available'
        ]);

        return response()->json([
            'message' => 'Parking spot created successfully.',
            'parking_spot' => $newParkingSpot,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ParkingSpot $parkingSpot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParkingSpot $parkingSpot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ParkingSpot $parkingSpot)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteSpot(Request $request)
    {
        $user = Auth::user();
        if($user->type_user === 'visitor')
        {
            return response()->json(['visito cannot create or update']);
        }
        // Validate that spot_number is provided and exists
        $validatedData = $request->validate([
            'spot_number' => 'required|string|exists:parking_spots,spot_number',
        ]);

        // Find the parking spot by spot_number
        $parkingSpot = ParkingSpot::where('spot_number', $validatedData['spot_number'])->first();

        // Delete the parking spot
        if ($parkingSpot) {
            $parkingSpot->delete();

            return response()->json([
                'message' => 'Parking spot deleted successfully.',
            ], 200);
        }

        return response()->json([
            'error' => 'Parking spot not found.',
        ], 404);
    }
}
