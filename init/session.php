<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * ------------------------------------------------
 * START SESSION
 * ------------------------------------------------
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * ------------------------------------------------
 * LOAD ENV FILE (LOCAL ONLY)
 * ------------------------------------------------
 */

$dotenvPath = __DIR__ . '/../';

if (file_exists($dotenvPath . '.env')) {

    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->safeLoad();
}

/**
 * ------------------------------------------------
 * DATABASE VARIABLES
 * ------------------------------------------------
 * Railway MySQL variables:
 * MYSQLHOST
 * MYSQLPORT
 * MYSQLDATABASE
 * MYSQLUSER
 * MYSQLPASSWORD
 */

$host = getenv('MYSQLHOST') ?: ($_ENV['MYSQLHOST'] ?? null);
$port = getenv('MYSQLPORT') ?: ($_ENV['MYSQLPORT'] ?? 3306);
$dbname = getenv('MYSQLDATABASE') ?: ($_ENV['MYSQLDATABASE'] ?? null);
$username = getenv('MYSQLUSER') ?: ($_ENV['MYSQLUSER'] ?? null);
$password = getenv('MYSQLPASSWORD') ?: ($_ENV['MYSQLPASSWORD'] ?? null);

/**
 * ------------------------------------------------
 * DATABASE CONNECTION
 * ------------------------------------------------
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

    // Store globally
    $GLOBALS['pdo'] = $pdo;

    error_log("✅ Database connected successfully");

} catch (PDOException $e) {

    error_log("❌ Database Connection Failed: " . $e->getMessage());

    die(
        '<pre style="padding:20px;font-size:16px;color:red;">' .
        htmlspecialchars($e->getMessage()) .
        '</pre>'
    );
}

/**
 * ------------------------------------------------
 * CSRF TOKEN
 * ------------------------------------------------
 */

if (empty($_SESSION['csrf_token'])) {

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
