<?php
require_once __DIR__ . '/core/init.php';

// Test login for user kiku
echo "=== LOGIN TEST FOR USER KIKU ===\n";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$password = 'Kiki@2025';
echo "Testing login with username: kiku, password: $password\n";

$loginResult = $user->login('kiku', $password);
echo "Login result: " . json_encode($loginResult) . "\n";

// Get stored hash
$query = "SELECT password_hash FROM users WHERE username = ?";
$stmt = $db->prepare($query);
$stmt->execute(['kiku']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo "Stored hash: " . $row['password_hash'] . "\n";
    echo "Password verify test: " . (password_verify($password, $row['password_hash']) ? 'SUCCESS' : 'FAILED') . "\n";
    echo "Security::verifyPassword test: " . (Security::verifyPassword($password, $row['password_hash']) ? 'SUCCESS' : 'FAILED') . "\n";
}
?>