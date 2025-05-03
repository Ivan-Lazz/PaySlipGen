<?php

namespace App\Utils;

use Core\Database;

class IDGenerator
{
    /**
     * Generate an employee ID
     *
     * @return string
     */
    public static function generateEmployeeID()
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
     * Generate a payslip number
     *
     * @return string
     */
    public static function generatePayslipNumber()
    {
        $conn = Database::getInstance()->getConnection();
        
        // Get the last payslip number
        $query = "SELECT payslip_no FROM payslip ORDER BY payslip_no DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        $lastId = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($lastId) {
            // Extract number part and increment
            $lastNumber = (int)$lastId['payslip_no'];
            $nextNumber = $lastNumber + 1;
        } else {
            // Start from 1 if no existing payslips
            $nextNumber = 1;
        }
        
        // Check if max has been reached
        if ($nextNumber > 999999999) {
            throw new \Exception("Maximum payslip number limit reached!");
        }
        
        // Format: 9 digits with leading zeros
        return str_pad($nextNumber, 9, '0', STR_PAD_LEFT);
    }
    
    /**
     * Generate a random account ID
     *
     * @return string
     */
    public static function generateAccountID()
    {
        $prefix = 'ACC';
        $randomPart = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
        
        return $prefix . $randomPart;
    }
}