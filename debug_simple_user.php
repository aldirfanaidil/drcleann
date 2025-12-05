<?php
require_once __DIR__ . '/core/init.php';

echo "=== CREATE SIMPLE USER TEST ===\n";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Simple test user
$testPassword = 'password123';
$testData = [
    'username' => 'simpleuser',
    'password' => $testPassword,
    'role' => 'kasir',
    'full_name' => 'Simple User',
    'email' => 'simple@test.com',
    'phone' => '08123456789'
];

echo "Creating user with password: $testPassword\n";

// Create user
$userId = $user->create($testData);
echo "User creation result: " . ($userId ? "SUCCESS (ID: $userId)" : "FAILED") . "\n";

if ($userId) {
    // Test login immediately
    echo "Testing login with created user...\n";
    $loginResult = $user->login($testData['username'], $testPassword);
    echo "Login result: " . json_encode($loginResult) . "\n";
    
    // Get stored hash
    $query = "SELECT password_hash FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$testData['username']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "Stored hash: " . $row['password_hash'] . "\n";
        echo "Password verify test: " . (password_verify($testPassword, $row['password_hash']) ? 'SUCCESS' : 'FAILED') . "\n";
        echo "Security::verifyPassword test: " . (Security::verifyPassword($testPassword, $row['password_hash']) ? 'SUCCESS' : 'FAILED') . "\n";
    }
    
    // Clean up
    $query = "DELETE FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$testData['username']]);
    echo "Test user cleaned up\n";
}
?>