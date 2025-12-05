<?php
/**
 * Get logo path - automatically finds the latest uploaded logo
 * @return string Path to logo image
 */
function getLogoPath() {
    $logoDir = __DIR__ . '/../assets/uploads/logo/';
    
    // Check if logo directory exists
    if (is_dir($logoDir)) {
        // Get all logo files and sort by modification time (newest first)
        $logoFiles = glob($logoDir . '*.{jpg,jpeg,png,gif,svg,webp}', GLOB_BRACE);
        
        if (!empty($logoFiles)) {
            // Sort files by modification time (newest first)
            usort($logoFiles, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            // Get the newest file
            $latestLogo = $logoFiles[0];
            $relativePath = '/drshoezclean/views/admin/assets/uploads/logo/' . basename($latestLogo);
            
            // Verify file exists and is readable
            if (file_exists($latestLogo) && is_readable($latestLogo)) {
                return $relativePath;
            }
        }
    }
    
    // Fallback to default logo
    return '../assets/images/logo.png';
}

/**
 * Get logo alt text consistently
 * @return string Alt text for logo
 */
function getLogoAlt() {
    return 'Dr.ShoezClean Logo';
}

/**
 * Update logo path setting after upload (called by upload handler)
 * @param string $filename The uploaded filename
 * @return bool Success status
 */
function updateLogoPath($filename) {
    if (empty($filename)) {
        return false;
    }
    
    $logoPath = '../assets/uploads/logo/' . $filename;
    
    // Update in database if settings table exists
    try {
        require_once __DIR__ . '/../../../core/Database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if setting exists
        $stmt = $db->prepare("SELECT id FROM settings WHERE setting_key = 'shop_logo_path'");
        $stmt->execute();
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($exists) {
            // Update existing setting
            $stmt = $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'shop_logo_path'");
            $stmt->execute([$logoPath]);
        } else {
            // Insert new setting
            $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('shop_logo_path', ?)");
            $stmt->execute([$logoPath]);
        }
        
        return true;
    } catch (Exception $e) {
        error_log('Failed to update logo path: ' . $e->getMessage());
        return false;
    }
}
?>