<?php

namespace App\Requests;

class CreateUserRequest
{
    /**
     * Get validation rules
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'firstname' => ['required', 'string', ['max', 100]],
            'lastname' => ['required', 'string', ['max', 100]],
            'username' => ['required', 'string', ['min', 3], ['max', 50]],
            'password' => ['required', 'string', ['min', 8]]
        ];
    }
    
    /**
     * Get custom error messages
     *
     * @return array
     */
    public static function messages()
    {
        return [
            'firstname.required' => 'First name is required',
            'firstname.max' => 'First name may not exceed 100 characters',
            'lastname.required' => 'Last name is required',
            'lastname.max' => 'Last name may not exceed 100 characters',
            'username.required' => 'Username is required',
            'username.min' => 'Username must be at least 3 characters',
            'username.max' => 'Username may not exceed 50 characters',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters'
        ];
    }
}