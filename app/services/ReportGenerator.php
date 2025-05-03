<?php

namespace App\Services;

use App\Models\Payslip;
use App\Models\Employee;
use App\Models\Account;
use App\Utils\PDFGenerator;
use App\Utils\ExcelGenerator;
use Core\Database;

class ReportGenerator
{
    protected $payslip;
    protected $employee;
    protected $account;
    protected $db;
    
    public function __construct()
    {
        $this->payslip = new Payslip();
        $this->employee = new Employee();
        $this->account = new Account();
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Generate payroll report
     *
     * @param string $startDate
     * @param string $endDate
     * @param string $format Format (pdf, excel, json)
     * @return mixed
     */
    public function generatePayrollReport($startDate, $endDate, $format = 'json')
    {
        $payslips = $this->payslip->getByDateRange($startDate, $endDate);
        
        $totalAmount = 0;
        $totalPaid = 0;
        $totalPending = 0;
        
        foreach ($payslips as $payslip) {
            $totalAmount += $payslip['amount'];
            
            if ($payslip['payment_status'] === 'PAID') {
                $totalPaid += $payslip['amount'];
            } else if ($payslip['payment_status'] === 'PENDING') {
                $totalPending += $payslip['amount'];
            }
        }
        
        $report = [
            'payslips' => $payslips,
            'summary' => [
                'total_amount' => $totalAmount,
                'paid_amount' => $totalPaid,
                'pending_amount' => $totalPending,
                'total_count' => count($payslips),
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ];
        
        switch ($format) {
            case 'pdf':
                return PDFGenerator::generatePayrollReportPDF($startDate, $endDate);
            case 'excel':
                return ExcelGenerator::generatePayrollExcel($startDate, $endDate);
            case 'json':
            default:
                return $report;
        }
    }
    
    /**
     * Generate employee report
     *
     * @param string $employeeId
     * @param string $format Format (pdf, excel, json)
     * @return mixed
     */
    public function generateEmployeeReport($employeeId, $format = 'json')
    {
        $employee = $this->employee->getWithDetails($employeeId);
        
        if (!$employee) {
            return null;
        }
        
        $payslips = $this->payslip->getForEmployee($employeeId);
        
        $totalAmount = 0;
        $totalPaid = 0;
        $totalPending = 0;
        $latestPayment = null;
        
        foreach ($payslips as $payslip) {
            $totalAmount += $payslip['amount'];
            
            if ($payslip['payment_status'] === 'PAID') {
                $totalPaid += $payslip['amount'];
            } else if ($payslip['payment_status'] === 'PENDING') {
                $totalPending += $payslip['amount'];
            }
            
            if ($latestPayment === null || strtotime($payslip['date_of_payment']) > strtotime($latestPayment)) {
                $latestPayment = $payslip['date_of_payment'];
            }
        }
        
        $report = [
            'employee' => $employee,
            'payslips' => $payslips,
            'summary' => [
                'total_payslips' => count($payslips),
                'total_paid' => $totalPaid,
                'total_pending' => $totalPending,
                'latest_payment' => $latestPayment
            ]
        ];
        
        switch ($format) {
            case 'pdf':
                return PDFGenerator::generateEmployeeReportPDF($employeeId);
            case 'excel':
                // Not implemented in ExcelGenerator yet
                return json_encode($report);
            case 'json':
            default:
                return $report;
        }
    }
    
    /**
     * Generate bank transfer report
     *
     * @param string $startDate
     * @param string $endDate
     * @param string $format Format (pdf, excel, json)
     * @return mixed
     */
    public function generateBankTransferReport($startDate, $endDate, $format = 'json')
    {
        // Get all pending payments in the date range
        $query = "SELECT 
                    p.payslip_no, 
                    p.employee_id, 
                    e.firstname, 
                    e.lastname, 
                    b.preferred_bank, 
                    b.bank_account, 
                    p.amount, 
                    p.date_of_payment
                FROM 
                    payslip p
                JOIN 
                    employees e ON p.employee_id = e.employee_id
                JOIN 
                    employee_banking_details b ON p.bank_acct = b.bank_account
                WHERE 
                    p.payment_status = 'PENDING' AND
                    p.date_of_payment BETWEEN :start_date AND :end_date
                ORDER BY 
                    b.preferred_bank, p.date_of_payment";
                    
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        $transfers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Group by bank
        $bankSummary = [];
        $totalAmount = 0;
        
        foreach ($transfers as $transfer) {
            $bank = $transfer['preferred_bank'];
            
            if (!isset($bankSummary[$bank])) {
                $bankSummary[$bank] = [
                    'bank' => $bank,
                    'count' => 0,
                    'amount' => 0
                ];
            }
            
            $bankSummary[$bank]['count']++;
            $bankSummary[$bank]['amount'] += $transfer['amount'];
            $totalAmount += $transfer['amount'];
        }
        
        $report = [
            'transfers' => $transfers,
            'bank_summary' => array_values($bankSummary),
            'summary' => [
                'total_amount' => $totalAmount,
                'total_count' => count($transfers),
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ];
        
        switch ($format) {
            case 'pdf':
                // Not implemented in PDFGenerator yet
                return json_encode($report);
            case 'excel':
                return ExcelGenerator::generateBankTransferExcel($startDate, $endDate);
            case 'json':
            default:
                return $report;
        }
    }
    
    /**
     * Generate monthly summary report
     *
     * @param int $year
     * @param int $month
     * @param string $format Format (pdf, excel, json)
     * @return mixed
     */
    public function generateMonthlySummaryReport($year, $month, $format = 'json')
    {
        // Validate input
        $year = (int)$year;
        $month = (int)$month;
        
        if ($month < 1 || $month > 12) {
            return null;
        }
        
        // Set date range for the month
        $startDate = sprintf("%04d-%02d-01", $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Get all payslips in the month, grouped by employee
        $query = "SELECT 
                    e.employee_id,
                    e.firstname,
                    e.lastname,
                    COUNT(p.payslip_no) as payslip_count,
                    SUM(p.amount) as total_amount,
                    SUM(CASE WHEN p.payment_status = 'PAID' THEN p.amount ELSE 0 END) as paid_amount,
                    SUM(CASE WHEN p.payment_status = 'PENDING' THEN p.amount ELSE 0 END) as pending_amount
                FROM 
                    employees e
                LEFT JOIN 
                    payslip p ON e.employee_id = p.employee_id AND p.date_of_payment BETWEEN :start_date AND :end_date
                GROUP BY 
                    e.employee_id
                ORDER BY 
                    e.lastname, e.firstname";
                    
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        $employees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $totalPayslips = 0;
        $totalAmount = 0;
        $totalPaid = 0;
        $totalPending = 0;
        
        // Calculate totals
        foreach ($employees as $emp) {
            $totalPayslips += (int)$emp['payslip_count'];
            $totalAmount += (float)$emp['total_amount'];
            $totalPaid += (float)$emp['paid_amount'];
            $totalPending += (float)$emp['pending_amount'];
        }
        
        $report = [
            'year' => $year,
            'month' => $month,
            'month_name' => date('F', strtotime($startDate)),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'employees' => $employees,
            'summary' => [
                'total_payslips' => $totalPayslips,
                'total_amount' => $totalAmount,
                'total_paid' => $totalPaid,
                'total_pending' => $totalPending
            ]
        ];
        
        switch ($format) {
            case 'pdf':
                // Not implemented in PDFGenerator yet
                return json_encode($report);
            case 'excel':
                return ExcelGenerator::generateMonthlySummaryExcel($year, $month);
            case 'json':
            default:
                return $report;
        }
    }
    
    /**
     * Generate annual summary report
     *
     * @param int $year
     * @param string $format Format (pdf, excel, json)
     * @return mixed
     */
    public function generateAnnualSummaryReport($year, $format = 'json')
    {
        $year = (int)$year;
        
        // Get monthly totals for the year
        $query = "SELECT 
                    MONTH(date_of_payment) as month,
                    COUNT(*) as payslip_count,
                    SUM(amount) as total_amount,
                    SUM(CASE WHEN payment_status = 'PAID' THEN amount ELSE 0 END) as paid_amount,
                    SUM(CASE WHEN payment_status = 'PENDING' THEN amount ELSE 0 END) as pending_amount
                FROM 
                    payslip
                WHERE 
                    YEAR(date_of_payment) = :year
                GROUP BY 
                    MONTH(date_of_payment)
                ORDER BY 
                    month";
                    
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year, \PDO::PARAM_INT);
        $stmt->execute();
        
        $monthlyData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Fill in missing months
        $completeMonthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $found = false;
            
            foreach ($monthlyData as $data) {
                if ((int)$data['month'] === $month) {
                    $data['month_name'] = date('F', mktime(0, 0, 0, $month, 1, $year));
                    $completeMonthlyData[] = $data;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $completeMonthlyData[] = [
                    'month' => $month,
                    'month_name' => date('F', mktime(0, 0, 0, $month, 1, $year)),
                    'payslip_count' => 0,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'pending_amount' => 0
                ];
            }
        }
        
        // Calculate annual totals
        $totalPayslips = 0;
        $totalAmount = 0;
        $totalPaid = 0;
        $totalPending = 0;
        
        foreach ($completeMonthlyData as $data) {
            $totalPayslips += (int)$data['payslip_count'];
            $totalAmount += (float)$data['total_amount'];
            $totalPaid += (float)$data['paid_amount'];
            $totalPending += (float)$data['pending_amount'];
        }
        
        $report = [
            'year' => $year,
            'monthly_data' => $completeMonthlyData,
            'summary' => [
                'total_payslips' => $totalPayslips,
                'total_amount' => $totalAmount,
                'total_paid' => $totalPaid,
                'total_pending' => $totalPending
            ]
        ];
        
        switch ($format) {
            case 'pdf':
                // Not implemented in PDFGenerator yet
                return json_encode($report);
            case 'excel':
                // Not implemented in ExcelGenerator yet
                return json_encode($report);
            case 'json':
            default:
                return $report;
        }
    }
    
    /**
     * Generate department report
     *
     * @param string $department
     * @param string $startDate
     * @param string $endDate
     * @param string $format Format (pdf, excel, json)
     * @return mixed
     */
    public function generateDepartmentReport($department, $startDate, $endDate, $format = 'json')
    {
        // In a real implementation, we would query by department
        // For this example, we'll simulate it
        
        // Get all payslips in the date range
        $query = "SELECT 
                    p.payslip_no, 
                    p.employee_id,
                    e.firstname,
                    e.lastname,
                    p.amount,
                    p.date_of_payment,
                    p.payment_status
                FROM 
                    payslip p
                JOIN 
                    employees e ON p.employee_id = e.employee_id
                WHERE 
                    p.date_of_payment BETWEEN :start_date AND :end_date
                ORDER BY 
                    p.date_of_payment DESC";
                    
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        $payslips = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
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
        
        $filteredPayslips = array_values($filteredPayslips);
        
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
        
        $report = [
            'department' => $department,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payslips' => $filteredPayslips,
            'employee_stats' => array_values($employeeStats),
            'summary' => [
                'total_amount' => $totalAmount,
                'paid_amount' => $totalPaid,
                'pending_amount' => $totalPending,
                'employee_count' => count($employeeStats),
                'payslip_count' => count($filteredPayslips)
            ]
        ];
        
        switch ($format) {
            case 'pdf':
                return PDFGenerator::generateDepartmentReportPDF($department, $startDate, $endDate);
            case 'excel':
                // Not implemented in ExcelGenerator yet
                return json_encode($report);
            case 'json':
            default:
                return $report;
        }
    }
}