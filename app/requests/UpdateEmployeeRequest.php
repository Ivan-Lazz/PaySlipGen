<?php

namespace App\Requests;

class UpdateEmployeeRequest
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
            'contact_number' => ['string', ['max', 50]],
            'email' => ['email', ['max', 100]]
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
            'email.email' => 'Email must be a valid email address'
        ];
    }
}