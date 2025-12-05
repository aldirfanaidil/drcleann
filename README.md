# Dr.ShoezClean - Aplikasi Web Laundry Sepatu

Aplikasi web berbasis PHP untuk manajemen laundry sepatu dengan fitur lengkap untuk customer walk-in dan admin panel.

## Fitur Utama

### 🛍️ Frontend Customer
- Form pemesanan walk-in yang sederhana dan responsif
- Input dinamis untuk jumlah pasang sepatu
- Validasi real-time dan user-friendly interface
- Desain modern dengan warna dominan putih dan aksen merah maron

### 👨‍💼 Admin Panel
- **Dashboard**: Grafik pendapatan, KPI harian/bulanan/tahunan
- **Manajemen Pesanan**: Create, read, update, delete orders
- **Pemilihan Layanan**: Dropdown dinamis untuk setiap sepatu
- **Sistem Invoice**: Print layout profesional dengan QR code
- **Laporan**: Export CSV/PDF, filter berdasarkan periode
- **Manajemen User**: Superuser dapat mengelola admin/kasir
- **Pengaturan**: Konfigurasi toko, bank, dan layanan

### 🔐 Keamanan
- SQL Injection prevention dengan PDO prepared statements
- XSS protection dengan output escaping
- CSRF token pada semua form
- Password hashing dengan Argon2ID
- Rate limiting pada login
- Activity logging dan audit trail

## Persyaratan Sistem

- **PHP**: 8.0 atau lebih tinggi
- **Database**: MySQL 5.7+ atau MariaDB 10.2+
- **Web Server**: Apache (dengan mod_rewrite) atau Nginx
- **Ekstensi PHP**: PDO, PDO_MySQL, mbstring, json, gd
- **Browser**: Modern browser dengan JavaScript enabled

## Instalasi

### 1. Clone/Download Repository

```bash
git clone https://github.com/username/drshoezclean.git
cd drshoezclean
```

### 2. Konfigurasi Database

1. Buat database baru di MySQL:
```sql
CREATE DATABASE drshoezclean_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import database schema:
```bash
mysql -u root -p drshoezclean_db < database/schema.sql
```

3. (Opsional) Import sample data:
```bash
mysql -u root -p drshoezclean_db < database/sample_data.sql
```

### 3. Konfigurasi Aplikasi

1. Edit file `config/config.php`:
```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'drshoezclean_db');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');

// Application Configuration
define('APP_URL', 'http://localhost/drshoezclean');
```

### 4. Set Permissions

```bash
chmod -R 755 .
chmod -R 777 uploads/
chmod -R 777 logs/
```

### 5. Konfigurasi Web Server

#### Apache
Pastikan mod_rewrite enabled dan `.htaccess` dapat bekerja:
```apache
<Directory "/var/www/html/drshoezclean">
    AllowOverride All
    Require all granted
</Directory>
```

#### Nginx
Tambahkan konfigurasi berikut:
```nginx
location /drshoezclean/ {
    try_files $uri $uri/ /drshoezclean/index.php?$query_string;
}
```

### 6. Akses Aplikasi

Buka browser dan akses:
- **Frontend Customer**: `http://localhost/drshoezclean/`
- **Admin Panel**: `http://localhost/drshoezclean/login.php`

## Akun Default

### Superuser
- **Username**: aldi
- **Password**: parcom75777

- **Username**: risma  
- **Password**: risma@2025

⚠️ **Penting**: Ganti password default setelah first login!

## Struktur Database

### Tabel Utama
- `users` - Manajemen user (superuser, admin, kasir)
- `orders` - Data pesanan customer
- `order_items` - Detail item per sepatu
- `payments` - Riwayat pembayaran
- `settings` - Konfigurasi sistem
- `activity_logs` - Log aktivitas user
- `login_attempts` - Monitoring login attempts

### View
- `monthly_revenue` - Laporan pendapatan bulanan
- `daily_stats` - Statistik harian

## Daftar Layanan & Harga

### DEEP CLEAN EXPRESS (1 hari)
- Silver: Rp 33.000
- Gold: Rp 35.000  
- Platinum: Rp 38.000
- White Shoes: Rp 40.000

