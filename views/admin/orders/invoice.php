<?php
require_once __DIR__ . '/../../../core/Database.php';
require_once __DIR__ . '/../../../core/Security.php';
require_once __DIR__ . '/../../../core/init.php';
require_once __DIR__ . '/../../../controllers/SettingsController.php';

// Get order data
$orderId = $_GET['id'] ?? 0;
if ($orderId <= 0) {
    die('Invalid order ID');
}

$database = new Database();
$db = $database->getConnection();
$orderQuery = "SELECT o.*, u.username as created_by_name 
               FROM orders o 
               LEFT JOIN users u ON o.created_by = u.id 
               WHERE o.id = ? LIMIT 1";
$stmt = $db->prepare($orderQuery);
$stmt->bindParam(1, $orderId);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die('Order not found');
}

// Get order items
$itemsQuery = "SELECT * FROM order_items WHERE order_id = ? ORDER BY pair_index";
$itemsStmt = $db->prepare($itemsQuery);
$itemsStmt->bindParam(1, $orderId);
$itemsStmt->execute();
$orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get settings
$settingsController = new SettingsController();
$shopName = $settingsController->getSetting('shop_name', 'DR.SHOEZCLEAN');
$shopPhone = $settingsController->getSetting('shop_phone', '0821-4840-5389');
$shopAddress = $settingsController->getSetting('shop_address', 'Jl. Yusuf Bauty No. 1A Gowa');
$bankName = $settingsController->getSetting('bank_name', 'BCA');
$bankAccount = $settingsController->getSetting('bank_account', '3501348811');
$bankHolder = $settingsController->getSetting('bank_holder', 'Risma Melani Alvionitha');
$qrCodePath = $settingsController->getSetting('qr_code_path', '');

// Format dates
$dateIn = !empty($order['date_in']) ? date('H:i d M y', strtotime($order['date_in'])) : '';
// Jika jam 00:00, tampilkan hanya tanggalnya
if (!empty($order['date_in']) && date('H:i', strtotime($order['date_in'])) === '00:00') {
    $dateIn = date('d M y', strtotime($order['date_in']));
}
$dateEstimate = !empty($order['date_estimate_out']) ? date('d M y', strtotime($order['date_estimate_out'])) : '';

// Format payment status
$paymentStatusText = $order['payment_status'] === 'paid' ? 'LUNAS' : 'BELUM BAYAR';

