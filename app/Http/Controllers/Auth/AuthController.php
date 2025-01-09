<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Http\Requests\LoginRequest;

use App\Http\Requests\StoreUserRequest;

use App\Models\User;

use App\Services\Auth\LoginService;

use App\Services\Auth\LogoutService;

use App\Services\Auth\SignUpService;

use App\Traits\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Http\Request;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class AuthController extends Controller
{
    use ApiResponse;
    public function signup(StoreUserRequest $request, SignUpService $signupService)
    {
        //dd($request);
        $data = $signupService->create($request);
        $message = __('user.signup_success');
        return $this->successResponse($data,$message);
    }

    public function logIn(LoginRequest $request, LoginService $loginService)
    {
        try
        {
            
        $data = $loginService->login($request);
        //dd( $data);
        $message = __('user.login_success');
        return $this->successResponse($data, $message);
        }
        catch (Exception $e) 
        {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
    
    
    public function logout(Request $request)    
    {
        $user = Auth::user();
        $user->tokens()->delete();
        $message = __('user.logout_success');
        return $this->successResponse(null, $message); 
    }
    


}
