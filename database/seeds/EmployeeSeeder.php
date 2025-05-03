<?php

namespace Database\Seeds;

use App\Models\Employee;
use App\Models\Account;
use App\Models\Banking;
use App\Utils\IDGenerator;
use Core\Security;
use PDO;

class EmployeeSeeder
{
    /**
     * Run the database seed
     *
     * @param PDO $conn
     * @return void
     */
    public function run(PDO $conn)
    {
        $employee = new Employee($conn);
        $account = new Account($conn);
        $banking = new Banking($conn);
        
        // Create sample employees
        $employees = [
            [
                'firstname' => 'Mark',
                'lastname' => 'Wilson',
                'contact_number' => '09123456789',
                'email' => 'mark.wilson@example.com',
                'account_type' => 'MANAGER',
                'preferred_bank' => 'BDO',
                'bank_account' => '1234567890'
            ],
            [
                'firstname' => 'Sarah',
                'lastname' => 'Johnson',
                'contact_number' => '09234567890',
                'email' => 'sarah.johnson@example.com',
                'account_type' => 'EMPLOYEE',
                'preferred_bank' => 'BPI',
                'bank_account' => '0987654321'
            ],
            [
                'firstname' => 'Michael',
                'lastname' => 'Brown',
                'contact_number' => '09345678901',
                'email' => 'michael.brown@example.com',
                'account_type' => 'EMPLOYEE',
                'preferred_bank' => 'Metrobank',
                'bank_account' => '5678901234'
            ],
            [
                'firstname' => 'Emily',
                'lastname' => 'Davis',
                'contact_number' => '09456789012',
                'email' => 'emily.davis@example.com',
                'account_type' => 'EMPLOYEE',
                'preferred_bank' => 'Landbank',
                'bank_account' => '6789012345'
            ],
            [
                'firstname' => 'David',
                'lastname' => 'Martinez',
                'contact_number' => '09567890123',
                'email' => 'david.martinez@example.com',
                'account_type' => 'EMPLOYEE',
                'preferred_bank' => 'RCBC',
                'bank_account' => '7890123456'
            ]
        ];
        
        $createdCount = 0;
        
        foreach ($employees as $employeeData) {
            // Check if employee email already exists
            $stmt = $conn->prepare("SELECT employee_id FROM employees WHERE email = :email");
            $stmt->bindParam(':email', $employeeData['email']);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                continue;
            }
            
            // Generate employee ID
            $employeeId = IDGenerator::generateEmployeeID();
            
            // Create employee
            $employee->create([
                'employee_id' => $employeeId,
                'firstname' => $employeeData['firstname'],
                'lastname' => $employeeData['lastname'],
                'contact_number' => $employeeData['contact_number'],
                'email' => $employeeData['email']
            ]);
            
            // Create account
            $accountId = IDGenerator::generateAccountID();
            $account->create([
                'account_id' => $accountId,
                'employee_id' => $employeeId,
                'account_email' => $employeeData['email'],
                'account_pass' => 'password123', // This will be hashed by the model
                'account_type' => $employeeData['account_type'],
                'account_status' => 'ACTIVE'
            ]);
            
            // Create banking details
            $banking->create([
                'employee_id' => $employeeId,
                'preferred_bank' => $employeeData['preferred_bank'],
                'bank_account' => $employeeData['bank_account'],
                'bank_details' => $employeeData['preferred_bank'] . ' - ' . $employeeData['bank_account']
            ]);
            
            $createdCount++;
        }
        
        echo "{$createdCount} sample employees created successfully.\n";
    }
}