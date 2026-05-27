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
 * DEBUG VARIABLES
 */
echo "<pre>";

echo "HOST: ";
var_dump($host);

echo "PORT: ";
var_dump($port);

echo "DATABASE: ";
var_dump($dbname);

echo "USERNAME: ";
var_dump($username);

echo "</pre>";

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

    echo "<h2 style='color:green'>✅ Database Connected Successfully</h2>";

} catch (PDOException $e) {

    echo "<h2 style='color:red'>❌ Database Connection Failed</h2>";

    echo "<pre>";
    echo htmlspecialchars($e->getMessage());
    echo "</pre>";
}

exit;
