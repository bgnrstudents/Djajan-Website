# 📖 CONTEXT & ARSITEKTUR PROYEK DJAJAN (FRONTEND & BACKEND)

Dokumen ini merupakan acuan utama pengembangan ekosistem **Djajan** (Sistem Informasi & Pemasaran UMKM Kuliner Desa Kreongan Atas, Jember).

---

## 1. Status & Arah Pengembangan Terkini (Current Direction)

* **Fokus Saat Ini**: **Menyelesaikan seluruh Frontend & UI/UX Mobile terlebih dahulu** sebelum memulai tahap integrasi API ke Backend Website.
* **Proyek Mobile Aktif**: **`djajan_mobile`** (lokasi: `D:\laragon\www\Djajan\djajan_mobile`). Ini adalah basis kode Flutter murni yang sedang dikerjakan langsung oleh pengembang.
* **Status `djajan_app`**: **TIDAK DIGUNAKAN / DEPRECATED**. Seluruh pengembangan mobile dipusatkan di `djajan_mobile`.
* **Backend & Web Admin**: **`Djajan-Website`** (Laravel 11) disiapkan untuk diintegrasikan setelah seluruh rancangan UI di `djajan_mobile` matang.

---

## 2. Struktur Komponen Proyek

### A. Mobile Frontend (`djajan_mobile`)
* **Framework**: Flutter (Dart)
* **Status**: Pengembangan antarmuka (UI/UX) dan interaksi dummy/mock.
* **Struktur Kode**:
  ```
  D:\laragon\www\Djajan\djajan_mobile\lib\
  ├── config/
  │   └── app_config.dart                  # Konfigurasi warna, gaya, & konstanta UI
  ├── models/
  │   └── product.dart                     # Model produk pembeli
  ├── routes/
  │   └── app_routes.dart                  # Routing (splash, login, register, customerHome, umkmHome, cart)
  ├── screens/
  │   ├── splash/
  │   │   └── splash_screen.dart           # Layar pembuka (branding Djajan)
  │   ├── auth/
  │   │   ├── login_screen.dart            # Form login (WhatsApp/Email + Password)
  │   │   └── register_screen.dart         # Form pendaftaran akun
  │   └── customer/
  │       ├── home/
  │       │   └── customer_home_screen.dart# Beranda pelanggan, filter kategori, switcher PO/Ready
  │       ├── cart/
  │       │   └── cart.dart                # Keranjang belanja pelanggan
  │       ├── order/
  │       │   ├── order_checkout_screen.dart    # Checkout: kurir warga/pickup, waktu butuh, catatan
  │       │   ├── order_payment_screen.dart     # Pembayaran: QRIS, Transfer, COD
  │       │   ├── order_confirmation_screen.dart# Konfirmasi pesanan sukses
  │       │   ├── order_list_screen.dart        # Daftar riwayat pesanan pelanggan
  │       │   ├── order_detail_screen.dart      # Rincian detail pesanan pelanggan
  │       │   ├── order_model.dart              # Model pesanan & draft vendor
  │       │   └── order_service.dart            # Service mock manajemen pesanan
  │       └── profile/
  │           └── profil.dart              # Profil & pengaturan akun pembeli
  └── widgets/
      ├── app_bottom_navigation.dart       # Navigasi bawah (Home, Pesanan, Profil)
      └── product_card.dart                # Kartu tampilan produk
  ```

### B. Web & Backend (`Djajan-Website`)
* **Framework**: Laravel 11 (PHP ^8.2), MySQL (`djajan_db`), Laravel Sanctum, Laravel Breeze, Tailwind CSS.
* **Peran**:
  1. **REST API Server** (`routes/api.php`): Menyediakan endpoint autentikasi, produk, pesanan, dan profil yang nantinya akan dihubungkan ke `djajan_mobile`.
  2. **Admin Panel Desa / BUMDes** (`/admin`): Verifikasi legalitas UMKM baru, kurasi produk, dan audit.
  3. **Web Publik Showcase** (`/`): Landing page profil desa dan katalog produk terverifikasi.

