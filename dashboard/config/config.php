<?php

// Load environment variables from .env file if exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

return [
    // Database
    'db' => [
        'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
        'path' => $_ENV['DB_PATH'] ?? null,
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'name' => $_ENV['DB_NAME'] ?? 'krause_dashboard',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASS'] ?? '',
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    ],

    // Application
    'app' => [
        'env' => $_ENV['APP_ENV'] ?? 'production',
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'url' => $_ENV['APP_URL'] ?? 'http://localhost/dashboard',
        'name' => 'Krause Global Deal Desk',
    ],

    // Security
    'security' => [
        'session_lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 3600),
        'session_name' => $_ENV['SESSION_NAME'] ?? 'KG_DASHBOARD_SESSION',
        'csrf_token_name' => $_ENV['CSRF_TOKEN_NAME'] ?? '_csrf_token',
    ],

    // Paths
    'paths' => [
        'storage' => $_ENV['STORAGE_PATH'] ?? '../storage',
        'uploads' => $_ENV['UPLOAD_PATH'] ?? '../storage/uploads',
        'extracted' => $_ENV['EXTRACTED_PATH'] ?? '../storage/extracted',
        'exports' => $_ENV['EXPORT_PATH'] ?? '../storage/exports',
        'sessions' => $_ENV['SESSION_PATH'] ?? '../storage/sessions',
        'logs' => $_ENV['LOG_PATH'] ?? '../storage/logs',
    ],

    // Upload Settings
    'upload' => [
        'max_file_size' => (int)($_ENV['MAX_FILE_SIZE'] ?? 52428800), // 50MB
        'allowed_mime_types' => explode(',', $_ENV['ALLOWED_MIME_TYPES'] ?? 'application/pdf,image/png,image/jpeg'),
    ],

    // PDF Extraction
    'pdf' => [
        'extraction_method' => $_ENV['PDF_EXTRACTION_METHOD'] ?? 'auto', // auto, pdftotext, parser
        'pdftotext_path' => $_ENV['PDFTOTEXT_PATH'] ?? '/usr/bin/pdftotext',
    ],
];
