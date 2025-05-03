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
    
    // ... rest of the ExcelGenerator methods remain the same
}