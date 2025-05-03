<?php

namespace Database\Migrations;

use PDO;

class CreatePayslipsTable
{
    /**
     * Run the migration
     *
     * @param PDO $conn
     * @return void
     */
    public function up(PDO $conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS payslip (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            payslip_no VARCHAR(255) UNIQUE NOT NULL,
            employee_id VARCHAR(255) NOT NULL,
            bank_acct VARCHAR(100) NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            person_in_charge VARCHAR(100) NOT NULL,
            cutoff_date DATE NOT NULL,
            date_of_payment DATE NOT NULL,
            payment_status VARCHAR(100) NOT NULL DEFAULT 'PENDING',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $conn->exec($sql);
    }
    
    /**
     * Reverse the migration
     *
     * @param PDO $conn
     * @return void
     */
    public function down(PDO $conn)
    {
        $conn->exec("DROP TABLE IF EXISTS payslip");
    }
}