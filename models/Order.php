<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Security.php';

class Order {
    private $conn;
    private $table_name = "orders";
    private $items_table = "order_items";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        try {
            $this->conn->beginTransaction();

            // Generate invoice number
            $invoiceNo = generateInvoiceNumber();

            // Insert order
            $query = "INSERT INTO " . $this->table_name . " 
                     (invoice_no, customer_name, phone, total_pairs, subtotal, total, payment_status, payment_method, date_in, date_estimate_out, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(1, $invoiceNo);
            $stmt->bindParam(2, $data['customer_name']);
            $stmt->bindParam(3, $data['phone']);
            $stmt->bindParam(4, $data['total_pairs']);
            $stmt->bindParam(5, $data['subtotal']);
            $stmt->bindParam(6, $data['total']);
            $stmt->bindParam(7, $data['payment_status']);
            $stmt->bindParam(8, $data['payment_method']);
            $stmt->bindParam(9, $data['date_in']);
            $stmt->bindParam(10, $data['date_estimate_out']);
            $stmt->bindParam(11, $data['status']);

            if ($stmt->execute()) {
                $orderId = $this->conn->lastInsertId();

                // Insert order items
                foreach ($data['items'] as $index => $item) {
                    $this->insertOrderItem($orderId, $index + 1, $item);
                }

                $this->conn->commit();
                
                // Log activity
                if (class_exists('Security')) {
                    Security::logActivity(
                        $_SESSION['user_id'] ?? null, 
                        'order_created', 
                        "Created order: $invoiceNo for {$data['customer_name']}"
                    );
                }

                return [
                    'success' => true,
                    'order_id' => $orderId,
                    'invoice_no' => $invoiceNo
                ];
            } else {
                $this->conn->rollback();
                return ['success' => false, 'message' => 'Failed to create order'];
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Order creation error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    private function insertOrderItem($orderId, $pairIndex, $item) {
        $query = "INSERT INTO " . $this->items_table . " 
                 (order_id, pair_index, brand, service_code, service_name, service_price) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(1, $orderId);
        $stmt->bindParam(2, $pairIndex);
        $stmt->bindParam(3, $item['brand']);
        $stmt->bindParam(4, $item['service_code']);
        $stmt->bindParam(5, $item['service_name']);
        $stmt->bindParam(6, $item['service_price']);

        return $stmt->execute();
    }

    public function getById($id) {
        $query = "SELECT o.*, u.username as created_by_name 
                 FROM " . $this->table_name . " o 
                 LEFT JOIN users u ON o.created_by = u.id 
                 WHERE o.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Get order items
            $order['items'] = $this->getOrderItems($id);
        }

        return $order;
    }

    public function getByInvoiceNo($invoiceNo) {
        $query = "SELECT o.*, u.username as created_by_name 
                 FROM " . $this->table_name . " o 
                 LEFT JOIN users u ON o.created_by = u.id 
                 WHERE o.invoice_no = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $invoiceNo);
        $stmt->execute();

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Get order items
            $order['items'] = $this->getOrderItems($order['id']);
        }

        return $order;
    }