// Calculate totals
$subtotal = 0;
foreach ($orderItems as $item) {
    $subtotal += $item['service_price'];
}
$total = $subtotal;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice <?= htmlspecialchars($order['invoice_no']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.2;
            color: #000;
            background: #f5f5f5;
            padding: 20px;
        }

        .invoice-container {
            max-width: 280px;
            margin: 0 auto;
            background: white;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }

        .shop-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .shop-info {
            font-size: 9px;
            line-height: 1.3;
        }

        /* Invoice Details */
        .invoice-header {
            text-align: center;
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px dashed #000;
        }

        .invoice-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .invoice-number {
            font-size: 11px;
            margin-bottom: 1px;
        }

        /* Customer Info */
        .customer-info {
            margin: 8px 0;
            font-size: 10px;
        }

        .info-row {
            display: flex;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .info-label {
            width: 85px;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        /* Items */
        .items-section {
            margin: 8px 0;
        }

        .item {
            margin-bottom: 6px;
            font-size: 10px;
        }

        .item-brand {
            font-weight: bold;
            margin-bottom: 1px;
        }

        .item-service {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
        }

        /* Summary */
        .summary {
            margin: 8px 0;
            padding-top: 5px;
            border-top: 1px dashed #000;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 10px;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px dashed #000;
            padding-top: 3px;
            margin-top: 3px;
        }

        /* Payment Status */
        .payment-status {
            text-align: center;
            margin: 10px 0;
            padding: 5px;
            font-weight: bold;
            font-size: 12px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }

        /* Payment Info */
        .payment-info {
            margin: 8px 0;
            font-size: 9px;
            text-align: center;
        }

        .payment-info strong {
            font-size: 10px;
        }

        .bank-details {
            margin: 5px 0;
            line-height: 1.4;
        }

        .qr-section {
            text-align: center;
            margin: 10px 0;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            margin: 5px auto;
            border: 1px solid #ddd;
        }

        .qr-placeholder {
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            margin: 5px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9f9;
            font-size: 9px;
        }

        .scan-text {
            font-size: 9px;
            margin-top: 3px;
        }

        /* Terms */
        .terms {
            margin: 10px 0;
            padding: 8px 5px;
            background: #f9f9f9;
            font-size: 8px;
            line-height: 1.4;
            border-top: 1px dashed #000;
        }

        .terms-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 9px;
        }

        .terms-content {
            text-align: justify;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #000;
            font-size: 10px;
            line-height: 1.3;
        }

        .footer-thanks {
            margin-bottom: 3px;
        }

        .footer-promo {
            font-size: 9px;
            margin-top: 3px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex !important;
            gap: 10px;
            justify-content: center;
            margin: 15px 0;
            padding: 15px 0;
            border-top: 2px solid #333;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex !important;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .print-button {
            background: #333;
            color: white;
        }

        .print-button:hover {
            background: #000;
        }

        .download-button {
            background: #28a745;
            color: white;
        }

        .download-button:hover {
            background: #218838;
        }

        /* Paper Size Selector */
        .paper-size-selector {
            text-align: center;
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .paper-size-selector label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .paper-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .paper-btn {
            padding: 8px 16px;
            border: 2px solid #333;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 11px;
        }

        .paper-btn:hover {
            background: #f0f0f0;
        }

        .paper-btn.active {
            background: #333;
            color: white;
        }

        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }

            .invoice-container {
                max-width: 58mm;
                padding: 2mm;
                box-shadow: none;
                margin: 0;
            }

            .action-buttons,
            .paper-size-selector {
                display: none !important;
            }

            .header {
                margin-bottom: 2mm;
            }

            .divider {
                margin: 1mm 0;
            }

            .footer {
                font-size: 9px;
            }
        }

        /* Paper size classes */
        body.paper-58 .invoice-container {
            max-width: 200px;
            font-size: 9px;
        }

        body.paper-80 .invoice-container {
            max-width: 280px;
            font-size: 11px;
        }

        body.paper-a4 .invoice-container {
            max-width: 300px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="shop-name"><?= htmlspecialchars($shopName) ?></div>
            <div class="shop-info">
                @dr.shoezclean<br>
                <?= htmlspecialchars($shopPhone) ?><br>
                <?= htmlspecialchars($shopAddress) ?>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-header">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">#<?= htmlspecialchars($order['invoice_no']) ?></div>
        </div>

        <!-- Customer Info -->
        <div class="customer-info">
            <div class="info-row">
                <span class="info-label">Customer:</span>
                <span class="info-value"><?= htmlspecialchars($order['customer_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tgl Masuk:</span>
                <span class="info-value"><?= $dateIn ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tgl Estimasi:</span>
                <span class="info-value"><?= $dateEstimate ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir:</span>
                <span class="info-value"><?= htmlspecialchars($order['created_by_name'] ?? 'Admin') ?></span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Items -->
        <div class="items-section">
            <?php foreach ($orderItems as $item): ?>
            <?php 
            // Persingkat nama layanan dengan menghapus text dalam kurung
            $serviceName = htmlspecialchars($item['service_name']);
            $serviceName = preg_replace('/\s*\([^)]*\)\s*/', '', $serviceName);
            ?>
            <div class="item">
                <div class="item-brand"><?= htmlspecialchars($item['brand']) ?></div>
                <div class="item-service">
                    <span><?= $serviceName ?></span>
                    <span><?= number_format($item['service_price'], 0, ',', '.') ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="divider"></div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <span>Sub Total</span>
                <span><?= number_format($subtotal, 0, ',', '.') ?></span>
            </div>
            <div class="summary-row total">
                <span>TOTAL</span>
                <span><?= number_format($total, 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="payment-status">
            -----<?= $paymentStatusText ?>-----
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <strong>Pembayaran:</strong><br>
            <div class="bank-details">
                <?= htmlspecialchars($bankName) ?> - <?= htmlspecialchars($bankAccount) ?><br>
                <?= htmlspecialchars($bankHolder) ?>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="terms">
            <div class="terms-title">Syarat & Ketentuan:</div>
            <div class="terms-content">
                Segala bentuk kerusakan akibat pencucian sepatu bukan tanggung jawab dari tim dr.shoezclean karena adanya beberapa faktor mulai dari usia sepatu atau bahan, kualitas hingga cara pemakaiannya. Melalupun sudah kami lakukan dengan semaksimal mungkin.
                <br><br>
                Perlu diketahui bahwa tidak semua noda/kotoran di sepatu dapat hilang dengan sempurna atau kembali seperti barunya, hal tetapi seburuk-buruknya kondisi sepatu akan jadi lebih baik dari sebelumnya.
            </div>
        </div>

        <!-- QR Code Section -->
        <div class="qr-section">
            <?php if (!empty($qrCodePath)): ?>
                <img src="<?= htmlspecialchars($qrCodePath) ?>" alt="QR Code Payment" class="qr-code">
            <?php else: ?>
                <div class="qr-placeholder">SCAN</div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-thanks">Terimakasih telah menggunakan jasa kami</div>
        </div>

        <!-- Paper Size Selector (Hidden on Print) -->
        <div class="paper-size-selector">
            <label><strong>Pilih Ukuran Kertas:</strong></label>
            <div class="paper-buttons">
                <button class="paper-btn active" data-size="58" onclick="setPaperSize('58')">
                    <i class="fas fa-receipt"></i> 58mm
                </button>
                <button class="paper-btn" data-size="80" onclick="setPaperSize('80')">
                    <i class="fas fa-file-invoice"></i> 80mm
                </button>
                <button class="paper-btn" data-size="a4" onclick="setPaperSize('a4')">
                    <i class="fas fa-file"></i> A4
                </button>
            </div>
        </div>

        <!-- Action Buttons (Hidden on Print) -->
        <div class="action-buttons">
            <button class="btn print-button" onclick="window.print()">
                <i class="fas fa-print"></i>
                Cetak Invoice
            </button>
            <button class="btn download-button" onclick="downloadInvoice()">
                <i class="fas fa-download"></i>
                Download PDF
            </button>
        </div>
    </div>

    <script>
        function setPaperSize(size) {
            document.body.classList.remove('paper-58', 'paper-80', 'paper-a4');
            document.body.classList.add('paper-' + size);
            
            document.querySelectorAll('.paper-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-size="${size}"]`).classList.add('active');
            
            localStorage.setItem('preferredPaperSize', size);
        }

        function downloadInvoice() {
            window.print();
        }

        window.addEventListener('load', function() {
            const savedSize = localStorage.getItem('preferredPaperSize') || '58';
            setPaperSize(savedSize);
        });

        const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile) {
            setPaperSize('58');
        }
    </script>
</body>
</html>