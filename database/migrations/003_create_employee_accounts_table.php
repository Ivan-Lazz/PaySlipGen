<?php

namespace Database\Migrations;

use PDO;

class CreateEmployeeAccountsTable
{
    /**
     * Run the migration
     *
     * @param PDO $conn
     * @return void
     */
    public function up(PDO $conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS employee_account (
            id INT AUTO_INCREMENT,
            account_id VARCHAR(255) PRIMARY KEY,
            employee_id VARCHAR(255) NOT NULL,
            account_email VARCHAR(150) NOT NULL UNIQUE,
            account_pass VARCHAR(255) NOT NULL,
            account_type VARCHAR(50) NOT NULL,
            account_status VARCHAR(50) NOT NULL DEFAULT 'ACTIVE',
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
        $conn->exec("DROP TABLE IF EXISTS employee_account");
    }
}