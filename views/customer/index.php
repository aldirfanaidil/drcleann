<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr.ShoezClean - Pemesanan Laundry Sepatu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #7B2C2C;
            --secondary-color: #800000;
            --light-bg: #f8f9fa;
            --white: #ffffff;
            --text-dark: #222222;
            --border-color: #dee2e6;
        }

        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, var(--white) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
        }

        .header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .logo-text h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: var(--primary-color);
        }

        .logo-text p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

        .main-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .form-card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 30px;
        }

        .form-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
            text-align: center;
        }

        .form-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(123, 44, 44, 0.25);
        }

        .brand-inputs {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .brand-input-item {
            background: var(--white);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            transition: all 0.3s;
        }

        .brand-input-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .brand-number {
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

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(123, 44, 44, 0.3);
        }

        .btn-secondary-custom {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .info-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
        }

        .info-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
        }

        .info-card p {
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .alert {
            border-radius: 10px;
            border: none;
            font-size: 14px;
        }

        .invalid-feedback {
            font-size: 12px;
            margin-top: 5px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 20px auto;
                padding: 0 15px;
            }

            .form-card {
                padding: 25px;
            }

            .form-title {
                font-size: 24px;
            }

            .logo-text h1 {
                font-size: 20px;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <?php 
                require_once __DIR__ . '/../admin/helpers/logo_helper.php';
                $logoPath = getLogoPath();
                ?>
                <div class="logo-icon w-12 h-12 bg-white rounded-full flex items-center justify-center overflow-hidden">
                    <img src="<?= $logoPath ?>" alt="<?= getLogoAlt() ?>" class="w-full h-full object-cover">
                </div>
                <div class="logo-text">
                    <h1>Dr.ShoezClean</h1>
                    <p>Professional Shoe Cleaning Service</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-container">
        <!-- Success Message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Order Form -->
        <div class="form-card fade-in">
            <h2 class="form-title">Form Pemesanan</h2>
            <p class="form-subtitle">Isi data diri Anda untuk memesan layanan cuci sepatu</p>

            <form method="POST" action="index.php?action=create_order" id="orderForm">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_name" class="form-label">
                            <i class="fas fa-user me-2"></i>Nama Lengkap
                        </label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" 
                               value="<?= $_SESSION['old']['customer_name'] ?? '' ?>" required>
                        <?php if (isset($_SESSION['errors']['customer_name'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= $_SESSION['errors']['customer_name'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone me-2"></i>Nomor WhatsApp
                        </label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?= $_SESSION['old']['phone'] ?? '' ?>" 
                               placeholder="0812-3456-7890" required>
                        <?php if (isset($_SESSION['errors']['phone'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= $_SESSION['errors']['phone'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="total_pairs" class="form-label">
                        <i class="fas fa-shoe-prints me-2"></i>Jumlah Pasang Sepatu
                    </label>
                    <input type="number" class="form-control" id="total_pairs" name="total_pairs" 
                           value="<?= $_SESSION['old']['total_pairs'] ?? 1 ?>" min="1" max="10" required>
                    <?php if (isset($_SESSION['errors']['total_pairs'])): ?>
                        <div class="invalid-feedback d-block">
                            <?= $_SESSION['errors']['total_pairs'] ?>
                        </div>
                    <?php endif; ?>
                    <small class="text-muted">Maksimal 10 pasang per pemesanan</small>
                </div>

                <!-- Dynamic Brand Inputs -->
                <div class="brand-inputs" id="brandInputs">
                    <h5 class="mb-3">
                        <i class="fas fa-tag me-2"></i>Data Sepatu
                    </h5>
                    <div id="brandInputContainer">
                        <!-- Brand inputs will be dynamically generated here -->
                    </div>
                </div>

                <div class="loading" id="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memproses pemesanan...</p>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="fas fa-paper-plane me-2"></i>Proses Pemesanan
                    </button>
                    <button type="reset" class="btn btn-secondary-custom">
                        <i class="fas fa-redo me-2"></i>Reset Form
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="info-card fade-in">
            <h3><i class="fas fa-info-circle me-2"></i>Informasi Penting</h3>
            <p><i class="fas fa-check me-2"></i>Pemesanan akan segera diproses oleh staff kami</p>
            <p><i class="fas fa-check me-2"></i>Pemilihan layanan dan pembayaran dilakukan di kasir</p>
            <p><i class="fas fa-check me-2"></i>Estimasi pengerjaan tergantung jenis layanan yang dipilih</p>
            <p><i class="fas fa-phone me-2"></i>Hubungi: +62 812-3456-7890 untuk info lebih lanjut</p>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Generate brand inputs dynamically
        function generateBrandInputs(count) {
            const container = document.getElementById('brandInputContainer');
            container.innerHTML = '';

            for (let i = 1; i <= count; i++) {
                const inputHtml = `
                    <div class="brand-input-item fade-in">
                        <div class="d-flex align-items-center mb-3">
                            <div class="brand-number me-3">${i}</div>
                            <h6 class="mb-0">Sepatu ke-${i}</h6>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Merek Sepatu</label>
                                <input type="text" class="form-control" name="brands[]" 
                                       placeholder="Contoh: Nike Air Max, Adidas Ultraboost, dll" required>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += inputHtml;
            }
        }

        // Initialize brand inputs on page load
        document.addEventListener('DOMContentLoaded', function() {
            const totalPairs = document.getElementById('total_pairs');
            generateBrandInputs(totalPairs.value);

            // Update brand inputs when total pairs changes
            totalPairs.addEventListener('change', function() {
                const count = parseInt(this.value) || 1;
                generateBrandInputs(count);
            });

            // Form submission
            document.getElementById('orderForm').addEventListener('submit', function(e) {
                // Show loading
                document.getElementById('loading').style.display = 'block';
                
                // Disable submit button
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4 && value.length <= 8) {
                value = value.slice(0, 4) + '-' + value.slice(4);
            } else if (value.length > 8) {
                value = value.slice(0, 4) + '-' + value.slice(4, 8) + '-' + value.slice(8, 12);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
<?php unset($_SESSION['errors'], $_SESSION['old']); ?>