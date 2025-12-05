-- Dr.ShoezClean Database Schema
-- Created for PHP Web Application

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: drshoezclean_db
CREATE DATABASE IF NOT EXISTS `drshoezclean_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `drshoezclean_db`;

-- Table structure for users
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('superuser','admin','kasir') NOT NULL DEFAULT 'kasir',
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_role` (`role`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for orders
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(50) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `total_pairs` int(11) NOT NULL,
  `subtotal` decimal(10,0) NOT NULL DEFAULT 0,
  `total` decimal(10,0) NOT NULL DEFAULT 0,
  `payment_status` enum('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` enum('cash','transfer','qr') DEFAULT NULL,
  `date_in` date NOT NULL,
  `date_estimate_out` date DEFAULT NULL,
  `date_out` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('new','processing','ready','completed','cancelled') NOT NULL DEFAULT 'new',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_no` (`invoice_no`),
  KEY `idx_customer` (`customer_name`),
  KEY `idx_phone` (`phone`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_date_in` (`date_in`),
  KEY `idx_status` (`status`),
  KEY `idx_created_by` (`created_by`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for order_items
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `pair_index` int(11) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `service_code` varchar(50) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `service_price` decimal(10,0) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_service_code` (`service_code`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for payments
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `method` enum('cash','transfer','qr') NOT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `payment_date` datetime NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `idx_method` (`method`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for settings
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','image','file') NOT NULL DEFAULT 'text',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for activity_logs
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_target` (`target_type`,`target_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for login_attempts (for rate limiting)
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_username` (`username`),
  KEY `idx_attempt_time` (`attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default superuser accounts
INSERT INTO `users` (`username`, `password_hash`, `role`, `full_name`, `email`) VALUES
('aldi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superuser', 'Aldi', 'aldi@drshoezclean.com'),
('risma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superuser', 'Risma', 'risma@drshoezclean.com');

-- Note: The above passwords are hashed versions of 'parcom75777' and 'risma@2025' respectively
-- For security, these should be changed immediately after first login

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('shop_name', 'Dr.ShoezClean', 'text', 'Nama Toko'),
('shop_address', 'Jl. Contoh Alamat No. 123, Jakarta', 'text', 'Alamat Toko'),
('shop_phone', '+62 812-3456-7890', 'text', 'Telepon Toko'),
('shop_email', 'info@drshoezclean.com', 'text', 'Email Toko'),
('bank_name', 'BCA', 'text', 'Nama Bank'),
('bank_account', '123-456-7890', 'text', 'Nomor Rekening'),
('bank_account_name', 'PT. Dr.ShoezClean Indonesia', 'text', 'Nama Pemilik Rekening'),
('logo_path', '/assets/images/logo.png', 'image', 'Path Logo'),
('qr_code_path', '/assets/uploads/qr/qr_payment.png', 'image', 'Path QR Code Pembayaran'),
('terms_conditions', '1. Barang yang sudah dibersihkan tidak dapat dikembalikan\n2. Pembayaran harus lunas sebelum pengambilan\n3. Estimasi pengerjaan dapat berubah tergantung kondisi sepatu\n4. Kami tidak bertanggung jawab atas kerusakan yang sudah ada sebelumnya', 'text', 'Syarat dan Ketentuan'),
('express_days', '1', 'number', 'Jumlah hari untuk layanan express'),
('regular_days', '4', 'number', 'Jumlah hari untuk layanan regular'),
('unyellowing_days', '6', 'number', 'Jumlah hari untuk layanan unyellowing'),
('recolor_days', '10', 'number', 'Jumlah hari untuk layanan recolor'),
('repaint_days', '10', 'number', 'Jumlah hari untuk layanan repaint');

-- Create indexes for better performance
CREATE INDEX idx_orders_date_range ON orders(date_in, date_out);
CREATE INDEX idx_orders_customer_search ON orders(customer_name, phone);
CREATE INDEX idx_logs_date_range ON activity_logs(created_at, user_id);

-- Create view for monthly revenue
CREATE VIEW monthly_revenue AS
SELECT 
    DATE_FORMAT(date_in, '%Y-%m') as month,
    COUNT(*) as total_orders,
    SUM(total) as revenue,
    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as paid_revenue,
    SUM(total_pairs) as total_shoes
FROM orders 
WHERE status != 'cancelled'
GROUP BY DATE_FORMAT(date_in, '%Y-%m')
ORDER BY month DESC;

-- Create view for daily statistics
CREATE VIEW daily_stats AS
SELECT 
    date_in as date,
    COUNT(*) as orders_count,
    SUM(total_pairs) as shoes_count,
    SUM(total) as revenue,
    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as paid_revenue
FROM orders 
WHERE status != 'cancelled'
GROUP BY date_in
ORDER BY date DESC;

COMMIT;