<?php
class ReportController {
    private $order;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->order = new Order($db);
    }

    public function index() {
        requireAdmin();
        
        $period = $_GET['period'] ?? 'month';
        $type = $_GET['type'] ?? 'revenue';
        
        include 'views/admin/reports/index.php';
    }

    public function generateReport() {
        requireAdmin();
        
        $period = $_GET['period'] ?? 'month';
        $type = $_GET['type'] ?? 'revenue';
        $format = $_GET['format'] ?? 'html';

        switch ($type) {
            case 'revenue':
                $this->generateRevenueReport($period, $format);
                break;
            case 'orders':
                $this->generateOrdersReport($period, $format);
                break;
            case 'payment':
                $this->generatePaymentReport($period, $format);
                break;
            default:
                $this->generateRevenueReport($period, $format);
                break;
        }
    }

    private function generateRevenueReport($period, $format) {
        $stats = $this->order->getStats($period);
        
        if ($format === 'csv') {
            $this->exportRevenueCSV($stats, $period);
        } elseif ($format === 'pdf') {
            $this->exportRevenuePDF($stats, $period);
        } else {
            include 'views/admin/reports/revenue.php';
        }
    }

    private function generateOrdersReport($period, $format) {
        $filters = $this->getPeriodFilters($period);
        $orders = $this->order->getAll($filters, 1000, 0); // Get up to 1000 orders
        
        if ($format === 'csv') {
            $this->exportOrdersCSV($orders, $period);
        } elseif ($format === 'pdf') {
            $this->exportOrdersPDF($orders, $period);
        } else {
            include 'views/admin/reports/orders.php';
        }
    }

    private function generatePaymentReport($period, $format) {
        $filters = $this->getPeriodFilters($period);
        $orders = $this->order->getAll($filters, 1000, 0);
        
        if ($format === 'csv') {
            $this->exportPaymentCSV($orders, $period);
        } elseif ($format === 'pdf') {
            $this->exportPaymentPDF($orders, $period);
        } else {
            include 'views/admin/reports/payment.php';
        }
    }

    private function getPeriodFilters($period) {
        $filters = [];
        
        switch ($period) {
            case 'today':
                $filters['date_from'] = date('Y-m-d');
                $filters['date_to'] = date('Y-m-d');
                break;
            case 'week':
                $filters['date_from'] = date('Y-m-d', strtotime('monday this week'));
                $filters['date_to'] = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'month':
                $filters['date_from'] = date('Y-m-01');
                $filters['date_to'] = date('Y-m-t');
                break;
            case 'year':
                $filters['date_from'] = date('Y-01-01');
                $filters['date_to'] = date('Y-12-31');
                break;
        }
        
        return $filters;
    }

    private function exportRevenueCSV($stats, $period) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="revenue_report_' . $period . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Header
        fputcsv($output, ['Period', 'Total Orders', 'Total Shoes', 'Revenue', 'Paid Revenue']);
        
        // CSV Data
        foreach ($stats as $stat) {
            fputcsv($output, [
                $stat['period'],
                $stat['total_orders'],
                $stat['total_shoes'],
                $stat['revenue'],
                $stat['paid_revenue']
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportOrdersCSV($orders, $period) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="orders_report_' . $period . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Header
        fputcsv($output, [
            'Invoice No', 'Customer Name', 'Phone', 'Total Pairs', 'Subtotal', 
            'Total', 'Payment Status', 'Payment Method', 'Date In', 'Date Out', 'Status'
        ]);
        
        // CSV Data
        foreach ($orders as $order) {
            fputcsv($output, [
                $order['invoice_no'],
                $order['customer_name'],
                $order['phone'],
                $order['total_pairs'],
                $order['subtotal'],
                $order['total'],
                $order['payment_status'],
                $order['payment_method'],
                $order['date_in'],
                $order['date_out'],
                $order['status']
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportPaymentCSV($orders, $period) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="payment_report_' . $period . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Header
        fputcsv($output, [
            'Invoice No', 'Customer Name', 'Total Amount', 'Payment Method', 
            'Payment Status', 'Date In', 'Date Paid'
        ]);
        
        // CSV Data
        foreach ($orders as $order) {
            if ($order['payment_status'] === 'paid') {
                fputcsv($output, [
                    $order['invoice_no'],
                    $order['customer_name'],
                    $order['total'],
                    $order['payment_method'],
                    $order['payment_status'],
                    $order['date_in'],
                    $order['date_out'] // Assuming date_out is when payment was made
                ]);
            }
        }
        
        fclose($output);
        exit;
    }

    private function exportRevenuePDF($stats, $period) {
        // For PDF export, we would typically use a library like TCPDF or FPDF
        // For now, we'll redirect to HTML view with print parameter
        header('Location: ?page=reports&type=revenue&period=' . $period . '&format=html&print=1');
        exit;
    }

    private function exportOrdersPDF($orders, $period) {
        // For PDF export, we would typically use a library like TCPDF or FPDF
        // For now, we'll redirect to HTML view with print parameter
        header('Location: ?page=reports&type=orders&period=' . $period . '&format=html&print=1');
        exit;
    }

    private function exportPaymentPDF($orders, $period) {
        // For PDF export, we would typically use a library like TCPDF or FPDF
        // For now, we'll redirect to HTML view with print parameter
        header('Location: ?page=reports&type=payment&period=' . $period . '&format=html&print=1');
        exit;
    }

    public function getDashboardData() {
        requireAdmin();
        
        header('Content-Type: application/json');
        
        $period = $_GET['period'] ?? 'month';
        $stats = $this->order->getStats($period);
        
        echo json_encode($stats);
    }
}
?>