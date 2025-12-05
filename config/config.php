<?php
// Database Configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'DRCLEAN');
define('DB_USER', $_ENV['DB_USER'] ?? '4MQbmjj75JU22dn.root');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'y5EfIFa7FJcZPq6C');
define('DB_PORT', $_ENV['DB_PORT'] ?? '4000');

// Application Configuration
define('APP_NAME', 'Dr.ShoezClean');

// Dynamic APP_URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_name = isset($_SERVER['SCRIPT_NAME']) ? str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) : '/aldd/';
// If script is in a subdirectory, ensure that path is correctly handled
$base_path = rtrim($script_name, '/');
define('APP_URL', $protocol . "://" . $host . $base_path);

define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('LOG_PATH', __DIR__ . '/../logs/');

// Security Configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Invoice Configuration
define('INVOICE_PREFIX', 'INV-');
define('DEFAULT_TAX_RATE', 0);

// Service Configuration
$SERVICES = [
    'DEEP_CLEAN_EXPRESS' => [
        'name' => 'Deep Clean Express (1 hari)',
        'prices' => [
            'Silver' => 33000,
            'Gold' => 35000,
            'Platinum' => 38000,
            'White Shoes' => 40000
        ]
    ],
    'DEEP_CLEAN_REGULER' => [
        'name' => 'Deep Clean Reguler (3-4 hari)',
        'prices' => [
            'Silver' => 19000,
            'Gold' => 22000,
            'Platinum' => 25000,
            'White Shoes' => 26000
        ]
    ],
    'FAST_CLEAN_EXPRESS' => [
        'name' => 'Fast Clean (cuci luar saja) Express 1 hari',
        'prices' => [
            'Silver' => 27000,
            'Gold' => 29000,
            'Platinum' => 31000,
            'White Shoes' => 33000
        ]
    ],
    'UNYELLOWING' => [
        'name' => 'Unyellowing (4-6 hari)',
        'prices' => [
            'Platinum' => 37000,
            'Premium' => 40000
        ]
    ],
    'RECOLOUR' => [
        'name' => 'Recolour (7-10 hari)',
        'prices' => [
            'Platinum' => 88000,
            'Premium' => 115000
        ]
    ],
    'REPAINT' => [
        'name' => 'Repaint (7-10 hari)',
        'prices' => [
            'Platinum' => 86000,
            'Premium' => 110000
        ]
    ]
];

// Time Configuration
define('TIMEZONE', 'Asia/Jakarta');
date_default_timezone_set(TIMEZONE);

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', LOG_PATH . 'php_errors.log');
?>
