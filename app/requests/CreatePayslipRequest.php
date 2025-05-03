<?php

namespace App\Requests;

class CreatePayslipRequest
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
            'bank_acct' => ['required', 'numeric'],
            'amount' => ['required', 'numeric', ['min', 0]],
            'person_in_charge' => ['required', 'string', ['max', 100]],
            'cutoff_date' => ['required', 'date'],
            'date_of_payment' => ['required', 'date'],
            'payment_status' => ['required', 'string', ['in', ['PENDING', 'PAID', 'CANCELLED']]]
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
            'bank_acct.required' => 'Bank account is required',
            'bank_acct.numeric' => 'Bank account must be a number',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => 'Amount must be at least 0',
            'person_in_charge.required' => 'Person in charge is required',
            'cutoff_date.required' => 'Cutoff date is required',
            'cutoff_date.date' => 'Cutoff date must be a valid date',
            'date_of_payment.required' => 'Payment date is required',
            'date_of_payment.date' => 'Payment date must be a valid date',
            'payment_status.required' => 'Payment status is required',
            'payment_status.in' => 'Payment status must be PENDING, PAID, or CANCELLED'
        ];
    }
}