<?php

namespace App\Services;

use App\Models\Payslip;
use App\Models\Employee;
use Core\Database;
use PDO;

class ReportService
{
    protected $payslip;
    protected $employee;
    protected $conn;
    
    public function __construct()
    {
        $this->payslip = new Payslip();
        $this->employee = new Employee();
        $this->conn = Database::getInstance()->getConnection();
    }
    
    /**
     * Generate a department report
     *
     * @param string $department
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function generateDepartmentReport($department, $startDate, $endDate)
    {
        // In a real implementation, we would have a department field
        // For this example, we'll simulate it
        
        // Get all payslips in the date range
        $query = "SELECT 
                    p.payslip_no, 
                    p.employee_id,
                    p.amount,
                    p.date_of_payment,
                    p.payment_status,
                    e.firstname,
                    e.lastname
                FROM 
                    payslip p
                JOIN 
                    employees e ON p.employee_id = e.employee_id
                WHERE 
                    p.date_of_payment BETWEEN :start_date AND :end_date
                ORDER BY 
                    p.date_of_payment DESC";
                    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        $payslips = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filter by department (simulated)
        $filteredPayslips = array_filter($payslips, function($payslip) use ($department) {
            // In a real implementation, this would check a department field
            // For this example, we'll use a simple pattern
            $firstLetter = strtolower(substr($payslip['firstname'], 0, 1));
            
            switch (strtolower($department)) {
                case 'finance':
                    return in_array($firstLetter, ['a', 'b', 'c', 'd']);
                case 'hr':
                    return in_array($firstLetter, ['e', 'f', 'g', 'h']);
                case 'it':
                    return in_array($firstLetter, ['i', 'j', 'k', 'l']);
                case 'marketing':
                    return in_array($firstLetter, ['m', 'n', 'o', 'p']);
                case 'operations':
                    return in_array($firstLetter, ['q', 'r', 's', 't']);
                case 'sales':
                    return in_array($firstLetter, ['u', 'v', 'w', 'x', 'y', 'z']);
                default:
                    return true;
            }
        });
        
        // Calculate department summary
        $totalAmount = 0;
        $totalPaid = 0;
        $totalPending = 0;
        $employeeStats = [];
        
        foreach ($filteredPayslips as $payslip) {
            $totalAmount += $payslip['amount'];
            
            if ($payslip['payment_status'] === 'PAID') {
                $totalPaid += $payslip['amount'];
            } else if ($payslip['payment_status'] === 'PENDING') {
                $totalPending += $payslip['amount'];
            }
            
            $employeeId = $payslip['employee_id'];
            $employeeName = $payslip['firstname'] . ' ' . $payslip['lastname'];
            
            if (!isset($employeeStats[$employeeId])) {
                $employeeStats[$employeeId] = [
                    'employee_id' => $employeeId,
                    'name' => $employeeName,
                    'total_amount' => 0,
                    'payslip_count' => 0
                ];
            }
            
            $employeeStats[$employeeId]['total_amount'] += $payslip['amount'];
            $employeeStats[$employeeId]['payslip_count']++;
        }
        
        // Sort employee stats by total amount
        usort($employeeStats, function($a, $b) {
            return $b['total_amount'] - $a['total_amount'];
        });
        
        return [
            'department' => $department,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payslips' => array_values($filteredPayslips),
            'summary' => [
                'total_amount' => $totalAmount,
                'paid_amount' => $totalPaid,
                'pending_amount' => $totalPending,
                'employee_count' => count($employeeStats),
                'payslip_count' => count($filteredPayslips)
            ],
            'employee_stats' => array_values($employeeStats)
        ];
    }
    
    /**
     * Generate a summary report
     *
     * @param int $year
     * @return array
     */
    public function generateAnnualSummary($year)
    {
        $startDate = "{$year}-01-01";
        $endDate = "{$year}-12-31";
        
        // Get all payslips in the year
        $query = "SELECT 
                    DATE_FORMAT(date_of_payment, '%m') as month,
                    COUNT(*) as payslip_count,
                    SUM(amount) as total_amount,
                    SUM(CASE WHEN payment_status = 'PAID' THEN amount ELSE 0 END) as paid_amount,
                    SUM(CASE WHEN payment_status = 'PENDING' THEN amount ELSE 0 END) as pending_amount
                FROM 
                    payslip
                WHERE 
                    date_of_payment BETWEEN :start_date AND :end_date
                GROUP BY 
                    DATE_FORMAT(date_of_payment, '%m')
                ORDER BY 
                    month ASC";
                    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fill in missing months
        $fullMonthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
            $found = false;
            
            foreach ($monthlyData as $data) {
                if ($data['month'] === $monthStr) {
                    $data['month_name'] = date('F', strtotime("{$year}-{$monthStr}-01"));
                    $fullMonthlyData[] = $data;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $fullMonthlyData[] = [
                    'month' => $monthStr,
                    'month_name' => date('F', strtotime("{$year}-{$monthStr}-01")),
                    'payslip_count' => 0,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'pending_amount' => 0
                ];
            }
        }
        
        // Calculate yearly totals
        $totalAmount = 0;
        $totalPaid = 0;
        $totalPending = 0;
        $totalCount = 0;
        
        foreach ($fullMonthlyData as $data) {
            $totalAmount += $data['total_amount'];
            $totalPaid += $data['paid_amount'];
            $totalPending += $data['pending_amount'];
            $totalCount += $data['payslip_count'];
        }
        
        return [
            'year' => $year,
            'monthly_data' => $fullMonthlyData,
            'summary' => [
                'total_amount' => $totalAmount,
                'paid_amount' => $totalPaid,
                'pending_amount' => $totalPending,
                'total_payslips' => $totalCount
            ]
        ];
    }
    
    /**
     * Generate a bank transfer report
     *
     * @param string $bankName
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function generateBankTransferReport($bankName, $startDate, $endDate)
    {
        $query = "SELECT 
                    p.payslip_no, 
                    p.employee_id,
                    e.firstname,
                    e.lastname,
                    b.bank_account,
                    b.bank_details,
                    p.amount,
                    p.date_of_payment,
                    p.payment_status
                FROM 
                    payslip p
                JOIN 
                    employees e ON p.employee_id = e.employee_id
                JOIN 
                    employee_banking_details b ON p.bank_acct = b.bank_account
                WHERE 
                    p.date_of_payment BETWEEN :start_date AND :end_date
                    AND b.preferred_bank LIKE :bank_name
                ORDER BY 
                    p.date_of_payment DESC";
                    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $bankNameParam = "%{$bankName}%";
        $stmt->bindParam(':bank_name', $bankNameParam);
        $stmt->execute();
        
        $transfers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate totals
        $totalAmount = 0;
        $totalPaid = 0;
        $totalPending = 0;
        
        foreach ($transfers as $transfer) {
            $totalAmount += $transfer['amount'];
            
            if ($transfer['payment_status'] === 'PAID') {
                $totalPaid += $transfer['amount'];
            } else if ($transfer['payment_status'] === 'PENDING') {
                $totalPending += $transfer['amount'];
            }
        }
        
        return [
            'bank_name' => $bankName,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'transfers' => $transfers,
            'summary' => [
                'total_amount' => $totalAmount,
                'paid_amount' => $totalPaid,
                'pending_amount' => $totalPending,
                'transfer_count' => count($transfers)
            ]
        ];
    }
}