<?php

namespace App\Models;

use Core\Model;
use Core\Security;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'password'
    ];
    
    protected $hidden = ['password'];
    
    /**
     * Create a new user
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data)
    {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }
        
        return parent::create($data);
    }
    
    /**
     * Update a user
     *
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        // Hash password if it's being updated
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        } else {
            // Don't update password if not provided
            unset($data['password']);
        }
        
        return parent::update($id, $data);
    }
    
    /**
     * Find a user by username
     *
     * @param string $username
     * @return array|null
     */
    public function findByUsername($username)
    {
        $query = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if user exists by username
     *
     * @param string $username
     * @return bool
     */
    public function usernameExists($username)
    {
        $query = "SELECT id FROM {$this->table} WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Authenticate a user
     *
     * @param string $username
     * @param string $password
     * @return array|bool
     */
    public function authenticate($username, $password)
    {
        $user = $this->findByUsername($username);
        
        if (!$user) {
            return false;
        }
        
        if (Security::verifyPassword($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
}