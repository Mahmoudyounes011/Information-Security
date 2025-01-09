<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ParkingSpotController;
use App\Http\Controllers\ServerKeyController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/signup',[AuthController::class,'signup']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function ()
{
    Route::post('/deposit', [UserController::class, 'deposit']);
    Route::post('/withdraw', [UserController::class, 'withdraw']);
    Route::get('/balance', [UserController::class, 'getBalance']);
    Route::get('/available_parking', [ParkingSpotController::class, 'index']);

    Route::post('/handCheck', [ServerKeyController::class, 'getPublicKey']);

    Route::post('/reservations', [ReservationController::class, 'createReservation']);


    Route::post('/logout', [AuthController::class, 'logout']);
});