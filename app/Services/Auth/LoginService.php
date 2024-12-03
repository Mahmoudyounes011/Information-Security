<?php

namespace App\Services\Auth;

use App\Http\Requests\LogInRequest;
use App\Services\User\UserService;
use Exception;

class LoginService
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();
            if (!auth()->attempt($credentials)) 
            {
                throw new Exception(__('user.logIN_error'));
            }
            $user = $this->userService->findByUserName($request->user_name);
            unset($user->role);
            return [
                'user' => $user,
                'token' => $user->createToken('accessToken')->plainTextToken,
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
