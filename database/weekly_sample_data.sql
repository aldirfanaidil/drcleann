-- Additional Sample Data for Weekly Chart Testing
USE `drshoezclean_db`;

-- Get the current week's dates and add sample data for each day
INSERT INTO `orders` (`invoice_no`, `customer_name`, `phone`, `total_pairs`, `subtotal`, `total`, `payment_status`, `payment_method`, `date_in`, `date_estimate_out`, `date_out`, `status`, `created_by`) VALUES
-- Monday
('INV-20241202001', 'Rudi Hartono', '+62 812-1111-3333', 1, 38000, 38000, 'paid', 'cash', DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), 'completed', 1),
-- Tuesday  
('INV-20241203001', 'Lisa Permata', '+62 813-2222-4444', 2, 71000, 71000, 'paid', 'qr', DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-1 DAY), DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-1 DAY), DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-1 DAY), 'completed', 2),
-- Wednesday
('INV-20241204001', 'Doni Prakoso', '+62 814-3333-5555', 1, 25000, 25000, 'paid', 'transfer', DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-2 DAY), DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-2 DAY), DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-2 DAY), 'completed', 1),
-- Thursday
('INV-20241205001', 'Siti Nurjanah', '+62 815-4444-6666', 3, 132000, 132000, 'paid', 'cash', DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-3 DAY), DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-3 DAY), DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-3 DAY), 'completed', 2),
-- Friday
('INV-20241206001', 'Bambang Sutrisno', '+62 816-5555-7777', 1, 40000, 40000, 'paid', 'qr', DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-4 DAY), DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-4 DAY), DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-4 DAY), 'completed', 1);

-- Insert corresponding order items
INSERT INTO `order_items` (`order_id`, `pair_index`, `brand`, `service_code`, `service_name`, `service_price`) VALUES
-- Monday order
(7, 1, 'Nike Air Jordan', 'DEEP_CLEAN_EXPRESS', 'Deep Clean Express (1 hari)', 38000),
-- Tuesday order
(8, 1, 'Adidas Yeezy', 'DEEP_CLEAN_EXPRESS', 'Deep Clean Express (1 hari)', 38000),
(8, 2, 'Puma RS-X', 'FAST_CLEAN_EXPRESS', 'Fast Clean (cuci luar saja) Express 1 hari', 33000),
-- Wednesday order
(9, 1, 'Converse CT70', 'DEEP_CLEAN_REGULER', 'Deep Clean Reguler (3-4 hari)', 25000),
-- Thursday order
(10, 1, 'Vans SK8', 'DEEP_CLEAN_EXPRESS', 'Deep Clean Express (1 hari)', 35000),
(10, 2, 'New Balance 990', 'DEEP_CLEAN_REGULER', 'Deep Clean Reguler (3-4 hari)', 22000),
(10, 3, 'Jordan 4 Retro', 'UNYELLOWING', 'Unyellowing (4-6 hari)', 40000),
-- Friday order
(11, 1, 'Reebok Classic', 'DEEP_CLEAN_EXPRESS', 'Deep Clean Express (1 hari)', 40000);

-- Insert payments for these orders
INSERT INTO `payments` (`order_id`, `amount`, `method`, `reference_no`, `payment_date`, `created_by`) VALUES
(7, 38000, 'cash', NULL, DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), 1),
(8, 71000, 'qr', 'QRIS-WEEK-001', DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-1 DAY), 2),
(9, 25000, 'transfer', 'TRF-WEEK-001', DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-2 DAY), 1),
(10, 132000, 'cash', NULL, DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-3 DAY), 2),
(11, 40000, 'qr', 'QRIS-WEEK-002', DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE())-4 DAY), 1);