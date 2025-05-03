<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Requests\CreateEmployeeRequest;
use App\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    protected $employee;
    protected $employeeService;
    
    public function __construct()
    {
        parent::__construct();
        $this->employee = new Employee();
        $this->employeeService = new EmployeeService();
    }
    
    /**
     * Get a list of employees
     *
     * @return \Core\Response
     */
    public function index()
    {
        $page = $this->request()->get('page', 1);
        $perPage = $this->request()->get('records_per_page', 10);
        $search = $this->request()->get('search');
        
        $result = $this->employee->paginate(
            $page, 
            $perPage, 
            $search, 
            ['employee_id', 'firstname', 'lastname', 'email']
        );
        
        return $this->success($result);
    }
    
    /**
     * Get a single employee
     *
     * @param string $id
     * @return \Core\Response
     */
    public function show($id)
    {
        $employee = $this->employee->find($id);
        
        if (!$employee) {
            return $this->error('Employee not found', 404);
        }
        
        return $this->success($employee);
    }
    
    /**
     * Create a new employee
     *
     * @return \Core\Response
     */
    public function store()
    {
        $data = $this->getInput();
        
        // Validate input
        $errors = $this->validate($data, CreateEmployeeRequest::rules());
        
        if ($errors) {
            return $this->error('Validation failed', 400, $errors);
        }
        
        // Generate employee ID if not provided
        if (empty($data['employee_id'])) {
            $data['employee_id'] = $this->employeeService->generateEmployeeId();
        }
        
        // Create employee
        $result = $this->employee->create($data);
        
        if (!$result) {
            return $this->error('Failed to create employee', 500);
        }
        
        return $this->success(['employee_id' => $data['employee_id']], 'Employee created successfully', 201);
    }
    
    /**
     * Update an employee
     *
     * @param string $id
     * @return \Core\Response
     */
    public function update($id)
    {
        $data = $this->getInput();
        
        // Validate input
        $errors = $this->validate($data, UpdateEmployeeRequest::rules());
        
        if ($errors) {
            return $this->error('Validation failed', 400, $errors);
        }
        
        // Check if employee exists
        if (!$this->employee->find($id)) {
            return $this->error('Employee not found', 404);
        }
        
        // Update employee
        $result = $this->employee->update($id, $data);
        
        if (!$result) {
            return $this->error('Failed to update employee', 500);
        }
        
        return $this->success([], 'Employee updated successfully');
    }
    
    /**
     * Delete an employee
     *
     * @param string $id
     * @return \Core\Response
     */
    public function destroy($id)
    {
        // Check if employee exists
        if (!$this->employee->find($id)) {
            return $this->error('Employee not found', 404);
        }
        
        // Delete employee
        $result = $this->employee->delete($id);
        
        if (!$result) {
            return $this->error('Failed to delete employee', 500);
        }
        
        return $this->success([], 'Employee deleted successfully');
    }
}