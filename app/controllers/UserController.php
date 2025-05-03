<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Requests\CreateUserRequest;
use App\Requests\UpdateUserRequest;
use Core\Security;

class UserController extends Controller
{
    protected $user;
    
    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }
    
    /**
     * Display a listing of users
     * 
     * @return \Core\Response
     */
    public function index()
    {
        $page = $this->request()->get('page', 1);
        $perPage = $this->request()->get('records_per_page', 10);
        $search = $this->request()->get('search');
        
        $result = $this->user->paginate(
            $page, 
            $perPage, 
            $search, 
            ['firstname', 'lastname', 'username']
        );
        
        return $this->success($result);
    }
    
    /**
     * Display a specific user
     * 
     * @param int $id User ID
     * @return \Core\Response
     */
    public function show($id)
    {
        $user = $this->user->find($id);
        
        if (!$user) {
            return $this->error('User not found', 404);
        }
        
        return $this->success($user);
    }
    
    /**
     * Create a new user
     * 
     * @return \Core\Response
     */
    public function store()
    {
        $data = $this->getInput();
        
        // Validate request data
        $errors = $this->validate($data, CreateUserRequest::rules());
        
        if ($errors) {
            return $this->error('Validation failed', 400, $errors);
        }
        
        // Check if username already exists
        if ($this->user->usernameExists($data['username'])) {
            return $this->error('Username is already taken', 400);
        }
        
        // Create user
        $result = $this->user->create($data);
        
        if (!$result) {
            return $this->error('Failed to create user', 500);
        }
        
        return $this->success([], 'User created successfully', 201);
    }
    
    /**
     * Update an existing user
     * 
     * @param int $id User ID
     * @return \Core\Response
     */
    public function update($id)
    {
        $data = $this->getInput();
        
        // Validate request data
        $errors = $this->validate($data, UpdateUserRequest::rules());
        
        if ($errors) {
            return $this->error('Validation failed', 400, $errors);
        }
        
        // Check if user exists
        if (!$this->user->find($id)) {
            return $this->error('User not found', 404);
        }
        
        // Check if username is taken (by a different user)
        if (isset($data['username'])) {
            $existingUser = $this->user->findByUsername($data['username']);
            if ($existingUser && $existingUser['id'] != $id) {
                return $this->error('Username is already taken', 400);
            }
        }
        
        // Update user
        $result = $this->user->update($id, $data);
        
        if (!$result) {
            return $this->error('Failed to update user', 500);
        }
        
        return $this->success([], 'User updated successfully');
    }
    
    /**
     * Delete a user
     * 
     * @param int $id User ID
     * @return \Core\Response
     */
    public function destroy($id)
    {
        // Check if user exists
        if (!$this->user->find($id)) {
            return $this->error('User not found', 404);
        }
        
        // Delete user
        $result = $this->user->delete($id);
        
        if (!$result) {
            return $this->error('Failed to delete user', 500);
        }
        
        return $this->success([], 'User deleted successfully');
    }
    
    /**
     * Change user password
     * 
     * @param int $id User ID
     * @return \Core\Response
     */
    public function changePassword($id)
    {
        $data = $this->getInput();
        
        // Validate request data
        if (empty($data['current_password']) || empty($data['new_password'])) {
            return $this->error('Current password and new password are required', 400);
        }
        
        // Check if user exists
        $user = $this->user->find($id);
        if (!$user) {
            return $this->error('User not found', 404);
        }
        
        // Verify current password
        $existingUser = $this->user->findByUsername($user['username']);
        if (!Security::verifyPassword($data['current_password'], $existingUser['password'])) {
            return $this->error('Current password is incorrect', 400);
        }
        
        // New password cannot be the same as current
        if ($data['current_password'] === $data['new_password']) {
            return $this->error('New password cannot be the same as current password', 400);
        }
        
        // Update password
        $result = $this->user->update($id, ['password' => $data['new_password']]);
        
        if (!$result) {
            return $this->error('Failed to change password', 500);
        }
        
        return $this->success([], 'Password changed successfully');
    }
}