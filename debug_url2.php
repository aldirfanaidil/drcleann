<?php
require_once __DIR__ . '/config/config.php';

echo "=== DEBUG APP_URL AFTER FIX ===\n";
echo "APP_URL: " . APP_URL . "\n";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Host: " . $_SERVER['HTTP_HOST'] . "\n";

echo "\n=== REDIRECT TESTS ===\n";
$testUrls = [
    'admin/index.php?page=dashboard',
    '?page=dashboard',
    '../login.php'
];

foreach ($testUrls as $url) {
    echo "Original: $url\n";
    
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        $finalUrl = $url;
    } else {
        $baseUrl = rtrim(APP_URL, '/');
        $url = ltrim($url, '/');
        $finalUrl = $baseUrl . '/' . $url;
    }
    
    echo "Final: $finalUrl\n\n";
}
?>