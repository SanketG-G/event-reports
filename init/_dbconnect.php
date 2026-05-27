<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * LOAD ENV FILE
 */
$dotenvPath = __DIR__ . '/../';

if (file_exists($dotenvPath . '.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->safeLoad();
}

/**
 * DATABASE VARIABLES
 */
$host = getenv('MYSQLHOST') ?: ($_ENV['MYSQLHOST'] ?? null);
$port = getenv('MYSQLPORT') ?: ($_ENV['MYSQLPORT'] ?? 3306);
$dbname = getenv('MYSQLDATABASE') ?: ($_ENV['MYSQLDATABASE'] ?? null);
$username = getenv('MYSQLUSER') ?: ($_ENV['MYSQLUSER'] ?? null);
$password = getenv('MYSQLPASSWORD') ?: ($_ENV['MYSQLPASSWORD'] ?? null);

/**
 * DATABASE CONNECTION
 */
try {

    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $GLOBALS['pdo'] = $pdo;

} catch (PDOException $e) {

    error_log("❌ Database Connection Failed: " . $e->getMessage());

    throw new Exception("Database connection failed.");
}
