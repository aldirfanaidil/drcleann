<?php
class Expense {
    private $db;
    private $table = 'expenses';

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (category, description, amount, expense_date, notes, created_by) 
                  VALUES (:category, :description, :amount, :expense_date, :notes, :created_by)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':expense_date', $data['expense_date']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':created_by', $data['created_by']);
        
        return $stmt->execute();
    }

    public function getAll($limit = 50, $offset = 0) {
        $query = "SELECT e.*, u.username as created_by_name 
                  FROM " . $this->table . " e
                  LEFT JOIN users u ON e.created_by = u.id
                  ORDER BY e.expense_date DESC, e.created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT e.*, u.username as created_by_name 
                  FROM " . $this->table . " e
                  LEFT JOIN users u ON e.created_by = u.id
                  WHERE e.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET category = :category, description = :description, amount = :amount, 
                      expense_date = :expense_date, notes = :notes
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':expense_date', $data['expense_date']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getTotalByDateRange($startDate, $endDate) {
        $query = "SELECT SUM(amount) as total, category 
                  FROM " . $this->table . " 
                  WHERE expense_date BETWEEN :start_date AND :end_date
                  GROUP BY category";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDailyExpenses($days = 30) {
        $query = "SELECT 
                    DATE(expense_date) as date,
                    SUM(amount) as total,
                    COUNT(*) as count
                  FROM " . $this->table . " 
                  WHERE expense_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                  GROUP BY DATE(expense_date)
                  ORDER BY date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyExpenses($months = 12) {
        $query = "SELECT 
                    DATE_FORMAT(expense_date, '%Y-%m') as month,
                    SUM(amount) as total,
                    COUNT(*) as count
                  FROM " . $this->table . " 
                  WHERE expense_date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
                  GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
                  ORDER BY month DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories() {
        $query = "SELECT DISTINCT category FROM " . $this->table . " ORDER BY category";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}