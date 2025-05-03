<?php

namespace App\Requests;

class UpdatePayslipRequest
{
    /**
     * Get validation rules
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'bank_acct' => ['numeric'],
            'salary' => ['numeric', ['min', 0]],
            'bonus' => ['numeric', ['min', 0]],
            'amount' => ['numeric', ['min', 0]],
            'person_in_charge' => ['string', ['max', 100]],
            'cutoff_date' => ['date'],
            'date_of_payment' => ['date'],
            'payment_status' => ['string', ['in', ['PENDING', 'PAID', 'CANCELLED']]]
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
            'bank_acct.numeric' => 'Bank account must be a number',
            'salary.numeric' => 'Salary must be a number',
            'salary.min' => 'Salary must be at least 0',
            'bonus.numeric' => 'Bonus must be a number',
            'bonus.min' => 'Bonus must be at least 0',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => 'Amount must be at least 0',
            'cutoff_date.date' => 'Cutoff date must be a valid date',
            'date_of_payment.date' => 'Payment date must be a valid date',
            'payment_status.in' => 'Payment status must be PENDING, PAID, or CANCELLED'
        ];
    }
}