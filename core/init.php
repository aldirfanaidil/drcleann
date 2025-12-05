<?php
// Start session first before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Security.php';

Security::setSecurityHeaders();

// Autoloader for classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/../core/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isSuperuser() {
    return isLoggedIn() && strtolower($_SESSION['user_role']) === 'superuser';
}

function isAdmin() {
    if (!isLoggedIn()) return false;
    $role = strtolower($_SESSION['user_role']);
    return in_array($role, ['admin', 'superuser', 'kasir']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['error'] = 'Access denied. Admin privileges required.';
        redirect('login.php');
    }
}

function requireSuperuser() {
    if (!isSuperuser()) {
        $_SESSION['error'] = 'Access denied. Superuser privileges required.';
        redirect('login.php');
    }
}

function generateInvoiceNumber() {
    $prefix = INVOICE_PREFIX;
    $date = date('Ymd');
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
    return $prefix . $date . $random;
}

function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function redirect($url) {
    // If it's already a full URL, use as-is
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        header("Location: $url");
        exit;
    }
    
    // Always use to base path from APP_URL
    $baseUrl = rtrim(APP_URL, '/');
    $url = ltrim($url, '/');
    
    $fullUrl = $baseUrl . '/' . $url;
    header("Location: $fullUrl");
    exit;
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function getFlashMessage($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

function validateRequired($data, $fields) {
    $errors = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    return $errors;
}

function getSetting($key, $default = null) {
    static $settingsController = null;
    
    if ($settingsController === null) {
        $settingsController = new SettingsController();
    }
    
    return $settingsController->getSetting($key, $default);
}
?>