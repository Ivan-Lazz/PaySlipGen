<?php

namespace App\Services;

class PayrollCalculator
{
    // Philippine tax brackets for 2023 (example)
    protected $taxBrackets = [
        [0, 250000, 0, 0],
        [250001, 400000, 0, 0.20],
        [400001, 800000, 30000, 0.25],
        [800001, 2000000, 130000, 0.30],
        [2000001, 8000000, 490000, 0.32],
        [8000001, PHP_INT_MAX, 2410000, 0.35]
    ];
    
    // SSS contribution table
    protected $sssTable = [
        // [min_salary, max_salary, employee_contribution, employer_contribution]
        [1000, 3249.99, 135.00, 265.00],
        [3250, 3749.99, 157.50, 307.50],
        [3750, 4249.99, 180.00, 350.00],
        [4250, 4749.99, 202.50, 392.50],
        [4750, 5249.99, 225.00, 435.00],
        [5250, 5749.99, 247.50, 477.50],
        [5750, 6249.99, 270.00, 520.00],
        [6250, 6749.99, 292.50, 562.50],
        [6750, 7249.99, 315.00, 605.00],
        [7250, 7749.99, 337.50, 647.50],
        [7750, 8249.99, 360.00, 690.00],
        [8250, 8749.99, 382.50, 732.50],
        [8750, 9249.99, 405.00, 775.00],
        [9250, 9749.99, 427.50, 817.50],
        [9750, 10249.99, 450.00, 860.00],
        [10250, 10749.99, 472.50, 902.50],
        [10750, 11249.99, 495.00, 945.00],
        [11250, 11749.99, 517.50, 987.50],
        [11750, 12249.99, 540.00, 1030.00],
        [12250, 12749.99, 562.50, 1072.50],
        [12750, 13249.99, 585.00, 1115.00],
        [13250, 13749.99, 607.50, 1157.50],
        [13750, 14249.99, 630.00, 1200.00],
        [14250, 14749.99, 652.50, 1242.50],
        [14750, 15249.99, 675.00, 1285.00],
        [15250, 15749.99, 697.50, 1327.50],
        [15750, 16249.99, 720.00, 1370.00],
        [16250, 16749.99, 742.50, 1412.50],
        [16750, 17249.99, 765.00, 1455.00],
        [17250, 17749.99, 787.50, 1497.50],
        [17750, 18249.99, 810.00, 1540.00],
        [18250, 18749.99, 832.50, 1582.50],
        [18750, 19249.99, 855.00, 1625.00],
        [19250, 19749.99, 877.50, 1667.50],
        [19750, PHP_INT_MAX, 900.00, 1700.00]
    ];
    
    // PhilHealth contribution rate
    protected $philhealthRate = 0.03; // 3%
    protected $philhealthMaxSalary = 60000; // Maximum salary base
    
    // Pag-IBIG contribution
    protected $pagibigRate = 0.02; // 2%
    protected $pagibigMaxContribution = 100; // Maximum monthly contribution
    
    /**
     * Calculate gross income
     *
     * @param float $basicPay
     * @param float $allowances
     * @param float $overtime
     * @param float $bonus
     * @return float
     */
    public function calculateGrossIncome($basicPay, $allowances = 0, $overtime = 0, $bonus = 0)
    {
        return $basicPay + $allowances + $overtime + $bonus;
    }
    
    /**
     * Calculate SSS contribution
     *
     * @param float $salary Monthly salary
     * @param bool $employeeShare Get employee's share (true) or employer's share (false)
     * @return float
     */
    public function calculateSSS($salary, $employeeShare = true)
    {
        foreach ($this->sssTable as $bracket) {
            if ($salary >= $bracket[0] && $salary <= $bracket[1]) {
                return $employeeShare ? $bracket[2] : $bracket[3];
            }
        }
        
        // If salary exceeds the maximum in the table
        $lastBracket = end($this->sssTable);
        return $employeeShare ? $lastBracket[2] : $lastBracket[3];
    }
    
