<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Payslip;
use App\Models\Employee;
use App\Services\PayslipService;

class ReportController extends Controller
{
    protected $payslip;
    protected $employee;
    protected $payslipService;
    
    public function __construct()
    {
        parent::__construct();
        $this->payslip = new Payslip();
        $this->employee = new Employee();
        $this->payslipService = new PayslipService();
    }
    
    /**
     * Generate payroll report
     *
     * @return \Core\Response
     */
    public function payroll()
    {
        $startDate = $this->request()->get('start_date');
        $endDate = $this->request()->get('end_date');
        
        if (empty($startDate) || empty($endDate)) {
            return $this->error('Start date and end date are required', 400);
        }
        
        $report = $this->payslipService->generateReport($startDate, $endDate);
        
        return $this->success($report);
    }
    
    /**
     * Generate employee report
     *
     * @param string $id
     * @return \Core\Response
     */
    public function employee($id)
    {
        // Check if employee exists
        $employee = $this->employee->find($id);
        
        if (!$employee) {
            return $this->error('Employee not found', 404);
        }
        
        // Get employee details with banking information
        $employeeDetails = $this->employee->getWithDetails($id);
        
        // Get employee payslips
        $payslips = $this->payslipService->getEmployeePayslips($id);
        
        // Calculate total amount paid
        $totalPaid = 0;
        $totalPending = 0;
        
        foreach ($payslips as $payslip) {
            if ($payslip['payment_status'] === 'PAID') {
                $totalPaid += $payslip['amount'];
            } else if ($payslip['payment_status'] === 'PENDING') {
                $totalPending += $payslip['amount'];
            }
        }
        
        // Build the report
        $report = [
            'employee' => $employeeDetails,
            'payslips' => $payslips,
            'summary' => [
                'total_payslips' => count($payslips),
                'total_paid' => $totalPaid,
                'total_pending' => $totalPending,
                'latest_payment' => !empty($payslips) ? $payslips[0]['date_of_payment'] : null
            ]
        ];
        
        return $this->success($report);
    }
    
    /**
     * Generate monthly report
     *
     * @return \Core\Response
     */
    public function monthly()
    {
        $year = $this->request()->get('year', date('Y'));
        $month = $this->request()->get('month', date('m'));
        
        // Validate input
        if (!is_numeric($year) || !is_numeric($month)) {
            return $this->error('Year and month must be numeric', 400);
        }
        
        // Set date range for the month
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $report = $this->payslipService->generateReport($startDate, $endDate);
        
        // Add month-specific information
        $report['summary']['year'] = (int)$year;
        $report['summary']['month'] = (int)$month;
        $report['summary']['month_name'] = date('F', strtotime($startDate));
        
        return $this->success($report);
    }
    
    /**
     * Generate annual report
     *
     * @return \Core\Response
     */
    public function annual()
    {
        $year = $this->request()->get('year', date('Y'));
        
        // Validate input
        if (!is_numeric($year)) {
            return $this->error('Year must be numeric', 400);
        }
        
        // Set date range for the year
        $startDate = "$year-01-01";
        $endDate = "$year-12-31";
        
        $report = $this->payslipService->generateReport($startDate, $endDate);
        
        // Add year-specific information
        $report['summary']['year'] = (int)$year;
        
        // Group by month
        $monthlyData = [];
        
        foreach ($report['payslips'] as $payslip) {
            $month = date('m', strtotime($payslip['date_of_payment']));
            $monthName = date('F', strtotime($payslip['date_of_payment']));
            
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = [
                    'month' => (int)$month,
                    'month_name' => $monthName,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'pending_amount' => 0,
                    'count' => 0
                ];
            }
            
            $monthlyData[$month]['total_amount'] += $payslip['amount'];
            $monthlyData[$month]['count']++;
            
            if ($payslip['payment_status'] === 'PAID') {
                $monthlyData[$month]['paid_amount'] += $payslip['amount'];
            } else if ($payslip['payment_status'] === 'PENDING') {
                $monthlyData[$month]['pending_amount'] += $payslip['amount'];
            }
        }
        
        // Sort by month
        ksort($monthlyData);
        
        $report['monthly'] = array_values($monthlyData);
        
        return $this->success($report);
    }
}