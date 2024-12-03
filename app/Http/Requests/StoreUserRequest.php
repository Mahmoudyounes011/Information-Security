<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules()
    {
        return [
            //'role_id' => 'required|exists:roles,id',
            'first_name' => 'required|string|max:15',
            'last_name' => 'required|string|max:15',
            'user_name' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'balance' => 'required',

            //'device_token'=>'required|string|max:255',
            // 'phone_number' => [
            //     'required',
            //     'string',
            //     //'regex:/^09\d{8}$/',
            //    // 'max:10',
            //     'unique:users',
            // ],
        ];
    }
    
    public function messages()
    {
        return [
                // 'role_id.required' => __('user.role_id_required'),
                // 'role_id.exists' => __('user.role_id_exists'),
            'first_name.required' => __('user.first_name_required'),
            'first_name.max' => __('user.first_name_max'),
            'last_name.required' => __('user.last_name_required'),
            'last_name.max' => __('user.last_name_max'),
            'email.required' => __('user.email_required'),
            //'email.email' => __('user.email_email'),
            'email.unique' => __('user.email_unique'),
            'password.required' => __('user.password_required'),
            'password.min' => __('user.password_min'),
            // 'phone_number.required' => __('user.phone_number_required'),
            // 'phone_number.unique' => __('user.phone_number_unique'),
        ];
    }
    
}