    /**
     * Calculate PhilHealth contribution
     *
     * @param float $salary Monthly salary
     * @param bool $employeeShare Get employee's share (true) or employer's share (false)
     * @return float
     */
    public function calculatePhilHealth($salary, $employeeShare = true)
    {
        // Cap the salary base
        $baseSalary = min($salary, $this->philhealthMaxSalary);
        
        // Calculate total contribution
        $totalContribution = $baseSalary * $this->philhealthRate;
        
        // Each party pays half
        return $employeeShare ? ($totalContribution / 2) : ($totalContribution / 2);
    }
    
    /**
     * Calculate Pag-IBIG contribution
     *
     * @param float $salary Monthly salary
     * @param bool $employeeShare Get employee's share (true) or employer's share (false)
     * @return float
     */
    public function calculatePagIBIG($salary, $employeeShare = true)
    {
        $contribution = $salary * $this->pagibigRate;
        $contribution = min($contribution, $this->pagibigMaxContribution);
        
        return $employeeShare ? $contribution : $contribution;
    }
    
    /**
     * Calculate withholding tax
     *
     * @param float $taxableIncome Annual taxable income
     * @return float Monthly withholding tax
     */
    public function calculateWithholdingTax($taxableIncome)
    {
        // Convert to annual if monthly income provided
        $annualIncome = $taxableIncome * 12;
        
        // Find applicable tax bracket
        foreach ($this->taxBrackets as $bracket) {
            if ($annualIncome >= $bracket[0] && $annualIncome <= $bracket[1]) {
                // Calculate tax: base tax + percentage of excess over minimum
                $tax = $bracket[2] + (($annualIncome - $bracket[0]) * $bracket[3]);
                
                // Convert back to monthly
                return $tax / 12;
            }
        }
        
        return 0;
    }
    
    /**
     * Calculate total deductions
     *
     * @param float $sss SSS contribution
     * @param float $philhealth PhilHealth contribution
     * @param float $pagibig Pag-IBIG contribution
     * @param float $tax Withholding tax
     * @param float $otherDeductions Other deductions
     * @return float
     */
    public function calculateTotalDeductions($sss, $philhealth, $pagibig, $tax, $otherDeductions = 0)
    {
        return $sss + $philhealth + $pagibig + $tax + $otherDeductions;
    }
    
    /**
     * Calculate net income
     *
     * @param float $grossIncome
     * @param float $totalDeductions
     * @return float
     */
    public function calculateNetIncome($grossIncome, $totalDeductions)
    {
        return $grossIncome - $totalDeductions;
    }
    
    /**
     * Calculate complete payroll for an employee
     *
     * @param float $basicPay Basic monthly salary
     * @param float $allowances Monthly allowances
     * @param float $overtime Overtime pay
     * @param float $bonus Bonus
     * @param float $otherDeductions Other deductions
     * @return array Payroll calculation details
     */
    public function calculatePayroll($basicPay, $allowances = 0, $overtime = 0, $bonus = 0, $otherDeductions = 0)
    {
        // Calculate gross income
        $grossIncome = $this->calculateGrossIncome($basicPay, $allowances, $overtime, $bonus);
        
        // Calculate mandatory contributions
        $sss = $this->calculateSSS($basicPay);
        $philhealth = $this->calculatePhilHealth($basicPay);
        $pagibig = $this->calculatePagIBIG($basicPay);
        
        // Calculate taxable income
        $taxableIncome = $grossIncome - $sss - $philhealth - $pagibig;
        
        // Calculate withholding tax
        $tax = $this->calculateWithholdingTax($taxableIncome);
        
        // Calculate total deductions
        $totalDeductions = $this->calculateTotalDeductions($sss, $philhealth, $pagibig, $tax, $otherDeductions);
        
        // Calculate net income
        $netIncome = $this->calculateNetIncome($grossIncome, $totalDeductions);
        
        // Return detailed calculation
        return [
            'gross_income' => [
                'basic_pay' => $basicPay,
                'allowances' => $allowances,
                'overtime' => $overtime,
                'bonus' => $bonus,
                'total' => $grossIncome
            ],
            'deductions' => [
                'sss' => $sss,
                'philhealth' => $philhealth,
                'pagibig' => $pagibig,
                'tax' => $tax,
                'other_deductions' => $otherDeductions,
                'total' => $totalDeductions
            ],
            'taxable_income' => $taxableIncome,
            'net_income' => $netIncome
        ];
    }
}