<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../core/Database.php';

class SettingsController {
    private $conn;
    
    public function __construct() {
        error_log("SettingsController constructor called");
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->fixOldPaths();
    }
    
    /**
     * Fix old paths that don't have base URL
     */
    private function fixOldPaths() {
        // Get base path from current request
        $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        if (strpos($basePath, 'admin') !== false) {
            $basePath = str_replace('/admin', '', $basePath);
        }
        
        // Fix logo path
        $logoPath = $this->getSetting('shop_logo_path');
        if ($logoPath && !str_starts_with($logoPath, '/')) {
            $newPath = $basePath . '/' . ltrim($logoPath, '/');
            $this->updateSetting('shop_logo_path', $newPath);
        }
        
        // Fix QR code path
        $qrPath = $this->getSetting('qr_code_path');
        if ($qrPath && !str_starts_with($qrPath, '/')) {
            $newPath = $basePath . '/' . ltrim($qrPath, '/');
            $this->updateSetting('qr_code_path', $newPath);
        }
    }
    
    public function saveGeneral() {
        requireAdmin();
        
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request';
            redirect('?page=settings');
        }
        
        $settings = [
            'shop_name' => Security::sanitizeInput($_POST['shop_name'] ?? ''),
            'shop_phone' => Security::sanitizeInput($_POST['shop_phone'] ?? ''),
            'shop_address' => Security::sanitizeInput($_POST['shop_address'] ?? ''),
            'shop_email' => Security::sanitizeInput($_POST['shop_email'] ?? ''),
            'shop_website' => Security::sanitizeInput($_POST['shop_website'] ?? '')
        ];
        
        foreach ($settings as $key => $value) {
            $this->updateSetting($key, $value);
        }
        
        error_log("Checking for logo upload...");
        error_log("FILES array: " . print_r($_FILES, true));
        error_log("POST max size: " . ini_get('post_max_size'));
        error_log("Upload max filesize: " . ini_get('upload_max_filesize'));
        error_log("Memory limit: " . ini_get('memory_limit'));
        
        // Handle logo upload
        if (isset($_FILES['shop_logo'])) {
            error_log("Logo file found in FILES array");
            error_log("Upload error code: " . $_FILES['shop_logo']['error']);
            
            if ($_FILES['shop_logo']['error'] === UPLOAD_ERR_OK) {
                error_log("Logo upload error is OK - proceeding with upload");
            error_log("Logo upload detected and error is OK");
            $uploadDir = __DIR__ . '/../views/admin/assets/uploads/logo/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileInfo = pathinfo($_FILES['shop_logo']['name']);
            $fileName = 'logo_' . time() . '.' . $fileInfo['extension'];
            $uploadPath = $uploadDir . $fileName;
            
            // Debug logging
            error_log("Upload Debug - File: " . $_FILES['shop_logo']['name']);
            error_log("Upload Debug - Type: " . $_FILES['shop_logo']['type']);
            error_log("Upload Debug - Size: " . $_FILES['shop_logo']['size']);
            error_log("Upload Debug - UploadDir: " . $uploadDir);
            error_log("Upload Debug - UploadPath: " . $uploadPath);
            error_log("Upload Debug - TmpName: " . $_FILES['shop_logo']['tmp_name']);
            error_log("Upload Debug - UploadDir exists: " . (is_dir($uploadDir) ? 'YES' : 'NO'));
            error_log("Upload Debug - UploadDir writable: " . (is_writable($uploadDir) ? 'YES' : 'NO'));
            
            // Validate file
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            if (in_array($_FILES['shop_logo']['type'], $allowedTypes) && $_FILES['shop_logo']['size'] <= $maxSize) {
                if (move_uploaded_file($_FILES['shop_logo']['tmp_name'], $uploadPath)) {
                    error_log("Upload Debug - File moved successfully to: " . $uploadPath);
                    // Get base path from current request
                    $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                    if (strpos($basePath, 'admin') !== false) {
                        $basePath = str_replace('/admin', '', $basePath);
                    }
                    $this->updateSetting('shop_logo_path', $basePath . '/views/admin/assets/uploads/logo/' . $fileName);
                    error_log("Upload Debug - Path saved to DB: " . $basePath . '/views/admin/assets/uploads/logo/' . $fileName);
                } else {
                    error_log("Upload Debug - move_uploaded_file failed");
                    $_SESSION['error'] = 'Gagal mengupload logo - move_uploaded_file failed';
                }
            } else {
                error_log("Upload Debug - File validation failed");
                $_SESSION['error'] = 'Format file tidak didukung atau ukuran file terlalu besar (max 2MB)';
            }
        } else {
            // Debug: Check if file was uploaded at all
            if (isset($_FILES['shop_logo'])) {
                error_log("Upload Debug - File upload error code: " . $_FILES['shop_logo']['error']);
            } else {
                error_log("Upload Debug - No file uploaded");
            }
        }
        
        $_SESSION['success'] = 'Pengaturan informasi toko berhasil disimpan';
        redirect('?page=settings');
    }
    
