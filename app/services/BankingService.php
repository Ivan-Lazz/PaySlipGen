<?php

namespace App\Services;

use App\Models\Banking;
use App\Models\Employee;
use Core\Database;

class BankingService
{
    protected $banking;
    protected $employee;
    
    public function __construct()
    {
        $this->banking = new Banking();
        $this->employee = new Employee();
    }
    
    /**
     * Add bank account to employee
     *
     * @param string $employeeId
     * @param array $bankData
     * @return bool
     */
    public function addBankAccount($employeeId, array $bankData)
    {
        // Check if employee exists
        if (!$this->employee->find($employeeId)) {
            return false;
        }
        
        // Check if bank account already exists
        if ($this->banking->accountExists($employeeId, $bankData['bank_account'])) {
            return false;
        }
        
        // Create bank account
        $data = [
            'employee_id' => $employeeId,
            'preferred_bank' => $bankData['preferred_bank'],
            'bank_account' => $bankData['bank_account'],
            'bank_details' => $bankData['bank_details'] ?? $bankData['preferred_bank'] . ' - ' . $bankData['bank_account']
        ];
        
        return $this->banking->create($data);
    }
    
    /**
     * Update bank account
     *
     * @param int $id
     * @param array $bankData
     * @return bool
     */
    public function updateBankAccount($id, array $bankData)
    {
        // Check if bank account exists
        if (!$this->banking->find($id)) {
            return false;
        }
        
        // Update bank account
        return $this->banking->update($id, $bankData);
    }
    
    /**
     * Delete bank account
     *
     * @param int $id
     * @return bool
     */
    public function deleteBankAccount($id)
    {
        // Check if bank account exists
        if (!$this->banking->find($id)) {
            return false;
        }
        
        // Delete bank account
        return $this->banking->delete($id);
    }
    
    /**
     * Get bank accounts for employee
     *
     * @param string $employeeId
     * @return array
     */
    public function getEmployeeBankAccounts($employeeId)
    {
        return $this->banking->getForEmployee($employeeId);
    }
    
    /**
     * Validate bank account format
     *
     * @param string $bankName
     * @param string $accountNumber
     * @return bool
     */
    public function validateBankAccount($bankName, $accountNumber)
    {
        // In a real implementation, this would validate based on bank-specific rules
        // For this example, we'll use simple validation
        
        $accountNumber = preg_replace('/\s+/', '', $accountNumber);
        
        switch (strtolower($bankName)) {
            case 'bdo':
            case 'bdo unibank':
                // BDO: 10-12 digits
                return preg_match('/^\d{10,12}$/', $accountNumber);
                
            case 'bpi':
            case 'bank of the philippine islands':
                // BPI: 10 digits
                return preg_match('/^\d{10}$/', $accountNumber);
                
            case 'metrobank':
                // Metrobank: 13 digits
                return preg_match('/^\d{13}$/', $accountNumber);
                
            case 'landbank':
            case 'land bank of the philippines':
                // Landbank: 10-12 digits
                return preg_match('/^\d{10,12}$/', $accountNumber);
                
            case 'rcbc':
            case 'rizal commercial banking corporation':
                // RCBC: 10 digits
                return preg_match('/^\d{10}$/', $accountNumber);
                
            default:
                // Generic validation: 10-20 digits
                return preg_match('/^\d{10,20}$/', $accountNumber);
        }
    }
    
    /**
     * Format bank account for display
     *
     * @param string $bankName
     * @param string $accountNumber
     * @return string
     */
    public function formatBankAccount($bankName, $accountNumber)
    {
        // In a real implementation, this would format based on bank-specific rules
        // For this example, we'll use simple formatting
        
        $accountNumber = preg_replace('/\s+/', '', $accountNumber);
        
        switch (strtolower($bankName)) {
            case 'bdo':
            case 'bdo unibank':
                // BDO: Format as XXX-XXXX-XXX
                if (strlen($accountNumber) >= 10) {
                    return substr($accountNumber, 0, 3) . '-' . 
                           substr($accountNumber, 3, 4) . '-' . 
                           substr($accountNumber, 7);
                }
                break;
                
            case 'bpi':
            case 'bank of the philippine islands':
                // BPI: Format as XXXX-XXXX-XX
                if (strlen($accountNumber) >= 10) {
                    return substr($accountNumber, 0, 4) . '-' . 
                           substr($accountNumber, 4, 4) . '-' . 
                           substr($accountNumber, 8);
                }
                break;
                
            case 'metrobank':
                // Metrobank: Format as XXX-XXXX-XXXXX
                if (strlen($accountNumber) >= 13) {
                    return substr($accountNumber, 0, 3) . '-' . 
                           substr($accountNumber, 3, 4) . '-' . 
                           substr($accountNumber, 7);
                }
                break;
        }
        
        // Default formatting
        if (strlen($accountNumber) >= 10) {
            return substr($accountNumber, 0, 4) . '-' . 
                   substr($accountNumber, 4, 4) . '-' . 
                   substr($accountNumber, 8);
        }
        
        return $accountNumber;
    }
}