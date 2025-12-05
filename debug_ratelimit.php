<?php
require_once __DIR__ . '/core/init.php';

// Check rate limit for user kiku
echo "=== RATE LIMIT CHECK FOR USER KIKU ===\n";

$ip = '10.12.60.236'; // From log
$cacheKey = "rate_limit_" . md5($ip);

echo "IP: $ip\n";
echo "Cache Key: $cacheKey\n";

if (isset($_SESSION[$cacheKey])) {
    $data = $_SESSION[$cacheKey];
    echo "Session data: " . json_encode($data) . "\n";
    echo "Current time: " . time() . "\n";
    echo "First attempt: " . $data['first_attempt'] . "\n";
    echo "Time diff: " . (time() - $data['first_attempt']) . "\n";
    echo "Time window: 900\n";
    echo "Attempts: " . $data['attempts'] . "\n";
    echo "Max attempts: 5\n";
    echo "Rate limit result: " . (Security::checkRateLimit($ip, 5, 900) ? 'ALLOWED' : 'BLOCKED') . "\n";
} else {
    echo "No rate limit data found\n";
}

// Clear rate limit for testing
unset($_SESSION[$cacheKey]);
echo "Rate limit cleared for testing\n";
?>