<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - <?= APP_NAME ?></title>
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
                        <h4 class="text-2xl font-semibold text-gray-800">Manajemen Pengguna</h4>
                        <small class="text-gray-600">Kelola pengguna sistem</small>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="?page=user_create" class="bg-[#7B2C2C] text-white px-2 py-2 sm:px-4 rounded-lg hover:bg-[#6B2222] transition-colors duration-300 flex items-center">
                        <i class="fas fa-plus sm:mr-2"></i> <span class="hidden sm:inline">Tambah Pengguna</span>
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

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada data pengguna
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['username']) ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?= htmlspecialchars($user['full_name']) ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?= htmlspecialchars($user['email'] ?? '-') ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?= htmlspecialchars($user['phone'] ?? '-') ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?php 
                                                    if ($user['role'] === 'superuser') echo 'bg-purple-100 text-purple-800';
                                                    elseif ($user['role'] === 'admin') echo 'bg-blue-100 text-blue-800';
                                                    else echo 'bg-green-100 text-green-800';
                                                    ?>">
                                                    <?= ucfirst($user['role']) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="?page=user_edit&id=<?= $user['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                                    <button onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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

        function deleteUser(userId, username) {
            if (confirm(`Apakah Anda yakin ingin menghapus pengguna "${username}"?`)) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                 '<?= $_SESSION['csrf_token'] ?? '' ?>';
                
                fetch('?page=user_delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${userId}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus pengguna');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
            }
        }
    </script>
</body>
</html>