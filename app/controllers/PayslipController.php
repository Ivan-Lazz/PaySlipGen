<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Payslip;
use App\Models\Employee;
use App\Services\PayslipService;
use App\Requests\CreatePayslipRequest;
use App\Requests\UpdatePayslipRequest;

class PayslipController extends Controller
{
    protected $payslip;
    protected $employee;
    protected $payslipService;
    
    public function __construct()
    {
        parent::__construct();
        $this->payslip = new Payslip();
        $this->employee = new Employee();
        $this->payslipService = new PayslipService();
    }
    
    /**
     * Get a list of payslips
     *
     * @return \Core\Response
     */
    public function index()
    {
        $page = $this->request()->get('page', 1);
        $perPage = $this->request()->get('records_per_page', 10);
        $search = $this->request()->get('search');
        
        $result = $this->payslip->paginate(
            $page, 
            $perPage, 
            $search, 
            ['payslip_no', 'employee_id', 'person_in_charge', 'payment_status']
        );
        
        return $this->success($result);
    }
    
    /**
     * Get a single payslip
     *
     * @param string $id
     * @return \Core\Response
     */
    public function show($id)
    {
        $payslip = $this->payslip->find($id);
        
        if (!$payslip) {
            return $this->error('Payslip not found', 404);
        }
        
        // Get detailed payslip info
        $detailedPayslip = $this->payslipService->getDetailedPayslip($id);
        
        return $this->success($detailedPayslip);
    }
    
    /**
     * Create a new payslip
     *
     * @return \Core\Response
     */
    public function store()
    {
        $data = $this->getInput();
        
        // Validate input
        $errors = $this->validate($data, CreatePayslipRequest::rules());
        
        if ($errors) {
            return $this->error('Validation failed', 400, $errors);
        }
        
        // Check if employee exists
        if (!$this->employee->find($data['employee_id'])) {
            return $this->error('Employee not found', 404);
        }
        
        // Generate payslip number
        $data['payslip_no'] = $this->payslipService->generatePayslipNumber();
        
        // Create payslip
        $result = $this->payslip->create($data);
        
        if (!$result) {
            return $this->error('Failed to create payslip', 500);
        }
        
        return $this->success(['payslip_no' => $data['payslip_no']], 'Payslip created successfully', 201);
    }
    
    /**
     * Update a payslip
     *
     * @param string $id
     * @return \Core\Response
     */
    public function update($id)
    {
        $data = $this->getInput();
        
        // Validate input
        $errors = $this->validate($data, UpdatePayslipRequest::rules());
        
        if ($errors) {
            return $this->error('Validation failed', 400, $errors);
        }
        
        // Check if payslip exists
        if (!$this->payslip->find($id)) {
            return $this->error('Payslip not found', 404);
        }
        
        // Update payslip
        $result = $this->payslip->update($id, $data);
        
        if (!$result) {
            return $this->error('Failed to update payslip', 500);
        }
        
        return $this->success([], 'Payslip updated successfully');
    }
    
    /**
     * Delete a payslip
     *
     * @param string $id
     * @return \Core\Response
     */
    public function destroy($id)
    {
        // Check if payslip exists
        if (!$this->payslip->find($id)) {
            return $this->error('Payslip not found', 404);
        }
        
        // Delete payslip
        $result = $this->payslip->delete($id);
        
        if (!$result) {
            return $this->error('Failed to delete payslip', 500);
        }
        
        return $this->success([], 'Payslip deleted successfully');
    }
}