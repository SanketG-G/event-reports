<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Configuration\Configuration;

// ------------------------------
// Load Cloudinary environment variables safely
// ------------------------------
$cloudName    = $_ENV['CLOUD_NAME']       ?? getenv('CLOUD_NAME')       ?: '';
$cloudApiKey  = $_ENV['CLOUD_API_KEY']    ?? getenv('CLOUD_API_KEY')    ?: '';
$cloudApiSecret = $_ENV['CLOUD_API_SECRET'] ?? getenv('CLOUD_API_SECRET') ?: '';

// Skip if placeholders or missing (local dev without Cloudinary)
$isPlaceholder = str_starts_with($cloudName, 'your_') ||
                 str_starts_with($cloudApiKey, 'your_') ||
                 str_starts_with($cloudApiSecret, 'your_');

if (empty($cloudName) || empty($cloudApiKey) || empty($cloudApiSecret) || $isPlaceholder) {
    // In production (Railway), throw error. Locally, just warn.
    $isLocal = ($_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'production') === 'local';
    if (!$isLocal) {
        die('Cloudinary environment variables are missing. Please add CLOUD_NAME, CLOUD_API_KEY, and CLOUD_API_SECRET.');
    }
    // Locally: skip Cloudinary config, photo upload will be disabled
    error_log("⚠️ Cloudinary not configured — photo upload disabled in local mode.");
    return;
}

// Configure Cloudinary
Configuration::instance([
    'cloud' => [
        'cloud_name' => $cloudName,
        'api_key'    => $cloudApiKey,
        'api_secret' => $cloudApiSecret,
    ],
    'url' => [
        'secure' => true
    ]
]);

// Optional: debug output in development
$debug = ($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG')) === 'true';
if ($debug) {
    error_log("✅ Cloudinary configured with cloud: $cloudName");
}