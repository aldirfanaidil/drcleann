<?php
class Database {
    private $conn;

    public function getConnection() {

        $host = $_ENV['DB_HOST'];
        $db   = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        $port = $_ENV['DB_PORT'];

        $caPath = __DIR__ . '/../database/ca.pem';  // FIXED: SSL path

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_SSL_CA => $caPath,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
            ];

            $this->conn = new PDO($dsn, $user, $pass, $options);

        } catch (PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }

        return $this->conn;
    }
}
?>
