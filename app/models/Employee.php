<?php

namespace App\Models;

use Core\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'employee_id';
    
    protected $fillable = [
        'employee_id',
        'firstname',
        'lastname',
        'contact_number',
        'email'
    ];
    
    /**
     * Get employee's bank accounts
     *
     * @param string $employeeId
     * @return array
     */
    public function getBankAccounts($employeeId)
    {
        $query = "SELECT * FROM employee_banking_details WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get employee's account details
     *
     * @param string $employeeId
     * @return array|null
     */
    public function getAccount($employeeId)
    {
        $query = "SELECT * FROM employee_account WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->execute();
        
        $account = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Remove password
        if ($account) {
            unset($account['account_pass']);
        }
        
        return $account;
    }
    
    /**
     * Get employee with accounts and banking details
     *
     * @param string $employeeId
     * @return array|null
     */
    public function getWithDetails($employeeId)
    {
        $employee = $this->find($employeeId);
        
        if (!$employee) {
            return null;
        }
        
        $employee['banking'] = $this->getBankAccounts($employeeId);
        $employee['account'] = $this->getAccount($employeeId);
        
        return $employee;
    }
    
    /**
     * Search employees by name or ID
     *
     * @param string $search
     * @return array
     */
    public function search($search)
    {
        $query = "SELECT * FROM {$this->table} WHERE 
                 firstname LIKE :search OR 
                 lastname LIKE :search OR 
                 employee_id LIKE :search";
                 
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$search}%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}