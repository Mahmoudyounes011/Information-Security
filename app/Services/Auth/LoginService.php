<?php

namespace App\Services\Auth;

use App\Http\Requests\LogInRequest;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Hash;
use phpseclib3\Crypt\RSA;
use Exception;

class LoginService
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
//     public function login(LoginRequest $request)
//     {
//         try {
//             $credentials = $request->validated();
//             dd($credentials)
// ;            if (!auth()->attempt($credentials)) 
//             {
//                 throw new Exception(__('user.logIN_error'));
//             }
//             $user = $this->userService->findByUserName($request->user_name);
//             unset($user->role);
//             return [
//                 'user' => $user,
//                 'token' => $user->createToken('accessToken')->plainTextToken,
//             ];
//         } catch (Exception $e) {
//             return [
//                 'error' => $e->getMessage(),
//             ];
//         }
//     }


public function login(LoginRequest $request)
{
    try {

        $rawPassword = $request->input('password');
        $userName = $request->input('user_name');

        //here client side 
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
        $encryptedPassword = base64_encode($rsaPublic->encrypt($rawPassword));

        $rsaPrivate = RSA::loadPrivateKey($privateKey);
        $decryptedPassword = $rsaPrivate->decrypt(base64_decode($encryptedPassword));

        if ($rawPassword !== $decryptedPassword) {
            throw new Exception('Decryption mismatch');
        }

        $user = \App\Models\User::where('user_name', $userName)->first();
        if (!$user || !Hash::check($rawPassword, $user->password)) {
            throw new Exception(__('Invalid username or password'));
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('accessToken')->plainTextToken,
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }

}
}
