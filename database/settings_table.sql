-- Create settings table if not exists
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('shop_name', 'Dr.ShoezClean'),
('shop_phone', ''),
('shop_address', ''),
('shop_email', ''),
('shop_website', ''),
('bank_name', ''),
('bank_account', ''),
('bank_holder', ''),
('qr_code_path', ''),
('duration_deep_clean_express', '1'),
('duration_deep_clean_reguler', '3'),
('duration_fast_clean_express', '1'),
('duration_unyellowing', '5'),
('duration_recolour', '7'),
('duration_repaint', '7');