<?php

namespace Database\Migrations;

use PDO;

class CreateEmployeesTable
{
    /**
     * Run the migration
     *
     * @param PDO $conn
     * @return void
     */
    public function up(PDO $conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS employees (
            employee_id VARCHAR(255) PRIMARY KEY,
            firstname VARCHAR(100) NOT NULL,
            lastname VARCHAR(100) NOT NULL,
            contact_number VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
        $conn->exec("DROP TABLE IF EXISTS employees");
    }
}