<?php
// Test TiDB Connection
require_once 'config/config.php';

try {
    // Create PDO connection
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        PDO::MYSQL_ATTR_SSL_CA => false, // Disable SSL verification for testing
    ];
    
    $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    echo "<h2>✅ TiDB Connection Successful!</h2>";
    echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
    echo "<p><strong>Port:</strong> " . DB_PORT . "</p>";
    echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
    echo "<p><strong>User:</strong> " . DB_USER . "</p>";
    
    // Test query
    $stmt = $conn->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "<p><strong>TiDB Version:</strong> " . $result['version'] . "</p>";
    
    // Show tables
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    if (count($tables) > 0) {
        echo "<h3>Existing Tables:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            echo "<li>" . $tableName . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<h3>No tables found. Ready to import schema!</h3>";
    }
    
} catch (PDOException $e) {
    echo "<h2>❌ Connection Failed!</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Check your credentials in conn.md</p>";
}
?>