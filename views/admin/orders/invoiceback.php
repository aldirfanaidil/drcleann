<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?= $order['invoice_no'] ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Base styles */
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
        }

        .invoice-container {
            max-width: 320px;
            margin: 0 auto;
            background: white;
            padding: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #7B2C2C;
            padding-bottom: 15px;
        }

        .logo {
            width: 40px;
            height: 40px;
            background: #7B2C2C;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            overflow: hidden;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .shop-name {
            font-size: 24px;
            font-weight: 700;
            color: #7B2C2C;
            margin: 0;
        }

        .shop-subtitle {
            font-size: 14px;
            color: #666;
            margin: 5px 0 0;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .invoice-number {
            font-size: 16px;
            font-weight: bold;
        }

        .invoice-date {
            font-size: 12px;
            color: #666;
        }

        .customer-section {
            margin-bottom: 10px;
        }

        .info-row {
            display: flex;
            margin-bottom: 4px;
        }

        .info-label {
            font-weight: bold;
            width: 80px;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
        }

        .items-section {
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 6px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
            padding: 2px 0;
        }

        .item-details {
            flex: 1;
        }

        .brand-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 1px;
        }

        .service-name {
            font-size: 11px;
            color: #333;
        }

        .item-price {
            font-weight: bold;
            font-size: 12px;
            text-align: right;
            margin-left: 10px;
        }

        .no-items {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 10px;
        }

        .summary-section {
            margin-bottom: 20px;
        }

        .summary-table {
            width: 100%;
            margin-left: auto;
            margin-bottom: 15px;
        }

        .summary-table td {
            padding: 5px;
            border: 1px solid #ddd;
        }

        .summary-table .total-row {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #7B2C2C;
        }

        .payment-status {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            font-weight: bold;
            font-size: 16px;
        }

        .payment-status.paid {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }

        .payment-status.pending {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }

        .payment-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .bank-info {
            margin-bottom: 10px;
            font-size: 12px;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            margin: 10px auto;
            display: block;
        }

        .qr-placeholder {
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            margin: 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9f9;
            color: #666;
            font-size: 10px;
        }

        .terms-section {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9f9;
            font-size: 10px;
            line-height: 1.4;
        }

        .terms-section h4 {
            font-size: 12px;
            margin-bottom: 10px;
            color: #333;
        }

        .terms-section ol {
            margin: 0;
            padding-left: 20px;
        }

        .terms-section li {
            margin-bottom: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }

        /* Action buttons styles */
        .action-buttons {
            display: flex !important;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
            padding: 20px 0;
            border-top: 2px solid #7B2C2C;
        }

        .print-button, .download-button {
            padding: 12px 24px;
            background: #7B2C2C;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-flex !important;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
        }

        .download-button {
            background: #28a745;
        }

        .print-button:hover {
            background: #5a2222;
        }

        .download-button:hover {
            background: #218838;
        }

        /* Paper size selector */
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
            color: #333;
        }

        .paper-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .paper-btn {
            padding: 8px 16px;
            border: 2px solid #7B2C2C;
            background: white;
            color: #7B2C2C;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .paper-btn:hover {
            background: #f8f9fa;
        }

        .paper-btn.active {
            background: #7B2C2C;
            color: white;
        }

        /* Print styles - HIDE BUTTONS WHEN PRINTING */
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
                font-family: 'Courier New', monospace;
                font-size: 14px;
                line-height: 1.4;
            }

            .invoice-container {
                background: white;
                padding: 12px;
                box-shadow: none;
                margin: 0 auto;
                max-width: 300px;
            }

            /* Hide all control elements when printing */
            .action-buttons,
            .paper-size-selector {
                display: none !important;
            }

            /* Compact print styles */
            .header {
                margin-bottom: 6px !important;
                border-bottom: 1px solid #000 !important;
                padding-bottom: 3px !important;
            }

            .logo {
                width: 20px !important;
                height: 20px !important;
                margin: 0 auto 2px !important;
            }

            .shop-name {
                font-size: 18px !important;
                margin: 0 !important;
                line-height: 1.3 !important;
            }

            .invoice-info {
                margin-bottom: 8px !important;
                font-size: 14px !important;
            }

            .customer-section {
                margin-bottom: 8px !important;
            }

            .info-row {
                margin-bottom: 3px !important;
                font-size: 12px !important;
                line-height: 1.3 !important;
            }

            .info-label {
                width: 60px !important;
                font-size: 12px !important;
            }

            .info-value {
                font-size: 12px !important;
            }

            .items-section {
                margin-bottom: 10px !important;
            }

            .section-title {
                font-size: 12px !important;
                margin-bottom: 4px !important;
                padding-bottom: 2px !important;
            }

            .item-row {
                margin-bottom: 4px !important;
                padding: 1px 0 !important;
            }

            .brand-name {
                font-size: 11px !important;
                margin-bottom: 1px !important;
            }

            .service-name {
                font-size: 10px !important;
            }

            .item-price {
                font-size: 11px !important;
            }

            .summary-section {
                margin-bottom: 8px !important;
                border-top: 1px solid #000 !important;
                padding-top: 4px !important;
            }

            .summary-table td {
                font-size: 12px !important;
                padding: 2px !important;
            }

            .total-row {
                font-size: 14px !important;
                margin-top: 2px !important;
                border-top: 1px dashed #000 !important;
                padding-top: 2px !important;
            }

            .payment-status {
                padding: 6px !important;
                font-size: 14px !important;
                margin-bottom: 6px !important;
            }

            .payment-info {
                margin-bottom: 8px !important;
                font-size: 10px !important;
            }

            .qr-code img {
                max-width: 30mm !important;
                max-height: 30mm !important;
            }

            .terms-section {
                margin-bottom: 8px !important;
                font-size: 11px !important;
            }

            .terms-section h4 {
                font-size: 12px !important;
                margin-bottom: 2px !important;
            }

            .terms-section li {
                margin-bottom: 1px !important;
                font-size: 11px !important;
                line-height: 1.2 !important;
            }

            .footer {
                margin-top: 6px !important;
                border-top: 1px solid #000 !important;
                padding-top: 4px !important;
                font-size: 10px !important;
                text-align: center !important;
            }
        }

        /* Paper size classes */
        body.paper-58 .invoice-container {
            max-width: 200px;
            font-size: 11px;
            padding: 8px;
        }

        body.paper-80 .invoice-container {
            max-width: 280px;
            font-size: 12px;
            padding: 10px;
        }

        body.paper-a4 .invoice-container {
            max-width: 300px;
            font-size: 14px;
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <?php 
            require_once __DIR__ . '/../helpers/logo_helper.php';
            $logoPath = getLogoPath();
            ?>
            <div class="logo w-16 h-16 flex items-center justify-center overflow-hidden">
                <img src="<?= $logoPath ?>" alt="<?= getLogoAlt() ?>" class="w-full h-full object-cover">
            </div>
            <div class="shop-name"><?= htmlspecialchars(getSetting('shop_name', 'Dr.ShoezClean')) ?></div>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div>
                <div class="invoice-number">INVOICE: <?= $order['invoice_no'] ?></div>
                <div class="invoice-date">Tanggal: <?= date('d/m/Y') ?></div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="customer-section">
            <div class="info-row">
                <div class="info-label">Customer:</div>
                <div class="info-value"><?= htmlspecialchars($order['customer_name']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Tgl Masuk:</div>
                <div class="info-value"><?= formatDate($order['date_in']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Estimasi:</div>
                <div class="info-value">
                    <?php 
                    if (!empty($order['date_estimate_out'])) {
                        echo formatDate($order['date_estimate_out']);
                    } else {
                        // Default 3 hari kerja dari tanggal masuk
                        echo formatDate(date('Y-m-d', strtotime($order['date_in'] . ' + 3 days')));
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Items List -->
        <div class="items-section">
            <div class="section-title">DETAIL LAYANAN:</div>
            <?php if (empty($order['items'])): ?>
                <div class="no-items">Belum ada layanan yang dipilih</div>
            <?php else: ?>
                <?php foreach ($order['items'] as $index => $item): ?>
                <div class="item-row">
                    <div class="item-details">
                        <div class="brand-name"><?= htmlspecialchars($item['brand']) ?></div>
                        <div class="service-name"><?= htmlspecialchars($item['service_name']) ?></div>
                    </div>
                    <div class="item-price"><?= formatCurrency($item['service_price']) ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Summary -->
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td>Jumlah Pasang:</td>
                    <td><?= $order['total_pairs'] ?></td>
                </tr>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right"><?= formatCurrency($order['subtotal']) ?></td>
                </tr>
                <tr class="total-row">
                    <td>TOTAL:</td>
                    <td class="text-right"><?= formatCurrency($order['total']) ?></td>
                </tr>
            </table>
        </div>

        <!-- Payment Status -->
        <div class="payment-status <?= $order['payment_status'] ?>">
            <?php if ($order['payment_status'] === 'paid'): ?>
                ✓ LUNAS
            <?php else: ?>
                ----- BELUM BAYAR -----
            <?php endif; ?>
        </div>

        <!-- Payment Information -->
        <div class="payment-info">
            <div class="bank-info">
                <strong>Informasi Pembayaran:</strong><br>
                Bank: <?= htmlspecialchars(getSetting('bank_name', 'BCA')) ?><br>
                No. Rekening: <?= htmlspecialchars(getSetting('bank_account', '876-543-2109')) ?><br>
                A.n: <?= htmlspecialchars(getSetting('bank_holder', 'PT. Dr.ShoezClean Indonesia')) ?>
            </div>
            
            <div class="qr-code">
                <?php 
                // Check if QR file exists in the uploads directory
                $qrFiles = glob(__DIR__ . '/../assets/uploads/qr/*.{png,jpg,jpeg,svg}', GLOB_BRACE);
                if (!empty($qrFiles)): 
                    $qrFileName = basename($qrFiles[0]);
                    // Use absolute path to ensure it works from any context
                    $qrUrl = '/drshoezclean/views/admin/assets/uploads/qr/' . $qrFileName;
                ?>
                    <img src="<?= $qrUrl ?>" alt="QR Code" style="max-width: 100%; max-height: 100%;">
                <?php else: ?>
                    <div class="qr-placeholder">QR Code</div>
                <?php endif; ?>
            </div>
            
            <small>Scan QR Code untuk pembayaran</small>
        </div>

        <!-- Terms and Conditions -->
        <div class="terms-section">
            <h4>Syarat & Ketentuan:</h4>
            <ol>
                <li>Barang yang sudah dibersihkan tidak dapat dikembalikan</li>
                <li>Pembayaran harus lunas sebelum pengambilan</li>
                <li>Estimasi pengerjaan dapat berubah tergantung kondisi sepatu</li>
                <li>Kami tidak bertanggung jawab atas kerusakan yang sudah ada sebelumnya</li>
            </ol>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>Terima kasih telah mempercayakan sepatu Anda kepada kami</div>
            <div><strong><?= htmlspecialchars(getSetting('shop_name', 'Dr.ShoezClean')) ?></strong></div>
            <div><?= htmlspecialchars(getSetting('shop_address', 'Jl. Sudirman No. 123, Jakarta Pusat')) ?> | <?= htmlspecialchars(getSetting('shop_phone', '+62 21-1234-5678')) ?></div>
        </div>

        <!-- Paper Size Selector -->
        <div class="paper-size-selector">
            <label><strong>Ukuran Kertas:</strong></label>
            <div class="paper-buttons">
                <button class="paper-btn active" data-size="58" onclick="setPaperSize('58')">
                    <i class="fas fa-receipt"></i>
                    58mm
                </button>
                <button class="paper-btn" data-size="80" onclick="setPaperSize('80')">
                    <i class="fas fa-file-invoice"></i>
                    80mm
                </button>
                <button class="paper-btn" data-size="a4" onclick="setPaperSize('a4')">
                    <i class="fas fa-file"></i>
                    A4
                </button>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="print-button" onclick="window.print()">
                <i class="fas fa-print"></i>
                Cetak Invoice
            </button>
            <button class="download-button" onclick="downloadInvoice()">
                <i class="fas fa-download"></i>
                Download PDF
            </button>
        </div>
    </div>

    <script>
        console.log('Invoice script loaded');
        
        // Set paper size function
        function setPaperSize(size) {
            console.log('Setting paper size to:', size);
            
            // Remove all paper size classes
            document.body.classList.remove('paper-58', 'paper-80', 'paper-a4');
            
            // Add selected paper size class
            document.body.classList.add('paper-' + size);
            
            // Update active button
            document.querySelectorAll('.paper-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-size="${size}"]`).classList.add('active');
            
            // Save to localStorage for persistence
            localStorage.setItem('preferredPaperSize', size);
        }

        // Download invoice as PDF (placeholder function)
        function downloadInvoice() {
            console.log('Download clicked');
            // For now, just trigger print
            // In the future, you can integrate a PDF library
            window.print();
        }

        // Auto-focus on print when page loads
        window.addEventListener('load', function() {
            console.log('Page loaded, initializing...');
            
            // Test button visibility
            const buttons = document.querySelectorAll('.print-button, .download-button');
            console.log('Buttons found:', buttons.length);
            
            // Load preferred paper size from localStorage
            const savedSize = localStorage.getItem('preferredPaperSize');
            if (savedSize) {
                setPaperSize(savedSize);
            }
            
            // Optional: Auto print after 2 seconds
            // setTimeout(() => window.print(), 2000);
        });

        // Simple mobile detection
        const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        if (isMobile) {
            console.log('Mobile device detected');
            setPaperSize('58');
        }
    </script>
</body>
</html>