---

## 3. Checklist Progres UI di `djajan_mobile`

### ✅ Sudah Selesai (UI Implemented):
1. [x] **Splash Screen**: Tampilan logo dan animasi loading awal.
2. [x] **Auth Screens**:
   - [x] Login Screen (input WA/Email, password show/hide, remember me switch, tombol masuk).
   - [x] Register Screen.
3. [x] **Customer Home**:
   - [x] Header profil & lokasi pelanggan.
   - [x] Filter kategori (Semua, Makanan, Minuman, Jajanan).
   - [x] Filter mode pesanan (*Pre-Order* vs *Pesan Sekarang*).
   - [x] Grid/List kartu produk (`ProductCard`).
4. [x] **Keranjang Belanja (`CartScreen`)**: Daftar belanjaan terkelompok per toko/UMKM.
5. [x] **Alur Pemesanan Penuh (Checkout Flow)**:
   - [x] `OrderCheckoutScreen`: Pemilihan Kurir Warga / Ambil Sendiri, penentuan waktu kebutuhan PO, keperluan acara, dan catatan.
   - [x] `OrderPaymentScreen`: Pilihan pembayaran QRIS, Transfer Bank, atau Tunai (COD).
   - [x] `OrderConfirmationScreen`: Halaman sukses setelah bayar/checkout.
   - [x] `OrderListScreen` & `OrderDetailScreen`: Riwayat dan status perjalanan pesanan.
6. [x] **Customer Profile (`profil.dart`)**: Info pengguna, alamat tersimpan, menu bantuan, keluar akun.

### ⏳ Yang Bisa Disempurnakan / Dibangun Selanjutnya (Frontend Tasks):
1. [ ] **Halaman Detail Produk (`ProductDetailScreen`)**:
   - Klik kartu produk dari Beranda membuka halaman detail (foto besar, deskripsi lengkap, batas minimal PO, estimasi waktu masak, profil UMKM penjual, tombol "Tambah ke Keranjang").
2. [ ] **Fitur Pencarian & Filter Lanjutan**:
   - Implementasi fungsionalitas input pencarian di Home (pencarian instan berdasarkan nama produk/toko).
3. [ ] **Role Mitra UMKM di Mobile (Jika Ingin Disatukan)**:
   - Di `app_routes.dart` terdapat rute `umkmHome = '/umkm/home'`. Jika ingin ada halaman khusus UMKM di dalam aplikasi yang sama:
     - Dashboard Toko (Ringkasan penjualan, pesanan masuk).
     - Halaman Tambah & Kelola Produk Toko.
     - Penerimaan Pesanan Masuk (PO Baru).
4. [ ] **Empty States & Feedback Visual**:
   - Ilustrasi saat keranjang kosong, riwayat pesanan kosong, atau hasil pencarian tidak ditemukan.
   - Notifikasi dialog / modal konfirmasi sebelum aksi penting (misal hapus item di cart).

---

## 4. Rencana Integrasi Mendatang (Setelah UI Rampung)

Setelah seluruh alur UI di `djajan_mobile` selesai dan disetujui:
1. **Penyelarasan Model Data**: Menyamakan struktur data model Dart (`product.dart`, `order_model.dart`) dengan skema JSON dari Laravel (`keFormatJson()`).
2. **Implementasi API Client**: Memasang layer HTTP client (seperti `http` / `dio`) dan state management ke `djajan_mobile`.
3. **Endpoint Backend Tambahan di Laravel**:
   - Endpoint autentikasi Pelanggan (`POST /api/pelanggan/register` & `POST /api/pelanggan/login`).
   - Endpoint katalog publik & pencarian (`GET /api/produk_explore`).
   - Endpoint pembuatan pesanan baru oleh pelanggan (`POST /api/pesanan_buat`).
