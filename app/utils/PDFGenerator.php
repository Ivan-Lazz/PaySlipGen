<?php

namespace App\Utils;

use App\Models\Payslip;
use App\Models\Employee;
use App\Models\Banking;

class PDFGenerator
{
    /**
     * Generate a payslip PDF
     *
     * @param string $payslipNo
     * @return string The PDF content
     */
    public static function generatePayslipPDF($payslipNo)
    {
        // In a real implementation, this would use a PDF library like FPDF or TCPDF
        // For this example, we'll simulate it by returning HTML content
        
        $payslip = new Payslip();
        $detailedPayslip = $payslip->getDetailed($payslipNo);
        
        if (!$detailedPayslip) {
            return "Payslip not found.";
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Payslip #' . $detailedPayslip['payslip_no'] . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                }
                .container {
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #ddd;
                }
                .header {
                    text-align: center;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #ddd;
                    margin-bottom: 20px;
                }
                .details {
                    margin-bottom: 20px;
                }
                .details table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .details th, .details td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .amount-breakdown {
                    margin-top: 30px;
                    margin-bottom: 30px;
                }
                .amount-total {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 18px;
                }
                .footer {
                    margin-top: 50px;
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>PAYSLIP</h1>
                    <p><strong>Payslip No:</strong> ' . $detailedPayslip['payslip_no'] . '</p>
                </div>
                
                <div class="details">
                    <h2>Employee Details</h2>
                    <table>
                        <tr>
                            <th>Employee ID</th>
                            <td>' . $detailedPayslip['employee_id'] . '</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>' . $detailedPayslip['firstname'] . ' ' . $detailedPayslip['lastname'] . '</td>
                        </tr>
                    </table>
                </div>
                
                <div class="details">
                    <h2>Payment Details</h2>
                    <table>
                        <tr>
                            <th>Bank</th>
                            <td>' . $detailedPayslip['preferred_bank'] . '</td>
                        </tr>
                        <tr>
                            <th>Account</th>
                            <td>' . $detailedPayslip['bank_acct'] . '</td>
                        </tr>
                        <tr>
                            <th>Cutoff Date</th>
                            <td>' . $detailedPayslip['cutoff_date'] . '</td>
                        </tr>
                        <tr>
                            <th>Payment Date</th>
                            <td>' . $detailedPayslip['date_of_payment'] . '</td>
                        </tr>
                        <tr>
                            <th>Payment Status</th>
                            <td>' . $detailedPayslip['payment_status'] . '</td>
                        </tr>
                    </table>
                </div>
                
                <div class="amount-breakdown">
                    <h2>Amount Breakdown</h2>
                    <table>
                        <tr>
                            <th>Basic Salary</th>
                            <td>PHP ' . number_format($detailedPayslip['salary'], 2) . '</td>
                        </tr>
                        <tr>
                            <th>Bonus</th>
                            <td>PHP ' . number_format($detailedPayslip['bonus'], 2) . '</td>
                        </tr>
                    </table>
                </div>
                
                <div class="amount-total">
                    <h2>Total Amount</h2>
                    <p><strong>PHP ' . number_format($detailedPayslip['amount'], 2) . '</strong></p>
                </div>
                
                <div class="footer">
                    <p>This is an electronically generated payslip and does not require a signature.</p>
                    <p>For any questions regarding this payslip, please contact the person in charge: ' . $detailedPayslip['person_in_charge'] . '</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        return $html;
    }
    
    /**
     * Generate a payroll report PDF
     *
     * @param string $startDate
     * @param string $endDate
     * @return string The PDF content
     */
    public static function generatePayrollReportPDF($startDate, $endDate)
    {
        // In a real implementation, this would use a PDF library like FPDF or TCPDF
        // For this example, we'll simulate it by returning HTML content
        
        $payslip = new Payslip();
        $payslips = $payslip->getByDateRange($startDate, $endDate);
        
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
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Payroll Report: ' . $startDate . ' to ' . $endDate . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                }
                .container {
                    max-width: 900px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #ddd;
                }
                .header {
                    text-align: center;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #ddd;
                    margin-bottom: 20px;
                }
                .summary {
                    margin-bottom: 30px;
                }
                .summary table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .summary th, .summary td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .details {
                    margin-bottom: 20px;
                }
                .details table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 12px;
                }
                .details th, .details td {
                    padding: 8px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .footer {
                    margin-top: 50px;
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>PAYROLL REPORT</h1>
                    <p><strong>Period:</strong> ' . $startDate . ' to ' . $endDate . '</p>
                </div>
                
                <div class="summary">
                    <h2>Summary</h2>
                    <table>
                        <tr>
                            <th>Total Payslips</th>
                            <td>' . count($payslips) . '</td>
                        </tr>
                        <tr>
                            <th>Total Amount</th>
                            <td>PHP ' . number_format($totalAmount, 2) . '</td>
                        </tr>
                        <tr>
                            <th>Total Paid</th>
                            <td>PHP ' . number_format($totalPaid, 2) . '</td>
                        </tr>
                        <tr>
                            <th>Total Pending</th>
                            <td>PHP ' . number_format($totalPending, 2) . '</td>
                        </tr>
                    </table>
                </div>
                
                <div class="details">
                    <h2>Payslip Details</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Payslip No</th>
                                <th>Employee ID</th>
                                <th>Payment Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($payslips as $payslip) {
            $html .= '
                            <tr>
                                <td>' . $payslip['payslip_no'] . '</td>
                                <td>' . $payslip['employee_id'] . '</td>
                                <td>' . $payslip['date_of_payment'] . '</td>
                                <td>PHP ' . number_format($payslip['amount'], 2) . '</td>
                                <td>' . $payslip['payment_status'] . '</td>
                            </tr>';
        }
        
        $html .= '
                        </tbody>
                    </table>
                </div>
                
                <div class="footer">
                    <p>This is an electronically generated report and does not require a signature.</p>
                    <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        return $html;
    }
    
    /**
     * Generate an employee report PDF
     *
     * @param string $employeeId
     * @return string The PDF content
     */
    public static function generateEmployeeReportPDF($employeeId)
    {
        // In a real implementation, this would use a PDF library like FPDF or TCPDF
        // For this example, we'll simulate it by returning HTML content
        
        $employee = new Employee();
        $banking = new Banking();
        $payslip = new Payslip();
        
        $employeeData = $employee->find($employeeId);
        
        if (!$employeeData) {
            return "Employee not found.";
        }
        
        $bankAccounts = $banking->getForEmployee($employeeId);
        $payslips = $payslip->getForEmployee($employeeId);
        
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
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Employee Report: ' . $employeeData['firstname'] . ' ' . $employeeData['lastname'] . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                }
                .container {
                    max-width: 900px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #ddd;
                }
                .header {
                    text-align: center;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #ddd;
                    margin-bottom: 20px;
                }
                .employee-details {
                    margin-bottom: 30px;
                }
                .employee-details table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .employee-details th, .employee-details td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .banking {
                    margin-bottom: 30px;
                }
                .banking table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .banking th, .banking td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .summary {
                    margin-bottom: 30px;
                }
                .summary table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .summary th, .summary td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .payslips {
                    margin-bottom: 20px;
                }
                .payslips table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 12px;
                }
                .payslips th, .payslips td {
                    padding: 8px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .footer {
                    margin-top: 50px;
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>EMPLOYEE REPORT</h1>
                    <p><strong>Employee ID:</strong> ' . $employeeData['employee_id'] . '</p>
                </div>
                
                <div class="employee-details">
                    <h2>Employee Details</h2>
                    <table>
                        <tr>
                            <th>Name</th>
                            <td>' . $employeeData['firstname'] . ' ' . $employeeData['lastname'] . '</td>
                        </tr>
                        <tr>
                            <th>Contact Number</th>
                            <td>' . $employeeData['contact_number'] . '</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>' . $employeeData['email'] . '</td>
                        </tr>
                    </table>
                </div>
                
                <div class="banking">
                    <h2>Banking Details</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Bank</th>
                                <th>Account Number</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($bankAccounts as $account) {
            $html .= '
                            <tr>
                                <td>' . $account['preferred_bank'] . '</td>
                                <td>' . $account['bank_account'] . '</td>
                                <td>' . $account['bank_details'] . '</td>
                            </tr>';
        }
        
        $html .= '
                        </tbody>
                    </table>
                </div>
                
                <div class="summary">
                    <h2>Payment Summary</h2>
                    <table>
                        <tr>
                            <th>Total Payslips</th>
                            <td>' . count($payslips) . '</td>
                        </tr>
                        <tr>
                            <th>Total Amount</th>
                            <td>PHP ' . number_format($totalAmount, 2) . '</td>
                        </tr>
                        <tr>
                            <th>Total Paid</th>
                            <td>PHP ' . number_format($totalPaid, 2) . '</td>
                        </tr>
                        <tr>
                            <th>Total Pending</th>
                            <td>PHP ' . number_format($totalPending, 2) . '</td>
                        </tr>
                    </table>
                </div>
                
                <div class="payslips">
                    <h2>Payslip History</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Payslip No</th>
                                <th>Payment Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($payslips as $payslip) {
            $html .= '
                            <tr>
                                <td>' . $payslip['payslip_no'] . '</td>
                                <td>' . $payslip['date_of_payment'] . '</td>
                                <td>PHP ' . number_format($payslip['amount'], 2) . '</td>
                                <td>' . $payslip['payment_status'] . '</td>
                            </tr>';
        }
        
        $html .= '
                        </tbody>
                    </table>
                </div>
                
                <div class="footer">
                    <p>This is an electronically generated report and does not require a signature.</p>
                    <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        return $html;
    }
    
    /**
     * Generate a department report PDF
     *
     * @param string $department
     * @param string $startDate
     * @param string $endDate
     * @return string The PDF content
     */
    public static function generateDepartmentReportPDF($department, $startDate, $endDate)
    {
        // In a real implementation, this would use a service to get department data
        // For this example, we'll simulate it
        
        $reportService = new \App\Services\ReportService();
        $report = $reportService->generateDepartmentReport($department, $startDate, $endDate);
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Department Report: ' . $department . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                }
                .container {
                    max-width: 900px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #ddd;
                }
                .header {
                    text-align: center;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #ddd;
                    margin-bottom: 20px;
                }
                .summary {
                    margin-bottom: 30px;
                }
                .summary table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .summary th, .summary td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .employee-stats {
                    margin-bottom: 30px;
                }
                .employee-stats table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .employee-stats th, .employee-stats td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .payslips {
                    margin-bottom: 20px;
                }
                .payslips table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 12px;
                }
                    .payslips th, .payslips td {
                    padding: 8px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .footer {
                    margin-top: 50px;
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>DEPARTMENT REPORT</h1>
                    <p><strong>Department:</strong> ' . $department . '</p>
                    <p><strong>Period:</strong> ' . $startDate . ' to ' . $endDate . '</p>
                </div>
                
                <div class="summary">
                    <h2>Summary</h2>
                    <table>
                        <tr>
                            <th>Total Employees</th>
                            <td>' . $report['summary']['employee_count'] . '</td>
                        </tr>
                        <tr>
                            <th>Total Payslips</th>
                            <td>' . $report['summary']['payslip_count'] . '</td>
                        </tr>
                        <tr>
                            <th>Total Amount</th>
                            <td>PHP ' . number_format($report['summary']['total_amount'], 2) . '</td>
                        </tr>
                        <tr>
                            <th>Total Paid</th>
                            <td>PHP ' . number_format($report['summary']['paid_amount'], 2) . '</td>
                        </tr>
                        <tr>
                            <th>Total Pending</th>
                            <td>PHP ' . number_format($report['summary']['pending_amount'], 2) . '</td>
                        </tr>
                    </table>
                </div>
                
                <div class="employee-stats">
                    <h2>Employee Statistics</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Total Amount</th>
                                <th>Payslip Count</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($report['employee_stats'] as $stat) {
            $html .= '
                            <tr>
                                <td>' . $stat['employee_id'] . '</td>
                                <td>' . $stat['name'] . '</td>
                                <td>PHP ' . number_format($stat['total_amount'], 2) . '</td>
                                <td>' . $stat['payslip_count'] . '</td>
                            </tr>';
        }
        
        $html .= '
                        </tbody>
                    </table>
                </div>
                
                <div class="payslips">
                    <h2>Payslip Details</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Payslip No</th>
                                <th>Employee</th>
                                <th>Payment Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($report['payslips'] as $payslip) {
            $html .= '
                            <tr>
                                <td>' . $payslip['payslip_no'] . '</td>
                                <td>' . $payslip['firstname'] . ' ' . $payslip['lastname'] . '</td>
                                <td>' . $payslip['date_of_payment'] . '</td>
                                <td>PHP ' . number_format($payslip['amount'], 2) . '</td>
                                <td>' . $payslip['payment_status'] . '</td>
                            </tr>';
        }
        
        $html .= '
                        </tbody>
                    </table>
                </div>
                
                <div class="footer">
                    <p>This is an electronically generated report and does not require a signature.</p>
                    <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        return $html;
    }
}