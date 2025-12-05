<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Order.php';

class OrderController {
    public $order;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->order = new Order($db);
    }

    public function index() {
        requireAdmin();

        $filters = $this->getFilters();
        $page = max(1, (int)($_GET['page_num'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $orders = $this->order->getAll($filters, $limit, $offset);
        $totalOrders = $this->getTotalOrders($filters);

        include __DIR__ . '/../views/admin/orders/index.php';
    }

    public function create() {
        requireAdmin();

        $action = $_GET['action'] ?? 'show';
        
        switch ($action) {
            case 'show':
                include __DIR__ . '/../views/admin/add_order.php';
                break;
                
            case 'create':
                $this->handleCreate();
                break;
                
            case 'searchCustomers':
                $this->searchCustomers();
                break;
        }
    }

    private function handleCreate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('?page=add_order');
        }

        // Validate CSRF token
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request. Please try again.';
            redirect('?page=add_order');
        }

        // Validate required fields
        $requiredFields = ['customer_name', 'phone', 'date_in'];
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Additional date validations
        $dateIn = $_POST['date_in'];
        $today = date('Y-m-d');
        
        // Validate date_in is not in future (more than 1 day allowed for advance orders)
        if (strtotime($dateIn) > strtotime($today . ' +1 day')) {
            $errors['date_in'] = 'Tanggal masuk tidak boleh lebih dari 1 hari ke depan';
        }
        
        // Validate date_in is not too far in the past (more than 30 days)
        if (strtotime($dateIn) < strtotime($today . ' -30 days')) {
            $errors['date_in'] = 'Tanggal masuk tidak boleh lebih dari 30 hari yang lalu';
        }
        
        // If date_in is today, warn about potential data entry errors
        if ($dateIn === $today) {
            // This is just a warning, not an error
            error_log("Warning: Creating order with today's date - verify this is intentional");
        }

        // Additional date validations
        $dateIn = $_POST['date_in'];
        $today = date('Y-m-d');
        
        // Validate date_in is not in the future (more than 1 day allowed for advance orders)
        if (strtotime($dateIn) > strtotime($today . ' +1 day')) {
            $errors['date_in'] = 'Tanggal masuk tidak boleh lebih dari 1 hari ke depan';
        }
        
        // Validate date_in is not too far in the past (more than 30 days)
        if (strtotime($dateIn) < strtotime($today . ' -30 days')) {
            $errors['date_in'] = 'Tanggal masuk tidak boleh lebih dari 30 hari yang lalu';
        }
        
        // If date_in is today, warn about potential data entry errors
        if ($dateIn === $today) {
            // This is just a warning, not an error
            error_log("Warning: Creating order with today's date - verify this is intentional");
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            redirect('?page=add_order');
        }

        // Process services
        $services = [];
        if (isset($_POST['services']) && is_array($_POST['services'])) {
            foreach ($_POST['services'] as $serviceData) {
                if (!empty($serviceData['brand']) && !empty($serviceData['service_code'])) {
                    $services[] = [
                        'brand' => Security::sanitizeInput($serviceData['brand']),
                        'service_code' => Security::sanitizeInput($serviceData['service_code']),
                        'service_name' => Security::sanitizeInput($serviceData['service_name']),
                        'service_price' => (float)($serviceData['service_price'])
                    ];
                }
            }
        }

        if (empty($services)) {
            $_SESSION['error'] = 'At least one service must be added';
            $_SESSION['old'] = $_POST;
            redirect('?page=add_order');
        }

        // Prepare order data
        $orderData = [
            'customer_name' => Security::sanitizeInput($_POST['customer_name']),
            'phone' => Security::sanitizeInput($_POST['phone']),
            'total_pairs' => count($services),
            'subtotal' => array_sum(array_column($services, 'service_price')),
            'total' => array_sum(array_column($services, 'service_price')),
            'payment_status' => Security::sanitizeInput($_POST['payment_status']),
            'payment_method' => !empty($_POST['payment_method']) ? Security::sanitizeInput($_POST['payment_method']) : null,
            'date_in' => Security::sanitizeInput($_POST['date_in']),
            'date_estimate_out' => !empty($_POST['date_estimate_out']) ? Security::sanitizeInput($_POST['date_estimate_out']) : null,
            'date_out' => !empty($_POST['date_out']) ? Security::sanitizeInput($_POST['date_out']) : null,
            'notes' => Security::sanitizeInput($_POST['notes'] ?? ''),
            'status' => 'new',
            'items' => $services
        ];

        // Create order
        $result = $this->order->create($orderData);
        
        if ($result['success']) {
            $_SESSION['success'] = 'Pesanan berhasil ditambahkan! Invoice: ' . $result['invoice_no'];
            redirect('?page=order_detail&id=' . $result['order_id']);
        } else {
            $_SESSION['error'] = 'Gagal menambahkan pesanan: ' . $result['message'];
            $_SESSION['old'] = $_POST;
            redirect('?page=add_order');
        }
    }

    private function searchCustomers() {
        header('Content-Type: application/json');
        
        $name = Security::sanitizeInput($_GET['name'] ?? '');
        $phone = Security::sanitizeInput($_GET['phone'] ?? '');
        
        if (strlen($name) < 2 && strlen($phone) < 2) {
            echo json_encode([]);
            exit;
        }

        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT 
                        customer_name, 
                        phone, 
                        COUNT(*) as order_count,
                        MAX(date_in) as last_order_date
                      FROM orders 
                      WHERE (customer_name LIKE ? OR phone LIKE ?)
                      GROUP BY customer_name, phone
                      ORDER BY 
                        CASE 
                            WHEN customer_name LIKE ? THEN 1
                            WHEN customer_name LIKE ? THEN 2
                            ELSE 3
                        END,
                        order_count DESC, 
                        last_order_date DESC
                      LIMIT 10";
            
            $stmt = $db->prepare($query);
            $stmt->execute([
                $name . '%', 
                $phone . '%',
                $name . '%',
                '%' . $name . '%'
            ]);
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($customers);
        } catch (Exception $e) {
            error_log('Customer search error: ' . $e->getMessage());
            echo json_encode([]);
        }
    }

    public function detail() {
        requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['error'] = 'Invalid order ID';
            redirect('?page=orders');
        }

        $order = $this->order->getById($id);
        if (!$order) {
            $_SESSION['error'] = 'Order not found';
            redirect('?page=orders');
        }

        include __DIR__ . '/../views/admin/orders/detail.php';
    }

    public function edit() {
        requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['error'] = 'Invalid order ID';
            redirect('?page=orders');
        }

        $order = $this->order->getById($id);
        if (!$order) {
            $_SESSION['error'] = "Order with ID $id not found. It may have been deleted.";
            redirect('?page=orders');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateOrder($id);
        } else {
            include __DIR__ . '/../views/admin/orders/edit.php';
        }
    }

    public function delete() {
        // Debug: Log entire request
        error_log("=== DELETE REQUEST DEBUG ===");
        error_log("POST data: " . json_encode($_POST));
        error_log("Session data: " . json_encode($_SESSION));
        error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Request URI: " . $_SERVER['REQUEST_URI']);
        
        requireSuperuser();

        $id = (int)($_POST['id'] ?? 0);
        error_log("Order ID to delete: " . $id);
        
        if ($id <= 0) {
            error_log("Invalid order ID");
            echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        error_log("CSRF token provided: " . substr($csrfToken, 0, 8) . "...");
        
        if (!Security::validateCSRFToken($csrfToken)) {
            error_log("CSRF validation failed");
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        error_log("Attempting to delete order...");
        $result = $this->order->delete($id);
        error_log("Delete result: " . json_encode($result));
        
        echo json_encode($result);
        error_log("=== END DELETE DEBUG ===");
    }

    public function checkNewOrders() {
        requireAdmin();
        header('Content-Type: application/json');
        
        $lastKnownId = (int)($_GET['last_id'] ?? 0);
        $currentLastId = (int)$this->order->getLastOrderId();
        
        echo json_encode([
            'has_new' => $currentLastId > $lastKnownId,
            'last_id' => $currentLastId
        ]);
    }

    public function updateOrder($id) {
        // Always return JSON for AJAX requests
        header('Content-Type: application/json');

        // Validate CSRF token
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Invalid request. Please try again.']);
            exit;
        }

        // Check if order exists
        $existingOrder = $this->order->getById($id);
        if (!$existingOrder) {
            echo json_encode(['success' => false, 'message' => "Order with ID $id not found. It may have been deleted."]);
            exit;
        }

        $data = [
            'customer_name' => Security::sanitizeInput($_POST['customer_name'] ?? ''),
            'phone' => Security::sanitizeInput($_POST['phone'] ?? ''),
            'payment_status' => $_POST['payment_status'] ?? 'pending',
            'payment_method' => (!empty($_POST['payment_method']) && in_array($_POST['payment_method'], ['cash', 'transfer', 'qr'])) ? $_POST['payment_method'] : null,
            'notes' => Security::sanitizeInput($_POST['notes'] ?? ''),
            'items' => []
        ];

        // Validate input
        $errors = $this->validateOrderUpdate($data);

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
            exit;
        }

        // Process order items
        $brands = $_POST['brands'] ?? [];
        $services = $_POST['services'] ?? [];
        $prices = $_POST['prices'] ?? [];

        $subtotal = 0;
        foreach ($brands as $index => $brand) {
            if (!empty($brand) && isset($services[$index]) && isset($prices[$index])) {
                $serviceInfo = $this->getServiceInfo($services[$index]);
                $data['items'][] = [
                    'brand' => Security::sanitizeInput($brand),
                    'service_code' => $services[$index],
                    'service_name' => $serviceInfo['name'],
                    'service_price' => (float)$prices[$index]
                ];
                $subtotal += (float)$prices[$index];
            }
        }

        $data['total_pairs'] = count($data['items']);
        $data['subtotal'] = $subtotal;
        $data['total'] = $subtotal;

        // Handle dates
        // Only use manually entered date_out, don't auto-fill based on payment status
        if (!empty($_POST['date_out'])) {
            $data['date_out'] = $_POST['date_out'];
            if ($data['date_out'] !== '0000-00-00' && !empty($data['date_out'])) {
                $data['status'] = 'completed';
            }
        } else {
            $data['date_out'] = null;
            // Keep current status or set to processing if not completed
            $data['status'] = $existingOrder['status'] === 'completed' ? 'completed' : 'processing';
        }

        // Handle dates
        $data['date_in'] = $existingOrder['date_in']; // Keep original date_in
        $data['date_estimate_out'] = !empty($_POST['date_estimate_out']) ? $_POST['date_estimate_out'] : null;

        // Update order
        $result = $this->order->update($id, $data);

        if ($result['success']) {
            echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Failed to update order']);
            exit;
        }
    }

    private function validateOrderUpdate($data) {
        $errors = [];

        if (empty($data['customer_name'])) {
            $errors['customer_name'] = 'Customer name is required';
        }

        if (empty($data['phone'])) {
            $errors['phone'] = 'Phone number is required';
        } elseif (!Security::validatePhone(preg_replace('/[^0-9]/', '', $data['phone']))) {
            $errors['phone'] = 'Invalid phone number format';
        }

        if (!in_array($data['payment_status'], ['pending', 'paid', 'cancelled'])) {
            $errors['payment_status'] = 'Invalid payment status';
        }

        if ($data['payment_status'] === 'paid' && empty($data['payment_method'])) {
            $errors['payment_method'] = 'Payment method is required when status is paid';
        }
        
        if (!empty($data['payment_method']) && !in_array($data['payment_method'], ['cash', 'transfer', 'qr'])) {
            $errors['payment_method'] = 'Invalid payment method selected';
        }

        return $errors;
    }

    private function getServiceInfo($serviceCode) {
        global $SERVICES;
        
        if (isset($SERVICES[$serviceCode])) {
            return [
                'code' => $serviceCode,
                'name' => $SERVICES[$serviceCode]['name']
            ];
        }

        return [
            'code' => $serviceCode,
            'name' => 'Unknown Service'
        ];
    }

    private function getFilters() {
        $filters = [];

        if (!empty($_GET['customer_name'])) {
            $filters['customer_name'] = Security::sanitizeInput($_GET['customer_name']);
        }

        if (!empty($_GET['phone'])) {
            $filters['phone'] = Security::sanitizeInput($_GET['phone']);
        }

        if (!empty($_GET['invoice_no'])) {
            $filters['invoice_no'] = Security::sanitizeInput($_GET['invoice_no']);
        }

        if (!empty($_GET['payment_status'])) {
            $filters['payment_status'] = $_GET['payment_status'];
        }

        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }

        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }

        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }

        return $filters;
    }

    private function getTotalOrders($filters) {
        // Simplified for now - return a reasonable number
        return 50;
    }

    public function printInvoice() {
        requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['error'] = 'Invalid order ID';
            redirect('?page=orders');
        }

        $order = $this->order->getById($id);
        if (!$order) {
            $_SESSION['error'] = 'Order not found';
            redirect('?page=orders');
        }

        include __DIR__ . '/../views/admin/orders/invoice.php';
    }

    public function getReportData() {
        requireAdmin();
        header('Content-Type: application/json');

        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');
        $paymentStatus = $_GET['payment_status'] ?? '';
        $status = $_GET['status'] ?? '';

        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];

        if (!empty($paymentStatus)) {
            $filters['payment_status'] = $paymentStatus;
        }

        if (!empty($status)) {
            $filters['status'] = $status;
        }

        $orders = $this->order->getAll($filters, 1000, 0); // Get up to 1000 orders for report

        // Calculate summary
        $summary = [
            'total_orders' => count($orders),
            'total_revenue' => array_sum(array_column($orders, 'total')),
            'total_shoes' => array_sum(array_column($orders, 'total_pairs')),
            'avg_transaction' => count($orders) > 0 ? array_sum(array_column($orders, 'total')) / count($orders) : 0
        ];

        // Prepare chart data (daily revenue)
        $chartData = $this->prepareChartData($orders, $dateFrom, $dateTo);

        echo json_encode([
            'success' => true,
            'orders' => $orders,
            'summary' => $summary,
            'chartData' => $chartData
        ]);
    }

    private function prepareChartData($orders, $dateFrom, $dateTo) {
        $labels = [];
        $revenue = [];

        // Create date range
        $period = new DatePeriod(
            new DateTime($dateFrom),
            new DateInterval('P1D'),
            new DateTime($dateTo . ' +1 day')
        );

        // Initialize arrays with all dates
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $revenue[$dateStr] = 0;
        }

        // Sum revenue by date
        foreach ($orders as $order) {
            $orderDate = date('Y-m-d', strtotime($order['date_in']));
            if (isset($revenue[$orderDate])) {
                $revenue[$orderDate] += (float)$order['total'];
            }
        }

        return [
            'labels' => $labels,
            'revenue' => array_values($revenue)
        ];
    }

    public function getServices() {
        return $GLOBALS['SERVICES'];
    }
}
?>