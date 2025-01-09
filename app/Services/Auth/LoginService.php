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
        $credentials = $request->validated();
        
        unset($credentials['session_key']);

        if (!auth()->attempt($credentials)) 
        {
            throw new Exception("error in inputs");
        }
        $user = $this->userService->findByUserName($request->phone_num);

        $sessionKey = $request->session_key;
    
        $user->update([
        'session_key' => $sessionKey,
        ]);
    
        return [
            
            'token' => $user->createToken('accessToken')->plainTextToken,
        ];
        
    } catch (Exception $e) {
    
        return response()->json(['error' => $e->getMessage()], 400);
    }

}
}