### DEEP CLEAN REGULER (3-4 hari)
- Silver: Rp 19.000
- Gold: Rp 22.000
- Platinum: Rp 25.000
- White Shoes: Rp 26.000

### FAST CLEAN EXPRESS (1 hari)
- Silver: Rp 27.000
- Gold: Rp 29.000
- Platinum: Rp 31.000
- White Shoes: Rp 33.000

### UNYELLOWING (4-6 hari)
- Platinum: Rp 37.000
- Premium: Rp 40.000

### RECOLOUR (7-10 hari)
- Platinum: Rp 88.000
- Premium: Rp 115.000

### REPAINT (7-10 hari)
- Platinum: Rp 86.000
- Premium: Rp 110.000

## API Endpoints

### Authentication
- `POST /login.php?action=login` - User login
- `GET /login.php?action=logout` - User logout

### Orders
- `POST /index.php?action=create_order` - Create new order
- `GET /admin/index.php?page=order_detail&id={id}` - Get order details
- `POST /admin/index.php?page=order_edit&id={id}` - Update order
- `DELETE /admin/index.php?page=user_delete` - Delete order (superuser only)

### Reports
- `GET /admin/index.php?page=reports&action=getDashboardData` - Get dashboard data
- `GET /admin/index.php?page=reports&action=generateReport` - Generate report

## Keamanan

### Implementasi Keamanan
1. **SQL Injection**: Semua query menggunakan PDO prepared statements
2. **XSS Prevention**: Output escaping dengan `htmlspecialchars()`
3. **CSRF Protection**: Token pada semua form yang melakukan perubahan data
4. **Password Security**: Hashing dengan Argon2ID
5. **Rate Limiting**: Maksimal 5 percobaan login dalam 15 menit
6. **Session Security**: Secure cookie flags dan session timeout
7. **Input Validation**: Server-side validation untuk semua input
8. **File Upload**: Validasi MIME type dan ukuran file

### Security Headers
- `X-Frame-Options: DENY`
- `X-Content-Type-Options: nosniff`
- `X-XSS-Protection: 1; mode=block`
- `Content-Security-Policy` dengan whitelist yang ketat

## Troubleshooting

### Common Issues

#### 1. Database Connection Error
```bash
# Check MySQL service
sudo systemctl status mysql

# Check credentials in config/config.php
```

#### 2. 404 Errors
```bash
# Enable Apache mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# Check .htaccess permissions
```

#### 3. File Upload Issues
```bash
# Set proper permissions
chmod -R 777 uploads/
chmod -R 777 logs/
```

#### 4. Session Issues
```bash
# Check session save path
php -i | grep session.save_path

# Set proper permissions
chmod 777 /var/lib/php/sessions
```

## Customization

### Menambah Layanan Baru
Edit file `config/config.php` pada array `$SERVICES`:

```php
$SERVICES['NEW_SERVICE'] = [
    'name' => 'Nama Layanan Baru',
    'prices' => [
        'Silver' => 25000,
        'Gold' => 30000,
        'Platinum' => 35000
    ]
];
```

### Mengubah Warna Tema
Edit CSS variables di file view:
```css
:root {
    --primary-color: #7B2C2C;
    --secondary-color: #800000;
}
```

## Backup & Maintenance

### Database Backup
```bash
# Full backup
mysqldump -u root -p drshoezclean_db > backup_$(date +%Y%m%d).sql

# Restore
mysql -u root -p drshoezclean_db < backup_20241117.sql
```

### Log Rotation
Setup log rotation untuk `logs/` directory:
```bash
# Add to /etc/logrotate.d/drshoezclean
/path/to/drshoezclean/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
}
```

## Performance Optimization

### Database Indexing
Schema sudah include optimal indexes untuk query performance.

### Caching
Consider implementing Redis/Memcached untuk:
- Session storage
- Dashboard cache
- Report cache

### CDN
Upload static assets ke CDN untuk production environment.

## Support

Untuk issues dan pertanyaan:
1. Check troubleshooting section
2. Review error logs di `logs/` directory
3. Check web server error logs
4. Verify database connection

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Changelog

### v1.0.0 (2024-11-17)
- Initial release
- Complete CRUD functionality
- Admin dashboard with charts
- Invoice printing system
- User management
- Security implementation
- Export functionality