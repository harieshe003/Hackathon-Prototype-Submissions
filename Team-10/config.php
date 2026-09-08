<?php
// config/config.php

// Prevent direct script access warnings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to load .env file
function loadEnv(string $path): void {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2) + [null, null];
        if ($name !== null && $value !== null) {
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// Load environment variables
$rootPath = dirname(__DIR__);
loadEnv($rootPath . '/.env');

// Define APP_URL dynamically based on current request host
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8000';
$defaultAppUrl = $protocol . $host;

define('APP_NAME', getenv('APP_NAME') ?: 'EcoLens');
define('APP_URL', getenv('APP_URL') ?: $defaultAppUrl);
define('ROOT_PATH', $rootPath);
define('UPLOADS_PATH', $rootPath . '/uploads');

// Display errors in dev mode
if (getenv('APP_ENV') === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
