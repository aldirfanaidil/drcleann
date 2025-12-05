<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arus Kas - <?= APP_NAME ?></title>
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar bg-gradient-to-br from-[#7B2C2C] to-[#800000] text-white w-64 fixed inset-y-0 left-0 z-50 overflow-y-auto transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full">
        <div class="sidebar-header p-5 text-center border-b border-white border-opacity-10">
            <?php 
            require_once __DIR__ . '/helpers/logo_helper.php';
            $logoPath = getLogoPath();
            $shopName = getSetting('shop_name', 'Dr.ShoezClean');
            ?>
            <div class="sidebar-logo w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-2 overflow-hidden">
                <img src="<?= $logoPath ?>" alt="<?= getLogoAlt() ?>" class="w-full h-full object-cover">
            </div>
            <h5 class="text-lg font-semibold"><?= htmlspecialchars($shopName) ?></h5>
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
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 " href="?page=orders">
                        <i class="fas fa-shopping-cart w-5 mr-3"></i> Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 " href="?page=reports">
                        <i class="fas fa-chart-bar w-5 mr-3"></i> Laporan
                    </a>
                </li>
                <?php if (isSuperuser()) : ?>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 bg-white bg-opacity-10 text-white" href="?page=cash_flow">
                        <i class="fas fa-chart-line w-5 mr-3"></i> Arus Kas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 " href="?page=users">
                        <i class="fas fa-users w-5 mr-3"></i> Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 " href="?page=settings">
                        <i class="fas fa-cog w-5 mr-3"></i> Pengaturan
                    </a>
                </li>
                <?php endif; ?>
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
        <?php
        $page_title = 'Arus Kas';
        $page_subtitle = 'Selamat datang, ' . htmlspecialchars($_SESSION['full_name']) . '!';
        $extra_buttons = [
            '<a href="?page=cash_flow&action=expenses" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors text-sm">
                <i class="fas fa-money-bill-wave"></i>
                <span class="hidden sm:inline">Kelola Pengeluaran</span>
                <span class="sm:hidden">Kelola</span>
            </a>',
            '<a href="?page=cash_flow&action=add_expense" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors text-sm">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline">Tambah Pengeluaran</span>
                <span class="sm:hidden">Tambah</span>
            </a>'
        ];
        include __DIR__ . '/layouts/header.php';
        ?>

        <!-- Filter Form -->
        <div class="bg-white p-5 rounded-lg shadow mb-6">
            <h5 class="text-lg font-semibold text-gray-800 mb-4">Filter Data</h5>
            <form method="GET" action="?page=cash_flow">
                <input type="hidden" name="page" value="cash_flow">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label for="filter_month" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                        <select name="month" id="filter_month" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#7B2C2C]">
                            <option value="">Semua</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= (isset($currentFilters['month']) && $currentFilters['month'] == $m) ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label for="filter_year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <select name="year" id="filter_year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#7B2C2C]">
                            <option value="">Semua</option>
                            <?php foreach ($availableYears as $year): ?>
                                <option value="<?= $year ?>" <?= (isset($currentFilters['year']) && $currentFilters['year'] == $year) ? 'selected' : '' ?>>
                                    <?= $year ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex items-end gap-2 lg:col-span-2">
                        <button type="submit" class="bg-[#7B2C2C] hover:bg-opacity-80 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                            <i class="fas fa-filter"></i> 
                            <span class="hidden sm:inline">Filter</span>
                        </button>
                        <a href="?page=cash_flow" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                            <i class="fas fa-times"></i> 
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <!-- Today's Summary -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-lg font-semibold text-gray-800">Hari Ini</h5>
                    <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pendapatan:</span>
                        <span class="font-semibold text-green-600"><?= formatCurrency($cashFlowData['today']['total_in'] ?? 0) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pengeluaran:</span>
                        <span class="font-semibold text-red-600"><?= formatCurrency($cashFlowData['today']['total_out'] ?? 0) ?></span>
                    </div>
                    <div class="border-t pt-2 flex justify-between">
                        <span class="font-semibold text-gray-800">Keuntungan:</span>
                        <span class="font-bold text-lg <?= ($cashFlowData['today']['profit'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                            <?= formatCurrency($cashFlowData['today']['profit'] ?? 0) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Filtered Summary -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-lg font-semibold text-gray-800">
                        Ringkasan Filter (<?= $summaryTitle ?>)
                    </h5>
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pendapatan:</span>
                        <span class="font-semibold text-green-600"><?= formatCurrency($cashFlowData['period']['total_in'] ?? 0) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pengeluaran:</span>
                        <span class="font-semibold text-red-600"><?= formatCurrency($cashFlowData['period']['total_out'] ?? 0) ?></span>
                    </div>
                    <div class="border-t pt-2 flex justify-between">
                        <span class="font-semibold text-gray-800">Keuntungan:</span>
                        <span class="font-bold text-lg <?= ($cashFlowData['period']['profit'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                            <?= formatCurrency($cashFlowData['period']['profit'] ?? 0) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Flow Chart -->
        <div class="bg-white p-5 rounded-lg shadow mt-6">
            <div class="mb-4">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <h2 class="text-lg font-semibold text-gray-800">Grafik Arus Kas</h2>
                    <div class="flex gap-2 filter-btn-group">
                        <button class="px-3 py-1 border border-gray-300 bg-white rounded-md cursor-pointer transition-all duration-300 hover:bg-gray-50 text-sm" data-chart-period="week">Mingguan</button>
                        <button class="px-3 py-1 border border-gray-300 bg-white rounded-md cursor-pointer transition-all duration-300 active text-sm" data-chart-period="month">Bulanan</button>
                        <button class="px-3 py-1 border border-gray-300 bg-white rounded-md cursor-pointer transition-all duration-300 hover:bg-gray-50 text-sm" data-chart-period="year">Tahunan</button>
                    </div>
                </div>
            </div>
            <div class="text-center py-10 text-gray-600" id="chart-loading">
                <div class="animate-spin inline-block w-8 h-8 border-4 rounded-full border-t-transparent border-blue-500" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div class="relative" style="height: 400px; max-height: 50vh;">
                <canvas id="cashFlowChart" style="display: none;"></canvas>
            </div>
            <div id="no-chart-data" class="text-center py-10 text-gray-600" style="display: none;">
                <p class="mt-2 text-sm opacity-70">Tidak ada data untuk ditampilkan.</p>
            </div>
        </div>

         <!-- Daily Transactions Table -->
         <div class="bg-white p-5 rounded-lg shadow mt-6">
             <h2 class="text-lg font-semibold text-gray-800 mb-4">Arus Kas Harian (30 Hari Terakhir)</h2>
             <div class="overflow-x-auto">
                 <table class="min-w-full divide-y divide-gray-200">
                     <thead class="bg-gray-50">
                         <tr>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengeluaran</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keuntungan</th>
                         </tr>
                     </thead>
                     <tbody class="bg-white divide-y divide-gray-200">
                         <?php if (!empty($cashFlowData['daily'])): ?>
                             <?php foreach ($cashFlowData['daily'] as $transaction): ?>
                                 <tr>
                                     <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                         <?= date('d/m/Y', strtotime($transaction['date'])) ?>
                                     </td>
                                     <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">
                                         <?= formatCurrency($transaction['total_in']) ?>
                                     </td>
                                     <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-semibold">
                                         <?= formatCurrency($transaction['total_out']) ?>
                                     </td>
                                     <td class="px-6 py-4 whitespace-nowrap text-sm font-bold <?= ($transaction['profit'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                         <?= formatCurrency($transaction['profit'] ?? 0) ?>
                                     </td>
                                 </tr>
                             <?php endforeach; ?>
                         <?php else: ?>
                             <tr>
                                 <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                     Tidak ada data transaksi
                                 </td>
                             </tr>
                         <?php endif; ?>
                     </tbody>
                 </table>
             </div>
         </div>

    </main>

    <script>
        // Global variables
        let cashFlowChart = null;
        let currentChartPeriod = 'month';

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadCashFlowChart();
            initializeEventListeners();
            initializeSidebarToggle();
        });

        // Load cash flow chart
        function loadCashFlowChart() {
            document.getElementById('chart-loading').style.display = 'block';
            document.getElementById('cashFlowChart').style.display = 'none';

            fetch(`./?page=cash_flow&action=getChartData&period=${currentChartPeriod}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Chart data received:', data);
                    updateCashFlowChart(data);
                    document.getElementById('chart-loading').style.display = 'none';
                    document.getElementById('cashFlowChart').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error loading chart data:', error);
                    document.getElementById('chart-loading').style.display = 'none';
                    // Show empty chart with sample data for testing
                    updateCashFlowChart({ 
                        labels: ['01 Jan', '02 Jan', '03 Jan', '04 Jan', '05 Jan'], 
                        cash: [1000000, 1500000, 1200000, 1800000, 2000000], 
                        qr: [500000, 750000, 600000, 900000, 1000000], 
                        transfer: [300000, 450000, 360000, 540000, 600000], 
                        total: [1800000, 2700000, 2160000, 3240000, 3600000] 
                    });
                    document.getElementById('cashFlowChart').style.display = 'block';
                });
        }

        // Update cash flow chart
        function updateCashFlowChart(data) {
            const ctx = document.getElementById('cashFlowChart').getContext('2d');
            const noDataMessage = document.getElementById('no-chart-data');

            if (cashFlowChart) {
                cashFlowChart.destroy();
            }

            if (!data.labels || data.labels.length === 0) {
                document.getElementById('cashFlowChart').style.display = 'none';
                noDataMessage.style.display = 'block';
                return;
            } else {
                document.getElementById('cashFlowChart').style.display = 'block';
                noDataMessage.style.display = 'none';
            }

            cashFlowChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: 'Tunai',
                        data: data.cash || [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'QRIS',
                        data: data.qris || [],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Transfer',
                        data: data.transfer || [],
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Total',
                        data: data.total || [],
                        borderColor: '#7B2C2C',
                        backgroundColor: 'rgba(123, 44, 44, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        borderDash: [5, 5]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize event listeners
        function initializeEventListeners() {
            // Chart period buttons
            document.querySelectorAll('[data-chart-period]').forEach(button => {
                button.addEventListener('click', function() {
                    const group = this.closest('.filter-btn-group');
                    if (group) {
                        group.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
                    }
                    this.classList.add('active');
                    
                    currentChartPeriod = this.dataset.chartPeriod;
                    loadCashFlowChart();
                });
            });
        }

        // Format currency
        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        // Initialize sidebar toggle
        function initializeSidebarToggle() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            sidebarToggle.addEventListener('click', function() {
                toggleSidebar();
            });

            sidebarOverlay.addEventListener('click', function() {
                toggleSidebar();
            });

            // Close sidebar on resize if it's open on mobile
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768 && !sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.remove('opacity-50', 'pointer-events-auto');
                    sidebarOverlay.classList.add('opacity-0', 'pointer-events-none');
                }
            });
        }

        // Toggle sidebar visibility
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('opacity-0');
            sidebarOverlay.classList.toggle('opacity-50');
            sidebarOverlay.classList.toggle('pointer-events-none');
            sidebarOverlay.classList.toggle('pointer-events-auto');
        }
    </script>
</body>
</html>