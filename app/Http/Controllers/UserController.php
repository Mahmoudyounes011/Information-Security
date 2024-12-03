<?php

namespace App\Http\Controllers;
use phpseclib3\Crypt\RSA;
use App\Http\Requests\BalanceRequest;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\SearchReqesut;
use App\Http\Resources\GroupResource;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
    
            $publicKeyPath = storage_path('keys/public_key.pem');
            $publicKey = file_get_contents($publicKeyPath);
            if (!$publicKey) {
                throw new Exception('Missing public key');
            }
    
            
            $rsaPublic = RSA::loadPublicKey($publicKey);
    
            $encryptedAmount = base64_encode($rsaPublic->encrypt($amount));
    
            
            $userId = Auth::user()->id;
    
            $user = User::find($userId);
    
            
            $Balance = $user->balance;

    
            $privateKey = env('PRIVATE_KEY');
            $rsaPrivate = RSA::loadPrivateKey($privateKey);
            $decryptedAmount = $rsaPrivate->decrypt(base64_decode($encryptedAmount));
    
            if ($amount != $decryptedAmount) {
                throw new Exception('Decryption mismatch');
            }
    
            
            $newBalance = $currentBalance + $decryptedAmount;
    
            $user->balance = Crypt::encryptString($newBalance);
            $user->save();
    
            $clientPublicKeyPath = storage_path('keys/client_public_key.pem');
            $clientPublicKey = file_get_contents($clientPublicKeyPath);
            if (!$clientPublicKey) {
                throw new Exception('Missing client public key');
            }
    
            $rsaClient = RSA::loadPublicKey($clientPublicKey);
            $encryptedResponse = base64_encode($rsaClient->encrypt($newBalance));
    
            return response()->json(['encrypted_balance' => $encryptedResponse], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    


    public function withdraw(Request $request)
    {
        //get private key
        $privateKey = file_get_contents(storage_path('keys/private_key.pem'));
        $rsa = RSA::loadPrivateKey($privateKey);

        //get input from client 
        $encryptedAmount = $request->input('amount'); 
        $amount = $rsa->decrypt(base64_decode($encryptedAmount));

        if (!is_numeric($amount) || $amount <= 0) {
            return response()->json(['message' => 'Invalid amount'], 400);

        $user = Auth::user();
        $currentBalance = Crypt::decryptString($user->balance);

        if ($amount > $currentBalance) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        $newBalance = $currentBalance - $amount;

        $user->balance = Crypt::encryptString($newBalance);
        $user->save();

        $clientPublicKey = file_get_contents(storage_path('keys/client_public_key.pem'));
        $rsaClient = RSA::loadPublicKey($clientPublicKey);
        $encryptedResponse = base64_encode($rsaClient->encrypt($newBalance));

        return response()->json(['encrypted_balance' => $encryptedResponse], 200);

        }
    }

    public function getBalance()
{

    $user = Auth::user();

    $currentBalance = Crypt::decryptString($user->balance);

   // dd( $currentBalance);

    $clientPublicKey = file_get_contents(storage_path('keys/client_public_key.pem'));
    $rsaClient = RSA::loadPublicKey($clientPublicKey);
    $encryptedResponse = base64_encode($rsaClient->encrypt($currentBalance));

    return response()->json(['encrypted_balance' => $encryptedResponse], 200);
}


    
}
