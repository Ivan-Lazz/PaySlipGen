<?php

namespace App\Utils;

use App\Models\Payslip;
use App\Models\Employee;
use Core\Database;

class ExcelGenerator
{
    /**
     * Generate a payroll Excel report
     *
     * @param string $startDate
     * @param string $endDate
     * @return string The Excel content
     */
    public static function generatePayrollExcel($startDate, $endDate)
    {
        // In a real implementation, this would use a library like PhpSpreadsheet
        // For this example, we'll return CSV content which can be opened in Excel
        
        $payslip = new Payslip();
        $payslips = $payslip->getByDateRange($startDate, $endDate);
        
        // Prepare CSV content
        $csv = "Payslip No,Employee ID,Employee Name,Payment Date,Salary,Bonus,Total Amount,Status\n";
        
        $employee = new Employee();
        
        foreach ($payslips as $p) {
            $employeeData = $employee->find($p['employee_id']);
            $employeeName = '';
            
            if ($employeeData) {
                $employeeName = $employeeData['firstname'] . ' ' . $employeeData['lastname'];
            }
            
            $csv .= "{$p['payslip_no']},{$p['employee_id']}," . 
                    "\"$employeeName\",{$p['date_of_payment']}," . 
                    "{$p['salary']},{$p['bonus']}," . 
                    "{$p['amount']},{$p['payment_status']}\n";
        }
        
        return $csv;
    }
    
    /**
     * Generate an employee list Excel report
     *
     * @return string The Excel content
     */
    public static function generateEmployeeListExcel()
    {
        // In a real implementation, this would use a library like PhpSpreadsheet
        // For this example, we'll return CSV content which can be opened in Excel
        
        $employee = new Employee();
        $employees = $employee->all();
        
        // Prepare CSV content
        $csv = "Employee ID,First Name,Last Name,Contact Number,Email\n";
        
        foreach ($employees as $e) {
            $csv .= "{$e['employee_id']},\"{$e['firstname']}\",\"{$e['lastname']}\"," . 
                    "{$e['contact_number']},{$e['email']}\n";
        }
        
        return $csv;
    }
    
    /**
     * Generate a bank transfer Excel report
     *
     * @param string $startDate
     * @param string $endDate
     * @return string The Excel content
     */
    public static function generateBankTransferExcel($startDate, $endDate)
    {
        // In a real implementation, this would use a library like PhpSpreadsheet
        // For this example, we'll return CSV content which can be opened in Excel
        
        // Get database connection
        $db = Database::getInstance()->getConnection();
        
        // Get all pending payments in the date range
        $query = "SELECT 
                    p.payslip_no, 
                    p.employee_id, 
                    e.firstname, 
                    e.lastname, 
                    b.preferred_bank, 
                    b.bank_account,
                    p.salary,
                    p.bonus, 
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
                    
        $stmt = $db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        $transfers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Prepare CSV content
        $csv = "Bank,Account Number,Account Name,Reference (Payslip No),Salary,Bonus,Total Amount,Payment Date\n";
        
        foreach ($transfers as $t) {
            $accountName = $t['firstname'] . ' ' . $t['lastname'];
            
            $csv .= "\"{$t['preferred_bank']}\",{$t['bank_account']},\"$accountName\"," . 
                    "{$t['payslip_no']},{$t['salary']},{$t['bonus']}," . 
                    "{$t['amount']},{$t['date_of_payment']}\n";
        }
        
        return $csv;
    }
    
    /**
     * Generate a monthly summary Excel report
     *
     * @param int $year
     * @param int $month
     * @return string The Excel content
     */
    public static function generateMonthlySummaryExcel($year, $month)
    {
        // Validate input
        $year = (int)$year;
        $month = (int)$month;
        
        if ($month < 1 || $month > 12) {
            return "Invalid month. Month must be between 1 and 12.";
        }
        
        // Set date range for the month
        $startDate = sprintf("%04d-%02d-01", $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Get database connection
        $db = Database::getInstance()->getConnection();
        
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
                    
        $stmt = $db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        $employees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Prepare CSV content
        $csv = "Employee ID,Employee Name,Payslip Count,Total Amount,Paid Amount,Pending Amount\n";
        
        $totalPayslips = 0;
        $grandTotal = 0;
        $grandPaid = 0;
        $grandPending = 0;
        
        foreach ($employees as $emp) {
            $employeeName = $emp['firstname'] . ' ' . $emp['lastname'];
            $payslipCount = (int)$emp['payslip_count'];
            $totalAmount = (float)$emp['total_amount'];
            $paidAmount = (float)$emp['paid_amount'];
            $pendingAmount = (float)$emp['pending_amount'];
            
            if ($payslipCount > 0) {
                $csv .= "{$emp['employee_id']},\"$employeeName\"," . 
                        "{$payslipCount}," . 
                        number_format($totalAmount, 2) . "," . 
                        number_format($paidAmount, 2) . "," . 
                        number_format($pendingAmount, 2) . "\n";
                        
                $totalPayslips += $payslipCount;
                $grandTotal += $totalAmount;
                $grandPaid += $paidAmount;
                $grandPending += $pendingAmount;
            }
        }
        
        // Add summary row
        $csv .= "\n\"SUMMARY\",\"" . date('F Y', strtotime($startDate)) . "\"," . 
                "{$totalPayslips}," . 
                number_format($grandTotal, 2) . "," . 
                number_format($grandPaid, 2) . "," . 
                number_format($grandPending, 2) . "\n";
        
        return $csv;
    }
}