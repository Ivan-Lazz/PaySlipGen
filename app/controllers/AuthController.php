<?php

namespace App\Controllers;

use Core\Controller;
use Core\Security;
use App\Models\User;
use App\Models\Account;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $user;
    protected $account;
    protected $authService;
    
    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
        $this->account = new Account();
        $this->authService = new AuthService();
    }
    
    /**
     * Authenticate user
     *
     * @return \Core\Response
     */
    public function login()
    {
        $data = $this->getInput();
        
        if (empty($data['email']) || empty($data['password'])) {
            return $this->error('Email and password are required', 400);
        }
        
        // Try to authenticate as admin user
        $user = $this->user->findByUsername($data['email']);
        
        if ($user && Security::verifyPassword($data['password'], $user['password'])) {
            $token = $this->authService->generateToken($user['id'], 'admin');
            
            return $this->success([
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'username' => $user['username'],
                    'type' => 'admin'
                ]
            ]);
        }
        
        // Try to authenticate as employee
        $account = $this->account->findByEmail($data['email']);
        
        if ($account && Security::verifyPassword($data['password'], $account['account_pass'])) {
            // Check if account is active
            if ($account['account_status'] !== 'ACTIVE') {
                return $this->error('Account is not active', 403);
            }
            
            $token = $this->authService->generateToken($account['account_id'], $account['account_type']);
            
            // Get employee details
            $employee = $this->authService->getEmployeeDetails($account['employee_id']);
            
            return $this->success([
                'token' => $token,
                'user' => [
                    'id' => $account['account_id'],
                    'employee_id' => $account['employee_id'],
                    'email' => $account['account_email'],
                    'type' => $account['account_type'],
                    'employee' => $employee
                ]
            ]);
        }
        
        return $this->error('Invalid credentials', 401);
    }
    
    /**
     * Verify token
     *
     * @return \Core\Response
     */
    public function verify()
    {
        $token = $this->request()->getHeader('Authorization');
        
        if (!$token) {
            return $this->error('Token is required', 400);
        }
        
        // Remove Bearer prefix if present
        $token = str_replace('Bearer ', '', $token);
        
        $payload = $this->authService->verifyToken($token);
        
        if (!$payload) {
            return $this->error('Invalid token', 401);
        }
        
        return $this->success(['valid' => true, 'payload' => $payload]);
    }
    
    /**
     * Logout
     *
     * @return \Core\Response
     */
    public function logout()
    {
        $token = $this->request()->getHeader('Authorization');
        
        if ($token) {
            // Invalidate token (add to blacklist in real implementation)
            $this->authService->invalidateToken($token);
        }
        
        return $this->success([], 'Logged out successfully');
    }
}