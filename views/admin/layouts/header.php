<?php
// File: views/admin/layouts/header.php

// Default title jika tidak di-set oleh halaman yang meng-include
$page_title = isset($page_title) ? $page_title : 'Admin Panel';
$page_subtitle = isset($page_subtitle) ? $page_subtitle : 'Selamat datang, ' . htmlspecialchars($_SESSION['full_name']);

?>
<!-- Top Header -->
<div class="bg-white p-5 rounded-lg shadow mb-8">
    <!-- First Row: Title and User Info -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-3">
        <div class="flex items-center flex-grow">
            <button id="sidebarToggle" class="text-gray-500 focus:outline-none focus:text-gray-900 mr-4 md:hidden">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div>
                <h4 class="text-2xl font-semibold text-gray-800"><?= $page_title ?></h4>
                <small class="text-gray-600"><?= $page_subtitle ?></small>
            </div>
        </div>
        <div class="hidden md:flex items-center gap-3">
            <div class="text-right">
                <div class="font-semibold text-gray-800"><?= htmlspecialchars($_SESSION['full_name']) ?></div>
                <div class="text-sm text-gray-600"><?= ucfirst(htmlspecialchars($_SESSION['user_role'])) ?></div>
            </div>
            <div class="w-10 h-10 rounded-full bg-[#7B2C2C] text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                <?= strtoupper(substr(htmlspecialchars($_SESSION['full_name']), 0, 2)) ?>
            </div>
        </div>
    </div>
    
    <!-- Second Row: Last Update and Action Buttons -->
    <?php if(($_GET['page'] ?? 'dashboard') === 'dashboard' || isset($extra_buttons)): ?>
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 pt-3 border-t border-gray-100">
        <?php if(($_GET['page'] ?? 'dashboard') === 'dashboard'): ?>
        <div class="text-xs text-gray-500 flex items-center">
            <i class="fas fa-sync-alt mr-1 opacity-75"></i>
            <span>Terakhir update: <span id="last-update">-</span></span>
        </div>
        <?php else: ?>
        <div class="order-2 md:order-1"></div>
        <?php endif; ?>
        
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 order-1 md:order-2">
            <?php
            // Opsi untuk menambahkan tombol ekstra dari halaman yang memanggil
            if (isset($extra_buttons) && is_array($extra_buttons)) {
                foreach ($extra_buttons as $button) {
                    echo $button;
                }
            }
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>
