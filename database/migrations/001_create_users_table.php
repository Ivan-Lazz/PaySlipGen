<?php

namespace Database\Migrations;

use PDO;

class CreateUsersTable
{
    /**
     * Run the migration
     *
     * @param PDO $conn
     * @return void
     */
    public function up(PDO $conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            firstname VARCHAR(100) NOT NULL,
            lastname VARCHAR(100) NOT NULL,
            username VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
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
        $conn->exec("DROP TABLE IF EXISTS users");
    }
}