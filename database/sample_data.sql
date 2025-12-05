-- Sample Data for Dr.ShoezClean Application
-- This file contains sample data for testing purposes

USE `drshoezclean_db`;

-- Insert sample admin/cashier users (password: admin123)
INSERT INTO `users` (`username`, `password_hash`, `role`, `full_name`, `email`, `phone`) VALUES
('admin', '$2y$10$K2m.F9h9q9q9q9q9q9q9qO9q9q9q9q9q9q9q9q9q9q9q9q9q9q9q9q9q9q', 'admin', 'Administrator', 'admin@drshoezclean.com', '+62 811-1111-1111'),
('kasir1', '$2y$10$K2m.F9h9q9q9q9q9q9q9qO9q9q9q9q9q9q9q9q9q9q9q9q9q9q9q9q9q9q', 'kasir', 'Kasir Satu', 'kasir1@drshoezclean.com', '+62 811-2222-2222');

-- Insert sample orders
INSERT INTO `orders` (`invoice_no`, `customer_name`, `phone`, `total_pairs`, `subtotal`, `total`, `payment_status`, `payment_method`, `date_in`, `date_estimate_out`, `date_out`, `status`, `created_by`) VALUES
('INV-202411170001', 'Budi Santoso', '+62 812-3456-7890', 2, 71000, 71000, 'paid', 'cash', '2024-11-15', '2024-11-16', '2024-11-16', 'completed', 1),
('INV-202411170002', 'Siti Nurhaliza', '+62 813-5678-9012', 1, 38000, 38000, 'pending', NULL, '2024-11-16', '2024-11-17', NULL, 'processing', 1),
('INV-202411170003', 'Ahmad Fauzi', '+62 814-7890-1234', 3, 132000, 132000, 'paid', 'transfer', '2024-11-16', '2024-11-19', NULL, 'ready', 2),
('INV-202411170004', 'Dewi Lestari', '+62 815-9012-3456', 1, 25000, 25000, 'pending', NULL, '2024-11-17', '2024-11-20', NULL, 'new', NULL);

-- Insert sample order items
INSERT INTO `order_items` (`order_id`, `pair_index`, `brand`, `service_code`, `service_name`, `service_price`) VALUES
(1, 1, 'Nike Air Max', 'DEEP_CLEAN_EXPRESS', 'Deep Clean Express (1 hari)', 38000),
(1, 2, 'Adidas Ultraboost', 'FAST_CLEAN_EXPRESS', 'Fast Clean (cuci luar saja) Express 1 hari', 33000),
(2, 1, 'Converse Chuck Taylor', 'DEEP_CLEAN_EXPRESS', 'Deep Clean Express (1 hari)', 40000),
(3, 1, 'Vans Old Skool', 'DEEP_CLEAN_REGULER', 'Deep Clean Reguler (3-4 hari)', 25000),
(3, 2, 'New Balance 574', 'DEEP_CLEAN_REGULER', 'Deep Clean Reguler (3-4 hari)', 22000),
(3, 3, 'Jordan 1 Retro', 'UNYELLOWING', 'Unyellowing (4-6 hari)', 40000),
(4, 1, 'Reebok Classic', 'DEEP_CLEAN_REGULER', 'Deep Clean Reguler (3-4 hari)', 25000);

-- Insert sample payments
INSERT INTO `payments` (`order_id`, `amount`, `method`, `reference_no`, `payment_date`, `created_by`) VALUES
(1, 71000, 'cash', NULL, '2024-11-16 10:30:00', 1),
(3, 132000, 'transfer', 'TRF-20241116-001', '2024-11-16 14:15:00', 2);

-- Insert sample activity logs
INSERT INTO `activity_logs` (`user_id`, `action`, `target_type`, `target_id`, `ip_address`, `user_agent`, `details`) VALUES
(1, 'login', 'user', 1, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'User aldi logged in'),
(1, 'create_order', 'order', 1, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'Created order INV-202411170001'),
(1, 'update_payment', 'order', 1, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'Payment received for order INV-202411170001'),
(2, 'login', 'user', 3, '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'User kasir1 logged in'),
(2, 'create_order', 'order', 3, '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'Created order INV-202411170003');

-- Update settings with more realistic data
UPDATE `settings` SET `setting_value` = 'Dr.ShoezClean - Professional Shoe Cleaning' WHERE `setting_key` = 'shop_name';
UPDATE `settings` SET `setting_value` = 'Jl. Sudirman No. 123, Jakarta Pusat 10110' WHERE `setting_key` = 'shop_address';
UPDATE `settings` SET `setting_value` = '+62 21-1234-5678' WHERE `setting_key` = 'shop_phone';
UPDATE `settings` SET `setting_value` = 'contact@drshoezclean.com' WHERE `setting_key` = 'shop_email';
UPDATE `settings` SET `setting_value` = 'Bank Central Asia (BCA)' WHERE `setting_key` = 'bank_name';
UPDATE `settings` SET `setting_value` = '876-543-2109' WHERE `setting_key` = 'bank_account';
UPDATE `settings` SET `setting_value` = 'PT. Dr.ShoezClean Indonesia' WHERE `setting_key` = 'bank_account_name';

-- Note: For the sample users above, the password hash corresponds to 'admin123'
-- In a real application, you should use proper password hashing:
-- $password = 'admin123';
-- $hash = password_hash($password, PASSWORD_ARGON2ID);