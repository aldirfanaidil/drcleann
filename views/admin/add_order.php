<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pesanan - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .customer-search-result {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .customer-search-result:hover {
            background-color: #f3f4f6;
        }
        .service-item {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: #fafafa;
            transition: all 0.3s;
        }
        .service-item:hover {
            border-color: #7B2C2C;
            background: #fff;
        }
        .remove-service {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .price-display {
            font-size: 1.2rem;
            font-weight: bold;
            color: #7B2C2C;
        }
        .service-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 10px;
        }
        .service-card:hover {
            border-color: #7B2C2C;
            box-shadow: 0 2px 8px rgba(123, 44, 44, 0.1);
        }
        .service-card.selected {
            border-color: #7B2C2C;
            background: #fef2f2;
        }
        .service-price {
            font-weight: bold;
            color: #7B2C2C;
        }
        
        /* Hamburger menu */
        .hamburger {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #7B2C2C;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
        }
        
        .hamburger:hover {
            background: #5a2222;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }
            
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 70px 15px 20px 15px;
            }
            
            .page-header {
                padding: 15px;
                margin: 15px;
            }
            
            .service-card {
                padding: 10px;
                margin-bottom: 8px;
            }
            
            .form-control, .form-select {
                padding: 8px 12px;
                font-size: 14px;
            }
            
            .btn-primary-custom {
                padding: 8px 16px;
                font-size: 14px;
            }
            
            .grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar bg-gradient-to-br from-[#7B2C2C] to-[#800000] text-white w-64 fixed inset-y-0 left-0 z-50 overflow-y-auto transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full">
        <div class="sidebar-header p-5 text-center border-b border-white border-opacity-10">
            <?php 
            require_once __DIR__ . '/helpers/logo_helper.php';
            $logoPath = getLogoPath();
            ?>
            <div class="sidebar-logo w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-2 overflow-hidden">
                <img src="<?= $logoPath ?>" alt="<?= getLogoAlt() ?>" class="w-full h-full object-cover">
            </div>
            <h5 class="text-lg font-semibold">Dr.ShoezClean</h5>
            <small class="text-sm opacity-80">Admin Panel</small>
        </div>
        <div class="sidebar-menu py-5">
            <ul class="flex flex-col list-none">
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 hover:text-white transition-all duration-300 ease-in-out" href="?page=dashboard">
                        <i class="fas fa-tachometer-alt w-5 mr-3"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 bg-white bg-opacity-10 text-white" href="?page=orders">
                        <i class="fas fa-shopping-cart w-5 mr-3"></i> Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 " href="?page=reports">
                        <i class="fas fa-chart-bar w-5 mr-3"></i> Laporan
                    </a>
                </li>
                <?php if (isSuperuser()): ?>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 " href="?page=users">
                        <i class="fas fa-users w-5 mr-3"></i> Pengguna
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 " href="?page=settings">
                        <i class="fas fa-cog w-5 mr-3"></i> Pengaturan
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 " href="?page=change_password">
                        <i class="fas fa-key w-5 mr-3"></i> Ubah Password
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 " href="../login.php?action=logout">
                        <i class="fas fa-sign-out-alt w-5 mr-3"></i> Keluar
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Overlay for sidebar on mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black opacity-0 z-40 md:hidden transition-opacity duration-300 ease-in-out pointer-events-none"></div>
    
    <!-- Main Content -->
    <main class="md:ml-64 p-5 min-h-screen bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Top Header -->
            <div class="bg-white p-5 rounded-lg shadow mb-8 flex justify-between items-center">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="text-gray-500 focus:outline-none focus:text-gray-900 mr-4 md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h4 class="text-2xl font-semibold text-gray-800">Tambah Pesanan Baru</h4>
                        <small class="text-gray-600">Input data pesanan customer</small>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="?page=orders" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['success'] ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['error'] ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Order Form -->
            <form method="POST" action="?page=add_order&action=create" id="orderForm" onsubmit="return validateForm()">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <!-- Customer Search Section -->
                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <h5 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user-search mr-2"></i>Cari Customer
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Customer</label>
                            <input type="text" id="customerSearch" name="customer_search" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent"
                                   placeholder="Ketik nama customer..." autocomplete="off">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                            <input type="text" id="phoneSearch" name="phone_search" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent"
                                   placeholder="Ketik nomor telepon..." autocomplete="off">
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div id="searchResults" class="hidden">
                        <div class="border border-gray-200 rounded-lg max-h-40 overflow-y-auto">
                            <!-- Results will be populated here -->
                        </div>
                    </div>

                    <!-- Customer Info (Hidden initially) -->
                    <div id="customerInfo" class="hidden mt-4 p-4 bg-blue-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Customer</label>
                                <input type="text" id="customerName" name="customer_name" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                                <input type="text" id="customerPhone" name="phone" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Details Section -->
                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <h5 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-calendar-alt mr-2"></i>Detail Pesanan
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Masuk</label>
                            <input type="date" id="dateIn" name="date_in" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Selesai</label>
                            <input type="date" id="dateEstimateOut" name="date_estimate_out"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengambilan</label>
                            <input type="date" id="dateOut" name="date_out"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent"
                                  placeholder="Catatan khusus untuk pesanan ini..."></textarea>
                    </div>
                </div>

                <!-- Services Section -->
                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h5 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-concierge-bell mr-2"></i>Layanan & Sepatu
                        </h5>
                        <button type="button" onclick="addService()" class="bg-[#7B2C2C] text-white px-4 py-2 rounded-lg hover:bg-[#6B2222] transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambah Sepatu
                        </button>
                    </div>

                    <div id="servicesContainer">
                        <!-- Services will be added here dynamically -->
                    </div>

                    <!-- Service Selection Modal -->
                    <div id="serviceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                        <div class="bg-white rounded-lg p-6 max-w-4xl max-h-[80vh] overflow-y-auto w-full mx-4">
                            <div class="flex justify-between items-center mb-4">
                                <h5 class="text-lg font-semibold">Pilih Layanan</h5>
                                <button type="button" onclick="closeServiceModal()" class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach ($GLOBALS['SERVICES'] as $serviceCode => $service): ?>
                                <div class="border rounded-lg p-4">
                                    <h6 class="font-semibold mb-3"><?= $service['name'] ?></h6>
                                    <div class="space-y-2">
                                        <?php foreach ($service['prices'] as $type => $price): ?>
                                        <div class="service-card" data-service="<?= $serviceCode ?>" data-type="<?= $type ?>" data-price="<?= $price ?>" data-name="<?= $service['name'] ?>">
                                            <div class="flex justify-between items-center">
                                                <span><?= $type ?></span>
                                                <span class="service-price"><?= formatCurrency($price) ?></span>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <h5 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-credit-card mr-2"></i>Pembayaran
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                            <select name="payment_status" id="paymentStatus" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                <option value="pending">Belum Bayar</option>
                                <option value="paid">Lunas</option>
                            </select>
                        </div>
                        <div id="paymentMethodDiv">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                            <select name="payment_method" id="paymentMethod"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                <option value="">Pilih metode...</option>
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="qr">QRIS</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="bg-gradient-to-r from-[#7B2C2C] to-[#800000] text-white p-6 rounded-lg shadow">
                    <h5 class="text-lg font-semibold mb-4">
                        <i class="fas fa-calculator mr-2"></i>Ringkasan Biaya
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm opacity-80">Total Pasang</div>
                            <div class="text-2xl font-bold" id="totalPairs">0</div>
                        </div>
                        <div>
                            <div class="text-sm opacity-80">Subtotal</div>
                            <div class="text-2xl font-bold" id="subtotal">Rp 0</div>
                        </div>
                        <div>
                            <div class="text-sm opacity-80">Total</div>
                            <div class="text-2xl font-bold" id="total">Rp 0</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 mt-6">
                    <a href="?page=orders" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="bg-[#7B2C2C] text-white px-6 py-3 rounded-lg hover:bg-[#6B2222] transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan Pesanan
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        let services = [];
        let currentServiceIndex = 0;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeSidebarToggle();
            setDefaultDates();
            addService(); // Add first service by default
            
            // Customer search
            document.getElementById('customerSearch').addEventListener('input', searchCustomers);
            document.getElementById('phoneSearch').addEventListener('input', searchCustomers);
            
            // Payment status change
            document.getElementById('paymentStatus').addEventListener('change', togglePaymentMethod);
        });

        function setDefaultDates() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dateIn').value = today;
            
            // Add date validation listener
            document.getElementById('dateIn').addEventListener('change', validateDateIn);
        }

        function validateDateIn() {
            const dateInput = document.getElementById('dateIn');
            const selectedDate = new Date(dateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const maxFutureDate = new Date(today);
            maxFutureDate.setDate(today.getDate() + 1);
            
            const maxPastDate = new Date(today);
            maxPastDate.setDate(today.getDate() - 30);
            
            // Clear previous validation
            dateInput.setCustomValidity('');
            
            if (selectedDate > maxFutureDate) {
                dateInput.setCustomValidity('Tanggal masuk tidak boleh lebih dari 1 hari ke depan');
                dateInput.reportValidity();
                return false;
            }
            
            if (selectedDate < maxPastDate) {
                dateInput.setCustomValidity('Tanggal masuk tidak boleh lebih dari 30 hari yang lalu');
                dateInput.reportValidity();
                return false;
            }
            
            // Warning for today's date (not an error, just a confirmation)
            if (dateInput.value === today.toISOString().split('T')[0]) {
                if (!confirm('Anda memilih tanggal hari ini. Apakah ini benar-benar pesanan untuk hari ini?')) {
                    dateInput.value = '';
                    return false;
                }
            }
            
            return true;
        }

        function searchCustomers() {
            const nameQuery = document.getElementById('customerSearch').value;
            const phoneQuery = document.getElementById('phoneSearch').value;
            
            if (nameQuery.length < 2 && phoneQuery.length < 2) {
                document.getElementById('searchResults').classList.add('hidden');
                return;
            }

            fetch(`?page=add_order&action=searchCustomers&name=${encodeURIComponent(nameQuery)}&phone=${encodeURIComponent(phoneQuery)}`)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data);
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        }

        function displaySearchResults(customers) {
            const resultsDiv = document.getElementById('searchResults');
            const resultsContainer = resultsDiv.querySelector('div');
            
            if (customers.length === 0) {
                resultsDiv.classList.add('hidden');
                return;
            }

            resultsContainer.innerHTML = customers.map(customer => `
                <div class="customer-search-result p-3 border-b border-gray-200" onclick="selectCustomer(${JSON.stringify(customer).replace(/"/g, '&quot;')})">
                    <div class="font-semibold">${customer.customer_name}</div>
                    <div class="text-sm text-gray-600">${customer.phone}</div>
                    <div class="text-xs text-gray-500">Pesanan sebelumnya: ${customer.order_count}</div>
                </div>
            `).join('');
            
            resultsDiv.classList.remove('hidden');
        }

        function selectCustomer(customer) {
            document.getElementById('customerName').value = customer.customer_name;
            document.getElementById('customerPhone').value = customer.phone;
            document.getElementById('customerInfo').classList.remove('hidden');
            document.getElementById('searchResults').classList.add('hidden');
            document.getElementById('customerSearch').value = '';
            document.getElementById('phoneSearch').value = '';
        }

        function addService() {
            const container = document.getElementById('servicesContainer');
            const serviceDiv = document.createElement('div');
            serviceDiv.className = 'service-item relative';
            serviceDiv.id = `service-${currentServiceIndex}`;
            
            serviceDiv.innerHTML = `
                <button type="button" class="remove-service" onclick="removeService(${currentServiceIndex})">
                    <i class="fas fa-times"></i>
                </button>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Merek Sepatu</label>
                        <input type="text" name="services[${currentServiceIndex}][brand]" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent"
                               placeholder="Contoh: Nike, Adidas">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Layanan</label>
                        <button type="button" onclick="openServiceModal(${currentServiceIndex})"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors text-left">
                            <span id="service-display-${currentServiceIndex}">Klik untuk pilih layanan</span>
                        </button>
                        <input type="hidden" name="services[${currentServiceIndex}][service_code]" id="service-code-${currentServiceIndex}">
                        <input type="hidden" name="services[${currentServiceIndex}][service_name]" id="service-name-${currentServiceIndex}">
                        <input type="hidden" name="services[${currentServiceIndex}][service_price]" id="service-price-${currentServiceIndex}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                        <div class="price-display" id="price-display-${currentServiceIndex}">Rp 0</div>
                    </div>
                </div>
            `;
            
            container.appendChild(serviceDiv);
            currentServiceIndex++;
        }

        function removeService(index) {
            const serviceDiv = document.getElementById(`service-${index}`);
            serviceDiv.remove();
            updateSummary();
        }

        function openServiceModal(serviceIndex) {
            window.currentServiceIndex = serviceIndex;
            document.getElementById('serviceModal').classList.remove('hidden');
            
            // Clear previous selections
            document.querySelectorAll('.service-card').forEach(card => {
                card.classList.remove('selected');
            });
        }

        function closeServiceModal() {
            document.getElementById('serviceModal').classList.add('hidden');
        }

        // Service card click handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.service-card')) {
                const card = e.target.closest('.service-card');
                const serviceCode = card.dataset.service;
                const serviceType = card.dataset.type;
                const servicePrice = card.dataset.price;
                const serviceName = card.dataset.name;
                
                // Update display
                document.getElementById(`service-display-${window.currentServiceIndex}`).textContent = `${serviceName} - ${serviceType}`;
                document.getElementById(`service-code-${window.currentServiceIndex}`).value = serviceCode;
                document.getElementById(`service-name-${window.currentServiceIndex}`).value = `${serviceName} - ${serviceType}`;
                document.getElementById(`service-price-${window.currentServiceIndex}`).value = servicePrice;
                document.getElementById(`price-display-${window.currentServiceIndex}`).textContent = formatCurrency(servicePrice);
                
                // Update selection
                document.querySelectorAll('.service-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                
                closeServiceModal();
                updateSummary();
            }
        });

        function togglePaymentMethod() {
            const paymentStatus = document.getElementById('paymentStatus').value;
            const paymentMethodDiv = document.getElementById('paymentMethodDiv');
            
            if (paymentStatus === 'paid') {
                paymentMethodDiv.style.display = 'block';
                document.getElementById('paymentMethod').required = true;
            } else {
                paymentMethodDiv.style.display = 'none';
                document.getElementById('paymentMethod').required = false;
                document.getElementById('paymentMethod').value = '';
            }
        }

        function updateSummary() {
            const serviceInputs = document.querySelectorAll('[name^="services["]');
            let totalPairs = 0;
            let subtotal = 0;
            
            serviceInputs.forEach(input => {
                if (input.name.includes('service_price')) {
                    const price = parseFloat(input.value) || 0;
                    subtotal += price;
                    totalPairs++;
                }
            });
            
            document.getElementById('totalPairs').textContent = totalPairs;
            document.getElementById('subtotal').textContent = formatCurrency(subtotal);
            document.getElementById('total').textContent = formatCurrency(subtotal);
        }

        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        function initializeSidebarToggle() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const sidebarToggle = document.getElementById('sidebarToggle');

            // Add event listener to toggle button
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            // Add event listener to overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768 && !sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.remove('opacity-50', 'pointer-events-auto');
                    sidebarOverlay.classList.add('opacity-0', 'pointer-events-none');
                }
            });
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('opacity-0');
            sidebarOverlay.classList.toggle('opacity-50');
            sidebarOverlay.classList.toggle('pointer-events-none');
            sidebarOverlay.classList.toggle('pointer-events-auto');
        }

        function validateForm() {
            // Validate date first
            if (!validateDateIn()) {
                return false;
            }
            
            // Check if at least one service is added
            const serviceInputs = document.querySelectorAll('[name^="services["][name$="[service_code]"]');
            let hasService = false;
            
            serviceInputs.forEach(input => {
                if (input.value.trim() !== '') {
                    hasService = true;
                }
            });
            
            if (!hasService) {
                alert('Harap tambahkan minimal satu layanan!');
                return false;
            }
            
            // Final confirmation for today's orders
            const dateInput = document.getElementById('dateIn');
            const today = new Date().toISOString().split('T')[0];
            
            if (dateInput.value === today) {
                const customerName = document.getElementById('customerName').value;
                const total = document.getElementById('total').textContent;
                
                if (!confirm(`Konfirmasi pesanan hari ini:\n\nCustomer: ${customerName}\nTotal: ${total}\n\nApakah data sudah benar?`)) {
                    return false;
                }
            }
            
            return true;
        }

        // Initialize sidebar when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeSidebarToggle();
        });
    </script>
</body>
</html>