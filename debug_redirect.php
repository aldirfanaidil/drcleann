<?php
require_once __DIR__ . '/config/config.php';

echo "=== CURRENT SITUATION ===\n";
echo "Current SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Current REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "APP_URL: " . APP_URL . "\n";

echo "\n=== REDIRECT TEST FROM LOGIN.PHP ===\n";
// Simulate we're in login.php (root)
$_SERVER['SCRIPT_NAME'] = '/al/login.php';

$url = 'admin/index.php?page=dashboard';
echo "Target URL: $url\n";

if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
    $finalUrl = $url;
} else {
    $baseUrl = rtrim(APP_URL, '/');
    $url = ltrim($url, '/');
    $finalUrl = $baseUrl . '/' . $url;
}

echo "Final URL: $finalUrl\n";

echo "\n=== REDIRECT TEST FROM ADMIN INDEX ===\n";
// Simulate we're in admin/index.php
$_SERVER['SCRIPT_NAME'] = '/al/admin/index.php';

$url = '?page=dashboard';
echo "Target URL: $url\n";

if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
    $finalUrl = $url;
} else {
    $baseUrl = rtrim(APP_URL, '/');
    $url = ltrim($url, '/');
    $finalUrl = $baseUrl . '/' . $url;
}

echo "Final URL: $finalUrl\n";
?>