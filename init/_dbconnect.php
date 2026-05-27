<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * ------------------------------------------------
 * LOAD ENV FILE (LOCAL DEVELOPMENT ONLY)
 * ------------------------------------------------
 */
$dotenvPath = __DIR__ . '/../';

if (file_exists($dotenvPath . '.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->safeLoad();
}

/**
 * ------------------------------------------------
 * DATABASE ENV VARIABLES
 * ------------------------------------------------
 * Railway MySQL variables:
 * MYSQLHOST
 * MYSQLPORT
 * MYSQLDATABASE
 * MYSQLUSER
 * MYSQLPASSWORD
 */

$host = getenv('MYSQLHOST') ?: $_ENV['MYSQLHOST'] ?? null;
$port = getenv('MYSQLPORT') ?: $_ENV['MYSQLPORT'] ?? 3306;
$dbname = getenv('MYSQLDATABASE') ?: $_ENV['MYSQLDATABASE'] ?? null;
$username = getenv('MYSQLUSER') ?: $_ENV['MYSQLUSER'] ?? null;
$password = getenv('MYSQLPASSWORD') ?: $_ENV['MYSQLPASSWORD'] ?? null;

/**
 * ------------------------------------------------
 * DEBUG CHECK (TEMPORARY)
 * ------------------------------------------------
 * Remove this block after deployment works
 */

if (!$host || !$dbname || !$username) {
    die(
        '<pre style="padding:20px;font-size:16px;">' .
        "❌ Railway MySQL environment variables missing\n\n" .
        "MYSQLHOST: " . var_export($host, true) . "\n" .
        "MYSQLPORT: " . var_export($port, true) . "\n" .
        "MYSQLDATABASE: " . var_export($dbname, true) . "\n" .
        "MYSQLUSER: " . var_export($username, true) . "\n" .
        '</pre>'
    );
}

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
        "Database Connection Failed\n\n" .
        htmlspecialchars($e->getMessage()) .
        '</pre>'
    );
}

/**
 * ------------------------------------------------
 * SESSION
 * ------------------------------------------------
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * ------------------------------------------------
 * CSRF TOKEN
 * ------------------------------------------------
 */

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
