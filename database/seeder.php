<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/helpers/app_helpers.php';

use Core\Database;

// Initialize the application
Core\App::init();

// Get the database connection
$db = Database::getInstance()->getConnection();

// Get seeder files
$seederFiles = glob(__DIR__ . '/seeds/*.php');
sort($seederFiles); // Sort to ensure proper order

// Load seeders
$seeders = [];

foreach ($seederFiles as $file) {
    require_once $file;
    $className = basename($file, '.php');
    $fullClassName = "Database\\Seeds\\{$className}";
    $seeders[] = new $fullClassName();
}

// Run seeders
foreach ($seeders as $seeder) {
    echo "Running seeder " . get_class($seeder) . "...\n";
    $seeder->run($db);
}

echo "Seeders completed successfully.\n";