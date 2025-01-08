<?php

namespace App\Http\Controllers;
use phpseclib3\Crypt\RSA;
use App\Http\Requests\BalanceRequest;

use App\Models\User;

use App\Traits\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    use ApiResponse;

    public function deposit(BalanceRequest $balanceRequest)
    {
        try {

            $amount = $balanceRequest->input('amount');
            if (!is_numeric($amount) || $amount <= 0) {
                return response()->json(['message' => 'Invalid amount'], 400);
            }
    
        $publicKeyPath = storage_path('app/keys/public_key.pem');
        $publicKey = file_get_contents($publicKeyPath);
        if (!$publicKey) {
            throw new Exception('Missing public key');
        }

        //here server side 
        $privateKeyPath = storage_path('app/keys/private_key.pem');
        $privateKey = file_get_contents($privateKeyPath);
        if (!$privateKey) {
            throw new Exception('Missing private key');
        }

            $rsaPublic = RSA::loadPublicKey($publicKey);
            $encryptedAmount = base64_encode($rsaPublic->encrypt($amount));

            $rsaPrivate = RSA::loadPrivateKey($privateKey);
            $decryptedAmount = $rsaPrivate->decrypt(base64_decode($encryptedAmount));

            $user = Auth::user();
    
            $userBalance =  $user->balance;

            $oldBalance =User::getBalanceAttribute($userBalance);

            $newBalance = $decryptedAmount + $oldBalance;
        
            $user->update(['balance' => Crypt::encryptString($newBalance)]);

            return response()->json(['encrypted_balance' => $user->update()], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function withdraw(BalanceRequest $balanceRequest)
    {
        try {

            $amount = $balanceRequest->input('amount');
            if (!is_numeric($amount) || $amount <= 0) {
                return response()->json(['message' => 'Invalid amount'], 400);
            }
    
            
            $publicKeyPath = storage_path('app/keys/public_key.pem');
            $publicKey = file_get_contents($publicKeyPath);
            if (!$publicKey) {
                throw new Exception('Missing public key');
            }

            //here server side 
            $privateKeyPath = storage_path('app/keys/private_key.pem');
            $privateKey = file_get_contents($privateKeyPath);
            if (!$privateKey) {
                throw new Exception('Missing private key');
            }

            $rsaPublic = RSA::loadPublicKey($publicKey);
            $encryptedAmount = base64_encode($rsaPublic->encrypt($amount));

            $rsaPrivate = RSA::loadPrivateKey($privateKey);
            $decryptedAmount = $rsaPrivate->decrypt(base64_decode($encryptedAmount));

            $user = Auth::user();

            $userBalance =  $user->balance;

            $oldBalance = User::getBalanceAttribute($userBalance);
            
            $newBalance = $oldBalance - $decryptedAmount ;
        
            $f=Crypt::encryptString($newBalance);
            $user->balance = $f;
            $user->save();
    
            return response()->json(['encrypted_balance' => $user->save()], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getBalance()
    {
        try{
            $user = Auth::user();

            $userBalance =  $user->balance;

            $oldBalance = User::getBalanceAttribute($userBalance);

            return response()->json(['encrypted_balance' => $oldBalance ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
}
