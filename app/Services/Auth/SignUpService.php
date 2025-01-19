<?php

namespace App\Services\Auth;

use App\Http\Requests\StoreUserRequest;
use App\Services\User\UserService;

class SignUpService
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function create(StoreUserRequest $request)
    {
        try {
            
            $user = $this->userService->createUser($request);
            //dd($user);
            $token = $user->createToken('accessToken')->plainTextToken;
            
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
        } catch (\Exception $e) {
            return [
                'error' => 'An error occurred while creating the user. Please try again.',
                'details' => $e->getMessage() 
            ]; 
        }
    }
    
}
