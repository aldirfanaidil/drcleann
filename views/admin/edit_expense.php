<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengeluaran - Dr.ShoezClean</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Pengeluaran</h5>
                        <a href="index.php?page=cash_flow&action=expenses" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($expense): ?>
                            <form method="POST" action="index.php?page=cash_flow&action=edit_expense&id=<?php echo $expense['id']; ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Kategori</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="category" name="category" 
                                                   list="categoryList" value="<?php echo htmlspecialchars($expense['category']); ?>"
                                                   placeholder="Ketik kategori atau pilih yang ada" required>
                                            <datalist id="categoryList">
                                                <?php 
                                                $existingCategories = ['Gaji', 'Sabun', 'Listrik', 'Sewa', 'Air', 'Internet', 'Peralatan', 'Marketing', 'Transportasi', 'Lainnya'];
                                                foreach ($existingCategories as $category): ?>
                                                    <option value="<?php echo $category; ?>">
                                                <?php endforeach; ?>
                                                <?php if (!empty($categories)): ?>
                                                    <?php foreach ($categories as $category): ?>
                                                        <?php if (!in_array($category, $existingCategories)): ?>
                                                            <option value="<?php echo htmlspecialchars($category); ?>">
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </datalist>
                                        </div>
                                        <small class="text-muted">Ketik kategori baru atau pilih dari daftar yang ada</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="amount" class="form-label">Jumlah (Rp)</label>
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                               value="<?php echo $expense['amount']; ?>" 
                                               min="0" step="1000" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <input type="text" class="form-control" id="description" name="description" 
                                           value="<?php echo htmlspecialchars($expense['description']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="expense_date" class="form-label">Tanggal Pengeluaran</label>
                                    <input type="date" class="form-control" id="expense_date" name="expense_date" 
                                           value="<?php echo $expense['expense_date']; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                                              placeholder="Tambahkan catatan jika diperlukan..."><?php echo htmlspecialchars($expense['notes'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="index.php?page=cash_flow&action=expenses" class="btn btn-secondary">
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Pengeluaran
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Pengeluaran tidak ditemukan.
                            </div>
                            <div class="text-center">
                                <a href="index.php?page=cash_flow&action=expenses" class="btn btn-primary">
                                    Kembali ke Daftar Pengeluaran
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-key.js" crossorigin="anonymous"></script>
</body>
</html>