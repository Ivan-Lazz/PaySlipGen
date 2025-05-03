<?php

namespace Database\Migrations;

use PDO;

class AddSalaryAndBonusToPayslipsTable
{
    /**
     * Run the migration
     *
     * @param PDO $conn
     * @return void
     */
    public function up(PDO $conn)
    {
        $sql = "ALTER TABLE payslip 
                ADD COLUMN salary DECIMAL(10, 2) NOT NULL AFTER bank_acct,
                ADD COLUMN bonus DECIMAL(10, 2) DEFAULT 0.00 AFTER salary";
        
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
        $sql = "ALTER TABLE payslip 
                DROP COLUMN salary,
                DROP COLUMN bonus";
        
        $conn->exec($sql);
    }
}