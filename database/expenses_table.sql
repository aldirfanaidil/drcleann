-- Table structure for expenses
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `expense_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_expense_date` (`expense_date`),
  KEY `idx_created_by` (`created_by`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default expense categories
INSERT INTO `expenses` (`category`, `description`, `amount`, `expense_date`, `created_by`) VALUES
('Gaji', 'Gaji karyawan bulanan', 0, CURDATE(), 1),
('Sabun', 'Pembelian sabun cuci sepatu', 0, CURDATE(), 1),
('Listrik', 'Tagihan listrik bulanan', 0, CURDATE(), 1),
('Sewa', 'Biaya sewa tempat', 0, CURDATE(), 1),
('Lainnya', 'Pengeluaran lainnya', 0, CURDATE(), 1);

-- Create indexes for better performance
CREATE INDEX idx_expenses_date_range ON expenses(expense_date, category);