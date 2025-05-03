<?php

namespace App\Services;

use Core\Security;
use App\Models\User;
use App\Models\Account;
use App\Models\Employee;

class AuthService
{
    protected $user;
    protected $account;
    protected $employee;
    protected $tokenBlacklist = [];
    
    public function __construct()
    {
        $this->user = new User();
        $this->account = new Account();
        $this->employee = new Employee();
    }
    
    /**
     * Generate JWT token
     *
     * @param string $userId
     * @param string $userType
     * @return string
     */
    public function generateToken($userId, $userType)
    {
        $payload = [
            'sub' => $userId,
            'type' => $userType,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // 24 hours
        ];
        
        // This is a simplified JWT implementation
        // In production, use a proper JWT library
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$header.$payload", config('app.key'));
        
        return "$header.$payload.$signature";
    }
    
    /**
     * Verify JWT token
     *
     * @param string $token
     * @return array|bool
     */
    public function verifyToken($token)
    {
        // Check if token is blacklisted
        if (in_array($token, $this->tokenBlacklist)) {
            return false;
        }
        
        // Split token
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        list($header, $payload, $signature) = $parts;
        
        // Verify signature
        $expectedSignature = hash_hmac('sha256', "$header.$payload", config('app.key'));
        
        if (!hash_equals($expectedSignature, $signature)) {
            return false;
        }
        
        // Decode payload
        $payload = json_decode(base64_decode($payload), true);
        
        // Check if token is expired
        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
    
    /**
     * Invalidate token
     *
     * @param string $token
     * @return void
     */
    public function invalidateToken($token)
    {
        // Remove Bearer prefix if present
        $token = str_replace('Bearer ', '', $token);
        
        // Add to blacklist
        $this->tokenBlacklist[] = $token;
        
        // In a real implementation, this would be stored in a database or cache
    }
    
    /**
     * Get employee details
     *
     * @param string $employeeId
     * @return array|null
     */
    public function getEmployeeDetails($employeeId)
    {
        return $this->employee->getWithDetails($employeeId);
    }
}