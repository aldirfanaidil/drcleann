<?php
// Debug upload script
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Upload Debug Results:</h2>";
    echo "<pre>";
    echo "FILES: " . print_r($_FILES, true) . PHP_EOL;
    echo "POST: " . print_r($_POST, true) . PHP_EOL;
    
    if (isset($_FILES['test_file'])) {
        echo "Upload Error Code: " . $_FILES['test_file']['error'] . PHP_EOL;
        echo "Upload Error Message: ";
        switch ($_FILES['test_file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                echo "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                echo "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                echo "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                echo "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                echo "File upload stopped by extension";
                break;
            default:
                echo "Unknown upload error";
                break;
        }
        echo PHP_EOL;
        
        // Test directory creation and permissions
        $uploadDir = __DIR__ . '/views/admin/assets/uploads/logo/';
        echo "Upload Dir: " . $uploadDir . PHP_EOL;
        echo "Dir exists: " . (is_dir($uploadDir) ? 'YES' : 'NO') . PHP_EOL;
        echo "Dir writable: " . (is_writable($uploadDir) ? 'YES' : 'NO') . PHP_EOL;
        
        if (!is_dir($uploadDir)) {
            echo "Creating directory..." . PHP_EOL;
            if (mkdir($uploadDir, 0755, true)) {
                echo "Directory created successfully" . PHP_EOL;
            } else {
                echo "Failed to create directory" . PHP_EOL;
            }
        }
        
        // Test file move
        if ($_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
            $testPath = $uploadDir . 'test_' . time() . '.jpg';
            echo "Attempting to move to: " . $testPath . PHP_EOL;
            if (move_uploaded_file($_FILES['test_file']['tmp_name'], $testPath)) {
                echo "File moved successfully!" . PHP_EOL;
                echo "File exists: " . (file_exists($testPath) ? 'YES' : 'NO') . PHP_EOL;
            } else {
                echo "Failed to move file" . PHP_EOL;
            }
        }
    }
    echo "</pre>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Debug</title>
</head>
<body>
    <h2>Test Upload</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="test_file" accept="image/*">
        <button type="submit">Upload</button>
    </form>
</body>
</html>