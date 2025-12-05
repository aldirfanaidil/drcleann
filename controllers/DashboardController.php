<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/User.php';

class DashboardController {
    private $order;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->order = new Order($db);
    }

    public function index() {
        requireAdmin();
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    public function getChartData() {
        requireAdmin();
        header('Content-Type: application/json');
        
        try {
            $period = $_GET['period'] ?? 'month';
            
            $stats = $this->order->getChartStats($period);

            $labels = array_column($stats, 'label');
            $revenue = array_column($stats, 'revenue');
            $orders = array_column($stats, 'orders');
            
            // Debug logging
            error_log("Dashboard Chart Data - Period: $period, Stats: " . json_encode($stats));
            
            echo json_encode([
                'labels' => $labels,
                'revenue' => $revenue,
                'orders' => $orders
            ]);
        } catch (Exception $e) {
            error_log("Dashboard Chart Error: " . $e->getMessage());
            echo json_encode([
                'labels' => [],
                'revenue' => [],
                'orders' => []
            ]);
        }
    }

    public function getKpiData() {
        requireAdmin();
        
        header('Content-Type: application/json');
        
        try {
            $todayStats = $this->order->getTodayStats();
            $weeklyStats = $this->order->getWeeklyStats();
            $monthlyStats = $this->order->getMonthlyStats();
            $yearlyStats = $this->order->getYearlyStats();
            
            // Get revenue-only stats for consistency
            $todayRevenue = $this->order->getTodayRevenue();
            $weeklyRevenue = $this->order->getWeeklyRevenue();
            $monthlyRevenue = $this->order->getMonthlyRevenue();
            $yearlyRevenue = $this->order->getYearlyRevenue();
            
            // Get payment method stats for each period
            $todayPayment = $this->order->getRevenueByPaymentMethod('today');
            $weeklyPayment = $this->order->getRevenueByPaymentMethod('week');
            $monthlyPayment = $this->order->getRevenueByPaymentMethod('month');
            $yearlyPayment = $this->order->getRevenueByPaymentMethod('year');
            
            // Debug logging
            error_log("Dashboard KPI Data - Today: " . json_encode($todayStats));
            error_log("Dashboard KPI Data - Weekly: " . json_encode($weeklyStats));
            error_log("Dashboard KPI Data - Monthly: " . json_encode($monthlyStats));
            error_log("Dashboard KPI Data - Yearly: " . json_encode($yearlyStats));
            
            echo json_encode([
                'today' => [
                    'revenue' => (float)($todayRevenue['revenue_today'] ?? 0),
                    'orders' => (int)($todayStats['orders_today'] ?? 0),
                    'shoes' => (int)($todayStats['shoes_today'] ?? 0),
                    'paid' => (float)($todayStats['paid_today'] ?? 0),
                    'unpaid' => (float)($todayStats['unpaid_today'] ?? 0),
                    'unpaid_orders' => (int)($todayStats['unpaid_orders_today'] ?? 0),
                    'total_cash' => (float)($todayPayment['cash'] ?? 0),
                    'total_transfer' => (float)($todayPayment['transfer'] ?? 0),
                    'total_qr' => (float)($todayPayment['qr'] ?? 0)
                ],
                'week' => [
                    'revenue' => (float)($weeklyRevenue['revenue_week'] ?? 0),
                    'orders' => (int)($weeklyStats['orders_week'] ?? 0),
                    'shoes' => (int)($weeklyStats['shoes_week'] ?? 0),
                    'paid' => (float)($weeklyStats['paid_week'] ?? 0),
                    'unpaid' => (float)($weeklyStats['unpaid_week'] ?? 0),
                    'unpaid_orders' => (int)($weeklyStats['unpaid_orders_week'] ?? 0),
                    'total_cash' => (float)($weeklyPayment['cash'] ?? 0),
                    'total_transfer' => (float)($weeklyPayment['transfer'] ?? 0),
                    'total_qr' => (float)($weeklyPayment['qr'] ?? 0)
                ],
                'month' => [
                    'revenue' => (float)($monthlyRevenue['revenue_month'] ?? 0),
                    'orders' => (int)($monthlyStats['orders_month'] ?? 0),
                    'shoes' => (int)($monthlyStats['shoes_month'] ?? 0),
                    'paid' => (float)($monthlyStats['paid_month'] ?? 0),
                    'unpaid' => (float)($monthlyStats['unpaid_month'] ?? 0),
                    'unpaid_orders' => (int)($monthlyStats['unpaid_orders_month'] ?? 0),
                    'total_cash' => (float)($monthlyPayment['cash'] ?? 0),
                    'total_transfer' => (float)($monthlyPayment['transfer'] ?? 0),
                    'total_qr' => (float)($monthlyPayment['qr'] ?? 0)
                ],
                'year' => [
                    'revenue' => (float)($yearlyRevenue['revenue_year'] ?? 0),
                    'orders' => (int)($yearlyStats['orders_year'] ?? 0),
                    'shoes' => (int)($yearlyStats['shoes_year'] ?? 0),
                    'paid' => (float)($yearlyStats['paid_year'] ?? 0),
                    'unpaid' => (float)($yearlyStats['unpaid_year'] ?? 0),
                    'unpaid_orders' => (int)($yearlyStats['unpaid_orders_year'] ?? 0),
                    'total_cash' => (float)($yearlyPayment['cash'] ?? 0),
                    'total_transfer' => (float)($yearlyPayment['transfer'] ?? 0),
                    'total_qr' => (float)($yearlyPayment['qr'] ?? 0)
                ]
            ]);
        } catch (Exception $e) {
            error_log("Dashboard KPI Error: " . $e->getMessage());
            echo json_encode([
                'error' => 'Failed to load KPI data',
                'today' => ['revenue' => 0, 'orders' => 0, 'shoes' => 0, 'paid' => 0, 'unpaid' => 0, 'unpaid_orders' => 0, 'total_cash' => 0, 'total_transfer' => 0, 'total_qr' => 0],
                'week' => ['revenue' => 0, 'orders' => 0, 'shoes' => 0, 'paid' => 0, 'unpaid' => 0, 'unpaid_orders' => 0, 'total_cash' => 0, 'total_transfer' => 0, 'total_qr' => 0],
                'month' => ['revenue' => 0, 'orders' => 0, 'shoes' => 0, 'paid' => 0, 'unpaid' => 0, 'unpaid_orders' => 0, 'total_cash' => 0, 'total_transfer' => 0, 'total_qr' => 0],
                'year' => ['revenue' => 0, 'orders' => 0, 'shoes' => 0, 'paid' => 0, 'unpaid' => 0, 'unpaid_orders' => 0, 'total_cash' => 0, 'total_transfer' => 0, 'total_qr' => 0]
            ]);
        }
    }

    public function getDetailData() {
        requireAdmin();
        header('Content-Type: application/json');
        
        try {
            $type = $_GET['type'] ?? '';
            $period = $_GET['period'] ?? 'today';
            $search = $_GET['search'] ?? '';
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = 10;
            
            $result = $this->order->getDetailData($type, $period, $search, $page, $limit);
            $summary = $result['summary'] ?? [];
            
            echo json_encode([
                'success' => true,
                'data' => $result['data'],
                'columns' => $result['columns'],
                'summary' => $summary,
                'pagination' => $result['pagination'] ?? []
            ]);
        } catch (Exception $e) {
            error_log("Dashboard Detail Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load detail data: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public function getSimpleDetailData() {
        try {
            // Check if user is admin
            if (!function_exists('requireAdmin')) {
                require_once __DIR__ . '/../core/init.php';
            }
            
            // Check authentication
            if (!function_exists('requireAdmin') || !function_exists('isLoggedIn')) {
                throw new Exception('Authentication functions not available');
            }
            
            if (!isLoggedIn()) {
                http_response_code(401);
                throw new Exception('User not logged in');
            }
            
            requireAdmin();
            
            // Set JSON header after authentication
            header('Content-Type: application/json');
            
            $search = $_GET['search'] ?? '';
            $payment_status = $_GET['payment_status'] ?? '';
            $page = max(1, (int)($_GET['page_num'] ?? 1));
            $limit = 10;
            
            // Build filters
            $filters = [];
            if (!empty($search)) {
                $filters['customer_name'] = $search;
                $filters['phone'] = $search;
                $filters['invoice_no'] = $search;
            }
            if (!empty($payment_status)) {
                $filters['payment_status'] = $payment_status;
            }
            
            // Get data using existing Order model methods
            $offset = ($page - 1) * $limit;
            $orders = $this->order->getAll($filters, $limit, $offset);
            $totalOrders = $this->order->countAll($filters);
            
            $totalPages = ceil($totalOrders / $limit);
            
            $response = [
                'success' => true,
                'data' => $orders,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_records' => $totalOrders,
                    'per_page' => $limit,
                    'has_next' => $page < $totalPages,
                    'has_prev' => $page > 1
                ]
            ];
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            error_log("Dashboard Simple Detail Error: " . $e->getMessage());
            http_response_code(500);
            
            $errorResponse = [
                'success' => false,
                'message' => 'Failed to load data: ' . $e->getMessage()
            ];
            
            echo json_encode($errorResponse);
        }
        exit;
    }


}