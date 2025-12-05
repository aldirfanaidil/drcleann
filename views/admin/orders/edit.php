<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesanan - <?= APP_NAME ?></title>
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

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(123, 44, 44, 0.25);
        }

        .service-item {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
        }

        .service-header {
            display: flex;
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
            margin-right: 15px;
        }

        .service-title {
            font-weight: 600;
            color: var(--text-dark);
        }

        .price-display {
            background: var(--success-color);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 14px;
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

        .btn-secondary-custom {
            background: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s;
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
            font-size: 18px;
            font-weight: 700;
            margin-top: 10px;
            padding-top: 10px;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .invalid-feedback {
            font-size: 12px;
            margin-top: 5px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .user-details {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .user-role {
            font-size: 12px;
            color: #666;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
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
                    <a class="nav-link" href="?page=logout">
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
                <h4 class="mb-1">Edit Pesanan</h4>
                <small class="text-muted">Invoice: <?= $order['invoice_no'] ?></small>
            </div>
            <div>
                <a href="?page=order_detail&id=<?= $order['id'] ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Container for AJAX alerts -->
        <div id="alert-container"></div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" action="?page=order_edit&id=<?= $order['id'] ?>" id="editOrderForm" onsubmit="return validateEditForm()">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            
            <div class="row">
                <!-- Customer Information -->
                <div class="col-lg-8">
                    <div class="form-card">
                        <h5 class="mb-4">
                            <i class="fas fa-user me-2"></i>Informasi Customer
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                       value="<?= $_SESSION['old']['customer_name'] ?? $order['customer_name'] ?>" required>
                                <?php if (isset($_SESSION['errors']['customer_name'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $_SESSION['errors']['customer_name'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Nomor WhatsApp</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= $_SESSION['old']['phone'] ?? $order['phone'] ?>" required>
                                <?php if (isset($_SESSION['errors']['phone'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $_SESSION['errors']['phone'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_in" class="form-label">Tanggal Masuk</label>
                                <input type="text" class="form-control" value="<?= $order['date_in'] ?>" readonly disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_estimate_out" class="form-label">Estimasi Selesai</label>
                                <input type="date" class="form-control" id="date_estimate_out" name="date_estimate_out" 
                                       value="<?= $order['date_estimate_out'] ?? '' ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_out" class="form-label">Tanggal Ambil (Selesai)</label>
                                <input type="date" class="form-control" id="date_out" name="date_out" 
                                       value="<?= $order['date_out'] ?? '' ?>">
                                <small class="text-muted">Isi tanggal saat pesanan selesai diambil</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="payment_status" class="form-label">Status Pembayaran</label>
                                <select class="form-select" id="payment_status" name="payment_status" required>
                                    <option value="pending" <?= ($order['payment_status'] === 'pending') ? 'selected' : '' ?>>Belum Bayar</option>
                                    <option value="paid" <?= ($order['payment_status'] === 'paid') ? 'selected' : '' ?>>Lunas</option>
                                    <option value="cancelled" <?= ($order['payment_status'] === 'cancelled') ? 'selected' : '' ?>>Dibatalkan</option>
                                </select>
                                <?php if (isset($_SESSION['errors']['payment_status'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $_SESSION['errors']['payment_status'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">Metode Pembayaran</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">Pilih Metode</option>
                                    <option value="cash" <?= ($order['payment_method'] === 'cash') ? 'selected' : '' ?>>Tunai</option>
                                    <option value="transfer" <?= ($order['payment_method'] === 'transfer') ? 'selected' : '' ?>>Transfer</option>
                                    <option value="qr" <?= ($order['payment_method'] === 'qr') ? 'selected' : '' ?>>QR Code</option>
                                </select>
                                <?php if (isset($_SESSION['errors']['payment_method'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $_SESSION['errors']['payment_method'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?= $_SESSION['old']['notes'] ?? $order['notes'] ?></textarea>
                        </div>
                    </div>

                    <!-- Service Selection -->
                    <div class="form-card">
                        <h5 class="mb-4">
                            <i class="fas fa-concierge-bell me-2"></i>Pemilihan Layanan
                        </h5>
                        
                        <div id="serviceItems">
                            <?php foreach ($order['items'] as $index => $item): ?>
                            <div class="service-item">
                                <div class="service-header">
                                    <div class="service-number"><?= $index + 1 ?></div>
                                    <div class="service-title">Sepatu ke-<?= $index + 1 ?></div>
                                    <div class="price-display" id="price-display-<?= $index ?>">
                                        <?= formatCurrency($item['service_price']) ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Merek Sepatu</label>
                                        <input type="text" class="form-control" name="brands[]" 
                                               value="<?= htmlspecialchars($item['brand']) ?>" required>
                                    </div>
                                    
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label">Jenis Layanan</label>
                                        <select class="form-select service-select" name="services[]" data-index="<?= $index ?>" required>
                                            <option value="">Pilih Layanan</option>
                                            <?php foreach ($GLOBALS['SERVICES'] as $code => $service): ?>
                                                <option value="<?= $code ?>" 
                                                        data-name="<?= $service['name'] ?>"
                                                        <?= ($item['service_code'] === $code) ? 'selected' : '' ?>>
                                                    <?= $service['name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Tipe Sepatu</label>
                                        <select class="form-select shoe-type-select" name="shoe_types[]" data-index="<?= $index ?>">
                                            <option value="">Pilih Tipe</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="prices[]" id="price-<?= $index ?>" value="<?= $item['service_price'] ?>">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="col-lg-4">
                    <div class="summary-card sticky-top" style="top: 20px;">
                        <h5 class="mb-4">
                            <i class="fas fa-calculator me-2"></i>Ringkasan Biaya
                        </h5>
                        
                        <div class="summary-row">
                            <span>Jumlah Pasang:</span>
                            <span id="total-pairs"><?= $order['total_pairs'] ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="subtotal"><?= formatCurrency($order['subtotal']) ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Total:</span>
                            <span id="total"><?= formatCurrency($order['total']) ?></span>
                        </div>
                        
                        <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-light">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="?page=print_invoice&id=<?= $order['id'] ?>" 
                               class="btn btn-outline-light" target="_blank">
                                <i class="fas fa-print me-2"></i>Cetak Invoice
                            </a>
                            <a href="?page=order_detail&id=<?= $order['id'] ?>" 
                               class="btn btn-outline-light">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Service prices data
        const services = <?= json_encode($GLOBALS['SERVICES']) ?>;

        // Initialize service selections
        document.addEventListener('DOMContentLoaded', function() {
            initializeServiceSelects();
            updateSummary();
            
            // Add event listeners
            document.querySelectorAll('.service-select').forEach(select => {
                select.addEventListener('change', function() {
                    const index = this.dataset.index;
                    updateShoeTypes(index, this.value);
                    updatePrice(index);
                    updateSummary();
                });
            });

            document.querySelectorAll('.shoe-type-select').forEach(select => {
                select.addEventListener('change', function() {
                    const index = this.dataset.index;
                    updatePrice(index);
                    updateSummary();
                });
            });

            // Payment status change handler
            document.getElementById('payment_status').addEventListener('change', function() {
                const paymentMethod = document.getElementById('payment_method');
                if (this.value === 'paid') {
                    paymentMethod.required = true;
                } else {
                    paymentMethod.required = false;
                    paymentMethod.value = '';
                }
            });
        });

        function initializeServiceSelects() {
            document.querySelectorAll('.service-select').forEach(select => {
                const index = select.dataset.index;
                if (select.value) {
                    updateShoeTypes(index, select.value);
                }
            });
        }

        function updateShoeTypes(index, serviceCode) {
            const shoeTypeSelect = document.querySelector(`.shoe-type-select[data-index="${index}"]`);
            const serviceSelect = document.querySelector(`.service-select[data-index="${index}"]`);
            
            // Clear existing options
            shoeTypeSelect.innerHTML = '<option value="">Pilih Tipe</option>';
            
            if (serviceCode && services[serviceCode]) {
                const prices = services[serviceCode].prices;
                Object.keys(prices).forEach(type => {
                    const option = document.createElement('option');
                    option.value = prices[type];
                    option.textContent = type;
                    option.dataset.price = prices[type];
                    
                    // Select if this was previously selected
                    const currentPrice = document.getElementById(`price-${index}`).value;
                    if (currentPrice == prices[type]) {
                        option.selected = true;
                    }
                    
                    shoeTypeSelect.appendChild(option);
                });
            }
        }

        function updatePrice(index) {
            const shoeTypeSelect = document.querySelector(`.shoe-type-select[data-index="${index}"]`);
            const priceDisplay = document.getElementById(`price-display-${index}`);
            const priceInput = document.getElementById(`price-${index}`);
            
            if (shoeTypeSelect.value) {
                const price = parseFloat(shoeTypeSelect.value);
                priceDisplay.textContent = formatCurrency(price);
                priceInput.value = price;
            } else {
                priceDisplay.textContent = 'Rp 0';
                priceInput.value = 0;
            }
        }

        function updateSummary() {
            const prices = document.querySelectorAll('input[name="prices[]"]');
            let subtotal = 0;
            
            prices.forEach(input => {
                subtotal += parseFloat(input.value) || 0;
            });
            
            document.getElementById('subtotal').textContent = formatCurrency(subtotal);
            document.getElementById('total').textContent = formatCurrency(subtotal);
        }

        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        // AJAX Form Submission
        document.getElementById('editOrderForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            // Disable button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;

            // Clear previous validation errors
            document.querySelectorAll('.invalid-feedback.d-block').forEach(el => el.remove());
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    // Redirect after successful update
                    setTimeout(() => {
                        window.location.href = '?page=orders';
                    }, 2000);
                } else {
                    showAlert(data.message || 'Terjadi kesalahan.', 'danger');
                    if (data.errors) {
                        displayValidationErrors(data.errors);
                    }
                    // Log detailed error for debugging
                    console.error('Server response:', data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Tidak dapat terhubung ke server. Silakan coba lagi. Error: ' + error.message, 'danger');
            })
            .finally(() => {
                // Restore button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });

        function showAlert(message, type = 'success') {
            const container = document.getElementById('alert-container');
            const alertId = `alert-${Date.now()}`;
            const alertHtml = `
                <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            container.innerHTML = alertHtml; // Replace previous alert

            // Auto-dismiss the alert after 5 seconds
            setTimeout(() => {
                const alertElement = document.getElementById(alertId);
                if (alertElement) {
                    new bootstrap.Alert(alertElement).close();
                }
            }, 5000);
        }

        function displayValidationErrors(errors) {
            for (const field in errors) {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = errors[field];
                    input.parentNode.appendChild(errorDiv);
                }
            }
        }

        function validateEditForm() {
            // Clear previous validation
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            // Check if at least one service is added
            const brands = document.querySelectorAll('[name="brands[]"]');
            let hasService = false;
            
            brands.forEach((brand, index) => {
                if (brand.value.trim() !== '') {
                    const service = document.querySelector(`[name="services[]"][data-index="${index}"]`);
                    if (service && service.value.trim() !== '') {
                        hasService = true;
                    }
                }
            });
            
            if (!hasService) {
                showAlert('Harap tambahkan minimal satu layanan!', 'warning');
                return false;
            }

            // Validate payment method if status is paid
            const paymentStatus = document.querySelector('[name="payment_status"]').value;
            const paymentMethod = document.querySelector('[name="payment_method"]').value;
            
            if (paymentStatus === 'paid' && !paymentMethod) {
                showAlert('Metode pembayaran wajib diisi jika status sudah lunas!', 'warning');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
<?php unset($_SESSION['errors'], $_SESSION['old']); ?>