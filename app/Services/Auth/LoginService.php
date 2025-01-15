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
        // Validate credentials
        $credentials = $request->validated();

        // Attempt authentication
        if (!auth()->attempt($credentials)) {
            throw new Exception("Invalid credentials");
        }

        // Find user by phone number
        $user = $this->userService->findByUserName($request->phone_num);

        // Update session_key and iv for the user
        $sessionKey = $request->session_key;
        $iv = $request->iv;
        $user->update([
            'session_key' => $sessionKey,
            'iv' => $iv
        ]);
        $token = $user->createToken('accessToken')->plainTextToken;

        // Return token and user information
        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'type_user' => $user->type_user,
                'phone_num' => $user->phone_num,
                'key'  =>$user->session_key,
                'iv' => $user->iv,
                'balance' => $user->getRawOriginal('balance')
            ],

        ];

    } catch (Exception $e) {
        // Handle exceptions and return an error response
        return response()->json(['error' => $e->getMessage()], 400);
    }
}

}
