<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengeluaran - <?= APP_NAME ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
        $page_title = 'Daftar Pengeluaran';
        $page_subtitle = 'Kelola pengeluaran bisnis Anda';
        $extra_buttons = [
            '<a href="?page=cash_flow" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors text-sm">
                <i class="fas fa-arrow-left"></i>
                <span class="hidden sm:inline">Kembali ke Arus Kas</span>
                <span class="sm:hidden">Kembali</span>
            </a>',
            '<a href="?page=cash_flow&action=add_expense" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors text-sm">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline">Tambah Pengeluaran</span>
                <span class="sm:hidden">Tambah</span>
            </a>'
        ];
        include __DIR__ . '/layouts/header.php';
        ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center">
                <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
                <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center">
                <span><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
                <button onclick="this.parentElement.style.display='none'" class="text-red-700 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>
        
        <div class="bg-white p-5 rounded-lg shadow">
            <!-- Filter Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label for="filterCategory" class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent" id="filterCategory" onchange="filterByCategory()">
                        <option value="">Semua Kategori</option>
                        <?php 
                        $allCategories = [];
                        if (!empty($expenses)) {
                            foreach ($expenses as $expense) {
                                $allCategories[] = $expense['category'];
                            }
                            $allCategories = array_unique($allCategories);
                            sort($allCategories);
                            foreach ($allCategories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>">
                                    <?php echo htmlspecialchars($category); ?>
                                </option>
                            <?php endforeach; 
                        } ?>
                    </select>
                </div>
                <div>
                    <label for="filterMonth" class="block text-sm font-medium text-gray-700 mb-2">Filter Bulan</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent" id="filterMonth" onchange="applyFilters()">
                        <option value="">Semua Bulan</option>
                        <option value="01">Januari</option>
                        <option value="02">Februari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div>
                    <label for="filterYear" class="block text-sm font-medium text-gray-700 mb-2">Filter Tahun</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent" id="filterYear" onchange="applyFilters()">
                        <option value="">Semua Tahun</option>
                        <?php 
                        $allYears = [];
                        if (!empty($expenses)) {
                            foreach ($expenses as $expense) {
                                $year = date('Y', strtotime($expense['expense_date']));
                                $allYears[] = $year;
                            }
                            $allYears = array_unique($allYears);
                            rsort($allYears);
                            foreach ($allYears as $year): ?>
                                <option value="<?php echo $year; ?>">
                                    <?php echo $year; ?>
                                </option>
                            <?php endforeach; 
                        } ?>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mb-4">
                <button onclick="clearFilters()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Reset Filter
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="expensesTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Dibuat Oleh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Belum ada data pengeluaran
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $expense): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('d/m/Y', strtotime($expense['expense_date'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-[#7B2C2C] text-white">
                                            <?php echo htmlspecialchars($expense['category']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php echo htmlspecialchars($expense['description']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">
                                        Rp <?php echo number_format($expense['amount'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 hidden sm:table-cell">
                                        <?php echo htmlspecialchars($expense['created_by_name'] ?? '-'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            <a href="index.php?page=cash_flow&action=edit_expense&id=<?php echo $expense['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900 px-3 py-1 border border-blue-600 rounded hover:bg-blue-50 transition-colors">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="index.php?page=cash_flow&action=delete_expense&id=<?php echo $expense['id']; ?>" 
                                               class="text-red-600 hover:text-red-900 px-3 py-1 border border-red-600 rounded hover:bg-red-50 transition-colors" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr class="font-bold">
                            <td colspan="3" class="px-6 py-4 text-sm text-gray-900">Total:</td>
                            <td id="totalAmount" class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">Rp 0</td>
                            <td class="hidden sm:table-cell"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </main>
    <script>
        // Calculate total amount
        function calculateTotal() {
            let total = 0;
            const rows = document.querySelectorAll('#expensesTable tbody tr');
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const amountCell = row.cells[3];
                    if (amountCell) {
                        const amountText = amountCell.textContent.replace(/[^\d]/g, '');
                        const amount = parseInt(amountText) || 0;
                        total += amount;
                    }
                }
            });
            document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
        
        // Apply all filters
        function applyFilters() {
            const category = document.getElementById('filterCategory').value.toLowerCase();
            const month = document.getElementById('filterMonth').value;
            const year = document.getElementById('filterYear').value;
            const rows = document.querySelectorAll('#expensesTable tbody tr');
            
            rows.forEach(row => {
                let show = true;
                const categoryCell = row.cells[1];
                const dateCell = row.cells[0];

                // Category filter
                if (category && categoryCell) {
                    const rowCategory = categoryCell.textContent.toLowerCase();
                    if (!rowCategory.includes(category)) {
                        show = false;
                    }
                }

                // Date filter
                if (dateCell && (month || year)) {
                    const dateText = dateCell.textContent; // "dd/mm/yyyy"
                    const dateParts = dateText.split('/');
                    if (dateParts.length === 3) {
                        const rowMonth = dateParts[1];
                        const rowYear = dateParts[2];
                        
                        if (month && rowMonth !== month) {
                            show = false;
                        }
                        if (year && rowYear !== year) {
                            show = false;
                        }
                    }
                }
                
                row.style.display = show ? '' : 'none';
            });
            
            calculateTotal();
        }

        // Refactor filterByCategory to use applyFilters
        function filterByCategory() {
            applyFilters();
        }
        
        // Clear all filters
        function clearFilters() {
            document.getElementById('filterCategory').value = '';
            document.getElementById('filterMonth').value = '';
            document.getElementById('filterYear').value = '';
            applyFilters();
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
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
            initializeSidebarToggle();
        });
    </script>
</body>
</html>