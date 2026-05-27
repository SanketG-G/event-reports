<?php

require_once __DIR__ . '/../vendor/autoload.php';

/* Load .env file if it exists (local development) */
$dotenvPath = __DIR__ . '/../';
if (file_exists($dotenvPath . '.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->safeLoad();
}

/* Fetch environment variables — fallback to XAMPP local defaults */
$host     = $_ENV['DB_HOST']  ?? getenv('DB_HOST')  ?: 'localhost';
$port     = $_ENV['DB_PORT']  ?? getenv('DB_PORT')  ?: 3306;
$dbname   = $_ENV['DB_NAME']  ?? getenv('DB_NAME')  ?: 'college_events';
$username = $_ENV['DB_USER']  ?? getenv('DB_USER')  ?: 'root';
$password = $_ENV['DB_PASS']  ?? getenv('DB_PASS')  ?: '';

/* Try DB Connection */
try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Store globally (your existing approach)
    $GLOBALS['pdo'] = $pdo;

} catch (PDOException $e) {
    // Log actual error (safe)
    error_log("Database Connection Failed: " . $e->getMessage());

    // Throw generic exception (handled in index.php)
    throw new Exception("Database connection failed.");
}

/* CSRF token generation */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
