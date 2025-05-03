<?php

namespace App\Requests;

class CreateEmployeeRequest
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
            'contact_number' => ['required', 'string', ['max', 50]],
            'email' => ['required', 'email', ['max', 100]]
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
            'contact_number.required' => 'Contact number is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address'
        ];
    }
}