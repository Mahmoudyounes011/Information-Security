<?php

namespace App\Http\Controllers;

use App\Models\ParkingSpot;
use Illuminate\Http\Request;

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
        //
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
    public function destroy(ParkingSpot $parkingSpot)
    {
        //
    }
}
