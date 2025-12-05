-- Sample Data for Dashboard Testing - December 2024
-- This file contains sample data for testing dashboard functionality

USE `drshoezclean_db`;

-- Insert sample orders for December 2024
INSERT INTO `orders` (`invoice_no`, `customer_name`, `phone`, `total_pairs`, `subtotal`, `total`, `payment_status`, `payment_method`, `date_in`, `date_estimate_out`, `date_out`, `status`, `created_by`) VALUES
('INV-20241201001', 'Andi Wijaya', '+62 812-1111-2222', 2, 71000, 71000, 'paid', 'cash', CURDATE(), CURDATE(), CURDATE(), 'completed', 1),
('INV-20241201002', 'Siti Aminah', '+62 813-3333-4444', 1, 38000, 38000, 'paid', 'qr', CURDATE(), CURDATE(), CURDATE(), 'completed', 1),
('INV-20241201003', 'Budi Pratama', '+62 814-5555-6666', 3, 132000, 132000, 'paid', 'transfer', CURDATE(), CURDATE(), CURDATE(), 'completed', 2),
('INV-20241201004', 'Dewi Ratnasari', '+62 815-7777-8888', 1, 25000, 25000, 'pending', NULL, CURDATE(), CURDATE() + INTERVAL 3 DAY, NULL, 'processing', 1),
('INV-20241130001', 'Ahmad Hidayat', '+62 816-9999-0000', 2, 66000, 66000, 'paid', 'cash', CURDATE() - INTERVAL 1 DAY, CURDATE() - INTERVAL 1 DAY, CURDATE() - INTERVAL 1 DAY, 'completed', 2),
('INV-20241130002', 'Rina Susanti', '+62 817-1111-2222', 1, 40000, 40000, 'paid', 'qr', CURDATE() - INTERVAL 1 DAY, CURDATE() - INTERVAL 1 DAY, CURDATE() - INTERVAL 1 DAY, 'completed', 1);

-- Insert sample order items
INSERT INTO `order_items` (`order_id`, `pair_index`, `brand`, `service_code`, `service_name`, `service_price`) VALUES
-- For order INV-20241201001
(1, 1, 'Nike Air Max', 'DEEP_CLEAN_EXPRESS', 'Deep Clean Express (1 hari)', 38000),
(1, 2, 'Adidas Ultraboost', 'FAST_CLEAN_EXPRESS', 'Fast Clean (cuci luar saja) Express 1 hari', 33000),
-- For order INV-20241201002
(2, 1, 'Converse Chuck Taylor', 'DEEP_CLEAN_EXPRESS', 'Deep Clean Express (1 hari)', 38000),
-- For order INV-20241201003
(3, 1, 'Vans Old Skool', 'DEEP_CLEAN_REGULER', 'Deep Clean Reguler (3-4 hari)', 25000),
(3, 2, 'New Balance 574', 'DEEP_CLEAN_REGULER', 'Deep Clean Reguler (3-4 hari)', 22000),
(3, 3, 'Jordan 1 Retro', 'UNYELLOWING', 'Unyellowing (4-6 hari)', 40000),
-- For order INV-20241201004
(4, 1, 'Reebok Classic', 'DEEP_CLEAN_REGULER', 'Deep Clean Reguler (3-4 hari)', 25000),
-- For order INV-20241130001
(5, 1, 'Puma Suede', 'DEEP_CLEAN_EXPRESS', 'Deep Clean Express (1 hari)', 35000),
(5, 2, 'Fila Disruptor', 'FAST_CLEAN_EXPRESS', 'Fast Clean (cuci luar saja) Express 1 hari', 31000),
-- For order INV-20241130002
(6, 1, 'Sketchers Go Walk', 'DEEP_CLEAN_EXPRESS', 'Deep Clean Express (1 hari)', 40000);

-- Insert sample payments
INSERT INTO `payments` (`order_id`, `amount`, `method`, `reference_no`, `payment_date`, `created_by`) VALUES
(1, 71000, 'cash', NULL, NOW(), 1),
(2, 38000, 'qr', 'QRIS-20241201-001', NOW(), 1),
(3, 132000, 'transfer', 'TRF-20241201-001', NOW(), 2),
(5, 66000, 'cash', NULL, NOW() - INTERVAL 1 DAY, 2),
(6, 40000, 'qr', 'QRIS-20241130-001', NOW() - INTERVAL 1 DAY, 1);

-- Insert sample activity logs
INSERT INTO `activity_logs` (`user_id`, `action`, `target_type`, `target_id`, `ip_address`, `user_agent`, `details`) VALUES
(1, 'create_order', 'order', 1, '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'Created order INV-20241201001'),
(1, 'payment_received', 'order', 1, '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'Payment received for order INV-20241201001'),
(1, 'create_order', 'order', 2, '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'Created order INV-20241201002'),
(1, 'payment_received', 'order', 2, '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'Payment received for order INV-20241201002'),
(2, 'create_order', 'order', 3, '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'Created order INV-20241201003'),
(2, 'payment_received', 'order', 3, '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'Payment received for order INV-20241201003'),
(1, 'create_order', 'order', 4, '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'Created order INV-20241201004');