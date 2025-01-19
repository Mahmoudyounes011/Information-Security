<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsrController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ValidateCertificate;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ServerKeyController;
use App\Http\Controllers\ParkingSpotController;
use App\Http\Controllers\ReservationController;
use App\Http\Middleware\PreventEmployeeActions;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/signup',[AuthController::class,'signup']);
Route::post('/login',[AuthController::class,'login']);



Route::middleware('auth:sanctum')->group(function ()
{

Route::post('/generate-csr', [CsrController::class, 'generateCsr']);
Route::get('/download-certificate-file', [CsrController::class, 'downloadFile'])->name('download.certificate.file');
Route::post('/test', [CsrController::class, 'test'])->middleware([ValidateCertificate::class]);

    Route::post('/parking-spots', [ParkingSpotController::class, 'store']);

    Route::post('/deposit', [UserController::class, 'withdraw']);
    Route::post('/withdraw', [UserController::class, 'withdraw']);
    Route::get('/balance', [UserController::class, 'getBalance']);
    Route::get('/available_parking', [ParkingSpotController::class, 'index']);

    Route::post('/handCheck', [ServerKeyController::class, 'getPublicKey']);

    Route::post('/reservations', [ReservationController::class, 'createReservation']);

    Route::delete('/parking-spots-delete', [ParkingSpotController::class, 'deleteSpot'])->name('parking-spots.destroy');
    

    Route::post('/logout', [AuthController::class, 'logout']);
});