<?php

namespace App\Models;

use Core\Model;
use Core\Security;

class Account extends Model
{
    protected $table = 'employee_account';
    protected $primaryKey = 'account_id';
    
    protected $fillable = [
        'account_id',
        'employee_id',
        'account_email',
        'account_pass',
        'account_type',
        'account_status'
    ];
    
    protected $hidden = ['account_pass'];
    
    /**
     * Create a new account
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data)
    {
        // Hash password
        if (isset($data['account_pass'])) {
            $data['account_pass'] = Security::hashPassword($data['account_pass']);
        }
        
        return parent::create($data);
    }
    
    /**
     * Update an account
     *
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        // Hash password if it's being updated
        if (isset($data['account_pass']) && !empty($data['account_pass'])) {
            $data['account_pass'] = Security::hashPassword($data['account_pass']);
        } else {
            // Don't update password if not provided
            unset($data['account_pass']);
        }
        
        return parent::update($id, $data);
    }
    
    /**
     * Find account by email
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email)
    {
        $query = "SELECT * FROM {$this->table} WHERE account_email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get accounts by type
     *
     * @param string $type
     * @return array
     */
    public function getByType($type)
    {
        $query = "SELECT * FROM {$this->table} WHERE account_type = :type";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        
        return array_map(function($item) {
            return $this->filterHidden($item);
        }, $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }
    
    /**
     * Update account status
     *
     * @param string $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status)
    {
        $query = "UPDATE {$this->table} SET account_status = :status WHERE account_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}