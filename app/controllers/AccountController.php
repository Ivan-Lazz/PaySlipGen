<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Account;
use App\Models\Employee;
use App\Services\AuthService;
use App\Requests\CreateAccountRequest;
use App\Requests\UpdateAccountRequest;
use App\Utils\IDGenerator;

class AccountController extends Controller
{
    protected $account;
    protected $employee;
    
    public function __construct()
    {
        parent::__construct();
        $this->account = new Account();
        $this->employee = new Employee();
    }
    
    /**
     * Get a list of accounts
     *
     * @return \Core\Response
     */
    public function index()
    {
        $page = $this->request()->get('page', 1);
        $perPage = $this->request()->get('records_per_page', 10);
        $search = $this->request()->get('search');
        $type = $this->request()->get('type');
        
        $result = $this->account->paginate(
            $page, 
            $perPage, 
            $search, 
            ['account_id', 'account_email', 'account_type']
        );
        
        if (!empty($type)) {
            // Filter by account type if provided
            $filtered = [];
            foreach ($result['data'] as $account) {
                if ($account['account_type'] === $type) {
                    $filtered[] = $account;
                }
            }
            $result['data'] = $filtered;
            $result['total'] = count($filtered);
            $result['last_page'] = ceil($result['total'] / $perPage);
        }
        
        return $this->success($result);
    }
    
    /**
     * Get a single account
     *
     * @param string $id
     * @return \Core\Response
     */
    public function show($id)
    {
        $account = $this->account->find($id);
        
        if (!$account) {
            return $this->error('Account not found', 404);
        }
        
        // Get employee details
        $employee = $this->employee->find($account['employee_id']);
        $account['employee'] = $employee;
        
        return $this->success($account);
    }
    
    /**
     * Create a new account
     *
     * @return \Core\Response
     */
    public function store()
    {
        $data = $this->getInput();
        
        // Validate input
        $errors = $this->validate($data, CreateAccountRequest::rules());
        
        if ($errors) {
            return $this->error('Validation failed', 400, $errors);
        }
        
        // Check if employee exists
        if (!$this->employee->find($data['employee_id'])) {
            return $this->error('Employee not found', 404);
        }
        
        // Check if email is already used
        if ($this->account->findByEmail($data['account_email'])) {
            return $this->error('Email already in use', 400);
        }
        
        // Generate account ID
        $data['account_id'] = IDGenerator::generateAccountID();
        
        // Set default status if not provided
        if (!isset($data['account_status'])) {
            $data['account_status'] = 'ACTIVE';
        }
        
        // Create account
        $result = $this->account->create($data);
        
        if (!$result) {
            return $this->error('Failed to create account', 500);
        }
        
        return $this->success(['account_id' => $data['account_id']], 'Account created successfully', 201);
    }
    
    /**
     * Update an account
     *
     * @param string $id
     * @return \Core\Response
     */
    public function update($id)
    {
        $data = $this->getInput();
        
        // Validate input
        $errors = $this->validate($data, UpdateAccountRequest::rules());
        
        if ($errors) {
            return $this->error('Validation failed', 400, $errors);
        }
        
        // Check if account exists
        if (!$this->account->find($id)) {
            return $this->error('Account not found', 404);
        }
        
        // Check if email is already used by another account
        $existingAccount = $this->account->findByEmail($data['account_email']);
        if ($existingAccount && $existingAccount['account_id'] !== $id) {
            return $this->error('Email already in use', 400);
        }
        
        // Update account
        $result = $this->account->update($id, $data);
        
        if (!$result) {
            return $this->error('Failed to update account', 500);
        }
        
        return $this->success([], 'Account updated successfully');
    }
    
    /**
     * Delete an account
     *
     * @param string $id
     * @return \Core\Response
     */
    public function destroy($id)
    {
        // Check if account exists
        if (!$this->account->find($id)) {
            return $this->error('Account not found', 404);
        }
        
        // Delete account
        $result = $this->account->delete($id);
        
        if (!$result) {
            return $this->error('Failed to delete account', 500);
        }
        
        return $this->success([], 'Account deleted successfully');
    }
    
    /**
     * Update account status
     *
     * @param string $id
     * @return \Core\Response
     */
    public function updateStatus($id)
    {
        $data = $this->getInput();
        
        if (empty($data['status'])) {
            return $this->error('Status is required', 400);
        }
        
        // Check if account exists
        if (!$this->account->find($id)) {
            return $this->error('Account not found', 404);
        }
        
        // Update status
        $result = $this->account->updateStatus($id, $data['status']);
        
        if (!$result) {
            return $this->error('Failed to update account status', 500);
        }
        
        return $this->success([], 'Account status updated successfully');
    }
}