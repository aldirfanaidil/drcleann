<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna - <?= APP_NAME ?></title>
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
                    <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 bg-white bg-opacity-10 text-white" href="?page=users">
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
                        <h4 class="text-2xl font-semibold text-gray-800">Tambah Pengguna</h4>
                        <small class="text-gray-600">Buat pengguna baru</small>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="?page=users" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-300">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php unset($_SESSION['errors']); ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <form method="POST" action="?page=user_create">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                                <input type="text" name="username" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent"
                                    value="<?= htmlspecialchars($_SESSION['old']['username'] ?? '') ?>">
                                <?php if (isset($_SESSION['errors']['username'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= $_SESSION['errors']['username'] ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                                <input type="password" name="password" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                <?php if (isset($_SESSION['errors']['password'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= $_SESSION['errors']['password'] ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                                <input type="text" name="full_name" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent"
                                    value="<?= htmlspecialchars($_SESSION['old']['full_name'] ?? '') ?>">
                                <?php if (isset($_SESSION['errors']['full_name'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= $_SESSION['errors']['full_name'] ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent"
                                    value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>">
                                <?php if (isset($_SESSION['errors']['email'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= $_SESSION['errors']['email'] ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                                <input type="tel" name="phone"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent"
                                    value="<?= htmlspecialchars($_SESSION['old']['phone'] ?? '') ?>">
                                <?php if (isset($_SESSION['errors']['phone'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= $_SESSION['errors']['phone'] ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                                <select name="role" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B2C2C] focus:border-transparent">
                                    <option value="kasir" <?= (($_SESSION['old']['role'] ?? '') === 'kasir') ? 'selected' : '' ?>>Kasir</option>
                                    <option value="admin" <?= (($_SESSION['old']['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
                                    <option value="superuser" <?= (($_SESSION['old']['role'] ?? '') === 'superuser') ? 'selected' : '' ?>>Superuser</option>
                                </select>
                                <?php if (isset($_SESSION['errors']['role'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= $_SESSION['errors']['role'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="?page=users" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-300">
                                Batal
                            </a>
                            <button type="submit" class="bg-[#7B2C2C] text-white px-4 py-2 rounded-lg hover:bg-[#6B2222] transition-colors duration-300">
                                <i class="fas fa-save mr-2"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Initialize sidebar toggle
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

            // Close sidebar on resize if it's open on mobile
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