    public function savePayment() {
        requireAdmin();
        
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request';
            redirect('?page=settings');
        }
        
        $settings = [
            'bank_name' => Security::sanitizeInput($_POST['bank_name'] ?? ''),
            'bank_account' => Security::sanitizeInput($_POST['bank_account'] ?? ''),
            'bank_holder' => Security::sanitizeInput($_POST['bank_holder'] ?? '')
        ];
        
        foreach ($settings as $key => $value) {
            $this->updateSetting($key, $value);
        }
        
        // Handle QR Code upload
        if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../views/admin/assets/uploads/qr/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = 'qr_payment.' . pathinfo($_FILES['qr_code']['name'], PATHINFO_EXTENSION);
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['qr_code']['tmp_name'], $uploadPath)) {
                // Get base path from current request
                $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                if (strpos($basePath, 'admin') !== false) {
                    $basePath = str_replace('/admin', '', $basePath);
                }
                $this->updateSetting('qr_code_path', $basePath . '/views/admin/assets/uploads/qr/' . $fileName);
            }
        }
        
        $_SESSION['success'] = 'Pengaturan pembayaran berhasil disimpan';
        redirect('?page=settings');
    }
    
    public function saveServices() {
        requireAdmin();
        
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request';
            redirect('?page=settings');
        }
        
        $settings = [
            'duration_deep_clean_express' => (int)($_POST['duration_deep_clean_express'] ?? 1),
            'duration_deep_clean_reguler' => (int)($_POST['duration_deep_clean_reguler'] ?? 3),
            'duration_fast_clean_express' => (int)($_POST['duration_fast_clean_express'] ?? 1),
            'duration_unyellowing' => (int)($_POST['duration_unyellowing'] ?? 5),
            'duration_recolour' => (int)($_POST['duration_recolour'] ?? 7),
            'duration_repaint' => (int)($_POST['duration_repaint'] ?? 7)
        ];
        
        foreach ($settings as $key => $value) {
            $this->updateSetting($key, $value);
        }
        
        $_SESSION['success'] = 'Pengaturan layanan berhasil disimpan';
        redirect('?page=settings');
    }
    
    public function backup() {
        requireSuperuser();
        
        // Create backup
        $backupFile = __DIR__ . '/../backups/backup_' . date('Y-m-d_H-i-s') . '.sql';
        $backupDir = dirname($backupFile);
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        // Get database config
        $database = new Database();
        $db = $database->getConnection();
        
        // Get all tables
        $tables = [];
        $result = $db->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        $sql = "-- Database Backup: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Generated by Dr.ShoezClean\n\n";
        
        foreach ($tables as $table) {
            $result = $db->query("SHOW CREATE TABLE `$table`");
            $row = $result->fetch(PDO::FETCH_NUM);
            $sql .= $row[1] . ";\n\n";
            
            $result = $db->query("SELECT * FROM `$table`");
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $sql .= "INSERT INTO `$table` VALUES (";
                $values = [];
                foreach ($row as $value) {
                    $values[] = $value === null ? 'NULL' : "'" . addslashes($value) . "'";
                }
                $sql .= implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }
        
        file_put_contents($backupFile, $sql);
        
        // Download file
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($backupFile) . '"');
        header('Content-Length: ' . filesize($backupFile));
        readfile($backupFile);
        unlink($backupFile); // Delete temp file
        exit;
    }
    
    public function clearCache() {
        requireSuperuser();
        header('Content-Type: application/json');
        
        // Clear session cache
        if (isset($_SESSION['cache'])) {
            unset($_SESSION['cache']);
        }
        
        // Clear any temporary files
        $tempDir = __DIR__ . '/../temp/';
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        
        echo json_encode(['success' => true]);
    }
    
    public function reset() {
        requireSuperuser();
        header('Content-Type: application/json');
        
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        // Reset all settings to default
        $defaultSettings = [
            'shop_name' => 'Dr.ShoezClean',
            'shop_phone' => '',
            'shop_address' => '',
            'shop_email' => '',
            'shop_website' => '',
            'bank_name' => '',
            'bank_account' => '',
            'bank_holder' => '',
            'duration_deep_clean_express' => 1,
            'duration_deep_clean_reguler' => 3,
            'duration_fast_clean_express' => 1,
            'duration_unyellowing' => 5,
            'duration_recolour' => 7,
            'duration_repaint' => 7
        ];
        
        foreach ($defaultSettings as $key => $value) {
            $this->updateSetting($key, $value);
        }
        
        echo json_encode(['success' => true]);
    }
    
    private function updateSetting($key, $value) {
        $query = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                  ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$key, $value]);
    }
    
    public function getSetting($key, $default = null) {
        $query = "SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$key]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['setting_value'] : $default;
    }
}