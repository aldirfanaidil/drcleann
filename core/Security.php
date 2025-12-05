<?php
class Security {
    public static function generateCSRFToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    public static function validateCSRFToken($token) {
        $sessionToken = $_SESSION[CSRF_TOKEN_NAME] ?? 'not_set';
        error_log("CSRF Validation - Session token: " . substr($sessionToken, 0, 8) . "... Provided token: " . substr($token ?? 'not_provided', 0, 8) . "...");
        
        $result = isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
        error_log("CSRF Validation result: " . ($result ? 'SUCCESS' : 'FAILED'));
        
        return $result;
    }

    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validatePhone($phone) {
        return preg_match('/^[0-9]{10,15}$/', $phone);
    }

    public static function validateNumber($number, $min = 1, $max = null) {
        if (!is_numeric($number)) return false;
        $num = (int)$number;
        if ($num < $min) return false;
        if ($max !== null && $num > $max) return false;
        return true;
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function generateRandomString($length = 10) {
        return bin2hex(random_bytes($length / 2));
    }

    public static function logActivity($userId, $action, $details = '') {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $stmt = $conn->prepare("
                INSERT INTO activity_logs (user_id, action, ip_address, user_agent, details, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userId,
                $action,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $details
            ]);
        } catch (Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    public static function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 900) {
        $cacheKey = "rate_limit_" . md5($identifier ?? 'default');
        
        if (!isset($_SESSION[$cacheKey])) {
            $_SESSION[$cacheKey] = ['attempts' => 0, 'first_attempt' => time()];
        }
        
        $data = $_SESSION[$cacheKey];
        
        // Reset if time window has passed
        if (time() - $data['first_attempt'] > $timeWindow) {
            $_SESSION[$cacheKey] = ['attempts' => 1, 'first_attempt' => time()];
            return true;
        }
        
        // Check if exceeded max attempts
        if ($data['attempts'] >= $maxAttempts) {
            return false;
        }
        
        // Increment attempts
        $_SESSION[$cacheKey]['attempts']++;
        return true;
    }

    public static function setSecurityHeaders() {
        if (!headers_sent()) {
            header("X-Frame-Options: DENY");
            header("X-Content-Type-Options: nosniff");
            header("X-XSS-Protection: 1; mode=block");
            header("Referrer-Policy: strict-origin-when-cross-origin");
            header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tailwindcss.com; img-src 'self' data:; font-src 'self' https://cdnjs.cloudflare.com; connect-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;");
            
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
            }
        }
    }
}
?>