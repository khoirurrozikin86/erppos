# 🎟️ ScanTiket

**ScanTiket** adalah sistem manajemen dan validasi tiket berbasis QR Code yang digunakan untuk melakukan proses scanning tiket pada berbagai outlet atau wahana.

Sistem mendukung **Camera Scanner** dan **Barcode Scanner**, dilengkapi dengan sistem permission berdasarkan outlet, pencatatan histori scan, laporan transaksi, filtering berdasarkan tanggal, serta export data ke Excel.

---

## ✨ Features

### 📷 Camera Scanner

Melakukan scanning QR Code menggunakan kamera perangkat.

- Scan QR Code menggunakan kamera
- Pemilihan outlet sebelum melakukan scan
- Proses validasi tiket secara otomatis
- Notifikasi tiket valid atau ditolak
- Otomatis mencatat waktu scan
- Menampilkan 10 scan terakhir
- Scan method tersimpan sebagai `camera`

---

### 🔳 Barcode Scanner

Mendukung barcode scanner yang bekerja seperti keyboard input.

- Scan barcode menggunakan perangkat scanner
- Tidak diperuntukkan untuk input manual
- Validasi tiket secara realtime
- Pemilihan outlet
- Notifikasi hasil scanning
- Otomatis membersihkan input setelah scan
- Scan method tersimpan sebagai `scanner`

---

### 🎫 Ticket Validation

Sistem melakukan validasi terhadap tiket sebelum menerima scan.

Validasi mencakup:

- QR Code / barcode tiket
- Nomor tiket
- Jenis tiket
- Status tiket
- Outlet yang melakukan scan
- Riwayat penggunaan tiket

### 🔒 One-Time Ticket Usage

Setiap tiket hanya dapat digunakan **satu kali**.

Contoh:

```text
Tiket 103501
        ↓
Scan di Wahana A
        ↓
VALID
        ↓
Tiket menjadi USED
        ↓
Scan kembali di Wahana A
        ↓
DITOLAK
        ↓
Scan di Wahana B
        ↓
DITOLAK
```

---

## 🔄 Flow Operasional ERP

Flow berikut merupakan prosedur yang benar untuk menjaga data persediaan dan biaya pokok produk tetap valid sebelum barang dijual lewat POS.

### 1. Kelola master barang

Sebelum transaksi jual, pastikan produk sudah tersedia di master data.

- Isi nama barang
- Pilih kategori dan satuan
- Isi harga beli
- Pastikan produk aktif dan bisa dijual
- Jika produk menggunakan stok, aktifkan tracking stok

### 2. Buat Purchase Order

Setelah kebutuhan pembelian diketahui, buat PO ke supplier.

- Pilih supplier
- Tambahkan produk yang dibeli
- Tentukan kuantitas dan harga unit
- Simpan PO

### 3. Catat penerimaan barang

Penerimaan barang adalah langkah yang wajib sebelum item bisa dijual.

Prosesnya:

- Buka modul Penerimaan Barang
- Pilih PO yang sudah dibuat
- Masukkan qty yang benar-benar diterima
- Simpan penerimaan

Saat penerimaan disimpan, sistem akan:

- menambah stok barang
- menghitung biaya rata-rata persediaan
- mengisi data `average_unit_cost` / biaya persediaan

### 4. Baru lalu barang bisa dijual di POS

Setelah stok dan biaya persediaan tersedia, transaksi POS dapat dilakukan.

Persyaratan agar POS bisa checkout:

- produk aktif
- `allow_sales` aktif
- stok cukup
- biaya persediaan tersedia
- harga jual produk sudah diatur

### 5. Penjualan POS

Pada saat checkout POS, sistem akan otomatis:

- mengurangi stok barang
- menghitung biaya pokok penjualan
- mencatat stock movement
- menjurnal transaksi terkait

### 6. Aturan penting

Jika muncul pesan:

```text
Biaya persediaan [KODE] belum tersedia. Isi harga beli atau catat penerimaan lebih dahulu.
```

maka artinya:

- barang belum pernah masuk stok secara valid, atau
- harga beli masih kosong, atau
- penerimaan barang belum dicatat

Solusi:

1. isi harga beli produk, atau
2. catat penerimaan barang dari supplier, lalu
3. ulangi proses penjualan

---

## 📌 Ringkasan flow

```text
Master Barang
    ↓
Buat Purchase Order
    ↓
Catat Penerimaan Barang
    ↓
Stok + biaya persediaan tersedia
    ↓
Jual melalui POS
```

Ini penting agar harga pokok penjualan tetap akurat dan laporan persediaan tidak salah.
