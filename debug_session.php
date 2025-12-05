<?php
require_once __DIR__ . '/core/init.php';

echo "<h2>Debug Session & Role</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "User ID: " . ($_SESSION['user_id'] ?? 'not set') . "\n";
echo "Username: " . ($_SESSION['username'] ?? 'not set') . "\n";
echo "User Role: " . ($_SESSION['user_role'] ?? 'not set') . "\n";
echo "Full Name: " . ($_SESSION['full_name'] ?? 'not set') . "\n";
echo "CSRF Token: " . substr($_SESSION['csrf_token'] ?? 'not set', 0, 8) . "...\n";
echo "</pre>";

echo "<h2>Function Results</h2>";
echo "<pre>";
echo "isLoggedIn(): " . (isLoggedIn() ? 'true' : 'false') . "\n";
echo "isSuperuser(): " . (isSuperuser() ? 'true' : 'false') . "\n";
echo "isAdmin(): " . (isAdmin() ? 'true' : 'false') . "\n";
echo "</pre>";

echo "<h2>Test Superuser Requirement</h2>";
try {
    requireSuperuser();
    echo "✅ Superuser requirement passed\n";
} catch (Exception $e) {
    echo "❌ Superuser requirement failed: " . $e->getMessage() . "\n";
}
?>