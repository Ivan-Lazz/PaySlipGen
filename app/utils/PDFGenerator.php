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
    
    // ... rest of the PDFGenerator methods remain the same
}