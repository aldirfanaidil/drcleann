<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Expense.php';

class CashFlowController {
    private $order;
    private $expense;
    private $db;

    public function __construct() {
        // Check if user is superuser
        if (!isSuperuser()) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Access denied';
            exit;
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->order = new Order($this->db);
        $this->expense = new Expense($this->db);
    }

    public function index() {
        $filters = [
            'month' => $_GET['month'] ?? null,
            'year' => $_GET['year'] ?? date('Y')
        ];

        $cashFlowData = $this->getCashFlowData($filters);
        $currentFilters = $filters;
        $availableYears = $this->getAvailableYears();
        
        $summaryTitle = 'Semua Waktu';
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $summaryTitle = date('F', mktime(0, 0, 0, $filters['month'], 10)) . ' ' . $filters['year'];
        } elseif (!empty($filters['year'])) {
            $summaryTitle = $filters['year'];
        }

        include __DIR__ . '/../views/admin/cash_flow.php';
    }

    public function getCashFlowData($filters = []) {
        $data = [];
        
        // Get revenue data
        $revenueData = $this->getRevenueData($filters);
        
        // Get expense data
        $expenseData = $this->getExpenseData($filters);
        
        // Calculate profit for each period
        $data['today'] = $this->calculateProfit($revenueData['today'], $expenseData['today']);
        $data['period'] = $this->calculateProfit($revenueData['period'], $expenseData['period']);
        
        // Combine daily data
        $data['daily'] = $this->combineDailyData($revenueData['daily'], $expenseData['daily']);
        
        // Combine monthly data
        $data['monthly'] = $this->combineMonthlyData($revenueData['monthly'], $expenseData['monthly']);
        
        return $data;
    }
    
    private function getRevenueData($filters = []) {
        $data = [];
        
        // Today's revenue
        $todayQuery = "SELECT 
            SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END) as cash_in,
            SUM(CASE WHEN payment_method = 'qr' THEN total ELSE 0 END) as qr_in,
            SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END) as transfer_in,
            SUM(total) as total_in,
            COUNT(*) as total_orders
            FROM orders 
            WHERE payment_status = 'paid' 
            AND DATE(date_in) = CURDATE()";
        
        $stmt = $this->db->prepare($todayQuery);
        $stmt->execute();
        $data['today'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Filtered period revenue
        $periodQuery = "SELECT 
            SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END) as cash_in,
            SUM(CASE WHEN payment_method = 'qr' THEN total ELSE 0 END) as qr_in,
            SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END) as transfer_in,
            SUM(total) as total_in,
            COUNT(*) as total_orders
            FROM orders 
            WHERE payment_status = 'paid'";
        
        $where = [];
        $params = [];
        if (!empty($filters['year'])) {
            $where[] = "YEAR(date_in) = :year";
            $params[':year'] = $filters['year'];
        }
        if (!empty($filters['month'])) {
            $where[] = "MONTH(date_in) = :month";
            $params[':month'] = $filters['month'];
        }
        if (!empty($where)) {
            $periodQuery .= " AND " . implode(" AND ", $where);
        }

        $stmt = $this->db->prepare($periodQuery);
        $stmt->execute($params);
        $data['period'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Daily revenue for current month
        $dailyQuery = "SELECT 
            DATE(date_in) as date,
            SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END) as cash_in,
            SUM(CASE WHEN payment_method = 'qr' THEN total ELSE 0 END) as qr_in,
            SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END) as transfer_in,
            SUM(total) as total_in
            FROM orders 
            WHERE payment_status = 'paid' 
            AND DATE(date_in) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(date_in)
            ORDER BY date DESC";
        
        $stmt = $this->db->prepare($dailyQuery);
        $stmt->execute();
        $data['daily'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Monthly revenue
        $monthlyQuery = "SELECT 
            DATE_FORMAT(date_in, '%Y-%m') as month,
            SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END) as cash_in,
            SUM(CASE WHEN payment_method = 'qr' THEN total ELSE 0 END) as qr_in,
            SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END) as transfer_in,
            SUM(total) as total_in,
            COUNT(*) as total_orders
            FROM orders 
            WHERE payment_status = 'paid'
            AND date_in >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(date_in, '%Y-%m')
            ORDER BY month DESC";
        
        $stmt = $this->db->prepare($monthlyQuery);
        $stmt->execute();
        $data['monthly'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $data;
    }
    
    private function getExpenseData($filters = []) {
        $data = [];
        
        // Today's expenses
        $todayQuery = "SELECT 
            SUM(amount) as total_out,
            COUNT(*) as total_expenses
            FROM expenses 
            WHERE expense_date = CURDATE()";
        
        $stmt = $this->db->prepare($todayQuery);
        $stmt->execute();
        $data['today'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Filtered period expenses
        $periodQuery = "SELECT 
            SUM(amount) as total_out,
            COUNT(*) as total_expenses
            FROM expenses";

        $where = [];
        $params = [];
        if (!empty($filters['year'])) {
            $where[] = "YEAR(expense_date) = :year";
            $params[':year'] = $filters['year'];
        }
        if (!empty($filters['month'])) {
            $where[] = "MONTH(expense_date) = :month";
            $params[':month'] = $filters['month'];
        }
        if (!empty($where)) {
            $periodQuery .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $this->db->prepare($periodQuery);
        $stmt->execute($params);
        $data['period'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Daily expenses for current month
        $dailyQuery = "SELECT 
            DATE(expense_date) as date,
            SUM(amount) as total_out
            FROM expenses 
            WHERE expense_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(expense_date)
            ORDER BY date DESC";
        
        $stmt = $this->db->prepare($dailyQuery);
        $stmt->execute();
        $data['daily'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Monthly expenses
        $monthlyQuery = "SELECT 
            DATE_FORMAT(expense_date, '%Y-%m') as month,
            SUM(amount) as total_out,
            COUNT(*) as total_expenses
            FROM expenses 
            WHERE expense_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
            ORDER BY month DESC";
        
        $stmt = $this->db->prepare($monthlyQuery);
        $stmt->execute();
        $data['monthly'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $data;
    }
    
    private function calculateProfit($revenue, $expenses) {
        $total_in = $revenue['total_in'] ?? 0;
        $total_out = $expenses['total_out'] ?? 0;
        
        return array_merge($revenue, [
            'total_out' => $total_out,
            'total_expenses' => $expenses['total_expenses'] ?? 0,
            'profit' => $total_in - $total_out
        ]);
    }
    
    private function combineDailyData($revenueDaily, $expenseDaily) {
        $combined = [];
        
        // Add revenue data
        foreach ($revenueDaily as $day) {
            $combined[$day['date']] = $day;
            $combined[$day['date']]['total_out'] = 0;
            $combined[$day['date']]['profit'] = $day['total_in'];
        }
        
        // Add expense data
        foreach ($expenseDaily as $day) {
            if (isset($combined[$day['date']])) {
                $combined[$day['date']]['total_out'] = $day['total_out'];
                $combined[$day['date']]['profit'] = $combined[$day['date']]['total_in'] - $day['total_out'];
            } else {
                $combined[$day['date']] = [
                    'date' => $day['date'],
                    'cash_in' => 0,
                    'qr_in' => 0,
                    'transfer_in' => 0,
                    'total_in' => 0,
                    'total_out' => $day['total_out'],
                    'profit' => -$day['total_out']
                ];
            }
        }
        
        // Sort by date and convert to array
        krsort($combined);
        return array_values($combined);
    }
    
    private function combineMonthlyData($revenueMonthly, $expenseMonthly) {
        $combined = [];
        
        // Add revenue data
        foreach ($revenueMonthly as $month) {
            $combined[$month['month']] = $month;
            $combined[$month['month']]['total_out'] = 0;
            $combined[$month['month']]['total_expenses'] = 0;
            $combined[$month['month']]['profit'] = $month['total_in'];
        }
        
        // Add expense data
        foreach ($expenseMonthly as $month) {
            if (isset($combined[$month['month']])) {
                $combined[$month['month']]['total_out'] = $month['total_out'];
                $combined[$month['month']]['total_expenses'] = $month['total_expenses'];
                $combined[$month['month']]['profit'] = $combined[$month['month']]['total_in'] - $month['total_out'];
            } else {
                $combined[$month['month']] = [
                    'month' => $month['month'],
                    'cash_in' => 0,
                    'qr_in' => 0,
                    'transfer_in' => 0,
                    'total_in' => 0,
                    'total_orders' => 0,
                    'total_out' => $month['total_out'],
                    'total_expenses' => $month['total_expenses'],
                    'profit' => -$month['total_out']
                ];
            }
        }
        
        // Sort by month and convert to array
        krsort($combined);
        return array_values($combined);
    }

    private function getAvailableYears() {
        $query = "SELECT DISTINCT YEAR(date_in) as year FROM orders
                  UNION
                  SELECT DISTINCT YEAR(expense_date) as year FROM expenses
                  ORDER BY year DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Ensure current year is included
        $currentYear = date('Y');
        if (!in_array($currentYear, $years)) {
            array_unshift($years, $currentYear);
        }
        
        return $years;
    }

    public function getCashFlowChartData() {
        header('Content-Type: application/json');
        
        try {
            $period = $_GET['period'] ?? 'month';
            
            switch($period) {
                case 'week':
                    $query = "SELECT 
                        DATE_FORMAT(date_in, '%d %b') as label,
                        SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END) as cash,
                        SUM(CASE WHEN payment_method = 'qr' THEN total ELSE 0 END) as qr,
                        SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END) as transfer,
                        SUM(total) as total
                        FROM orders 
                        WHERE payment_status = 'paid' 
                        AND date_in >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        GROUP BY DATE_FORMAT(date_in, '%Y-%m-%d'), label
                        ORDER BY date_in";
                    break;
                    
                case 'month':
                    $query = "SELECT 
                        DATE_FORMAT(date_in, '%d %b') as label,
                        SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END) as cash,
                        SUM(CASE WHEN payment_method = 'qr' THEN total ELSE 0 END) as qr,
                        SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END) as transfer,
                        SUM(total) as total
                        FROM orders 
                        WHERE payment_status = 'paid' 
                        AND DATE_FORMAT(date_in, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
                        GROUP BY DATE_FORMAT(date_in, '%Y-%m-%d'), label
                        ORDER BY date_in";
                    break;
                    
                case 'year':
                    $query = "SELECT 
                        DATE_FORMAT(date_in, '%b') as label,
                        SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END) as cash,
                        SUM(CASE WHEN payment_method = 'qr' THEN total ELSE 0 END) as qr,
                        SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END) as transfer,
                        SUM(total) as total
                        FROM orders 
                        WHERE payment_status = 'paid' 
                        AND YEAR(date_in) = YEAR(CURDATE())
                        GROUP BY MONTH(date_in), label
                        ORDER BY date_in";
                    break;
                    
                default:
                    $query = "SELECT 
                        DATE_FORMAT(created_at, '%d %b') as label,
                        SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END) as cash,
                        SUM(CASE WHEN payment_method = 'qr' THEN total ELSE 0 END) as qr,
                        SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END) as transfer,
                        SUM(total) as total
                        FROM orders 
                        WHERE status = 'completed' 
                        AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
                        GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d'), label
                        ORDER BY created_at";
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format data for chart.js
            $labels = [];
            $cashData = [];
            $qrData = [];
            $transferData = [];
            $totalData = [];
            
            foreach ($results as $row) {
                $labels[] = $row['label'];
                $cashData[] = (float)$row['cash'];
                $qrData[] = (float)$row['qr'];
                $transferData[] = (float)$row['transfer'];
                $totalData[] = (float)$row['total'];
            }
            
            echo json_encode([
                'labels' => $labels,
                'cash' => $cashData,
                'qr' => $qrData,
                'transfer' => $transferData,
                'total' => $totalData
            ]);
            
        } catch (Exception $e) {
            error_log("Cash Flow Chart Error: " . $e->getMessage());
            echo json_encode([
                'labels' => [],
                'cash' => [],
                'qr' => [],
                'transfer' => [],
                'total' => []
            ]);
        }
        exit;
    }
    
    public function expenses() {
        $expenses = $this->expense->getAll();
        $categories = $this->expense->getCategories();
        include __DIR__ . '/../views/admin/expenses.php';
    }
    
    public function addExpense() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category' => $_POST['category'],
                'description' => $_POST['description'],
                'amount' => $_POST['amount'],
                'expense_date' => $_POST['expense_date'],
                'notes' => $_POST['notes'] ?? '',
                'created_by' => $_SESSION['user_id']
            ];
            
            if ($this->expense->create($data)) {
                $_SESSION['success'] = "Pengeluaran berhasil ditambahkan";
                header('Location: index.php?page=cash_flow&action=expenses');
                exit;
            } else {
                $_SESSION['error'] = "Gagal menambahkan pengeluaran";
            }
        }
        
        $categories = $this->expense->getCategories();
        include __DIR__ . '/../views/admin/add_expense.php';
    }
    
    public function editExpense($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category' => $_POST['category'],
                'description' => $_POST['description'],
                'amount' => $_POST['amount'],
                'expense_date' => $_POST['expense_date'],
                'notes' => $_POST['notes'] ?? ''
            ];
            
            if ($this->expense->update($id, $data)) {
                $_SESSION['success'] = "Pengeluaran berhasil diperbarui";
                header('Location: index.php?page=cash_flow&action=expenses');
                exit;
            } else {
                $_SESSION['error'] = "Gagal memperbarui pengeluaran";
            }
        }
        
        $expense = $this->expense->getById($id);
        $categories = $this->expense->getCategories();
        include __DIR__ . '/../views/admin/edit_expense.php';
    }
    
    public function deleteExpense($id) {
        if ($this->expense->delete($id)) {
            $_SESSION['success'] = "Pengeluaran berhasil dihapus";
        } else {
            $_SESSION['error'] = "Gagal menghapus pengeluaran";
        }
        
        header('Location: index.php?page=cash_flow&action=expenses');
        exit;
    }
}