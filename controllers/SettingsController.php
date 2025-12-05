<?php
require_once __DIR__ . '/../core/Database.php';

class SettingsController {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
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
        
        // Handle logo upload
        if (isset($_FILES['shop_logo']) && $_FILES['shop_logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../views/admin/assets/uploads/logo/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileInfo = pathinfo($_FILES['shop_logo']['name']);
            $fileName = 'logo_' . time() . '.' . $fileInfo['extension'];
            $uploadPath = $uploadDir . $fileName;
            
            // Validate file
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            if (in_array($_FILES['shop_logo']['type'], $allowedTypes) && $_FILES['shop_logo']['size'] <= $maxSize) {
                if (move_uploaded_file($_FILES['shop_logo']['tmp_name'], $uploadPath)) {
                    // Update logo path using helper function
                    require_once __DIR__ . '/../views/admin/helpers/logo_helper.php';
                    updateLogoPath($fileName);
                } else {
                    $_SESSION['error'] = 'Gagal mengupload logo';
                }
            } else {
                $_SESSION['error'] = 'Format file tidak didukung atau ukuran file terlalu besar (max 2MB)';
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
    
    public function updateSetting($key, $value) {
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