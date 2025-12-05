# Dr.ShoezClean Installation Guide

## Quick Start

### Prerequisites
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.2+
- Apache with mod_rewrite or Nginx
- Composer (optional, for dependency management)

### Step 1: Setup Database

```sql
-- Create database
CREATE DATABASE drshoezclean_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (optional)
CREATE USER 'drshoezclean'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON drshoezclean_db.* TO 'drshoezclean'@'localhost';
FLUSH PRIVILEGES;
```

### Step 2: Import Database

```bash
# Import schema
mysql -u root -p drshoezclean_db < database/schema.sql

# Import sample data (optional)
mysql -u root -p drshoezclean_db < database/sample_data.sql
```

### Step 3: Configure Application

Edit `config/config.php`:

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'drshoezclean_db');
define('DB_USER', 'drshoezclean'); // Your database username
define('DB_PASS', 'your_password'); // Your database password

// Application Configuration
define('APP_URL', 'http://localhost/drshoezclean'); // Update to your domain
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('LOG_PATH', __DIR__ . '/../logs/');
```

### Step 4: Set File Permissions

```bash
# Set basic permissions
chmod -R 755 .

# Set writable permissions for uploads and logs
chmod -R 777 uploads/
chmod -R 777 logs/

# Ensure .htaccess is readable
chmod 644 .htaccess
```

### Step 5: Configure Web Server

#### Apache Configuration

Create `/etc/apache2/sites-available/drshoezclean.conf`:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/drshoezclean
    
    <Directory /var/www/html/drshoezclean>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/drshoezclean_error.log
    CustomLog ${APACHE_LOG_DIR}/drshoezclean_access.log combined
</VirtualHost>
```

Enable the site:
```bash
sudo a2ensite drshoezclean
sudo a2enmod rewrite
sudo systemctl reload apache2
```

#### Nginx Configuration

Add to your Nginx server block:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/drshoezclean;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### Step 6: Verify Installation

1. Open browser: `http://your-domain.com/`
2. Should see customer order form
3. Access admin: `http://your-domain.com/login.php`
4. Login with default credentials:
   - Username: `aldi`
   - Password: `parcom75777`

### Step 7: Post-Installation

1. **Change Default Passwords**
   - Login as superuser
   - Go to Settings → Change Password
   - Update all default accounts

2. **Upload Logo & QR Code**
   - Go to Settings → Media
   - Upload shop logo and payment QR code

3. **Configure Shop Information**
   - Update shop name, address, phone
   - Set bank account details

4. **Test Workflow**
   - Create test order from customer form
   - Process order in admin panel
   - Print invoice to verify layout

## Troubleshooting

### Database Connection Failed

**Error**: `Connection error: Access denied for user`

**Solution**:
1. Verify database credentials in `config/config.php`
2. Check database server is running: `sudo systemctl status mysql`
3. Test connection manually: `mysql -u username -p -h localhost drshoezclean_db`

### 404 Not Found Errors

**Error**: Pages not found except homepage

**Solution**:
1. Enable mod_rewrite: `sudo a2enmod rewrite`
2. Check `.htaccess` exists and is readable
3. Verify Apache allows `.htaccess`: `AllowOverride All`
4. Check file permissions: `chmod 644 .htaccess`

### File Upload Issues

**Error**: Cannot upload logo/QR code

**Solution**:
1. Check permissions: `ls -la uploads/`
2. Set proper permissions: `chmod -R 777 uploads/`
3. Check PHP upload limits in `php.ini`:
   ```ini
   upload_max_filesize = 2M
   post_max_size = 8M
   max_file_uploads = 20
   ```

### Session Issues

**Error**: Cannot login, session not working

**Solution**:
1. Check session save path: `php -i | grep session.save_path`
2. Set proper permissions: `chmod 777 /var/lib/php/sessions`
3. Check cookie settings in browser
4. Verify session path is writable

### Blank White Pages

**Error**: PHP errors not showing

**Solution**:
1. Enable error reporting in `config/config.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
2. Check PHP error log: `tail -f /var/log/php_errors.log`
3. Check web server error log

### Performance Issues

**Symptoms**: Slow page loads

**Solutions**:
1. Enable PHP OPcache
2. Add database indexes if missing
3. Optimize images and assets
4. Consider CDN for static files

## Security Hardening

### Production Checklist

1. **Change Default Passwords**
   ```bash
   # Update in database or via admin panel
   UPDATE users SET password_hash = '$2y$10$...' WHERE username IN ('aldi', 'risma');
   ```

2. **File Permissions**
   ```bash
   # Restrict access to sensitive files
   chmod 600 config/config.php
   chmod 644 .htaccess
   ```

3. **HTTPS Setup**
   - Install SSL certificate (Let's Encrypt recommended)
   - Force HTTPS in `.htaccess`:
   ```apache
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

4. **PHP Security**
   ```ini
   ; Disable dangerous functions
   disable_functions = exec,passthru,shell_exec,system,proc_open,popen
   
   ; Hide PHP version
   expose_php = Off
   
   ; Secure session cookies
   session.cookie_httponly = 1
   session.cookie_secure = 1
   session.use_only_cookies = 1
   ```

5. **Database Security**
   ```sql
   -- Remove test database
   DROP DATABASE IF EXISTS test;
   
   -- Remove anonymous users
   DELETE FROM mysql.user WHERE User='';
   
   -- Restrict database user permissions
   REVOKE ALL PRIVILEGES ON *.* FROM 'drshoezclean'@'localhost';
   GRANT SELECT, INSERT, UPDATE, DELETE ON drshoezclean_db.* TO 'drshoezclean'@'localhost';
   ```

## Backup Strategy

### Automated Database Backup

Create `/etc/cron.daily/drshoezclean-backup`:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/drshoezclean"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="drshoezclean_db"
DB_USER="drshoezclean"
DB_PASS="your_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Create database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Remove backups older than 30 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete

# Backup files
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/html/drshoezclean/uploads/
```

Make it executable:
```bash
sudo chmod +x /etc/cron.daily/drshoezclean-backup
```

### File Backup

```bash
# Backup application files
tar -czf drshoezclean_files_$(date +%Y%m%d).tar.gz \
    --exclude='logs/*' \
    --exclude='uploads/*' \
    /var/www/html/drshoezclean/
```

## Monitoring

### Log Monitoring

Monitor these log files:
- Application logs: `logs/php_errors.log`
- Web server logs: `/var/log/apache2/error.log`
- Database logs: `/var/log/mysql/error.log`

### Health Check Script

Create `health_check.php`:

```php
<?php
// Database connection test
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    echo "Database: OK\n";
} catch (PDOException $e) {
    echo "Database: FAILED - " . $e->getMessage() . "\n";
}

// Upload directory test
if (is_writable(UPLOAD_PATH)) {
    echo "Upload directory: OK\n";
} else {
    echo "Upload directory: FAILED\n";
}

// Log directory test
if (is_writable(LOG_PATH)) {
    echo "Log directory: OK\n";
} else {
    echo "Log directory: FAILED\n";
}
?>
```

Run health check:
```bash
php health_check.php
```

## Support

For additional support:
1. Check this installation guide
2. Review the main README.md
3. Check application logs
4. Verify system requirements
5. Test with fresh database if needed