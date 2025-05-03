<?php

namespace App\Models;

use Core\Model;

class Banking extends Model
{
    protected $table = 'employee_banking_details';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'employee_id',
        'preferred_bank',
        'bank_account',
        'bank_details'
    ];
    
    /**
     * Get banking details for an employee
     *
     * @param string $employeeId
     * @return array
     */
    public function getForEmployee($employeeId)
    {
        $query = "SELECT * FROM {$this->table} WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Find bank account by number
     *
     * @param string $accountNumber
     * @return array|null
     */
    public function findByAccountNumber($accountNumber)
    {
        $query = "SELECT * FROM {$this->table} WHERE bank_account = :account_number LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':account_number', $accountNumber);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if bank account exists
     *
     * @param string $employeeId
     * @param string $accountNumber
     * @return bool
     */
    public function accountExists($employeeId, $accountNumber)
    {
        $query = "SELECT * FROM {$this->table} WHERE employee_id = :employee_id AND bank_account = :account_number";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->bindParam(':account_number', $accountNumber);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}