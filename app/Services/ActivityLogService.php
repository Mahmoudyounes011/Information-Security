<?php 

namespace App\Services;

use phpseclib3\Crypt\RSA;
use App\Models\ActivityLog;
use App\Contract\MainInterface;
use App\Repositories\BaseRepository;

class ActivityLogService{

public function logPay($signature,$amount)
{
    // Verify digital signature
  //  if ($this->verifySignature($data, $signature)) {
        ActivityLog::create([
         'description' => $amount.'Paied',
         'user_id' => auth()->user()->id,
         'activity_type' => 'Pay',
         'signature' => $signature,
        ]);
   // }
}

public function logReservation($signature,$slot_number)
{
    // Verify digital signature
   // if ($this->verifySignature($data, $signature)) {
        ActivityLog::create([
         'description' => $slot_number.' Reserved',
         'user_id' => auth()->user()->id,
         'activity_type' => 'Reservation',
         'signature' => $signature,
        ]);
    
}



function verifySignature($data, $signature) {
    $publicKey = auth()->user()->clientKey->public_key;
    $rsa = RSA::loadPublicKey($publicKey);
    return $rsa->verify($data, base64_decode($signature));
}

}