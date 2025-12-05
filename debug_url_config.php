<?php
require_once __DIR__ . '/config/config.php';

echo "=== DEBUG URL CONFIGURATION ===\n";
echo "Current URL: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Protocol: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "\n";
echo "APP_URL: " . APP_URL . "\n";

echo "\n=== FORM ACTION TEST ===\n";
// Simulate we're in /al/admin/views/auth/login.php
$_SERVER['SCRIPT_NAME'] = '/al/admin/index.php'; // Simulate admin index
$baseDir = dirname(dirname($_SERVER['SCRIPT_NAME']));
echo "Base dir: " . $baseDir . "\n";

$formAction = '../login.php?action=login';
echo "Form action: " . $formAction . "\n";

// Resolve relative path
if (strpos($formAction, 'http://') === 0 || strpos($formAction, 'https://') === 0) {
    $finalUrl = $formAction;
} else {
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $currentPath = dirname($_SERVER['SCRIPT_NAME']);
    $finalUrl = $baseUrl . $currentPath . '/' . ltrim($formAction, '/');
}

echo "Final URL: " . $finalUrl . "\n";
?>