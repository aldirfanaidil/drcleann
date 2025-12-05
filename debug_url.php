<?php
require_once __DIR__ . '/config/config.php';

echo "=== DEBUG APP_URL ===\n";
echo "Protocol: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "\n";
echo "Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Base Path (before replace): " . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) . "\n";
echo "Base Path (after replace): " . rtrim(str_replace('/admin', '', str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME'])), '/') . "\n";
echo "Final APP_URL: " . APP_URL . "\n";

echo "\n=== REDIRECT TEST ===\n";
$testUrl = 'admin/index.php?page=dashboard';
echo "Original URL: " . $testUrl . "\n";

if (strpos($testUrl, 'http://') !== 0 && strpos($testUrl, 'https://') !== 0 && $testUrl[0] !== '/') {
    $base = rtrim(APP_URL, '/');
    $testUrl = $base . '/' . ltrim($testUrl, '/');
}

echo "Final Redirect URL: " . $testUrl . "\n";
?>