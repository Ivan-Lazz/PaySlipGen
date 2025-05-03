<?php

namespace App\Requests;

class CreateAccountRequest
{
    /**
     * Get validation rules
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'employee_id' => ['required', 'string'],
            'account_email' => ['required', 'email', ['max', 150]],
            'account_pass' => ['required', 'string', ['min', 8]],
            'account_type' => ['required', 'string', ['in', ['ADMIN', 'MANAGER', 'EMPLOYEE']]],
            'account_status' => ['string', ['in', ['ACTIVE', 'INACTIVE', 'SUSPENDED']]]
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
            'employee_id.required' => 'Employee ID is required',
            'account_email.required' => 'Email is required',
            'account_email.email' => 'Email must be a valid email address',
            'account_email.max' => 'Email may not exceed 150 characters',
            'account_pass.required' => 'Password is required',
            'account_pass.min' => 'Password must be at least 8 characters',
            'account_type.required' => 'Account type is required',
            'account_type.in' => 'Account type must be ADMIN, MANAGER, or EMPLOYEE',
            'account_status.in' => 'Account status must be ACTIVE, INACTIVE, or SUSPENDED'
        ];
    }
}