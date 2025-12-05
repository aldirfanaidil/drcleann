<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log ke file khusus
function log_upload($message) {
    file_put_contents('/tmp/upload_test.log', date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

log_upload("Upload test started");
log_upload("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
log_upload("CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
log_upload("CONTENT_LENGTH: " . ($_SERVER['CONTENT_LENGTH'] ?? 'not set'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    log_upload("POST request detected");
    log_upload("POST data: " . print_r($_POST, true));
    log_upload("FILES data: " . print_r($_FILES, true));
    
    if (isset($_FILES['test_file'])) {
        log_upload("File uploaded detected");
        log_upload("File error: " . $_FILES['test_file']['error']);
        log_upload("File name: " . $_FILES['test_file']['name']);
        log_upload("File type: " . $_FILES['test_file']['type']);
        log_upload("File size: " . $_FILES['test_file']['size']);
        log_upload("Tmp name: " . $_FILES['test_file']['tmp_name']);
        
        if ($_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/views/admin/assets/uploads/logo/';
            log_upload("Upload dir: " . $uploadDir);
            log_upload("Dir exists: " . (is_dir($uploadDir) ? 'YES' : 'NO'));
            log_upload("Dir writable: " . (is_writable($uploadDir) ? 'YES' : 'NO'));
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
                log_upload("Created directory");
            }
            
            $fileName = 'test_' . time() . '.' . pathinfo($_FILES['test_file']['name'], PATHINFO_EXTENSION);
            $uploadPath = $uploadDir . $fileName;
            log_upload("Target path: " . $uploadPath);
            
            if (move_uploaded_file($_FILES['test_file']['tmp_name'], $uploadPath)) {
                log_upload("SUCCESS: File moved to " . $uploadPath);
                log_upload("File exists: " . (file_exists($uploadPath) ? 'YES' : 'NO'));
                log_upload("File size: " . filesize($uploadPath));
            } else {
                log_upload("FAILED: move_uploaded_file failed");
                $error = error_get_last();
                if ($error) {
                    log_upload("Error: " . $error['message']);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Test</title>
</head>
<body>
    <h2>Upload Test</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="test_file" accept="image/*">
        <button type="submit">Upload</button>
    </form>
    
    <?php
    if (file_exists('/tmp/upload_test.log')) {
        echo "<h3>Log:</h3>";
        echo "<pre>" . file_get_contents('/tmp/upload_test.log') . "</pre>";
    }
    ?>
</body>
</html>