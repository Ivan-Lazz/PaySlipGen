<?php

namespace Database\Seeds;

use App\Models\User;
use Core\Security;
use PDO;

class UserSeeder
{
    /**
     * Run the database seed
     *
     * @param PDO $conn
     * @return void
     */
    public function run(PDO $conn)
    {
        $user = new User($conn);
        
        // Check if admin user exists
        if (!$user->usernameExists('admin')) {
            // Create admin user
            $adminData = [
                'firstname' => 'System',
                'lastname' => 'Administrator',
                'username' => 'admin',
                'password' => 'admin123' // This will be hashed by the model
            ];
            
            $user->create($adminData);
            echo "Admin user created successfully.\n";
        } else {
            echo "Admin user already exists.\n";
        }
        
        // Create sample users
        $users = [
            [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'username' => 'johndoe',
                'password' => 'password123'
            ],
            [
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'username' => 'janesmith',
                'password' => 'password123'
            ],
            [
                'firstname' => 'Bob',
                'lastname' => 'Johnson',
                'username' => 'bjohnson',
                'password' => 'password123'
            ]
        ];
        
        $createdCount = 0;
        
        foreach ($users as $userData) {
            if (!$user->usernameExists($userData['username'])) {
                $user->create($userData);
                $createdCount++;
            }
        }
        
        echo "{$createdCount} sample users created successfully.\n";
    }
}