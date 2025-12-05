<?php
class CustomerController {
    private $order;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->order = new Order($db);
    }

    public function index() {
        include 'views/customer/index.php';
    }

    public function createOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'customer_name' => Security::sanitizeInput($_POST['customer_name'] ?? ''),
                'phone' => Security::sanitizeInput($_POST['phone'] ?? ''),
                'total_pairs' => (int)($_POST['total_pairs'] ?? 1),
                'brands' => $_POST['brands'] ?? []
            ];

            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request. Please try again.';
                redirect('index.php');
            }

            // Validate input
            $errors = $this->validateOrderData($data);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                redirect('index.php');
            }

            // Prepare order data
            $orderData = [
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'total_pairs' => $data['total_pairs'],
                'subtotal' => 0, // Will be calculated by admin
                'total' => 0, // Will be calculated by admin
                'payment_status' => 'pending',
                'date_in' => date('Y-m-d'),
                'date_estimate_out' => null, // Will be set by admin based on service
                'status' => 'new',
                'items' => []
            ];

            // Create order items (without service info - will be added by admin)
            foreach ($data['brands'] as $index => $brand) {
                $orderData['items'][] = [
                    'brand' => Security::sanitizeInput($brand),
                    'service_code' => 'PENDING',
                    'service_name' => 'Menunggu pemilihan layanan',
                    'service_price' => 0
                ];
            }

            // Create order
            $result = $this->order->create($orderData);

            if ($result['success']) {
                $_SESSION['success'] = "Pemesanan berhasil! Nomor invoice: {$result['invoice_no']}. Silakan menuju kasir untuk pemilihan layanan dan pembayaran.";
                redirect('index.php');
            } else {
                $_SESSION['error'] = $result['message'] ?? 'Terjadi kesalahan saat memproses pemesanan. Silakan coba lagi.';
                $_SESSION['old'] = $data;
                redirect('index.php');
            }
        }
    }

    private function validateOrderData($data) {
        $errors = [];

        // Validate customer name
        if (empty($data['customer_name'])) {
            $errors['customer_name'] = 'Nama lengkap wajib diisi';
        } elseif (strlen($data['customer_name']) < 3) {
            $errors['customer_name'] = 'Nama lengkap minimal 3 karakter';
        } elseif (strlen($data['customer_name']) > 100) {
            $errors['customer_name'] = 'Nama lengkap maksimal 100 karakter';
        }

        // Validate phone
        if (empty($data['phone'])) {
            $errors['phone'] = 'Nomor WhatsApp wajib diisi';
        } elseif (!Security::validatePhone(preg_replace('/[^0-9]/', '', $data['phone']))) {
            $errors['phone'] = 'Format nomor WhatsApp tidak valid';
        }

        // Validate total pairs
        if (empty($data['total_pairs'])) {
            $errors['total_pairs'] = 'Jumlah pasang sepatu wajib diisi';
        } elseif (!Security::validateNumber($data['total_pairs'], 1, 10)) {
            $errors['total_pairs'] = 'Jumlah pasang sepatu harus antara 1-10';
        }

        // Validate brands
        if (empty($data['brands']) || !is_array($data['brands'])) {
            $errors['brands'] = 'Data merek sepatu tidak valid';
        } else {
            $brandCount = count($data['brands']);
            if ($brandCount != $data['total_pairs']) {
                $errors['brands'] = 'Jumlah merek sepatu tidak sesuai dengan jumlah pasang';
            } else {
                foreach ($data['brands'] as $index => $brand) {
                    if (empty($brand)) {
                        $errors["brand_$index"] = "Merek sepatu ke-" . ($index + 1) . " wajib diisi";
                    } elseif (strlen($brand) < 2) {
                        $errors["brand_$index"] = "Merek sepatu ke-" . ($index + 1) . " minimal 2 karakter";
                    } elseif (strlen($brand) > 100) {
                        $errors["brand_$index"] = "Merek sepatu ke-" . ($index + 1) . " maksimal 100 karakter";
                    }
                }
            }
        }

        return $errors;
    }

    public function thankYou() {
        include 'views/customer/thank_you.php';
    }
}
?>