<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - <?= APP_NAME ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.31/dist/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar bg-gradient-to-br from-[#7B2C2C] to-[#800000] text-white w-64 fixed inset-y-0 left-0 z-50 overflow-y-auto transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full">
        <div class="sidebar-header p-5 text-center border-b border-white border-opacity-10">
            <?php 
            require_once __DIR__ . '/../helpers/logo_helper.php';
            $logoPath = getLogoPath();
            ?>
            <img src="<?= $logoPath ?>" alt="<?= getLogoAlt() ?>" class="sidebar-logo w-12 h-12 bg-white rounded-full mx-auto mb-2 object-contain">
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
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 " href="?page=orders">
                        <i class="fas fa-shopping-cart w-5 mr-3"></i> Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 bg-white bg-opacity-10 text-white" href="?page=reports">
                        <i class="fas fa-chart-bar w-5 mr-3"></i> Laporan
                    </a>
                </li>
                <?php if (isSuperuser()): ?>
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
            <!-- Top Header -->
            <div class="bg-white p-5 rounded-lg shadow mb-8 flex justify-between items-center">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="text-gray-500 focus:outline-none focus:text-gray-900 mr-4 md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h4 class="text-2xl font-semibold text-gray-800">Laporan</h4>
                        <small class="text-gray-600">Analisis dan export data</small>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <div class="font-semibold text-gray-800"><?= $_SESSION['full_name'] ?></div>
                        <div class="text-sm text-gray-600"><?= ucfirst($_SESSION['user_role']) ?></div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-[#7B2C2C] text-white flex items-center justify-center font-bold">
                        <?= substr($_SESSION['full_name'], 0, 2) ?>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white p-5 rounded-lg shadow mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                        <input type="date" id="dateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                        <input type="date" id="dateTo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                        <select id="paymentStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                            <option value="">Semua</option>
                            <option value="paid">Dibayar</option>
                            <option value="unpaid">Belum Dibayar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Pesanan</label>
                        <select id="orderStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                            <option value="">Semua</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Proses</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-y-2 justify-between items-center">
                    <div class="flex gap-2 flex-wrap">
                        <button onclick="loadReportData()" class="bg-[#7B2C2C] text-white px-2 py-2 sm:px-4 rounded-lg hover:bg-[#6B2222] transition-colors duration-300 flex items-center">
                            <i class="fas fa-search sm:mr-2"></i><span class="hidden sm:inline">Tampilkan Data</span>
                        </button>
                        <button onclick="resetFilters()" class="bg-gray-500 text-white px-2 py-2 sm:px-4 rounded-lg hover:bg-gray-600 transition-colors duration-300 flex items-center">
                            <i class="fas fa-redo sm:mr-2"></i><span class="hidden sm:inline">Reset</span>
                        </button>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <button onclick="exportToCSV()" class="bg-green-600 text-white px-2 py-2 sm:px-4 rounded-lg hover:bg-green-700 transition-colors duration-300 flex items-center">
                            <i class="fas fa-file-csv sm:mr-2"></i><span class="hidden sm:inline">Export CSV</span>
                        </button>
                        <button onclick="exportToPDF()" class="bg-red-600 text-white px-2 py-2 sm:px-4 rounded-lg hover:bg-red-700 transition-colors duration-300 flex items-center">
                            <i class="fas fa-file-pdf sm:mr-2"></i><span class="hidden sm:inline">Export PDF</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Pesanan</p>
                            <p class="text-2xl font-semibold text-gray-800" id="totalOrders">0</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Pendapatan</p>
                            <p class="text-2xl font-semibold text-gray-800" id="totalRevenue">Rp 0</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Sepatu</p>
                            <p class="text-2xl font-semibold text-gray-800" id="totalShoes">0</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center">
                            <i class="fas fa-shoe-prints"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Rata-rata Transaksi</p>
                            <p class="text-2xl font-semibold text-gray-800" id="avgTransaction">Rp 0</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white p-5 rounded-lg shadow mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Grafik Pendapatan</h2>
                <div class="relative" style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-5">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Pesanan</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Invoice</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTableBody" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-chart-bar text-4xl mb-2"></i>
                                        <p>Pilih filter dan klik "Tampilkan Data" untuk melihat laporan</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        let reportData = [];
        let revenueChart = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeSidebarToggle();
            setDefaultDates();
        });

        function setDefaultDates() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            
            document.getElementById('dateFrom').value = firstDay.toISOString().split('T')[0];
            document.getElementById('dateTo').value = today.toISOString().split('T')[0];
        }

        function resetFilters() {
            setDefaultDates();
            document.getElementById('paymentStatus').value = '';
            document.getElementById('orderStatus').value = '';
            clearReportData();
        }

        function clearReportData() {
            reportData = [];
            document.getElementById('totalOrders').textContent = '0';
            document.getElementById('totalRevenue').textContent = 'Rp 0';
            document.getElementById('totalShoes').textContent = '0';
            document.getElementById('avgTransaction').textContent = 'Rp 0';
            
            const tbody = document.getElementById('ordersTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-chart-bar text-4xl mb-2"></i>
                        <p>Pilih filter dan klik "Tampilkan Data" untuk melihat laporan</p>
                    </td>
                </tr>
            `;
            
            if (revenueChart) {
                revenueChart.destroy();
                revenueChart = null;
            }
        }

        function loadReportData() {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            const paymentStatus = document.getElementById('paymentStatus').value;
            const orderStatus = document.getElementById('orderStatus').value;

            // Show loading
            const tbody = document.getElementById('ordersTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <div class="animate-spin inline-block w-8 h-8 border-4 rounded-full border-t-transparent border-blue-500" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            `;

            // Fetch data from API
            fetch(`?page=orders&action=getReportData&date_from=${dateFrom}&date_to=${dateTo}&payment_status=${paymentStatus}&status=${orderStatus}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        reportData = data.orders;
                        updateSummaryCards(data.summary);
                        updateOrdersTable(data.orders);
                        updateRevenueChart(data.chartData);
                    } else {
                        showError('Gagal memuat data: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Terjadi kesalahan saat memuat data');
                });
        }

        function updateSummaryCards(summary) {
            document.getElementById('totalOrders').textContent = summary.total_orders || 0;
            document.getElementById('totalRevenue').textContent = formatCurrency(summary.total_revenue || 0);
            document.getElementById('totalShoes').textContent = summary.total_shoes || 0;
            document.getElementById('avgTransaction').textContent = formatCurrency(summary.avg_transaction || 0);
        }

        function updateOrdersTable(orders) {
            const tbody = document.getElementById('ordersTableBody');
            
            if (orders.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Tidak ada data untuk periode yang dipilih</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = orders.map(order => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${order.invoice_no}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${formatDate(order.date_in)}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${order.customer_name}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${formatCurrency(order.total)}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(order.status)}">
                            ${getStatusLabel(order.status)}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getPaymentStatusClass(order.payment_status)}">
                            ${getPaymentStatusLabel(order.payment_status)}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        function updateRevenueChart(chartData) {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            if (revenueChart) {
                revenueChart.destroy();
            }

            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels || [],
                    datasets: [{
                        label: 'Pendapatan Harian',
                        data: chartData.revenue || [],
                        borderColor: '#7B2C2C',
                        backgroundColor: 'rgba(123, 44, 44, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
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

        function exportToCSV() {
            if (reportData.length === 0) {
                alert('Tidak ada data untuk diekspor');
                return;
            }

            let csv = 'No. Invoice,Tanggal,Pelanggan,Total Sepatu,Total,Status,Pembayaran\n';
            
            reportData.forEach(order => {
                csv += `"${order.invoice_no}","${formatDate(order.date_in)}","${order.customer_name}","${order.total_pairs}","${order.total}","${getStatusLabel(order.status)}","${getPaymentStatusLabel(order.payment_status)}"\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `laporan_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        }

        function exportToPDF() {
            if (reportData.length === 0) {
                alert('Tidak ada data untuk diekspor');
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Title
            doc.setFontSize(16);
            doc.text('Laporan Pesanan Dr.ShoezClean', 14, 15);
            
            // Period
            doc.setFontSize(10);
            doc.text(`Periode: ${document.getElementById('dateFrom').value} s/d ${document.getElementById('dateTo').value}`, 14, 25);

            // Table
            const tableData = reportData.map(order => [
                order.invoice_no,
                formatDate(order.date_in),
                order.customer_name,
                order.total_pairs.toString(),
                formatCurrency(order.total),
                getStatusLabel(order.status),
                getPaymentStatusLabel(order.payment_status)
            ]);

            doc.autoTable({
                head: [['Invoice', 'Tanggal', 'Pelanggan', 'Sepatu', 'Total', 'Status', 'Pembayaran']],
                body: tableData,
                startY: 35,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [123, 44, 44] }
            });

            doc.save(`laporan_${new Date().toISOString().split('T')[0]}.pdf`);
        }

        // Helper functions
        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID');
        }

        function getStatusClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'processing': 'bg-blue-100 text-blue-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        }

        function getStatusLabel(status) {
            const labels = {
                'pending': 'Pending',
                'processing': 'Proses',
                'completed': 'Selesai',
                'cancelled': 'Dibatalkan'
            };
            return labels[status] || status;
        }

        function getPaymentStatusClass(status) {
            const classes = {
                'paid': 'bg-green-100 text-green-800',
                'unpaid': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        }

        function getPaymentStatusLabel(status) {
            const labels = {
                'paid': 'Dibayar',
                'unpaid': 'Belum Dibayar'
            };
            return labels[status] || status;
        }

        function showError(message) {
            const tbody = document.getElementById('ordersTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-red-500">
                        <i class="fas fa-exclamation-triangle text-4xl mb-2"></i>
                        <p>${message}</p>
                    </td>
                </tr>
            `;
        }

        // Sidebar toggle
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
    </script>
</body>
</html>