    private function getOrderItems($orderId) {
        $query = "SELECT * FROM " . $this->items_table . " WHERE order_id = ? ORDER BY pair_index";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $orderId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetailData($type, $period, $search = '', $page = 1, $limit = 10) {
        $baseQuery = "FROM " . $this->table_name . " o";
        $where = [];
        $params = [];

        // Build date filter
        switch ($period) {
            case 'today':
                $where[] = "DATE(o.date_in) = CURDATE()";
                break;
            case 'week':
                $where[] = "o.date_in >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $where[] = "MONTH(o.date_in) = MONTH(CURDATE()) AND YEAR(o.date_in) = YEAR(CURDATE())";
                break;
            case 'year':
                $where[] = "YEAR(o.date_in) = YEAR(CURDATE())";
                break;
        }

        // Add search filter
        if (!empty($search)) {
            $where[] = "(o.customer_name LIKE ? OR o.phone LIKE ? OR o.invoice_no LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        // Define columns and data query based on type
        $selectColumns = "o.invoice_no, o.customer_name, o.phone, o.total_pairs, o.total, o.payment_status, o.payment_method, o.status, o.date_in, o.date_estimate_out";
        $columns = [];

        switch ($type) {
            case 'revenue':
                $where[] = "o.payment_status = 'paid'";
                $columns = [
                    ['key' => 'invoice_no', 'label' => 'Invoice'],
                    ['key' => 'customer_name', 'label' => 'Customer'],
                    ['key' => 'phone', 'label' => 'Telepon'],
                    ['key' => 'total', 'label' => 'Total Bayar'],
                    ['key' => 'payment_method', 'label' => 'Metode Bayar'],
                    ['key' => 'date_in', 'label' => 'Tgl. Masuk']
                ];
                break;
            case 'orders':
                $columns = [
                    ['key' => 'invoice_no', 'label' => 'Invoice'],
                    ['key' => 'customer_name', 'label' => 'Customer'],
                    ['key' => 'phone', 'label' => 'Telepon'],
                    ['key' => 'total_pairs', 'label' => 'Jumlah Sepatu'],
                    ['key' => 'total', 'label' => 'Total'],
                    ['key' => 'payment_status', 'label' => 'Status Bayar'],
                    ['key' => 'status', 'label' => 'Status Pesanan'],
                    ['key' => 'date_in', 'label' => 'Tgl. Masuk']
                ];
                break;
            case 'shoes':
                $columns = [
                    ['key' => 'invoice_no', 'label' => 'Invoice'],
                    ['key' => 'customer_name', 'label' => 'Customer'],
                    ['key' => 'total_pairs', 'label' => 'Jumlah Sepatu'],
                    ['key' => 'status', 'label' => 'Status Pesanan'],
                    ['key' => 'date_estimate_out', 'label' => 'Estimasi Selesai'],
                    ['key' => 'date_in', 'label' => 'Tgl. Masuk']
                ];
                break;
            case 'paid':
                $where[] = "o.payment_status = 'paid'";
                $columns = [
                    ['key' => 'invoice_no', 'label' => 'Invoice'],
                    ['key' => 'customer_name', 'label' => 'Customer'],
                    ['key' => 'phone', 'label' => 'Telepon'],
                    ['key' => 'total', 'label' => 'Total Bayar'],
                    ['key' => 'payment_method', 'label' => 'Metode Bayar'],
                    ['key' => 'date_in', 'label' => 'Tgl. Masuk']
                ];
                break;
            case 'unpaid':
                $where[] = "o.payment_status = 'pending'";
                $columns = [
                    ['key' => 'invoice_no', 'label' => 'Invoice'],
                    ['key' => 'customer_name', 'label' => 'Customer'],
                    ['key' => 'phone', 'label' => 'Telepon'],
                    ['key' => 'total', 'label' => 'Total Harus Dibayar'],
                    ['key' => 'date_in', 'label' => 'Tgl. Masuk'],
                    ['key' => 'date_estimate_out', 'label' => 'Estimasi Selesai']
                ];
                break;
            default:
                return ['data' => [], 'columns' => [], 'pagination' => []];
        }

        $whereClause = !empty($where) ? " WHERE " . implode(" AND ", $where) : "";

        // Get total records for pagination
        $countQuery = "SELECT COUNT(o.id) as total " . $baseQuery . $whereClause;
        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute($params);
        $totalRecords = (int)$countStmt->fetchColumn();
        $totalPages = ceil($totalRecords / $limit);

        // Get data with pagination
        $offset = ($page - 1) * $limit;
        $dataQuery = "SELECT " . $selectColumns . " " . $baseQuery . $whereClause . " ORDER BY o.date_in DESC LIMIT {$limit} OFFSET {$offset}";
        
        $dataStmt = $this->conn->prepare($dataQuery);
        $dataStmt->execute($params);
        $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'per_page' => $limit,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ];

        // Calculate summary
        $summary = $this->calculateDetailSummary($type, $whereClause, $params);

        return ['data' => $data, 'columns' => $columns, 'pagination' => $pagination, 'summary' => $summary];
    }

    private function calculateDetailSummary($type, $whereClause, $params) {
        $query = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(total) as total_amount,
                    SUM(total_pairs) as total_shoes,
                    SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_orders,
                    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as unpaid_orders
                  FROM " . $this->table_name . $whereClause;

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $summary = [];
        
        switch ($type) {
            case 'revenue':
            case 'paid':
                $summary = [
                    'total_revenue' => (float)($result['total_amount'] ?? 0),
                    'total_transactions' => (int)($result['total_orders'] ?? 0),
                    'average' => (int)($result['total_orders'] ?? 0) > 0 ? (float)($result['total_amount'] ?? 0) / (int)($result['total_orders'] ?? 0) : 0
                ];
                break;
            case 'unpaid':
                $summary = [
                    'total_unpaid' => (float)($result['total_amount'] ?? 0),
                    'total_orders' => (int)($result['total_orders'] ?? 0),
                    'average' => (int)($result['total_orders'] ?? 0) > 0 ? (float)($result['total_amount'] ?? 0) / (int)($result['total_orders'] ?? 0) : 0
                ];
                break;
            case 'orders':
                $summary = [
                    'total_orders' => (int)($result['total_orders'] ?? 0),
                    'paid_orders' => (int)($result['paid_orders'] ?? 0),
                    'unpaid_orders' => (int)($result['unpaid_orders'] ?? 0)
                ];
                break;
            case 'shoes':
                $summary = [
                    'total_shoes' => (int)($result['total_shoes'] ?? 0),
                    'total_orders' => (int)($result['total_orders'] ?? 0),
                    'average_shoes' => (int)($result['total_orders'] ?? 0) > 0 ? (int)($result['total_shoes'] ?? 0) / (int)($result['total_orders'] ?? 0) : 0
                ];
                break;
        }

        return $summary;
    }

    public function update($id, $data) {
        try {
            $this->conn->beginTransaction();

            // Get current order data
            $currentOrder = $this->getById($id);
            if (!$currentOrder) {
                throw new Exception("Order not found");
            }

            // Update order
            $query = "UPDATE " . $this->table_name . " 
                      SET customer_name = ?, phone = ?, total_pairs = ?, subtotal = ?, total = ?, 
                          payment_status = ?, payment_method = ?, date_in = ?, date_estimate_out = ?, 
                          date_out = ?, status = ?, notes = ?
                      WHERE id = ?";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(1, $data['customer_name']);
            $stmt->bindParam(2, $data['phone']);
            $stmt->bindParam(3, $data['total_pairs']);
            $stmt->bindParam(4, $data['subtotal']);
            $stmt->bindParam(5, $data['total']);
            $stmt->bindParam(6, $data['payment_status']);
            $stmt->bindParam(7, $data['payment_method']);
            $stmt->bindParam(8, $data['date_in']);
            $stmt->bindParam(9, $data['date_estimate_out']);
            $stmt->bindParam(10, $data['date_out']);
            $stmt->bindParam(11, $data['status']);
            $stmt->bindParam(12, $data['notes']);
            $stmt->bindParam(13, $id);

            if ($stmt->execute()) {
                // Update order items if provided
                if (isset($data['items'])) {
                    $this->updateOrderItems($id, $data['items']);
                }

                $this->conn->commit();
                
                // Log activity
                Security::logActivity(
                    $_SESSION['user_id'] ?? null, 
                    'order_updated', 
                    "Updated order: {$currentOrder['invoice_no']} for {$data['customer_name']}"
                );

                return ['success' => true];
            } else {
                $this->conn->rollback();
                return ['success' => false, 'message' => 'Failed to update order'];
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Order update error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    private function updateOrderItems($orderId, $items) {
        try {
            // Delete existing items
            $query = "DELETE FROM " . $this->items_table . " WHERE order_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $orderId);
            
            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Failed to delete existing items: " . $errorInfo[2]);
            }

            // Insert new items
            foreach ($items as $index => $item) {
                $result = $this->insertOrderItem($orderId, $index + 1, $item);
                if (!$result) {
                    throw new Exception("Failed to insert item " . ($index + 1));
                }
            }
        } catch (Exception $e) {
            error_log("Error updating order items: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id) {
        try {
            $this->conn->beginTransaction();

            // Get order info for logging
            $order = $this->getById($id);

            // Delete order (cascade will delete items)
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);

            if ($stmt->execute()) {
                $this->conn->commit();
                
                // Log activity
                Security::logActivity(
                    $_SESSION['user_id'] ?? null, 
                    'order_deleted', 
                    "Deleted order: {$order['invoice_no']} for {$order['customer_name']}"
                );

                return ['success' => true];
            } else {
                $this->conn->rollback();
                return ['success' => false, 'message' => 'Failed to delete order'];
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Order deletion error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public function getAll($filters = [], $limit = 50, $offset = 0) {
        $query = "SELECT o.*, u.username as created_by_name 
                 FROM " . $this->table_name . " o 
                 LEFT JOIN users u ON o.created_by = u.id 
                 WHERE 1=1";
        
        $params = [];

        // Apply filters
        if (!empty($filters['customer_name'])) {
            $query .= " AND o.customer_name LIKE ?";
            $params[] = '%' . $filters['customer_name'] . '%';
        }

        if (!empty($filters['phone'])) {
            $query .= " AND o.phone LIKE ?";
            $params[] = '%' . $filters['phone'] . '%';
        }

        if (!empty($filters['invoice_no'])) {
            $query .= " AND o.invoice_no LIKE ?";
            $params[] = '%' . $filters['invoice_no'] . '%';
        }

        if (!empty($filters['payment_status'])) {
            $query .= " AND o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['status'])) {
            $query .= " AND o.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND o.date_in >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND o.date_in <= ?";
            $params[] = $filters['date_to'];
        }

        $query .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll($filters = []) {
        $query = "SELECT COUNT(o.id) FROM " . $this->table_name . " o WHERE 1=1";
        
        $params = [];

        // Apply filters (must be identical to getAll)
        if (!empty($filters['customer_name'])) {
            $query .= " AND o.customer_name LIKE ?";
            $params[] = '%' . $filters['customer_name'] . '%';
        }

        if (!empty($filters['phone'])) {
            $query .= " AND o.phone LIKE ?";
            $params[] = '%' . $filters['phone'] . '%';
        }

        if (!empty($filters['invoice_no'])) {
            $query .= " AND o.invoice_no LIKE ?";
            $params[] = '%' . $filters['invoice_no'] . '%';
        }

        if (!empty($filters['payment_status'])) {
            $query .= " AND o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['status'])) {
            $query .= " AND o.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND o.date_in >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND o.date_in <= ?";
            $params[] = $filters['date_to'];
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    public function getChartStats($period = 'year') {
        $params = [];
        $query = "";

        switch ($period) {
            case 'week': // Harian (Senin-Minggu)
                $query = "SELECT 
                            DAYOFWEEK(date_in) as day_index, 
                            CASE DAYOFWEEK(date_in)
                                WHEN 1 THEN 'Minggu'
                                WHEN 2 THEN 'Senin'
                                WHEN 3 THEN 'Selasa'
                                WHEN 4 THEN 'Rabu'
                                WHEN 5 THEN 'Kamis'
                                WHEN 6 THEN 'Jumat'
                                WHEN 7 THEN 'Sabtu'
                            END as label, 
                            SUM(total) as revenue,
                            COUNT(*) as orders
                          FROM " . $this->table_name . " 
                          WHERE YEARWEEK(date_in, 1) = YEARWEEK(CURDATE(), 1) AND payment_status = 'paid'
                          GROUP BY day_index, label
                          ORDER BY day_index ASC";
                break;

            case 'month': // Harian untuk 30 hari terakhir
                 $query = "SELECT 
                            DATE_FORMAT(date_in, '%d %b') as label,
                            SUM(total) as revenue,
                            COUNT(*) as orders
                          FROM " . $this->table_name . " 
                          WHERE date_in >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND payment_status = 'paid'
                          GROUP BY DATE_FORMAT(date_in, '%Y-%m-%d'), label
                          ORDER BY date_in ASC";
                break;
            
            case 'year': // Bulanan (Jan-Des)
            default:
                $query = "SELECT 
                            MONTH(date_in) as month_num, 
                            CASE MONTH(date_in)
                                WHEN 1 THEN 'Jan'
                                WHEN 2 THEN 'Feb'
                                WHEN 3 THEN 'Mar'
                                WHEN 4 THEN 'Apr'
                                WHEN 5 THEN 'Mei'
                                WHEN 6 THEN 'Jun'
                                WHEN 7 THEN 'Jul'
                                WHEN 8 THEN 'Agu'
                                WHEN 9 THEN 'Sep'
                                WHEN 10 THEN 'Okt'
                                WHEN 11 THEN 'Nov'
                                WHEN 12 THEN 'Des'
                            END as label, 
                            SUM(total) as revenue,
                            COUNT(*) as orders
                          FROM " . $this->table_name . " 
                          WHERE YEAR(date_in) = YEAR(CURDATE()) AND payment_status = 'paid'
                          GROUP BY month_num, label
                          ORDER BY month_num ASC";
                break;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTodayStats() {
        $query = "SELECT 
                    COUNT(*) as orders_today,
                    SUM(total_pairs) as shoes_today,
                    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as revenue_today,
                    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as paid_today,
                    SUM(CASE WHEN payment_status = 'pending' THEN total ELSE 0 END) as unpaid_today,
                    COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as unpaid_orders_today
                 FROM " . $this->table_name . " 
                 WHERE DATE(date_in) = CURDATE()";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getWeeklyStats() {
        $query = "SELECT 
                    COUNT(*) as orders_week,
                    SUM(total_pairs) as shoes_week,
                    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as revenue_week,
                    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as paid_week,
                    SUM(CASE WHEN payment_status = 'pending' THEN total ELSE 0 END) as unpaid_week,
                    COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as unpaid_orders_week
                 FROM " . $this->table_name . " 
                 WHERE DATE(date_in) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMonthlyStats() {
        $query = "SELECT 
                    COUNT(*) as orders_month,
                    SUM(total_pairs) as shoes_month,
                    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as revenue_month,
                    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as paid_month,
                    SUM(CASE WHEN payment_status = 'pending' THEN total ELSE 0 END) as unpaid_month,
                    COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as unpaid_orders_month
                 FROM " . $this->table_name . " 
                 WHERE MONTH(date_in) = MONTH(CURDATE()) AND YEAR(date_in) = YEAR(CURDATE())";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getYearlyStats() {
        $query = "SELECT 
                    COUNT(*) as orders_year,
                    SUM(total_pairs) as shoes_year,
                    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as revenue_year,
                    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as paid_year,
                    SUM(CASE WHEN payment_status = 'pending' THEN total ELSE 0 END) as unpaid_year,
                    COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as unpaid_orders_year
                 FROM " . $this->table_name . " 
                 WHERE YEAR(date_in) = YEAR(CURDATE())";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStats($period = 'month') {
        $query = "";
        $params = [];

        switch ($period) {
            case 'month':
                $query = "SELECT 
                            DATE_FORMAT(date_in, '%Y-%m') as period,
                            SUM(total) as revenue,
                            COUNT(*) as total_orders
                          FROM " . $this->table_name . " 
                          WHERE payment_status = 'paid'
                          GROUP BY DATE_FORMAT(date_in, '%Y-%m')
                          ORDER BY period DESC
                          LIMIT 12";
                break;
            
            case 'year':
                $query = "SELECT 
                            YEAR(date_in) as period,
                            SUM(total) as revenue,
                            COUNT(*) as total_orders
                          FROM " . $this->table_name . " 
                          WHERE payment_status = 'paid'
                          GROUP BY YEAR(date_in)
                          ORDER BY period DESC
                          LIMIT 5";
                break;
            
            default:
                $query = "SELECT 
                            DATE_FORMAT(date_in, '%Y-%m') as period,
                            SUM(total) as revenue,
                            COUNT(*) as total_orders
                          FROM " . $this->table_name . " 
                          WHERE payment_status = 'paid'
                          GROUP BY DATE_FORMAT(date_in, '%Y-%m')
                          ORDER BY period DESC
                          LIMIT 12";
                break;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastOrderId() {
        $query = "SELECT MAX(id) as last_id FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['last_id'] ?? 0;
    }

    public function getRevenueByPaymentMethod($period = 'all') {
        $whereClause = "";
        
        switch ($period) {
            case 'today':
                $whereClause = " AND DATE(date_in) = CURDATE()";
                break;
            case 'week':
                $whereClause = " AND DATE(date_in) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $whereClause = " AND MONTH(date_in) = MONTH(CURDATE()) AND YEAR(date_in) = YEAR(CURDATE())";
                break;
            case 'year':
                $whereClause = " AND YEAR(date_in) = YEAR(CURDATE())";
                break;
            default:
                $whereClause = "";
                break;
        }
        
        $query = "SELECT 
                    COALESCE(payment_method, 'cash') as payment_method,
                    SUM(total) as total_revenue
                 FROM " . $this->table_name . " 
                 WHERE payment_status = 'paid' 
                   AND (payment_method IN ('cash', 'transfer', 'qr') OR payment_method IS NULL)
                   $whereClause
                 GROUP BY COALESCE(payment_method, 'cash')";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $revenue = [
            'cash' => 0,
            'transfer' => 0,
            'qr' => 0
        ];

        foreach ($results as $row) {
            if (isset($revenue[$row['payment_method']])) {
                $revenue[$row['payment_method']] = (float)$row['total_revenue'];
            }
        }

        return $revenue;
    }

    // New methods for consistent revenue calculation (PAID ORDERS ONLY)
    public function getTodayRevenue() {
        $query = "SELECT 
                    COALESCE(SUM(total), 0) as revenue_today,
                    COUNT(*) as paid_orders_today
                 FROM " . $this->table_name . " 
                 WHERE DATE(date_in) = CURDATE() AND payment_status = 'paid'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getWeeklyRevenue() {
        $query = "SELECT 
                    COALESCE(SUM(total), 0) as revenue_week,
                    COUNT(*) as paid_orders_week
                 FROM " . $this->table_name . " 
                 WHERE DATE(date_in) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND payment_status = 'paid'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMonthlyRevenue() {
        $query = "SELECT 
                    COALESCE(SUM(total), 0) as revenue_month,
                    COUNT(*) as paid_orders_month
                 FROM " . $this->table_name . " 
                 WHERE MONTH(date_in) = MONTH(CURDATE()) AND YEAR(date_in) = YEAR(CURDATE()) AND payment_status = 'paid'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getYearlyRevenue() {
        $query = "SELECT 
                    COALESCE(SUM(total), 0) as revenue_year,
                    COUNT(*) as paid_orders_year
                 FROM " . $this->table_name . " 
                 WHERE YEAR(date_in) = YEAR(CURDATE()) AND payment_status = 'paid'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>