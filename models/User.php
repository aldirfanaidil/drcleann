<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        // Check rate limiting
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (!Security::checkRateLimit($ip, MAX_LOGIN_ATTEMPTS, LOGIN_LOCKOUT_TIME)) {
            return ['success' => false, 'message' => 'Too many login attempts. Please try again later.'];
        }

        $query = "SELECT id, username, password_hash, role, full_name, email, is_active FROM " . $this->table_name . " WHERE username = ? AND is_active = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (Security::verifyPassword($password, $row['password_hash'])) {
                // Successful login - log it
                $this->logLoginAttempt($username, $ip, true);
                
                // Update last login
                $this->updateLastLogin($row['id']);
                
                return [
                    'success' => true,
                    'user' => [
                        'id' => $row['id'],
                        'username' => $row['username'],
                        'role' => $row['role'],
                        'full_name' => $row['full_name'],
                        'email' => $row['email']
                    ]
                ];
            } else {
                // Failed login - log it
                $this->logLoginAttempt($username, $ip, false);
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
        } else {
            // Failed login - log it
            $this->logLoginAttempt($username, $ip, false);
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
    }

    private function logLoginAttempt($username, $ip, $success) {
        try {
            $query = "INSERT INTO login_attempts (username, ip_address, success) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$username, $ip, $success ? 1 : 0]);
        } catch (Exception $e) {
            // Log error but don't break login process
            error_log("Login attempt logging failed: " . $e->getMessage());
        }
    }

    private function updateLastLogin($userId) {
        $query = "UPDATE " . $this->table_name . " SET last_login = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (username, password_hash, role, full_name, email, phone) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = Security::hashPassword($data['password']);
        
        $stmt->bindParam(1, $data['username']);
        $stmt->bindParam(2, $hashedPassword);
        $stmt->bindParam(3, $data['role']);
        $stmt->bindParam(4, $data['full_name']);
        $stmt->bindParam(5, $data['email']);
        $stmt->bindParam(6, $data['phone']);

        if ($stmt->execute()) {
            $userId = $this->conn->lastInsertId();
            Security::logActivity($userId, 'user_created', "Created user: {$data['username']}");
            return $userId;
        }
        
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET username = ?, role = ?, full_name = ?, email = ?, phone = ?, updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(1, $data['username']);
        $stmt->bindParam(2, $data['role']);
        $stmt->bindParam(3, $data['full_name']);
        $stmt->bindParam(4, $data['email']);
        $stmt->bindParam(5, $data['phone']);
        $stmt->bindParam(6, $id);

        if ($stmt->execute()) {
            Security::logActivity($id, 'user_updated', "Updated user: {$data['username']}");
            return true;
        }
        
        return false;
    }

    public function updatePassword($id, $newPassword) {
        $query = "UPDATE " . $this->table_name . " SET password_hash = ?, updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = Security::hashPassword($newPassword);
        $stmt->bindParam(1, $hashedPassword);
        $stmt->bindParam(2, $id);

        if ($stmt->execute()) {
            Security::logActivity($id, 'password_changed', "Password changed for user ID: $id");
            return true;
        }
        
        return false;
    }

    public function delete($id) {
        // Soft delete by setting is_active = 0
        $query = "UPDATE " . $this->table_name . " SET is_active = 0, updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        if ($stmt->execute()) {
            Security::logActivity($id, 'user_deleted', "Deleted user ID: $id");
            return true;
        }
        
        return false;
    }

    public function getById($id) {
        $query = "SELECT id, username, role, full_name, email, phone, is_active, last_login, created_at FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll($activeOnly = true) {
        $query = "SELECT id, username, role, full_name, email, phone, is_active, last_login, created_at FROM " . $this->table_name;
        
        if ($activeOnly) {
            $query .= " WHERE is_active = 1";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function usernameExists($username, $excludeId = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }

    public function emailExists($email, $excludeId = null) {
        if (empty($email)) return false;
        
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }

    public function getStats() {
        $query = "SELECT 
                    COUNT(*) as total_users,
                    COUNT(CASE WHEN role = 'superuser' THEN 1 END) as superusers,
                    COUNT(CASE WHEN role = 'admin' THEN 1 END) as admins,
                    COUNT(CASE WHEN role = 'kasir' THEN 1 END) as cashiers,
                    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_users,
                    COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as active_last_week
                  FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>