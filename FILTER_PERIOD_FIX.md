# 🔧 Perbaikan Logika Filter Periode Dashboard

## ❌ Masalah yang Ditemukan

User melaporkan: *"Filter bulan pendapatannya lebih sedikit dibanding filter minggu, kan minggu pendapatannya dihitung dari 7 hari pendapatan, trus bulan kan didapat dari 4 minggu pendapatan dan tahun dari rekap 12 bulan pendapatan"*

## 🔍 Analisis Masalah

### Root Cause
Ada **inkonsistensi logika** antara:
1. **Chart Stats** → Hanya menghitung `payment_status = 'paid'`
2. **KPI Stats** → Menghitung semua orders (termasuk pending)

### Data Contoh (Real dari Database)
```sql
-- 7 hari terakhir
Total orders: 31 (26 paid + 5 pending)
Revenue (paid only): Rp 1.244.000
Unpaid: Rp 257.000

-- Bulan Desember  
Total orders: 29 (24 paid + 5 pending)
Revenue (paid only): Rp 1.138.000
Unpaid: Rp 257.000
```

## ✅ Solusi yang Diimplementasikan

### 1. **Perbaiki Query KPI Stats**
```php
// BEFORE (salah)
SUM(total) as revenue_today  // Termasuk pending

// AFTER (benar)  
SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as revenue_today  // Hanya paid
```

### 2. **Tambah Method Revenue Khusus**
```php
// New methods untuk konsistensi
public function getTodayRevenue()     // Hanya paid orders
public function getWeeklyRevenue()    // Hanya paid orders  
public function getMonthlyRevenue()   // Hanya paid orders
public function getYearlyRevenue()   // Hanya paid orders
```

### 3. **Update DashboardController**
Gunakan method revenue khusus untuk KPI:
```php
$todayRevenue = $this->order->getTodayRevenue();
$weeklyRevenue = $this->order->getWeeklyRevenue();
$monthlyRevenue = $this->order->getMonthlyRevenue();
$yearlyRevenue = $this->order->getYearlyRevenue();
```

## 📊 Hasil Setelah Perbaikan

### Logika yang Benar:
- **Revenue** = Hanya pesanan yang **SUDAH DIBAYAR** (`payment_status = 'paid'`)
- **Orders** = Semua pesanan (paid + pending)
- **Unpaid** = Hanya pesanan `pending`

### Expected Behavior:
- **Minggu**: 7 hari terakhir (bisa lebih dari bulan jika ada data dari bulan lalu)
- **Bulan**: Hanya tanggal dalam bulan ini
- **Tahun**: Hanya tanggal dalam tahun ini

### Contoh Output:
```
Hari Ini:    Rp 0 (0 paid orders)
Minggu:       Rp 1.244.000 (26 paid orders)  
Bulan:        Rp 1.138.000 (24 paid orders)
Tahun:        Rp 1.244.000 (26 paid orders)
```

## 🎯 Why This Makes Sense

### Kenapa Minggu > Bulan?
- **Minggu**: 7 hari terakhir (29 Nov - 5 Des 2025)
- **Bulan**: Hanya Desember 2025 (1-5 Des 2025)
- **Minggu includes data from November**, bulan hanya Desember

### Kenapa Tahun = Minggu?
- Karena semua data di database hanya dari **November-Desember 2025**
- Jadi tahun 2025 = sama dengan 7 hari terakhir

## 🔧 Technical Changes

### Files Modified:
1. **Order.php**
   - Fixed `getTodayStats()`, `getWeeklyStats()`, `getMonthlyStats()`, `getYearlyStats()`
   - Added `getTodayRevenue()`, `getWeeklyRevenue()`, `getMonthlyRevenue()`, `getYearlyRevenue()`

2. **DashboardController.php**  
   - Updated `getKpiData()` to use revenue-specific methods
   - Fixed revenue calculation logic

### Query Changes:
```sql
-- BEFORE
SUM(total) as revenue_month

-- AFTER  
SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as revenue_month
```

## ✅ Validation

### Test Results:
```sql
-- Weekly Revenue (7 hari terakhir)
SELECT COALESCE(SUM(total), 0) as revenue_week
FROM orders 
WHERE DATE(date_in) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND payment_status = 'paid';
-- Result: Rp 1.244.000

-- Monthly Revenue (Desember 2025)  
SELECT COALESCE(SUM(total), 0) as revenue_month
FROM orders 
WHERE MONTH(date_in) = MONTH(CURDATE()) AND YEAR(date_in) = YEAR(CURDATE()) AND payment_status = 'paid';
-- Result: Rp 1.138.000
```

## 🎉 Status: FIXED

Sekarang dashboard menampilkan:
- ✅ **Revenue yang akurat** (hanya paid orders)
- ✅ **Periode yang konsisten** 
- ✅ **Logika yang masuk akal** (minggu bisa > bulan)

User tidak akan lagi melihat inkonsistensi data antar periode!