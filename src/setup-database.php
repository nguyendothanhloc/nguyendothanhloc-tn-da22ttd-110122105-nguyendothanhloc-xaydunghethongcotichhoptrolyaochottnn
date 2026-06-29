<?php

/**
 * Simple database setup script
 * Run: php setup-database.php
 */

echo "=== Language Center Database Setup ===\n\n";

// Read .env file to get database credentials
$envFile = file_get_contents('.env');
preg_match('/DB_HOST=(.*)/', $envFile, $host);
preg_match('/DB_USERNAME=(.*)/', $envFile, $username);
preg_match('/DB_PASSWORD=(.*)/', $envFile, $password);
preg_match('/DB_DATABASE=(.*)/', $envFile, $database);

$dbHost = trim($host[1] ?? '127.0.0.1');
$dbUser = trim($username[1] ?? 'root');
$dbPass = trim($password[1] ?? '');
$dbName = trim($database[1] ?? 'language_center');

echo "Connecting to MySQL...\n";
echo "Host: $dbHost\n";
echo "User: $dbUser\n";
echo "Database: $dbName\n\n";

try {
    // Connect to MySQL without selecting database
    $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to MySQL successfully!\n\n";
    
    // Drop database if exists
    echo "Dropping existing database (if exists)...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$dbName`");
    echo "✓ Done\n\n";
    
    // Create database
    echo "Creating database '$dbName'...\n";
    $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database created successfully!\n\n";
    
    echo "=== Setup Complete! ===\n\n";
    echo "Now run: php artisan migrate\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
    echo "Please check your MySQL credentials in .env file\n";
    exit(1);
}
