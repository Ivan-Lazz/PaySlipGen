<?php

namespace App\Models;

use Core\Model;

class Payslip extends Model
{
    protected $table = 'payslip';
    protected $primaryKey = 'payslip_no';
    
    protected $fillable = [
        'payslip_no',
        'employee_id',
        'bank_acct',
        'salary',
        'bonus',
        'amount',
        'person_in_charge',
        'cutoff_date',
        'date_of_payment',
        'payment_status'
    ];
    
    /**
     * Get detailed payslip with employee and banking information
     *
     * @param string $payslipNo
     * @return array|null
     */
    public function getDetailed($payslipNo)
    {
        $query = "SELECT 
                    p.payslip_no, 
                    p.employee_id,
                    e.firstname,
                    e.lastname,
                    p.bank_acct,
                    b.preferred_bank,
                    b.bank_details,
                    p.salary,
                    p.bonus,
                    p.amount,
                    p.person_in_charge,
                    p.cutoff_date,
                    p.date_of_payment,
                    p.payment_status
                FROM 
                    {$this->table} p
                JOIN 
                    employees e ON p.employee_id = e.employee_id
                LEFT JOIN 
                    employee_banking_details b ON p.bank_acct = b.bank_account
                WHERE 
                    p.payslip_no = :payslip_no";
                    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':payslip_no', $payslipNo);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get payslips for specific employee
     *
     * @param string $employeeId
     * @return array
     */
    public function getForEmployee($employeeId)
    {
        $query = "SELECT * FROM {$this->table} WHERE employee_id = :employee_id ORDER BY date_of_payment DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get payslips by date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getByDateRange($startDate, $endDate)
    {
        $query = "SELECT * FROM {$this->table} WHERE date_of_payment BETWEEN :start_date AND :end_date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Update payment status
     *
     * @param string $payslipNo
     * @param string $status
     * @return bool
     */
    public function updateStatus($payslipNo, $status)
    {
        $query = "UPDATE {$this->table} SET payment_status = :status WHERE payslip_no = :payslip_no";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':payslip_no', $payslipNo);
        
        return $stmt->execute();
    }
}