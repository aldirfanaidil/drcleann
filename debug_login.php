<?php
require_once __DIR__ . '/core/init.php';

// Test login for user kiku
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

echo "=== LOGIN TEST FOR USER KIKU ===\n";

// Test with password
$password = 'Kiki@2025'; // Assuming this is the password
$result = $user->login('kiku', $password);

echo "Login result: " . json_encode($result) . "\n";

// Test password verification
$storedHash = '$argon2id$v=19$m=65536,t=4,p=1$eGlyS3dsaDF5dGg3cTBNbg$qgZAhFt9hy+C8rFruOXhIcoIb5x3kRStFOHzXEXsr50';
echo "Password verification test: " . (password_verify($password, $storedHash) ? 'SUCCESS' : 'FAILED') . "\n";

// Test Security::verifyPassword
echo "Security::verifyPassword test: " . (Security::verifyPassword($password, $storedHash) ? 'SUCCESS' : 'FAILED') . "\n";
?>