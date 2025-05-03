# Payroll System API Documentation

## Overview

This document provides documentation for the Payroll System API. The API follows RESTful principles and uses JSON for data exchange.

## Base URL
http://your-domain.com/api

## Authentication

All API endpoints require authentication. Use Bearer token authentication by including the token in the Authorization header:
Authorization: Bearer {your_token}

To obtain a token, use the login endpoint.

## Endpoints

### Authentication

#### Login
POST /login

Request body:

```json
{
  "email": "user@example.com",
  "password": "password123"
}
Response:
json{
  "status": "success",
  "message": "Login successful",
  "data": {
    "token": "your_jwt_token",
    "user": {
      "id": "user_id",
      "email": "user@example.com",
      "type": "ADMIN"
    }
  }
}
Logout
POST /logout
Response:
json{
  "status": "success",
  "message": "Logged out successfully"
}
Users
Get All Users
GET /users
Query parameters:

page: Page number (default: 1)
records_per_page: Records per page (default: 10)
search: Search term

Response:
json{
  "status": "success",
  "message": "Users retrieved successfully",
  "data": {
    "current_page": 1,
    "records_per_page": 10,
    "total_records": 100,
    "total_pages": 10,
    "data": [
      {
        "id": "user_id",
        "firstname": "John",
        "lastname": "Doe",
        "username": "johndoe"
      }
    ]
  }
}
Get Single User
GET /users/{id}
Response:
json{
  "status": "success",
  "message": "User retrieved successfully",
  "data": {
    "id": "user_id",
    "firstname": "John",
    "lastname": "Doe",
    "username": "johndoe"
  }
}
Create User
POST /users
Request body:
json{
  "firstname": "John",
  "lastname": "Doe",
  "username": "johndoe",
  "password": "password123"
}
Response:
json{
  "status": "success",
  "message": "User created successfully"
}
Update User
PUT /users/{id}
Request body:
json{
  "firstname": "John",
  "lastname": "Doe",
  "username": "johndoe",
  "password": "newpassword123"
}
Response:
json{
  "status": "success",
  "message": "User updated successfully"
}
Delete User
DELETE /users/{id}
Response:
json{
  "status": "success",
  "message": "User deleted successfully"
}
Employees
Get All Employees
GET /employees
Query parameters:

page: Page number (default: 1)
records_per_page: Records per page (default: 10)
search: Search term

Response:
json{
  "status": "success",
  "message": "Employees retrieved successfully",
  "data": {
    "current_page": 1,
    "records_per_page": 10,
    "total_records": 100,
    "total_pages": 10,
    "data": [
      {
        "employee_id": "employee_id",
        "firstname": "John",
        "lastname": "Doe",
        "contact_number": "1234567890",
        "email": "john.doe@example.com"
      }
    ]
  }
}
Get Single Employee
GET /employees/{id}
Response:
json{
  "status": "success",
  "message": "Employee retrieved successfully",
  "data": {
    "employee_id": "employee_id",
    "firstname": "John",
    "lastname": "Doe",
    "contact_number": "1234567890",
    "email": "john.doe@example.com",
    "banking": [
      {
        "id": 1,
        "preferred_bank": "BDO",
        "bank_account": "1234567890",
        "bank_details": "BDO - 1234567890"
      }
    ],
    "account": {
      "account_id": "account_id",
      "account_email": "john.doe@example.com",
      "account_type": "EMPLOYEE",
      "account_status": "ACTIVE"
    }
  }
}
Create Employee
POST /employees
Request body:
json{
  "firstname": "John",
  "lastname": "Doe",
  "contact_number": "1234567890",
  "email": "john.doe@example.com"
}
Response:
json{
  "status": "success",
  "message": "Employee created successfully",
  "data": {
    "employee_id": "generated_employee_id"
  }
}
Update Employee
PUT /employees/{id}
Request body:
json{
  "firstname": "John",
  "lastname": "Doe",
  "contact_number": "1234567890",
  "email": "john.doe@example.com"
}
Response:
json{
  "status": "success",
  "message": "Employee updated successfully"
}
Delete Employee
DELETE /employees/{id}
Response:
json{
  "status": "success",
  "message": "Employee deleted successfully"
}
Accounts
Get All Accounts
GET /accounts
Query parameters:

page: Page number (default: 1)
records_per_page: Records per page (default: 10)
search: Search term
type: Account type filter (ADMIN, MANAGER, EMPLOYEE)

Response:
json{
  "status": "success",
  "message": "Accounts retrieved successfully",
  "data": {
    "current_page": 1,
    "records_per_page": 10,
    "total_records": 100,
    "total_pages": 10,
    "data": [
      {
        "account_id": "account_id",
        "employee_id": "employee_id",
        "account_email": "john.doe@example.com",
        "account_type": "EMPLOYEE",
        "account_status": "ACTIVE"
      }
    ]
  }
}
Get Single Account
GET /accounts/{id}
Response:
json{
  "status": "success",
  "message": "Account retrieved successfully",
  "data": {
    "account_id": "account_id",
    "employee_id": "employee_id",
    "account_email": "john.doe@example.com",
    "account_type": "EMPLOYEE",
    "account_status": "ACTIVE",
    "employee": {
      "employee_id": "employee_id",
      "firstname": "John",
      "lastname": "Doe",
      "contact_number": "1234567890",
      "email": "john.doe@example.com"
    }
  }
}
Create Account
POST /accounts
Request body:
json{
  "employee_id": "employee_id",
  "account_email": "john.doe@example.com",
  "account_pass": "password123",
  "account_type": "EMPLOYEE",
  "account_status": "ACTIVE"
}
Response:
json{
  "status": "success",
  "message": "Account created successfully",
  "data": {
    "account_id": "generated_account_id"
  }
}
Update Account
PUT /accounts/{id}
Request body:
json{
  "account_email": "john.doe@example.com",
  "account_pass": "newpassword123",
  "account_type": "EMPLOYEE",
  "account_status": "ACTIVE"
}
Response:
json{
  "status": "success",
  "message": "Account updated successfully"
}
Delete Account
DELETE /accounts/{id}
Response:
json{
  "status": "success",
  "message": "Account deleted successfully"
}
Payslips
Get All Payslips
GET /payslips
Query parameters:

page: Page number (default: 1)
records_per_page: Records per page (default: 10)
search: Search term

Response:
json{
  "status": "success",
  "message": "Payslips retrieved successfully",
  "data": {
    "current_page": 1,
    "records_per_page": 10,
    "total_records": 100,
    "total_pages": 10,
    "data": [
      {
        "payslip_no": "payslip_no",
        "employee_id": "employee_id",
        "bank_acct": "bank_account",
        "amount": 5000,
        "person_in_charge": "Person Name",
        "cutoff_date": "2023-01-15",
        "date_of_payment": "2023-01-31",
        "payment_status": "PAID"
      }
    ]
  }
}
Get Single Payslip
GET /payslips/{id}
Response:
json{
  "status": "success",
  "message": "Payslip retrieved successfully",
  "data": {
    "payslip_no": "payslip_no",
    "employee_id": "employee_id",
    "firstname": "John",
    "lastname": "Doe",
    "bank_acct": "bank_account",
    "preferred_bank": "BDO",
    "bank_details": "BDO - 1234567890",
    "amount": 5000,
    "person_in_charge": "Person Name",
    "cutoff_date": "2023-01-15",
    "date_of_payment": "2023-01-31",
    "payment_status": "PAID"
  }
}
Create Payslip
POST /payslips
Request body:
json{
  "employee_id": "employee_id",
  "bank_acct": "bank_account",
  "amount": 5000,
  "person_in_charge": "Person Name",
  "cutoff_date": "2023-01-15",
  "date_of_payment": "2023-01-31",
  "payment_status": "PENDING"
}
Response:
json{
  "status": "success",
  "message": "Payslip created successfully",
  "data": {
    "payslip_no": "generated_payslip_no"
  }
}
Update Payslip
PUT /payslips/{id}
Request body:
json{
  "bank_acct": "bank_account",
  "amount": 5000,
  "person_in_charge": "Person Name",
  "cutoff_date": "2023-01-15",
  "date_of_payment": "2023-01-31",
  "payment_status": "PAID"
}
Response:
json{
  "status": "success",
  "message": "Payslip updated successfully"
}
Delete Payslip
DELETE /payslips/{id}
Response:
json{
  "status": "success",
  "message": "Payslip deleted successfully"
}
Reports
Payroll Report
GET /reports/payroll
Query parameters:

start_date: Start date (required)
end_date: End date (required)

Response:
json{
  "status": "success",
  "message": "Report generated successfully",
  "data": {
    "payslips": [...],
    "summary": {
      "total_amount": 50000,
      "paid_amount": 40000,
      "pending_amount": 10000,
      "total_count": 10,
      "start_date": "2023-01-01",
      "end_date": "2023-01-31"
    }
  }
}
Employee Report
GET /reports/employee/{id}
Response:
json{
  "status": "success",
  "message": "Report generated successfully",
  "data": {
    "employee": {...},
    "payslips": [...],
    "summary": {
      "total_payslips": 10,
      "total_paid": 40000,
      "total_pending": 10000,
      "latest_payment": "2023-01-31"
    }
  }
}
Error Responses
All error responses follow this format:
json{
  "status": "error",
  "message": "Error message",
  "errors": {
    "field": "Field-specific error message"
  }
}
Common HTTP status codes:

200: Success
201: Created
400: Bad Request
401: Unauthorized
403: Forbidden
404: Not Found
422: Validation Error
500: Server Error


## Installation Script

### `install.php`
```php
<?php

