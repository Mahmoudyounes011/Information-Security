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
            
            $token = $user->createToken('accessToken')->plainTextToken;
            
            return [
                'token' => $token,
                'user' => $user
            ]; 
        } catch (\Exception $e) {
            return [
                'error' => 'An error occurred while creating the user. Please try again.',
                'details' => $e->getMessage() 
            ]; 
        }
    }
    
}
