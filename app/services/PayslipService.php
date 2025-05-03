<?php

namespace App\Services;

use App\Models\Payslip;
use App\Models\Employee;
use Core\Database;

class PayslipService
{
    protected $payslip;
    protected $employee;
    
    public function __construct()
    {
        $this->payslip = new Payslip();
        $this->employee = new Employee();
    }
    
    /**
     * Generate a new payslip number
     *
     * @return string
     */
    public function generatePayslipNumber()
    {
        $conn = Database::getInstance()->getConnection();
        
        // Get the last payslip number
        $query = "SELECT payslip_no FROM payslip ORDER BY payslip_no DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        $lastId = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($lastId) {
            // Extract number part and increment
            $lastNumber = (int)$lastId['payslip_no'];
            $nextNumber = $lastNumber + 1;
        } else {
            // Start from 1 if no existing payslips
            $nextNumber = 1;
        }
        
        // Check if max has been reached
        if ($nextNumber > 999999999) {
            throw new \Exception("Maximum payslip number limit reached!");
        }
        
        // Format: 9 digits with leading zeros
        return str_pad($nextNumber, 9, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get detailed payslip including employee and banking information
     *
     * @param string $payslipNo
     * @return array|null
     */
    public function getDetailedPayslip($payslipNo)
    {
        return $this->payslip->getDetailed($payslipNo);
    }
    
    /**
     * Get payslips for an employee
     *
     * @param string $employeeId
     * @return array
     */
    public function getEmployeePayslips($employeeId)
    {
        return $this->payslip->getForEmployee($employeeId);
    }
    
    /**
     * Process payslip payment
     *
     * @param string $payslipNo
     * @return bool
     */
    public function processPayment($payslipNo)
    {
        // In a real application, this would connect to payment gateway
        // For now, just mark as paid
        return $this->payslip->updateStatus($payslipNo, 'PAID');
    }
    
    /**
     * Generate report for date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function generateReport($startDate, $endDate)
    {
        $payslips = $this->payslip->getByDateRange($startDate, $endDate);
        
        $total = 0;
        $paid = 0;
        $pending = 0;
        
        foreach ($payslips as $payslip) {
            $total += $payslip['amount'];
            
            if ($payslip['payment_status'] === 'PAID') {
                $paid += $payslip['amount'];
            } else {
                $pending += $payslip['amount'];
            }
        }
        
        return [
            'payslips' => $payslips,
            'summary' => [
                'total_amount' => $total,
                'paid_amount' => $paid,
                'pending_amount' => $pending,
                'total_count' => count($payslips),
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ];
    }
}