// Define constants
define('APP_NAME', 'Payroll System');
define('APP_VERSION', '1.0.0');
define('APP_PATH', dirname(__FILE__));

// Show header
echo "========================================\n";
echo APP_NAME . " Installer (v" . APP_VERSION . ")\n";
echo "========================================\n\n";

// Check PHP version
echo "Checking PHP version... ";
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    echo "FAILED\n";
    echo "PHP 7.4.0 or higher is required. Your PHP version is " . PHP_VERSION . "\n";
    exit(1);
}
echo "OK (PHP " . PHP_VERSION . ")\n";

// Check extensions
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
echo "Checking required extensions... ";
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        echo "FAILED\n";
        echo "The extension '$ext' is required but not installed.\n";
        exit(1);
    }
}
echo "OK\n";

// Check if .env file exists
echo "Checking .env file... ";
if (!file_exists(APP_PATH . '/.env')) {
    echo "CREATING\n";
    
    // Create .env file from .env.example
    if (file_exists(APP_PATH . '/.env.example')) {
        copy(APP_PATH . '/.env.example', APP_PATH . '/.env');
        echo "Created .env file from .env.example\n";
    } else {
        // Create a basic .env file
        $env_content = "APP_ENV=development\n";
        $env_content .= "APP_DEBUG=true\n";
        $env_content .= "APP_KEY=" . bin2hex(random_bytes(32)) . "\n\n";
        $env_content .= "DB_HOST=localhost\n";
        $env_content .= "DB_NAME=bm_payroll\n";
        $env_content .= "DB_USER=root\n";
        $env_content .= "DB_PASS=\n\n";
        $env_content .= "LOG_CHANNEL=file\n";
        $env_content .= "LOG_LEVEL=debug\n";
        
        file_put_contents(APP_PATH . '/.env', $env_content);
        echo "Created basic .env file\n";
    }
} else {
    echo "OK\n";
}

