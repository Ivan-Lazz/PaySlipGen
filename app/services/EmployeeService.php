<?php

namespace App\Services;

use App\Models\Employee;
use Core\Database;

class EmployeeService
{
    protected $employee;
    
    public function __construct()
    {
        $this->employee = new Employee();
    }
    
    /**
     * Generate a new employee ID
     *
     * @return string
     */
    public function generateEmployeeId()
    {
        $conn = Database::getInstance()->getConnection();
        $currentYear = date("Y");
        
        // Get the last employee ID for this year
        $query = "SELECT employee_id FROM employees WHERE employee_id LIKE :year_prefix ORDER BY employee_id DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $yearPrefix = $currentYear . '%';
        $stmt->bindParam(':year_prefix', $yearPrefix);
        $stmt->execute();
        
        $lastId = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($lastId) {
            // Extract number part and increment
            $lastNumber = (int)substr($lastId['employee_id'], -5);
            $nextNumber = $lastNumber + 1;
        } else {
            // Start from 1 if no existing IDs for this year
            $nextNumber = 1;
        }
        
        // Check if max has been reached
        if ($nextNumber > 99999) {
            throw new \Exception("Maximum employee ID limit reached for year {$currentYear}.");
        }
        
        // Format: YYYY#####
        return $currentYear . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get complete employee details
     *
     * @param string $employeeId
     * @return array|null
     */
    public function getEmployeeDetails($employeeId)
    {
        return $this->employee->getWithDetails($employeeId);
    }
    
    /**
     * Search for employees
     *
     * @param string $search
     * @return array
     */
    public function searchEmployees($search)
    {
        return $this->employee->search($search);
    }
}