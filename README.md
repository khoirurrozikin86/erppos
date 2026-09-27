# ERP POS

Aplikasi ERP untuk mengelola data operasional, persediaan, pembelian, penjualan, kas/bank, dan akuntansi dalam satu sistem.

## Modul

- **Master data:** perusahaan, kategori, satuan, produk, supplier, customer, dan daftar harga.
- **Pembelian:** permintaan pembelian, purchase order, penerimaan barang, dan retur pembelian.
- **Persediaan:** stok, kartu stok, stock opname, serta laporan persediaan.
- **Penjualan:** quotation, sales order, pengiriman, invoice, dan piutang.
- **POS:** sesi kasir, transaksi penjualan, pengurangan stok, dan pencatatan biaya pokok.
- **Keuangan dan akuntansi:** kas/bank, utang, akun, jurnal, dan laporan laba rugi.
- **Administrasi:** dashboard, pengaturan, pengguna, role, permission, dan audit log.

## Flow Operasional Pembelian hingga POS

### 1. Siapkan master barang

Buat produk dan lengkapi nama, kategori, satuan, harga jual, serta harga beli. Pastikan produk aktif dan pengaturan penjualannya sesuai.

### 2. Buat purchase order

Pilih supplier, masukkan produk dan kuantitas yang akan dibeli, lalu simpan PO.

### 3. Catat penerimaan barang

Dari PO, masukkan kuantitas barang yang benar-benar diterima. Setelah penerimaan dicatat, sistem menambah stok dan memperbarui biaya rata-rata persediaan (`average_unit_cost`).

### 4. Jual melalui POS

Setelah stok tersedia, lakukan transaksi di POS. Sistem memvalidasi ketersediaan stok dan biaya persediaan, lalu mencatat penjualan, mengurangi stok, dan menghitung biaya pokok penjualan.

> Jika POS menampilkan pesan bahwa biaya persediaan belum tersedia, periksa harga beli produk dan pastikan penerimaan barang sudah dicatat. Stok tanpa biaya yang valid tidak dapat digunakan untuk transaksi POS.

```text
Master Barang
    ↓
Purchase Order
    ↓
Penerimaan Barang
    ↓
Stok dan biaya persediaan tersedia
    ↓
Penjualan melalui POS
```

## Teknologi

- PHP 8.2 atau lebih baru
- Laravel 12
- Composer
- Node.js dan npm
- Database SQLite atau MySQL

## Menjalankan Secara Lokal

1. Pasang dependensi PHP dan JavaScript:

   ```bash
   composer install
   npm install
   ```

2. Siapkan file environment dan application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Atur koneksi database pada `.env`.

   Konfigurasi contoh menggunakan SQLite. Buat file database jika belum ada:

   ```bash
   touch database/database.sqlite
   ```

   Pastikan `.env` berisi:

   ```dotenv
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/project/database/database.sqlite
   ```

   Untuk MySQL, atur `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai database lokal.

4. Jalankan migrasi dan siapkan data pengguna serta permission:

   ```bash
   php artisan migrate
   php artisan db:seed --class=UsersAndPermissionsSeeder
   ```

   Seeder data awal lain tersedia di `database/seeders`; jalankan seeder yang dibutuhkan sesuai lingkungan.

5. Jalankan aplikasi dan Vite di terminal terpisah:

   ```bash
   php artisan serve
   ```

   ```bash
   npm run dev
   ```

   Buka URL yang ditampilkan oleh `php artisan serve`.

## Build Asset dan Pengujian

Build asset untuk penggunaan tanpa Vite dev server:

```bash
npm run build
```

Jalankan test suite:

```bash
php artisan test
```

## Struktur Direktori

- `app/Domain/` — logika domain untuk pembelian, persediaan, POS, penjualan, akuntansi, dan modul lainnya.
- `app/Http/Controllers/` — controller aplikasi.
- `database/migrations/` — skema database.
- `database/seeders/` — data awal.
- `resources/views/` — tampilan Blade.
- `routes/` — definisi route aplikasi.
