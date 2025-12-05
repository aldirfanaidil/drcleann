<?php
require_once __DIR__ . '/core/init.php';

echo "=== ROLE CHECK DEBUG ===\n";

// Simulate different roles
$testRoles = ['superuser', 'admin', 'kasir'];

foreach ($testRoles as $role) {
    echo "\n--- Testing role: $role ---\n";
    
    // Set session role
    $_SESSION['user_role'] = $role;
    $_SESSION['user_id'] = 1;
    
    echo "Session role: " . $_SESSION['user_role'] . "\n";
    echo "isLoggedIn(): " . (isLoggedIn() ? 'true' : 'false') . "\n";
    echo "isAdmin(): " . (isAdmin() ? 'true' : 'false') . "\n";
    echo "isSuperuser(): " . (isSuperuser() ? 'true' : 'false') . "\n";
    
    // Check menu visibility
    echo "Pengguna menu visible: " . (isSuperuser() ? 'true' : 'false') . "\n";
    echo "Pengaturan menu visible: " . (isAdmin() ? 'true' : 'false') . "\n";
    echo "Ubah Password menu visible: " . (isLoggedIn() ? 'true' : 'false') . "\n";
}

// Clean up
unset($_SESSION['user_role']);
unset($_SESSION['user_id']);
?>