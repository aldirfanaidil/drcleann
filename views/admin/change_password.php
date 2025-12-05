<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <?php include __DIR__ . '/layouts/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="md:ml-64 p-5 min-h-screen">
        <div class="max-w-7xl mx-auto px-4">
            <?php
            $page_title = 'Ubah Password';
            $page_subtitle = 'Perbarui password demi keamanan akun Anda';
            include __DIR__ . '/layouts/header.php';
            ?>

            <!-- Change Password Form -->
            <div class="bg-white rounded-lg shadow-lg p-6 md:p-8 max-w-lg mx-auto mt-8">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Ubah Password Akun</h2>
                <p class="text-center text-gray-500 mb-6">Pastikan untuk menggunakan password yang kuat.</p>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6" role="alert">
                        <p class="font-bold">Sukses</p>
                        <p><?= $_SESSION['success'] ?></p>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                     <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
                        <p class="font-bold">Gagal</p>
                        <p><?= $_SESSION['error'] ?></p>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form method="POST" action="?page=change_password" id="changePasswordForm" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                        <div class="relative">
                             <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-lock text-gray-400"></i>
                            </span>
                            <input type="password" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#7B2C2C] focus:border-[#7B2C2C] sm:text-sm" id="current_password" name="current_password" required>
                        </div>
                        <?php if (isset($_SESSION['errors']['current_password'])): ?>
                            <p class="text-red-500 text-xs mt-1"><?= $_SESSION['errors']['current_password'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                         <div class="relative">
                             <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-key text-gray-400"></i>
                            </span>
                            <input type="password" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#7B2C2C] focus:border-[#7B2C2C] sm:text-sm" id="new_password" name="new_password" required>
                        </div>
                        <div class="text-xs mt-1 text-gray-500" id="passwordStrength"></div>
                        <?php if (isset($_SESSION['errors']['new_password'])): ?>
                             <p class="text-red-500 text-xs mt-1"><?= $_SESSION['errors']['new_password'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                             <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-check-double text-gray-400"></i>
                            </span>
                            <input type="password" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#7B2C2C] focus:border-[#7B2C2C] sm:text-sm" id="confirm_password" name="confirm_password" required>
                        </div>
                        <?php if (isset($_SESSION['errors']['confirm_password'])): ?>
                            <p class="text-red-500 text-xs mt-1"><?= $_SESSION['errors']['confirm_password'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-col sm:flex-row-reverse gap-3">
                        <button type="submit" class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#7B2C2C] hover:bg-[#6B2222] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7B2C2C] transition-colors">
                            <i class="fas fa-save mr-2"></i>Ubah Password
                        </button>
                        <a href="?page=dashboard" class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Password strength checker
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthDiv.innerHTML = '';
                return;
            }
            
            let strength = 0;
            let feedback = [];
            
            if (password.length >= 8) strength++; else feedback.push('8+ karakter');
            if (/[A-Z]/.test(password)) strength++; else feedback.push('huruf besar');
            if (/[a-z]/.test(password)) strength++; else feedback.push('huruf kecil');
            if (/[0-9]/.test(password)) strength++; else feedback.push('angka');
            if (/[^A-Za-z0-9]/.test(password)) strength++; else feedback.push('simbol');
            
            let strengthText = '';
            let strengthClass = '';
            
            if (strength <= 2) {
                strengthText = 'Lemah';
                strengthClass = 'text-red-500';
            } else if (strength <= 4) {
                strengthText = 'Sedang';
                strengthClass = 'text-yellow-500';
            } else {
                strengthText = 'Kuat';
                strengthClass = 'text-green-500';
            }
            
            strengthDiv.innerHTML = `<span class="${strengthClass} font-semibold">Kekuatan: ${strengthText}</span>`;
            if (feedback.length > 0 && strength < 5) {
                strengthDiv.innerHTML += `<span class="text-gray-500 ml-2">| Perlu: ${feedback.slice(0, 2).join(', ')}</span>`;
            }
        });

        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            if (this.value && newPassword !== this.value) {
                this.setCustomValidity('Password tidak cocok');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
<?php unset($_SESSION['errors']); ?>