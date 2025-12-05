<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - <?= APP_NAME ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar bg-gradient-to-br from-[#7B2C2C] to-[#800000] text-white w-64 fixed inset-y-0 left-0 z-50 overflow-y-auto transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full">
        <div class="sidebar-header p-5 text-center border-b border-white border-opacity-10">
            <?php 
            require_once __DIR__ . '/../helpers/logo_helper.php';
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
                <?php if (isSuperuser()): ?>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 " href="?page=users">
                        <i class="fas fa-users w-5 mr-3"></i> Pengguna
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 bg-white bg-opacity-10 text-white" href="?page=settings">
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
            <div class="bg-white p-5 rounded-lg shadow mb-8 flex justify-between items-center gap-4">
                <div class="flex items-center flex-grow">
                    <button id="sidebarToggle" class="text-gray-500 focus:outline-none focus:text-gray-900 mr-4 md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h4 class="text-2xl font-semibold text-gray-800">Pengaturan</h4>
                        <small class="text-gray-600">Konfigurasi sistem</small>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right hidden md:block">
                        <div class="font-semibold text-gray-800"><?= $_SESSION['full_name'] ?></div>
                        <div class="text-sm text-gray-600"><?= ucfirst($_SESSION['user_role']) ?></div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-[#7B2C2C] text-white flex items-center justify-center font-bold">
                        <?= substr($_SESSION['full_name'], 0, 2) ?>
                    </div>
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

            <!-- Settings Tabs -->
            <div class="bg-white rounded-lg shadow">
                <div class="border-b border-gray-200">
                    <div class="overflow-x-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
                        <style>::-webkit-scrollbar { display: none; }</style>
                        <nav class="flex -mb-px">
                            <button onclick="showTab('general')" class="tab-btn active px-4 py-3 border-b-2 border-[#7B2C2C] font-medium text-sm text-[#7B2C2C] whitespace-nowrap">
                                <i class="fas fa-store mr-2"></i>Informasi Toko
                            </button>
                            <button onclick="showTab('payment')" class="tab-btn px-4 py-3 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                                <i class="fas fa-credit-card mr-2"></i>Pembayaran
                            </button>
                            <button onclick="showTab('services')" class="tab-btn px-4 py-3 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                                <i class="fas fa-concierge-bell mr-2"></i>Layanan
                            </button>
                            <button onclick="showTab('system')" class="tab-btn px-4 py-3 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                                <i class="fas fa-cogs mr-2"></i>Sistem
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- General Settings Tab -->
                    <div id="general-tab" class="tab-content">
                        <form method="POST" action="?page=settings&action=saveGeneral" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo Toko</label>
                                    <div class="flex items-center space-x-4">
                                        <div class="shrink-0">
                                            <?php 
                                            require_once __DIR__ . '/../helpers/logo_helper.php';
                                            $logoPath = getLogoPath();
                                            if ($logoPath !== '../assets/images/logo.png'): ?>
                                                <img id="logo-preview" src="<?= $logoPath ?>" alt="<?= getLogoAlt() ?>" class="h-16 w-16 object-cover rounded-lg">
                                            <?php else: ?>
                                                <div id="logo-preview" class="h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-store text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" name="shop_logo" accept="image/*" id="shop_logo" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent"
                                                   onchange="previewLogo(event)">
                                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Max: 2MB</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Toko</label>
                                    <input type="text" name="shop_name" value="<?= htmlspecialchars(getSetting('shop_name', 'Dr.ShoezClean')) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                                    <input type="tel" name="shop_phone" value="<?= htmlspecialchars(getSetting('shop_phone', '')) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                    <textarea name="shop_address" rows="3" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent"><?= htmlspecialchars(getSetting('shop_address', '')) ?></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" name="shop_email" value="<?= htmlspecialchars(getSetting('shop_email', '')) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Website/Sosial Media</label>
                                    <input type="url" name="shop_website" value="<?= htmlspecialchars(getSetting('shop_website', '')) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="bg-[#7B2C2C] text-white px-4 py-2 rounded-lg hover:bg-[#6B2222] transition-colors duration-300">
                                    <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Payment Settings Tab -->
                    <div id="payment-tab" class="tab-content hidden">
                        <form method="POST" action="?page=settings&action=savePayment" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bank</label>
                                    <input type="text" name="bank_name" value="<?= htmlspecialchars(getSetting('bank_name', '')) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening</label>
                                    <input type="text" name="bank_account" value="<?= htmlspecialchars(getSetting('bank_account', '')) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Atas Nama</label>
                                    <input type="text" name="bank_holder" value="<?= htmlspecialchars(getSetting('bank_holder', '')) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">QR Code Payment</label>
                                    <input type="file" name="qr_code" accept="image/*" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                    <?php if ($qrCode = getSetting('qr_code_path')): ?>
                                        <div class="mt-2">
                                            <img src="<?= $qrCode ?>" alt="QR Code" class="h-20 w-20 object-cover rounded">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="bg-[#7B2C2C] text-white px-4 py-2 rounded-lg hover:bg-[#6B2222] transition-colors duration-300">
                                    <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Services Settings Tab -->
                    <div id="services-tab" class="tab-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Durasi Layanan (dalam hari)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Deep Clean Express</label>
                                    <input type="number" name="duration_deep_clean_express" min="1" value="<?= getSetting('duration_deep_clean_express', 1) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Deep Clean Reguler</label>
                                    <input type="number" name="duration_deep_clean_reguler" min="1" value="<?= getSetting('duration_deep_clean_reguler', 3) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Fast Clean Express</label>
                                    <input type="number" name="duration_fast_clean_express" min="1" value="<?= getSetting('duration_fast_clean_express', 1) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Unyellowing</label>
                                    <input type="number" name="duration_unyellowing" min="1" value="<?= getSetting('duration_unyellowing', 5) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Recolour</label>
                                    <input type="number" name="duration_recolour" min="1" value="<?= getSetting('duration_recolour', 7) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Repaint</label>
                                    <input type="number" name="duration_repaint" min="1" value="<?= getSetting('duration_repaint', 7) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="?page=settings&action=saveServices">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            <button type="submit" class="bg-[#7B2C2C] text-white px-4 py-2 rounded-lg hover:bg-[#6B2222] transition-colors duration-300">
                                <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                            </button>
                        </form>
                    </div>

                    <!-- System Settings Tab -->
                    <div id="system-tab" class="tab-content hidden">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Backup Database</h3>
                                <p class="text-gray-600 mb-4">Download backup database untuk keamanan data</p>
                                <button onclick="backupDatabase()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-300">
                                    <i class="fas fa-download mr-2"></i>Download Backup
                                </button>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Sistem</h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium">Versi PHP:</span> <?= PHP_VERSION ?>
                                        </div>
                                        <div>
                                            <span class="font-medium">Server:</span> <?= $_SERVER['SERVER_SOFTWARE'] ?>
                                        </div>
                                        <div>
                                            <span class="font-medium">Database:</span> MySQL
                                        </div>
                                        <div>
                                            <span class="font-medium">Zona Waktu:</span> <?= date_default_timezone_get() ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (isSuperuser()): ?>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Maintenance</h3>
                                <div class="space-y-3">
                                    <button onclick="clearCache()" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-300">
                                        <i class="fas fa-broom mr-2"></i>Bersihkan Cache
                                    </button>
                                    <button onclick="resetSettings()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-300">
                                        <i class="fas fa-undo mr-2"></i>Reset Pengaturan
                                    </button>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Tab functionality
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-[#7B2C2C]', 'text-[#7B2C2C]');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Add active class to clicked button
            event.target.classList.remove('border-transparent', 'text-gray-500');
            event.target.classList.add('border-[#7B2C2C]', 'text-[#7B2C2C]');
        }

        // Preview logo
        function previewLogo(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('logo-preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Logo Preview" class="h-16 w-16 object-cover rounded-lg">`;
                }
                reader.readAsDataURL(file);
            }
        }

        // Backup database
        function backupDatabase() {
            if (confirm('Download backup database sekarang?')) {
                window.location.href = '?page=settings&action=backup';
            }
        }

        // Clear cache
        function clearCache() {
            if (confirm('Bersihkan cache sistem?')) {
                fetch('?page=settings&action=clearCache', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cache berhasil dibersihkan');
                        location.reload();
                    } else {
                        alert('Gagal membersihkan cache');
                    }
                });
            }
        }

        // Reset settings
        function resetSettings() {
            if (confirm('Reset semua pengaturan ke default? Tindakan ini tidak dapat dibatalkan!')) {
                fetch('?page=settings&action=reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pengaturan berhasil direset');
                        location.reload();
                    } else {
                        alert('Gagal reset pengaturan');
                    }
                });
            }
        }

        // Sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            initializeSidebarToggle();
        });

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