<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/helpers/app_helpers.php';

use Core\Database;

// Initialize the application
Core\App::init();

// Get the database connection
$db = Database::getInstance()->getConnection();

// Determine action
$action = $argv[1] ?? 'up';

if (!in_array($action, ['up', 'down', 'refresh'])) {
    echo "Invalid action. Use 'up', 'down', or 'refresh'.\n";
    exit(1);
}

// Get migration files
$migrationFiles = glob(__DIR__ . '/migrations/*.php');
sort($migrationFiles); // Sort to ensure proper order

// Load migrations
$migrations = [];

foreach ($migrationFiles as $file) {
    require_once $file;
    $className = basename($file, '.php');
    $fullClassName = "Database\\Migrations\\{$className}";
    $migrations[] = new $fullClassName();
}

// Run migrations
if ($action === 'up' || $action === 'refresh') {
    if ($action === 'refresh') {
        // Drop all tables first
        foreach (array_reverse($migrations) as $migration) {
            echo "Dropping table from " . get_class($migration) . "...\n";
            $migration->down($db);
        }
    }
    
    // Create tables
    foreach ($migrations as $migration) {
        echo "Running migration " . get_class($migration) . "...\n";
        $migration->up($db);
    }
    
    echo "Migrations completed successfully.\n";
} else if ($action === 'down') {
    // Reverse order for down migrations
    foreach (array_reverse($migrations) as $migration) {
        echo "Reversing migration " . get_class($migration) . "...\n";
        $migration->down($db);
    }
    
    echo "Migrations reversed successfully.\n";
}