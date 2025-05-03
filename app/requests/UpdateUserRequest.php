<?php

namespace App\Requests;

class UpdateUserRequest
{
    /**
     * Get validation rules
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'firstname' => ['string', ['max', 100]],
            'lastname' => ['string', ['max', 100]],
            'username' => ['string', ['min', 3], ['max', 50]],
            'password' => ['string', ['min', 8]]
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
            'firstname.max' => 'First name may not exceed 100 characters',
            'lastname.max' => 'Last name may not exceed 100 characters',
            'username.min' => 'Username must be at least 3 characters',
            'username.max' => 'Username may not exceed 50 characters',
            'password.min' => 'Password must be at least 8 characters'
        ];
    }
}