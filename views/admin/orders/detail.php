<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #7B2C2C;
            --secondary-color: #800000;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-bg: #f8f9fa;
            --white: #ffffff;
            --text-dark: #222222;
            --border-color: #dee2e6;
        }

        body {
            background: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar ul, .sidebar li {
            list-style: none !important;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .sidebar-menu .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }

        .page-header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }

        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            width: 150px;
            flex-shrink: 0;
        }

        .info-value {
            color: var(--text-dark);
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }

        .status-paid {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }

        .status-cancelled {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }

        .service-item {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary-color);
        }

        .service-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .service-number {
            background: var(--primary-color);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .service-price {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(123, 44, 44, 0.3);
        }

        .summary-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 25px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .summary-row:last-child {
            border-bottom: none;
            font-size: 20px;
            font-weight: 700;
            margin-top: 10px;
            padding-top: 10px;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .info-row {
                flex-direction: column;
            }

            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <?php 
            require_once __DIR__ . '/../helpers/logo_helper.php';
            $logoPath = getLogoPath();
            ?>
            <div class="sidebar-logo w-12 h-12 bg-white rounded-full flex items-center justify-center overflow-hidden">
                <img src="<?= $logoPath ?>" alt="<?= getLogoAlt() ?>" class="w-full h-full object-cover">
            </div>
            <h5>Dr.ShoezClean</h5>
            <small>Admin Panel</small>
        </div>
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="?page=dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="?page=orders">
                        <i class="fas fa-shopping-cart"></i> Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?page=reports">
                        <i class="fas fa-chart-bar"></i> Laporan
                    </a>
                </li>
                <?php if (isSuperuser()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="?page=users">
                        <i class="fas fa-users"></i> Pengguna
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="?page=settings">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="?page=change_password">
                        <i class="fas fa-key"></i> Ubah Password
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../login.php?action=logout">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h4 class="mb-1">Detail Pesanan</h4>
                <small class="text-muted">Invoice: <?= $order['invoice_no'] ?></small>
            </div>
            <div>
                <a href="?page=orders" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="row">
            <!-- Order Information -->
            <div class="col-lg-8">
                <div class="detail-card">
                    <h5 class="section-title">
                        <i class="fas fa-info-circle me-2"></i>Informasi Pesanan
                    </h5>
                    
                    <div class="info-row">
                        <div class="info-label">Nomor Invoice</div>
                        <div class="info-value"><strong><?= $order['invoice_no'] ?></strong></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Nama Customer</div>
                        <div class="info-value"><?= htmlspecialchars($order['customer_name']) ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Telepon</div>
                        <div class="info-value"><?= htmlspecialchars($order['phone']) ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Tanggal Masuk</div>
                        <div class="info-value"><?= formatDate($order['date_in']) ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Estimasi Selesai</div>
                        <div class="info-value">
                            <?= $order['date_estimate_out'] ? formatDate($order['date_estimate_out']) : 'Menunggu pemilihan layanan' ?>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Tanggal Pengambilan</div>
                        <div class="info-value">
                            <?= $order['date_out'] ? formatDate($order['date_out']) : 'Belum diambil' ?>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Status Pembayaran</div>
                        <div class="info-value">
                            <span class="status-badge status-<?= $order['payment_status'] ?>">
                                <?= $order['payment_status'] === 'paid' ? 'LUNAS' : ($order['payment_status'] === 'pending' ? 'BELUM BAYAR' : 'DIBATALKAN') ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($order['payment_method']): ?>
                    <div class="info-row">
                        <div class="info-label">Metode Pembayaran</div>
                        <div class="info-value">
                            <?= ucfirst($order['payment_method']) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($order['notes']): ?>
                    <div class="info-row">
                        <div class="info-label">Catatan</div>
                        <div class="info-value"><?= htmlspecialchars($order['notes']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Service Details -->
                <div class="detail-card">
                    <h5 class="section-title">
                        <i class="fas fa-concierge-bell me-2"></i>Detail Layanan
                    </h5>
                    
                    <?php if (empty($order['items'])): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada layanan yang dipilih</p>
                        <a href="?page=order_edit&id=<?= $order['id'] ?>" class="btn btn-primary-custom">
                            <i class="fas fa-plus me-2"></i>Tambah Layanan
                        </a>
                    </div>
                    <?php else: ?>
                    <?php foreach ($order['items'] as $index => $item): ?>
                    <div class="service-item">
                        <div class="service-header">
                            <div class="d-flex align-items-center">
                                <div class="service-number me-3"><?= $index + 1 ?></div>
                                <div>
                                    <h6 class="mb-1">Sepatu ke-<?= $index + 1 ?></h6>
                                    <p class="mb-0 text-muted"><?= htmlspecialchars($item['brand']) ?></p>
                                </div>
                            </div>
                            <div class="service-price">
                                <?= formatCurrency($item['service_price']) ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Layanan:</small>
                                <p class="mb-0"><?= htmlspecialchars($item['service_name']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Kode Layanan:</small>
                                <p class="mb-0"><?= htmlspecialchars($item['service_code']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Summary & Actions -->
            <div class="col-lg-4">
                <div class="summary-card sticky-top" style="top: 20px;">
                    <h5 class="mb-4">
                        <i class="fas fa-calculator me-2"></i>Ringkasan Biaya
                    </h5>
                    
                    <div class="summary-row">
                        <span>Jumlah Pasang:</span>
                        <span><?= $order['total_pairs'] ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span><?= formatCurrency($order['subtotal']) ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Total:</span>
                        <span><?= formatCurrency($order['total']) ?></span>
                    </div>
                    
                    <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
                    
                    <div class="d-grid gap-2">
                        <a href="?page=order_edit&id=<?= $order['id'] ?>" class="btn btn-light">
                            <i class="fas fa-edit me-2"></i>Edit Pesanan
                        </a>
                        
                        <a href="?page=print_invoice&id=<?= $order['id'] ?>" 
                           class="btn btn-outline-light" target="_blank">
                            <i class="fas fa-print me-2"></i>Cetak Invoice
                        </a>
                        
                        <?php if (isSuperuser()): ?>
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="deleteOrder(<?= $order['id'] ?>)">
                            <i class="fas fa-trash me-2"></i>Hapus Pesanan
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteOrder(orderId) {
            if (confirm('Apakah Anda yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan.')) {
                const csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';
                
                fetch('?page=order_delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${orderId}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pesanan berhasil dihapus');
                        window.location.href = '?page=orders';
                    } else {
                        alert('Gagal menghapus pesanan: ' + (data.message || 'Terjadi kesalahan'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus pesanan');
                });
            }
        }
    </script>
</body>
</html>