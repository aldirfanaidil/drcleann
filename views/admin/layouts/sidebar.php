<?php
// File: views/admin/layouts/sidebar.php
$current_page = $_GET['page'] ?? 'dashboard';
?>
<!-- Sidebar -->
<nav id="sidebar" class="sidebar bg-gradient-to-br from-[#7B2C2C] to-[#800000] text-white w-64 fixed inset-y-0 left-0 z-50 overflow-y-auto transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full">
    <div class="sidebar-header p-5 text-center border-b border-white border-opacity-10">
        <?php
        // Menggunakan path absolut untuk memastikan helper selalu ditemukan
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
                <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 hover:text-white transition-all duration-300 ease-in-out <?= $current_page == 'dashboard' ? 'bg-white bg-opacity-10' : '' ?>" href="?page=dashboard">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 hover:text-white transition-all duration-300 ease-in-out <?= $current_page == 'orders' ? 'bg-white bg-opacity-10' : '' ?>" href="?page=orders">
                    <i class="fas fa-shopping-cart w-5 mr-3"></i> Pesanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 hover:text-white transition-all duration-300 ease-in-out <?= $current_page == 'reports' ? 'bg-white bg-opacity-10' : '' ?>" href="?page=reports">
                    <i class="fas fa-chart-bar w-5 mr-3"></i> Laporan
                </a>
            </li>
            <?php if (isSuperuser()) : ?>
            <li class="nav-item">
                <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 hover:text-white transition-all duration-300 ease-in-out <?= $current_page == 'cash_flow' ? 'bg-white bg-opacity-10' : '' ?>" href="?page=cash_flow">
                    <i class="fas fa-chart-line w-5 mr-3"></i> Arus Kas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 hover:text-white transition-all duration-300 ease-in-out <?= $current_page == 'users' ? 'bg-white bg-opacity-10' : '' ?>" href="?page=users">
                    <i class="fas fa-users w-5 mr-3"></i> Pengguna
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 hover:text-white transition-all duration-300 ease-in-out <?= $current_page == 'settings' ? 'bg-white bg-opacity-10' : '' ?>" href="?page=settings">
                    <i class="fas fa-cog w-5 mr-3"></i> Pengaturan
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item mt-3">
                <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 hover:text-white transition-all duration-300 ease-in-out <?= $current_page == 'change_password' ? 'bg-white bg-opacity-10' : '' ?>" href="?page=change_password">
                    <i class="fas fa-key w-5 mr-3"></i> Ubah Password
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link flex items-center py-3 px-5 text-white text-opacity-80 hover:bg-white hover:bg-opacity-10 hover:text-white transition-all duration-300 ease-in-out" href="../login.php?action=logout">
                    <i class="fas fa-sign-out-alt w-5 mr-3"></i> Keluar
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Overlay for sidebar on mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden opacity-0 transition-opacity duration-300 ease-in-out pointer-events-none"></div>

<script>
// Pindahkan script sidebar ke sini agar reusable
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    // Pastikan elemen ada sebelum menambahkan event listener
    if (sidebar && sidebarToggle && sidebarOverlay) {
        const toggle = () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('opacity-0');
            // Ganti opacity-50 dengan class Tailwind yang benar
            sidebarOverlay.classList.toggle('opacity-50');
            sidebarOverlay.classList.toggle('pointer-events-none');
            // Ganti pointer-events-auto dengan class Tailwind yang benar
            sidebarOverlay.classList.toggle('pointer-events-auto');
        };
        sidebarToggle.addEventListener('click', toggle);
        sidebarOverlay.addEventListener('click', toggle);
    }
});
</script>