// Check and create storage directories
echo "Setting up storage directories... ";
$storage_dirs = [
    '/logs',
    '/storage/rate_limits',
    '/storage/cache',
    '/storage/uploads'
];

foreach ($storage_dirs as $dir) {
    $path = APP_PATH . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}
echo "OK\n";

// Install dependencies
echo "Installing dependencies...\n";
if (!file_exists(APP_PATH . '/vendor')) {
    echo "Running composer install...\n";
    exec('cd ' . APP_PATH . ' && composer install', $output, $return_var);
    
    if ($return_var !== 0) {
        echo "FAILED\n";
        echo "Failed to install dependencies. Please run 'composer install' manually.\n";
    } else {
        echo "Dependencies installed successfully.\n";
    }
} else {
    echo "Vendor directory already exists. Skipping composer install.\n";
}

// Setup database
echo "\nDatabase setup\n";
echo "-------------\n";

// Get database configuration
$db_host = readline("Database host [localhost]: ");
$db_host = $db_host ?: 'localhost';

$db_name = readline("Database name [bm_payroll]: ");
$db_name = $db_name ?: 'bm_payroll';

$db_user = readline("Database username [root]: ");
$db_user = $db_user ?: 'root';

$db_pass = readline("Database password []: ");

// Update .env file with database configuration
$env_content = file_get_contents(APP_PATH . '/.env');
$env_content = preg_replace('/DB_HOST=.*/', "DB_HOST=$db_host", $env_content);
$env_content = preg_replace('/DB_NAME=.*/', "DB_NAME=$db_name", $env_content);
$env_content = preg_replace('/DB_USER=.*/', "DB_USER=$db_user", $env_content);
$env_content = preg_replace('/DB_PASS=.*/', "DB_PASS=$db_pass", $env_content);
file_put_contents(APP_PATH . '/.env', $env_content);

// Test database connection
echo "Testing database connection... ";
try {
    $dsn = "mysql:host=$db_host";
    $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new \PDO($dsn, $db_user, $db_pass, $options);
    echo "OK\n";
    
    // Check if database exists, create if it doesn't
    echo "Checking database... ";
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");
    $db_exists = $stmt->fetchColumn();
    
    if (!$db_exists) {
        echo "CREATING\n";
        $pdo->exec("CREATE DATABASE `$db_name`");
        echo "Database '$db_name' created successfully.\n";
    } else {
        echo "OK\n";
    }
    
    // Select the database
    $pdo->exec("USE `$db_name`");
    
    // Run migrations
    echo "Running database migrations... ";
    require_once APP_PATH . '/vendor/autoload.php';
    require_once APP_PATH . '/helpers/app_helpers.php';
    require_once APP_PATH . '/database/migrator.php';
    echo "OK\n";
    
    // Run seeders
    echo "Seeding database... ";
    require_once APP_PATH . '/database/seeder.php';
    echo "OK\n";
    
} catch (\PDOException $e) {
    echo "FAILED\n";
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Set directory permissions
echo "\nSetting directory permissions... ";
chmod(APP_PATH . '/logs', 0777);
chmod(APP_PATH . '/storage', 0777);
chmod(APP_PATH . '/storage/rate_limits', 0777);
chmod(APP_PATH . '/storage/cache', 0777);
chmod(APP_PATH . '/storage/uploads', 0777);
echo "OK\n";

// Installation complete
echo "\n========================================\n";
echo APP_NAME . " installation completed successfully!\n";
echo "========================================\n\n";

echo "You can now access the application by navigating to:\n";
echo "http://localhost/path-to-installation/public/\n\n";

echo "Default login credentials:\n";
echo "Email: admin@example.com\n";
echo "Password: admin123\n\n";

echo "Don't forget to change the default credentials!\n\n";

echo "Thank you for installing " . APP_NAME . "!\n";