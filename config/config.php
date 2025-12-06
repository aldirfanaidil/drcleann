<?php
class Database {
    private $host = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com";
    private $port = 4000;
    private $db_name = "DRCLEAN";
    private $username = "4MQbmjj75JU22dn.root";
    private $password = "y5EfIFa7FJcZPq6C";
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $options = [
                PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/../database/ca.pem',
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ];

            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }

        return $this->conn;
    }
}
