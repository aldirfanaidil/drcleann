<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= APP_NAME ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom style for active filter buttons */
        .filter-btn-group .active {
            background-color: #7B2C2C !important;
            color: white !important;
            border-color: #7B2C2C !important;
        }
        
        /* Style for :active state on filter buttons */
        .filter-btn-group button:active,
        .filter-btn-group button.active {
            background-color: #7B2C2C !important;
            color: white !important;
            border-color: #7B2C2C !important;
        }
        
        /* Ensure active state persists */
        .filter-btn-group button.active:active {
            background-color: #6B2222 !important;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/layouts/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="md:ml-64 p-5 min-h-screen bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">
        <?php
        $page_title = 'Dashboard';
        $page_subtitle = 'Selamat datang, ' . htmlspecialchars($_SESSION['full_name']) . '!';
        $extra_buttons = [
            '<button onclick="refreshDashboard()" class="px-3 py-2 bg-[#7B2C2C] text-white rounded-lg hover:bg-[#6B2222] transition-colors text-sm">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>'
        ];
        include __DIR__ . '/layouts/header.php';
        ?>

        <!-- Filter Bar -->
        <div class="bg-gray-100 p-3 rounded-lg mb-6">
            <div class="flex flex-wrap gap-2 justify-center">
                <button class="px-3 py-2 text-sm md:px-4 bg-white rounded-md cursor-pointer transition-all duration-300 hover:bg-gray-50 active:bg-[#7B2C2C] active:text-white" data-period="today">
                    <i class="fas fa-calendar-day mr-2 hidden md:inline-block"></i>Hari
                </button>
                <button class="px-3 py-2 text-sm md:px-4 bg-white rounded-md cursor-pointer transition-all duration-300 hover:bg-gray-50" data-period="week">
                    <i class="fas fa-calendar-week mr-2 hidden md:inline-block"></i>Minggu
                </button>
                <button class="px-3 py-2 text-sm md:px-4 bg-white rounded-md cursor-pointer transition-all duration-300 hover:bg-gray-50" data-period="month">
                    <i class="fas fa-calendar-alt mr-2 hidden md:inline-block"></i>Bulan
                </button>
                <button class="px-3 py-2 text-sm md:px-4 bg-white rounded-md cursor-pointer transition-all duration-300 hover:bg-gray-50" data-period="year">
                    <i class="fas fa-calendar mr-2 hidden md:inline-block"></i>Tahun
                </button>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-4 rounded-lg shadow-sm flex flex-col justify-between cursor-pointer hover:shadow-md transition-shadow duration-300" onclick="showDetailModal('revenue')">
                <div class="flex items-center mb-2">
                    <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-lg mr-3">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="text-gray-600 text-sm font-medium">Pendapatan</div>
                </div>
                <div class="text-2xl font-semibold text-gray-800" id="revenue-kpi">Rp 0</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm flex flex-col justify-between cursor-pointer hover:shadow-md transition-shadow duration-300" onclick="showDetailModal('orders')">
                <div class="flex items-center mb-2">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg mr-3">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="text-gray-600 text-sm font-medium">Pesanan</div>
                </div>
                <div class="text-2xl font-semibold text-gray-800" id="orders-kpi">0</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm flex flex-col justify-between cursor-pointer hover:shadow-md transition-shadow duration-300" onclick="showDetailModal('shoes')">
                <div class="flex items-center mb-2">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center text-lg mr-3">
                        <i class="fas fa-shoe-prints"></i>
                    </div>
                    <div class="text-gray-600 text-sm font-medium">Sepatu Masuk</div>
                </div>
                <div class="text-2xl font-semibold text-gray-800" id="shoes-kpi">0</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm flex flex-col justify-between cursor-pointer hover:shadow-md transition-shadow duration-300" onclick="showDetailModal('paid')">
                <div class="flex items-center mb-2">
                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-lg mr-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="text-gray-600 text-sm font-medium">Sudah Dibayar</div>
                </div>
                <div class="text-2xl font-semibold text-gray-800" id="paid-kpi">Rp 0</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm flex flex-col justify-between cursor-pointer hover:shadow-md transition-shadow duration-300" onclick="showDetailModal('unpaid')">
                <div class="flex items-center mb-2">
                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-lg mr-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="text-gray-600 text-sm font-medium">Belum Bayar</div>
                </div>
                <div class="text-2xl font-semibold text-gray-800" id="unpaid-kpi">Rp 0</div>
            </div>
            <!-- New KPI Cards -->
            <div class="bg-white p-4 rounded-lg shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-lg">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>
                <div class="text-2xl font-semibold text-gray-800" id="total-transfer-kpi">Rp 0</div>
                <div class="text-gray-600 text-sm font-medium">Total Transfer</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-lg">
                        <i class="fas fa-qrcode"></i>
                    </div>
                </div>
                <div class="text-2xl font-semibold text-gray-800" id="total-qris-kpi">Rp 0</div>
                <div class="text-gray-600 text-sm font-medium">Total QRIS</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-lg">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="text-2xl font-semibold text-gray-800" id="total-tunai-kpi">Rp 0</div>
                <div class="text-gray-600 text-sm font-medium">Total Tunai</div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-7xl w-full max-h-[90vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-[#7B2C2C] to-[#800000] text-white p-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold" id="modalTitle">Detail Pesanan</h3>
                        <p class="text-sm opacity-80" id="modalSubtitle">Daftar pesanan</p>
                    </div>
                    <button onclick="closeDetailModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4 overflow-y-auto" style="max-height: calc(90vh - 80px);">
                    <!-- Filter Section -->
                    <div class="bg-gray-100 p-3 rounded-lg mb-4">
                        <div class="flex flex-wrap gap-2 items-center">
                            <label class="text-sm font-medium text-gray-700">Cari:</label>
                            <input type="text" id="searchInput" placeholder="Nama customer, telepon, atau invoice..." class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] flex-1">
                            <select id="statusFilter" class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#7B2C2C]">
                                <option value="">Semua Status</option>
                                <option value="pending">Belum Bayar</option>
                                <option value="paid">Sudah Bayar</option>
                            </select>
                            <button onclick="refreshDetailData()" class="px-3 py-1 bg-[#7B2C2C] text-white rounded-md text-sm hover:bg-[#6B2222] transition-colors">
                                <i class="fas fa-sync-alt mr-1"></i>Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="bg-white rounded-lg overflow-hidden border border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[600px]">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sepatu</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Bayar</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Masuk</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="tableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="detailLoading" class="text-center py-10">
                        <div class="animate-spin inline-block w-8 h-8 border-4 rounded-full border-t-transparent border-blue-500" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-gray-600">Memuat data...</p>
                    </div>

                    <!-- Empty State -->
                    <div id="detailEmpty" class="hidden text-center py-10">
                        <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">Tidak ada data untuk ditampilkan</p>
                    </div>

                    <!-- Pagination -->
                    <div id="paginationContainer" class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Menampilkan <span id="startRecord">0</span> - <span id="endRecord">0</span> dari <span id="totalRecords">0</span> data
                        </div>
                        <div class="flex items-center space-x-2" id="paginationButtons">
                            <!-- Pagination buttons will be populated dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="bg-white p-5 rounded-lg shadow mt-6">
            <div class="flex flex-wrap justify-between items-center mb-4 gap-y-2">
                <h2 class="text-lg font-semibold text-gray-800">Grafik Pendapatan</h2>
                <div class="flex gap-2 filter-btn-group flex-wrap justify-end">
                    <button class="px-2 py-1 text-xs sm:text-sm md:px-3 border border-gray-300 bg-white rounded-md cursor-pointer transition-all duration-300 active" data-chart-period="month">30 Hari</button>
                    <button class="px-2 py-1 text-xs sm:text-sm md:px-3 border border-gray-300 bg-white rounded-md cursor-pointer transition-all duration-300 hover:bg-gray-50" data-chart-period="week">Minggu Ini</button>
                    <button class="px-2 py-1 text-xs sm:text-sm md:px-3 border border-gray-300 bg-white rounded-md cursor-pointer transition-all duration-300 hover:bg-gray-50" data-chart-period="year">Tahun Ini</button>
                </div>
            </div>
            <div class="text-center py-10 text-gray-600" id="chart-loading">
                <div class="animate-spin inline-block w-8 h-8 border-4 rounded-full border-t-transparent border-blue-500" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div class="relative" style="height: 400px; max-height: 50vh;">
                <canvas id="pendapatanChart" style="display: none;"></canvas>
            </div>
            <div id="no-chart-data" class="text-center py-10 text-gray-600" style="display: none;">
                <p class="mt-2 text-sm opacity-70">Tidak ada data untuk ditampilkan.</p>
            </div>
        </div>


    </main>

    <script>
        // Global variables
        let revenueChart = null;
        let currentKpiPeriod = 'today';
        let currentChartPeriod = 'month';

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadKpiData();
            loadChartData();
            initializeEventListeners();
            updateLastUpdateTime();
        });

        // Load KPI data
        function loadKpiData() {
            fetch('./?page=dashboard&action=getKpiData')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    updateKpiDisplay(data);
                    updateLastUpdateTime();
                })
                .catch(error => {
                    console.error('Error loading KPI data:', error);
                    // Set default values if API fails
                    updateKpiDisplay({
                        today: { revenue: 0, orders: 0, shoes: 0, paid: 0, unpaid: 0, total_cash: 0, total_transfer: 0, total_qr: 0 },
                        week: { revenue: 0, orders: 0, shoes: 0, paid: 0, unpaid: 0, total_cash: 0, total_transfer: 0, total_qr: 0 },
                        month: { revenue: 0, orders: 0, shoes: 0, paid: 0, unpaid: 0, total_cash: 0, total_transfer: 0, total_qr: 0 },
                        year: { revenue: 0, orders: 0, shoes: 0, paid: 0, unpaid: 0, total_cash: 0, total_transfer: 0, total_qr: 0 }
                    });
                });
        }

        // Update KPI display
        function updateKpiDisplay(data) {
            const period = currentKpiPeriod;
            const periodData = data[period] || { 
                revenue: 0, 
                orders: 0, 
                shoes: 0, 
                paid: 0,
                unpaid: 0,
                total_cash: 0,
                total_qr: 0,
                total_transfer: 0
            };

            // Animate number changes
            animateValue('revenue-kpi', periodData.revenue, true);
            animateValue('orders-kpi', periodData.orders, false);
            animateValue('shoes-kpi', periodData.shoes, false);
            animateValue('paid-kpi', periodData.paid, true);
            animateValue('unpaid-kpi', periodData.unpaid || 0, true);
            animateValue('total-transfer-kpi', periodData.total_transfer || 0, true);
            animateValue('total-qris-kpi', periodData.total_qr || 0, true);
            animateValue('total-tunai-kpi', periodData.total_cash || 0, true);
        }

        // Animate number changes
        function animateValue(id, value, isCurrency = false) {
            const element = document.getElementById(id);
            if (!element) return;
            const currentText = element.textContent;
            const currentValue = parseFloat(currentText.replace(/[^\d.-]/g, '')) || 0;
            const newValue = parseFloat(value) || 0;
            
            if (currentValue !== newValue) {
                element.style.transition = 'color 0.3s';
                element.style.color = newValue > currentValue ? '#10b981' : '#ef4444';
                
                setTimeout(() => {
                    element.textContent = isCurrency ? formatCurrency(newValue) : newValue.toLocaleString('id-ID');
                    element.style.color = '';
                }, 300);
            }
        }

        // Load chart data
        function loadChartData() {
            document.getElementById('chart-loading').style.display = 'block';
            document.getElementById('pendapatanChart').style.display = 'none';

            fetch(`./?page=dashboard&action=getChartData&period=${currentChartPeriod}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    updateChart(data);
                    document.getElementById('chart-loading').style.display = 'none';
                    document.getElementById('pendapatanChart').style.display = 'block';
                    updateLastUpdateTime();
                })
                .catch(error => {
                    console.error('Error loading chart data:', error);
                    document.getElementById('chart-loading').style.display = 'none';
                    updateChart({ labels: [], revenue: [], orders: [] });
                    document.getElementById('pendapatanChart').style.display = 'block';
                });
        }

        // Update chart
        function updateChart(data) {
            const ctx = document.getElementById('pendapatanChart').getContext('2d');
            const noDataMessage = document.getElementById('no-chart-data');

            if (revenueChart) {
                revenueChart.destroy();
            }

            if (!data.labels || data.labels.length === 0) {
                document.getElementById('pendapatanChart').style.display = 'none';
                noDataMessage.style.display = 'block';
                return;
            } else {
                document.getElementById('pendapatanChart').style.display = 'block';
                noDataMessage.style.display = 'none';
            }

            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: data.revenue || [],
                        borderColor: '#7B2C2C',
                        backgroundColor: 'rgba(123, 44, 44, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Jumlah Pesanan',
                        data: data.orders || [],
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: true, position: 'top', labels: { usePointStyle: true, padding: 15 }},
                        tooltip: { backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12, cornerRadius: 8, titleFont: { size: 14, weight: 'bold' }, bodyFont: { size: 13 }}
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 0 }},
                        y: { type: 'linear', display: true, position: 'left', beginAtZero: true, ticks: { callback: value => formatCurrency(value) }},
                        y1: { type: 'linear', display: true, position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }}
                    }
                }
            });
        }

        // Initialize event listeners
        function initializeEventListeners() {
            document.querySelectorAll('[data-period]').forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active classes from all buttons
                    document.querySelectorAll('[data-period]').forEach(btn => {
                        btn.classList.remove('active', 'bg-[#7B2C2C]', 'text-white');
                    });
                    // Add active classes to clicked button
                    this.classList.add('active', 'bg-[#7B2C2C]', 'text-white');
                    currentKpiPeriod = this.dataset.period;
                    loadKpiData();
                });
            });

            document.querySelectorAll('[data-chart-period]').forEach(button => {
                button.addEventListener('click', function() {
                    const group = this.closest('.filter-btn-group');
                    if (group) group.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    currentChartPeriod = this.dataset.chartPeriod;
                    loadChartData();
                });
            });
        }

        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        }

        setInterval(function() {
            loadKpiData();
            loadChartData();
        }, 30000);

        // --- MODAL DETAIL LOGIC ---
        let currentDetailType = '';
        let currentDetailPage = 1;

        function showDetailModal(type) {
            currentDetailType = type;
            currentDetailPage = 1;
            
            const modal = document.getElementById('detailModal');
            const title = document.getElementById('modalTitle');
            const subtitle = document.getElementById('modalSubtitle');
            
            if (!modal || !title || !subtitle) {
                console.error('Modal elements not found');
                return;
            }
            
            const titles = {
                'revenue': { title: 'Detail Pendapatan', subtitle: 'Daftar pesanan yang sudah lunas' },
                'orders': { title: 'Detail Pesanan', subtitle: 'Daftar semua pesanan' },
                'shoes': { title: 'Detail Sepatu', subtitle: 'Daftar semua sepatu masuk' },
                'paid': { title: 'Detail Sudah Bayar', subtitle: 'Daftar pesanan yang sudah dibayar' },
                'unpaid': { title: 'Detail Belum Bayar', subtitle: 'Daftar pesanan yang belum dibayar' }
            };
            
            title.textContent = titles[type]?.title || 'Detail';
            subtitle.textContent = titles[type]?.subtitle || 'Ringkasan informasi';
            
            // Reset filters
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = type === 'paid' ? 'paid' : (type === 'unpaid' ? 'pending' : '');
            
            modal.classList.remove('hidden');
            loadDetailData(1);
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function loadDetailData(page = 1) {
            const loading = document.getElementById('detailLoading');
            const empty = document.getElementById('detailEmpty');
            const tableBody = document.getElementById('tableBody');
            const paginationContainer = document.getElementById('paginationContainer');
            
            // Check if elements exist
            if (!loading || !empty || !tableBody || !paginationContainer) {
                console.error('Modal elements not found');
                return;
            }
            
            loading.style.display = 'block';
            empty.style.display = 'none';
            tableBody.innerHTML = '';
            paginationContainer.innerHTML = '';

            const search = document.getElementById('searchInput').value;
            const statusFilter = document.getElementById('statusFilter').value;
            currentDetailPage = page;

            // Build filter based on type
            let paymentStatus = statusFilter;
            if (!paymentStatus) {
                if (currentDetailType === 'revenue' || currentDetailType === 'paid') {
                    paymentStatus = 'paid';
                } else if (currentDetailType === 'unpaid') {
                    paymentStatus = 'pending';
                }
            }

            // Use current URL and just change the action parameter
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('page', 'dashboard');
            currentUrl.searchParams.set('action', 'getSimpleDetailData');
            currentUrl.searchParams.set('search', search);
            currentUrl.searchParams.set('payment_status', paymentStatus);
            currentUrl.searchParams.set('page_num', page);
            const url = currentUrl.toString();
            
            console.log('Loading detail data from:', url);
            console.log('Current location:', window.location.href);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers.get('content-type'));
                    
                    if (!response.ok) {
                        if (response.status === 401) {
                            throw new Error('Session expired. Please refresh the page.');
                        } else if (response.status === 403) {
                            throw new Error('Access denied. Admin privileges required.');
                        } else {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                    }
                    
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            console.error('Non-JSON response (first 200 chars):', text.substring(0, 200));
                            throw new Error('Server returned HTML instead of JSON. Check console for details.');
                        });
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    loading.style.display = 'none';
                    
                    if (data.success && data.data && data.data.length > 0) {
                        renderDetailTable(data.data);
                        renderPagination(data.pagination);
                    } else {
                        empty.style.display = 'block';
                        if (paginationContainer) {
                            paginationContainer.innerHTML = '';
                        }
                        if (data.message) {
                            const emptyP = empty.querySelector('p');
                            if (emptyP) {
                                emptyP.textContent = data.message;
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading detail data:', error);
                    if (loading) loading.style.display = 'none';
                    if (empty) {
                        empty.style.display = 'block';
                        const emptyP = empty.querySelector('p');
                        if (emptyP) {
                            emptyP.textContent = `Gagal memuat data: ${error.message}`;
                        }
                    }
                });
        }
        
        function renderDetailTable(data) {
            const tableBody = document.getElementById('tableBody');
            
            if (!tableBody) {
                console.error('Table body element not found');
                return;
            }
            
            let bodyHtml = '';
            data.forEach(row => {
                const isPaid = row.payment_status === 'paid';
                const statusBadge = isPaid 
                    ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>'
                    : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Belum</span>';
                
                bodyHtml += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">${row.invoice_no || ''}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">${row.customer_name || ''}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">${row.phone || ''}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">${row.total_pairs || 0} Pasang</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">${formatCurrency(row.total || 0)}</td>
                        <td class="px-4 py-3 text-sm">${statusBadge}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">${formatDate(row.date_in)}</td>
                    </tr>
                `;
            });
            tableBody.innerHTML = bodyHtml;
        }

        function renderPagination(pagination) {
            if (!pagination || pagination.total_pages <= 1) {
                const paginationContainer = document.getElementById('paginationContainer');
                if (paginationContainer) {
                    paginationContainer.innerHTML = '';
                }
                return;
            }

            const { current_page, total_pages, total_records } = pagination;
            const startRecord = (current_page - 1) * 10 + 1;
            const endRecord = Math.min(current_page * 10, total_records);

            // Update record info with null checks
            const startRecordEl = document.getElementById('startRecord');
            const endRecordEl = document.getElementById('endRecord');
            const totalRecordsEl = document.getElementById('totalRecords');
            

            
            if (startRecordEl) startRecordEl.textContent = startRecord;
            if (endRecordEl) endRecordEl.textContent = endRecord;
            if (totalRecordsEl) totalRecordsEl.textContent = total_records;

            let buttonsHtml = '';
            
            // Previous button
            buttonsHtml += `<button onclick="loadDetailData(${current_page - 1})" class="px-3 py-1 text-sm border border-gray-300 rounded-l-md hover:bg-gray-50 ${current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${current_page === 1 ? 'disabled' : ''}>Previous</button>`;
            
            // Page numbers (show max 5 pages)
            let startPage = Math.max(1, current_page - 2);
            let endPage = Math.min(total_pages, startPage + 4);
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                if (i === current_page) {
                    buttonsHtml += `<button class="px-3 py-1 text-sm bg-[#7B2C2C] text-white border border-[#7B2C2C]">${i}</button>`;
                } else {
                    buttonsHtml += `<button onclick="loadDetailData(${i})" class="px-3 py-1 text-sm border border-gray-300 hover:bg-gray-50">${i}</button>`;
                }
            }
            
            // Next button
            buttonsHtml += `<button onclick="loadDetailData(${current_page + 1})" class="px-3 py-1 text-sm border border-gray-300 rounded-r-md hover:bg-gray-50 ${current_page === total_pages ? 'opacity-50 cursor-not-allowed' : ''}" ${current_page === total_pages ? 'disabled' : ''}>Next</button>`;

            const paginationButtonsEl = document.getElementById('paginationButtons');

            if (paginationButtonsEl) {
                paginationButtonsEl.innerHTML = buttonsHtml;
            }
        }

        function refreshDetailData() {
            loadDetailData(currentDetailPage);
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
            } catch(e) {
                return dateString;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Detail modal event listeners
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => loadDetailData(1), 500);
            });
            
            document.getElementById('statusFilter').addEventListener('change', () => loadDetailData(1));
            
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetailModal(); });
            document.getElementById('detailModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeDetailModal(); });
        });
        
        function refreshDashboard() {
            const refreshBtn = event.target.closest('button');
            const icon = refreshBtn.querySelector('i');
            icon.classList.add('fa-spin');
            
            loadKpiData();
            loadChartData();
            
            setTimeout(() => icon.classList.remove('fa-spin'), 1000);
        }

        function updateLastUpdateTime() {
            const timeString = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('last-update').textContent = timeString;
        }
    </script>
</body>
</html>