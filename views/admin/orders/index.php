<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="md:ml-64 p-5 min-h-screen">
        <div class="max-w-7xl mx-auto px-4">
            <?php
            $page_title = 'Pesanan';
            $page_subtitle = 'Manajemen pesanan customer';
            $extra_buttons = [
                '<a href="?page=add_order" class="bg-[#7B2C2C] text-white px-4 py-2 rounded-lg hover:bg-[#6B2222] transition-colors text-sm shadow">
                    <i class="fas fa-plus mr-2"></i>Tambah Pesanan
                </a>'
            ];
            include __DIR__ . '/../layouts/header.php';
            ?>

            <?php
            // --- START NEW PAGINATION & FILTER LOGIC (No Change in Logic) ---
            $database = new Database();
            $db = $database->getConnection();
            $orderModel = new Order($db);

            $filters = [];
            $allowed_filters = ['customer_name', 'phone', 'invoice_no', 'payment_status'];
            foreach ($allowed_filters as $key) {
                if (!empty($_GET[$key])) {
                    $filters[$key] = Security::sanitizeInput($_GET[$key]);
                }
            }

            $limit_options = [10, 20, 50, 100];
            $limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $limit_options) ? (int)$_GET['limit'] : 20;
            $page_num = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
            $offset = ($page_num - 1) * $limit;

            $total_orders = $orderModel->countAll($filters);
            $total_pages = ceil($total_orders / $limit);

            $orders = $orderModel->getAll($filters, $limit, $offset);
            $lastOrderId = $orderModel->getLastOrderId();
            // --- END NEW PAGINATION & FILTER LOGIC ---
            ?>

            <!-- Filter Section -->
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <form method="GET">
                    <input type="hidden" name="page" value="orders">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-4 items-end">
                        
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Customer</label>
                            <input type="text" id="customer_name" name="customer_name" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#7B2C2C] focus:border-[#7B2C2C] sm:text-sm" value="<?= htmlspecialchars($filters['customer_name'] ?? '') ?>" placeholder="Cari nama...">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                            <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#7B2C2C] focus:border-[#7B2C2C] sm:text-sm" value="<?= htmlspecialchars($filters['phone'] ?? '') ?>" placeholder="No. HP">
                        </div>

                        <div>
                            <label for="invoice_no" class="block text-sm font-medium text-gray-700 mb-1">Invoice</label>
                            <input type="text" id="invoice_no" name="invoice_no" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#7B2C2C] focus:border-[#7B2C2C] sm:text-sm" value="<?= htmlspecialchars($filters['invoice_no'] ?? '') ?>" placeholder="No. Invoice">
                        </div>

                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Status Bayar</label>
                            <select id="payment_status" name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#7B2C2C] focus:border-[#7B2C2C] sm:text-sm">
                                <option value="">Semua</option>
                                <option value="pending" <?= ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Belum Bayar</option>
                                <option value="paid" <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Lunas</option>
                                <option value="cancelled" <?= ($filters['payment_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>

                        <div>
                            <label for="limit" class="block text-sm font-medium text-gray-700 mb-1">Baris</label>
                            <select id="limit" name="limit" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#7B2C2C] focus:border-[#7B2C2C] sm:text-sm">
                                <?php foreach ($limit_options as $option): ?>
                                <option value="<?= $option ?>" <?= $limit === $option ? 'selected' : '' ?>><?= $option ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="w-full bg-[#7B2C2C] text-white px-4 py-2 rounded-md hover:bg-[#6B2222] transition-colors shadow flex items-center justify-center">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="?page=orders" class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors shadow flex items-center justify-center">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-10">
                                        <div class="text-gray-400">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p class="text-gray-500">Tidak ada pesanan yang cocok dengan filter.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $index => $order): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $offset + $index + 1 ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($order['invoice_no']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($order['phone']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $order['total_pairs'] ?> pasang</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold"><?= formatCurrency($order['total']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $statusClass = [
                                            'paid' => 'bg-green-100 text-green-800', 
                                            'pending' => 'bg-yellow-100 text-yellow-800', 
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ][$order['payment_status']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                            <?= strtoupper(htmlspecialchars($order['payment_status'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= formatDate($order['date_in']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="?page=order_detail&id=<?= $order['id'] ?>" class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-100 transition-colors"><i class="fas fa-eye"></i></a>
                                            <a href="?page=order_edit&id=<?= $order['id'] ?>" class="text-yellow-600 hover:text-yellow-900 p-2 rounded-full hover:bg-yellow-100 transition-colors"><i class="fas fa-edit"></i></a>
                                            <?php if (isSuperuser()): ?>
                                            <button type="button" class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-100 transition-colors" onclick="deleteOrder(<?= $order['id'] ?>)"><i class="fas fa-trash"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                         <?php if ($page_num > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page_num' => $page_num - 1])) ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"> Previous </a>
                        <?php endif; ?>
                        <?php if ($page_num < $total_pages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page_num' => $page_num + 1])) ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"> Next </a>
                        <?php endif; ?>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium"><?= $offset + 1 ?></span>
                                sampai
                                <span class="font-medium"><?= $offset + count($orders) ?></span>
                                dari
                                <span class="font-medium"><?= $total_orders ?></span>
                                hasil
                            </p>
                        </div>
                        <div>
                            <?php if ($total_pages > 1): ?>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php
                                $query_params = $_GET;
                                unset($query_params['page_num']);
                                $base_url = '?' . http_build_query($query_params);

                                // Previous button
                                if ($page_num > 1) {
                                    echo '<a href="' . $base_url . '&page_num=' . ($page_num - 1) . '" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"><span class="sr-only">Previous</span><i class="fas fa-chevron-left h-5 w-5"></i></a>';
                                }

                                // Page numbers
                                for ($i = 1; $i <= $total_pages; $i++) {
                                    $activeClass = $i == $page_num ? 'z-10 bg-red-50 border-red-500 text-red-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50';
                                    echo '<a href="' . $base_url . '&page_num=' . $i . '" aria-current="page" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ' . $activeClass . '">' . $i . '</a>';
                                }
                                
                                // Next button
                                if ($page_num < $total_pages) {
                                    echo '<a href="' . $base_url . '&page_num=' . ($page_num + 1) . '" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"><span class="sr-only">Next</span><i class="fas fa-chevron-right h-5 w-5"></i></a>';
                                }
                                ?>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
    
    <script>
        function deleteOrder(orderId) {
            if (confirm('Apakah Anda yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan.')) {
                const formData = new FormData();
                formData.append('id', orderId);
                formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');
                
                fetch('?page=order_delete', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pesanan berhasil dihapus');
                        window.location.reload();
                    } else {
                        alert('Gagal menghapus pesanan: ' + (data.message || 'Terjadi kesalahan'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus pesanan.');
                });
            }
        }

        // Auto Refresh Logic
        let lastKnownId = <?= (int)$lastOrderId ?>;
        const checkInterval = 5000; // Cek setiap 5 detik

        function checkForNewOrders() {
            fetch(`?page=orders&action=checkNewOrders&last_id=${lastKnownId}`)
            .then(response => response.json())
            .then(data => {
                if (data.has_new) {
                    // Beri notifikasi atau langsung reload
                    // Untuk kesederhanaan, kita reload saja
                    window.location.reload();
                }
            })
            .catch(error => console.log('Auto-refresh check failed', error));
        }

        setInterval(checkForNewOrders, checkInterval);
    </script>
</body>
</html>