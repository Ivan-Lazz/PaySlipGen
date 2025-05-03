<?php

namespace Database\Migrations;

use PDO;

class CreateBankingDetailsTable
{
    /**
     * Run the migration
     *
     * @param PDO $conn
     * @return void
     */
    public function up(PDO $conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS employee_banking_details (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employee_id VARCHAR(255) NOT NULL,
            preferred_bank VARCHAR(150) NOT NULL,
            bank_account VARCHAR(100) NOT NULL,
            bank_details VARCHAR(255) NOT NULL,
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
        $conn->exec("DROP TABLE IF EXISTS employee_banking_details");
    }
}