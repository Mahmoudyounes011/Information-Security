<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_num' => 'required|string',
            'password' => 'required|string',
            'session_key' => 'required|string',
            'iv' => 'required|string',
        ];
    }

    public function messages()
{
    return [
        'email.required' => __('user.email_required'),
        'email.email' => __('user.email_email'),
        'password.required' => __('user.password_required'),
        'password.min' => __('user.password_min'),
        'session_key.required'=>'you should insert the session key',
        'session_key.string'=>'you should insert the session key is a string',
        'iv.required'=>'you should insert an array iv',
        'iv.string'=>'you should insert an array iv is a string',


    ];
}
}
