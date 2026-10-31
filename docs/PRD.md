# PRD — Migrasi MyFinance dari Laravel ke CodeIgniter 4

| | |
|---|---|
| **Versi** | 1.0 |
| **Tanggal** | 31 Oktober 2026 |
| **Sumber (as-is)** | `C:\Laravel-herd\myfinance` — Laravel 12 + Livewire 3 + Tailwind v4 (Vite) |
| **Target (to-be)** | `D:\project-app\finance-app` — CodeIgniter 4 (rilis stabil terbaru) + Tailwind v4 CDN + htmx + Alpine.js |
| **Status** | Draft untuk implementasi |

---

## 1. Ringkasan

MyFinance adalah aplikasi keuangan pribadi untuk mengelola **tagihan rumah**, **utang & piutang**, dan **bisnis kecil** (klien, produk, penawaran, invoice, kwitansi). Saat ini aplikasi dibangun dengan Laravel 12 + Livewire 3, dan aset di-build dengan Vite/npm.

Proyek ini memigrasikan aplikasi ke **CodeIgniter 4** dengan syarat:

1. **Tampilan UI identik.** Markup HTML dan kelas Tailwind disalin dari Blade sumber tanpa redesain.
2. **Tanpa npm/build step.** Tailwind dimuat dari CDN (`@tailwindcss/browser@4`). Alpine.js dan htmx juga dari CDN.
3. **Interaksi tanpa reload.** Semua yang sekarang memakai Livewire (search, filter, paginasi, toggle status, hapus, navigasi bulan, validasi form, tambah/hapus cicilan, hitung total invoice) tetap berjalan tanpa reload halaman penuh.
4. **Kompatibel dengan database yang ada.** Skema MySQL dipertahankan agar data lama bisa diimpor langsung.

---

## 2. Analisis Sistem Saat Ini (As-Is)

### 2.1 Stack

| Lapisan | Saat ini |
|---|---|
| Framework | Laravel 12, PHP 8.2+ (lokal: PHP 8.3) |
| UI reaktif | Livewire 3 (full-page components), Alpine.js (bawaan Livewire) |
| CSS | Tailwind CSS v4 via `@tailwindcss/vite`, `resources/css/app.css` |
| JS | `resources/js/app.js`: grafik bar native + modal detail, banner install PWA |
| DB | MySQL (`DB_CONNECTION=mysql`, db `myfinance`) |
| Session/Cache/Queue | database |
| Storage | disk `public` → `storage/app/public/bill-payments/*.jpg` |
| Font | Inter (app), Plus Jakarta Sans (halaman login) — via fonts.bunny.net |
| PWA | `public/manifest.json`, `public/sw.js`, ikon di `public/img/icons` |
| Timezone | `UTC` (config/app.php) |

### 2.2 Status Modul

| Modul | Status di Laravel | Tampil di sidebar? | Lingkup migrasi |
|---|---|---|---|
| Auth (login, logout) | Berjalan | — | **Fase 1** |
| Register | Route ada, **view kosong (0 baris)** | Tidak | Fase 1 (lihat Q-1) |
| Dashboard utama | Berjalan | Ya | **Fase 1** |
| Operasional Rumah (dashboard tagihan, master tagihan, riwayat pembayaran) | Berjalan | Ya | **Fase 1** |
| Utang, Piutang, Kontak | Berjalan | Ya | **Fase 1** |
| Settings (profil, password, hapus akun) | Berjalan | Lewat dropdown user | **Fase 1** |
| Manajemen Bisnis (dashboard, klien, produk, penawaran, invoice, kwitansi) | Berjalan (CRUD) | **Disembunyikan** | **Fase 2** (tetap disembunyikan, akses via URL) |
| Keuangan (akun, transaksi, kategori, budget) | Placeholder "🚧" (pakai `flux:` yang sudah dihapus, sehingga rusak) | Tidak | Fase 3: skema DB + placeholder saja |
| Kendaraan (kendaraan, servis, BBM) | Placeholder "🚧" | Tidak | Fase 3: skema DB + placeholder saja |

### 2.3 Inventaris Halaman & Interaksi Livewire

| Halaman (URL) | Interaksi Livewire saat ini |
|---|---|
| `/login` | `wire:submit` + validasi inline + rate-limit 5x; toggle show password (Alpine) |
| `/dashboard` | Statis (server-render) |
| `/house` | `prevMonth` / `nextMonth` (tidak boleh melewati bulan berjalan), filter kategori grafik `wire:model.live`, grafik 12 bulan + modal detail per bulan |
| `/house/bills` | search debounce 300ms, paginasi 15, toggle aktif, modal hapus |
| `/house/bills/create`, `/{id}/edit` | form + validasi inline |
| `/house/payments` | filter tagihan/bulan/tahun (live), paginasi, modal hapus; mendukung `?bill_id=` |
| `/house/payments/create`, `/{id}/edit` | input nominal berformat ribuan (Alpine), upload screenshot + preview + indikator upload, cek duplikat periode; mendukung `?bill=` |
| `/house/payments/{id}` | detail + gambar bukti |
| `/debt/contacts` | search (nama/telepon/email), paginasi, modal hapus |
| `/debt/debts`, `/debt/receivables` | search (nama kontak/tujuan), filter status, paginasi, total belum lunas, modal hapus |
| `/debt/debts/{id}`, `/debt/receivables/{id}` | toggle form bayar, simpan cicilan (validasi ≤ sisa), modal hapus cicilan; status & sisa diperbarui otomatis |
| `/debt/*/create`, `/edit` | form + validasi inline |
| `/business/*` (Fase 2) | search, filter status, toggle aktif, modal hapus; form penawaran/invoice dengan **line items dinamis**, isi otomatis dari produk, hitung subtotal/diskon/pajak/total secara live; konversi penawaran ke invoice (`from_quotation`) |
| `/settings/profile`, `/settings/password` | form + validasi inline, hapus akun dengan konfirmasi password |

### 2.4 Logika Bisnis Kritis (wajib dipertahankan)

1. **Isolasi data per user.** Semua query memakai filter `user_id = auth()->id()`.
2. **Periode default dashboard.** Dashboard utama dan dashboard tagihan memakai periode pembayaran terakhir (bulan/tahun) yang **tidak melewati bulan berjalan**. Jika belum ada pembayaran, dipakai bulan berjalan.
3. **Pembayaran tagihan.** Kombinasi `(utility_bill_id, period_month, period_year)` unik. Jika sudah ada, tampilkan error di field `period_month`: "Tagihan ini sudah dibayar untuk periode tersebut."
4. **Kompresi screenshot.** Gambar di-resize hingga sisi terpanjang maksimal 1600px, latar putih, disimpan sebagai JPEG kualitas 80 dengan nama `bill-payments/{uuid}.jpg`. Saat edit, file lama dihapus jika diganti. Input yang diterima: jpg/jpeg/png/webp/gif/bmp, maksimal 5MB.
5. **Due soon.** `UtilityBill::isDueSoon(3)` hanya ditampilkan jika periode yang dilihat adalah bulan berjalan.
6. **Grafik 12 bulan.** Total `amount_paid` per periode untuk 12 bulan sampai periode terpilih, bisa difilter per kategori. Setiap batang menyimpan `details` per kategori (label, total, count) untuk modal.
7. **Utang/Piutang.**
   - Saat dibuat: `remaining_amount = amount`, `status = unpaid`, `interest_rate = null`.
   - Saat diedit: `remaining_amount = max(0, remaining_lama + (amount_baru − amount_lama))`, lalu status dihitung ulang.
   - Tambah cicilan: `remaining -= amount`. Hapus cicilan: `remaining += amount`. Status: `≤0 → paid`, `< amount → partial`, selain itu `unpaid`.
   - Validasi cicilan: `0.01 ≤ pay_amount ≤ remaining_amount`.
   - Urutan list: status (unpaid, partial, paid), lalu `due_date`.
8. **Invoice/Penawaran (Fase 2).**
   - Subtotal item: `qty × unit_price`, lalu diskon item (`percent` atau `nominal`), minimal 0.
   - Subtotal dokumen = Σ subtotal item. Diskon dokumen dihitung dari subtotal. `tax = (subtotal − diskon) × tax_rate%`. `total = subtotal − diskon + tax`.
   - Invoice: `remaining_amount = total − paid_amount`.
   - Nomor dokumen: `{PREFIX}/{YYYY}/{MM}/{counter 3 digit}` dari `business_settings`. Prefix default: QUO / INV / RCP.
   - Default tanggal: penawaran `valid_until = +14 hari`, invoice `due_date = +30 hari`.
9. **Auth.** Login memakai email + password (bcrypt), "ingat saya", dan rate-limit 5 percobaan per `email|ip`. Pesan: "Terlalu banyak percobaan login. Coba lagi dalam {n} detik."
10. **Inisial avatar.** Diambil dari huruf pertama setiap kata pada nama user.

### 2.5 Bug & Celah yang Ditemukan (diperbaiki saat migrasi)

| ID | Temuan | Dampak | Perbaikan di CI4 |
|---|---|---|---|
| BUG-1 | Search memakai `orWhere` tanpa grouping (Kontak, Utang, Piutang, Klien, Produk, Penawaran, Invoice, Kwitansi): `WHERE user_id=? AND name LIKE ? OR phone LIKE ?` | **Kebocoran data antar user** saat search | Selalu bungkus dengan `groupStart()/groupEnd()` |
| BUG-2 | Edit tagihan (`house/bills/{id}/edit`) dan edit pembayaran tidak mengecek pemilik | User lain bisa membuka/mengubah data via ID | Cek `user_id` → 404 |
| BUG-3 | Nomor invoice/penawaran: jika `business_settings` belum ada, counter selalu 1 | Error unique constraint mulai dokumen kedua | Auto-create `business_settings` + transaksi DB + row lock |
| BUG-4 | Hapus kontak/klien yang masih punya utang/invoice terkena FK `RESTRICT` | Error 500 | Cek relasi dulu, tampilkan pesan ramah |
| BUG-5 | Prefill `?bill=` (form pembayaran) dan `?bill_id=` (riwayat) tidak diteruskan ke `mount()` Livewire | Tombol "✓ Bayar" / "Riwayat" tidak mem-prefill | Baca dari query string |
| BUG-6 | Halaman register kosong | Halaman blank | Lihat Q-1 |
| BUG-7 | Timezone `UTC` | Antara 00:00–07:00 WIB tanggal 1, "bulan berjalan" masih bulan lalu | `Asia/Jakarta` |
| BUG-8 | Screenshot disimpan di disk publik | Bukti bayar bisa diakses tanpa login | Simpan di `writable/uploads`, sajikan via controller ber-auth |

---

## 3. Tujuan & Non-Tujuan

### 3.1 Tujuan
- G-1: Paritas fungsional 100% untuk modul Fase 1 (dan Fase 2 saat dikerjakan).
- G-2: Paritas visual. Screenshot tiap halaman di lebar 1440px dan 390px sama dengan versi Laravel (toleransi: perbedaan anti-aliasing font).
- G-3: Nol dependensi Node/npm. Proyek bisa dijalankan dengan `composer install` + `php spark serve`.
- G-4: Interaksi yang sekarang tanpa reload tetap tanpa reload, dengan latensi setara atau lebih baik.
- G-5: Data produksi lama bisa diimpor tanpa transformasi (skema identik).

### 3.2 Non-Tujuan
- Redesain UI atau menambah fitur baru (kecuali perbaikan bug di §2.5).
- Mengimplementasikan modul Keuangan & Kendaraan (tetap placeholder).
- API publik/mobile.
- Dark mode (tidak dipakai di layout aktif).

---

## 4. Arsitektur Target (To-Be)

### 4.1 Stack

| Komponen | Pilihan | Alasan |
|---|---|---|
| Framework | **CodeIgniter 4**, rilis stabil terbaru (`composer create-project codeigniter4/appstarter`) | Permintaan user; PHP 8.3 lokal kompatibel |
| CSS | **Tailwind v4 Browser CDN**: `<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>` | Versi mayor sama dengan sumber (v4), sehingga semua kelas (`bg-white/[.07]`, `rounded-[1.35rem]`, `shadow-blue-600/20`, dll.) dirender identik. **Jangan** pakai CDN v3 (`cdn.tailwindcss.com`) karena sintaks & default berbeda. |
| CSS kustom | Isi `resources/css/app.css` (tanpa `@import`/`@source`) ditaruh di `<style type="text/tailwindcss">` | Urutan cascade (`@layer base/utilities` vs CSS tanpa layer) tetap sama seperti hasil build Vite |
| Interaksi server | **htmx 2** (CDN) | Pola yang sama dengan Livewire (HTML-over-the-wire): server merender partial PHP yang sama untuk render awal & update, sehingga tidak ada duplikasi template di JS dan UI dijamin identik. Pada dasarnya AJAX, tetapi deklaratif. |
| Interaksi klien | **Alpine.js 3** (CDN) | Sudah dipakai di layout (sidebar, dropdown, show password, input nominal). Dipakai juga untuk modal, preview upload, dan kalkulasi line-item invoice yang sepenuhnya di klien (lebih cepat dari Livewire yang round-trip) |
| Auth | Kustom (session + filter), tabel `users` yang sama | CodeIgniter Shield memakai tabel `auth_identities` dan menuntut migrasi hash password. Kustom menjaga skema & hash bcrypt Laravel (`$2y$`) tetap valid dengan `password_verify()` |
| DB | MySQL, skema identik dengan Laravel | Impor data langsung |
| Session | `FileHandler` (default CI4) | Sederhana; tabel `sessions` Laravel tidak dipakai |
| Upload | `writable/uploads/bill-payments/` + route `GET /house/payments/{id}/screenshot` (auth + cek pemilik) | Perbaikan BUG-8; path relatif di DB (`bill-payments/x.jpg`) tetap sama |
| Grafik | Renderer native dari `resources/js/app.js` | Di produksi, `window.Chart` tidak pernah dimuat sehingga yang tampil selalu renderer native. Dipertahankan agar identik. |

**Alternatif yang ditolak:**
- *AJAX manual + render JSON di JS*: harus menulis ulang markup di JS, rawan beda tampilan.
- *Full reload (form GET biasa)*: melanggar G-4.
- *Tailwind CLI standalone*: tetap ada build step; user meminta CDN.

### 4.2 Pola Interaksi (pengganti Livewire)

| Livewire | Pengganti di CI4 |
|---|---|
| `wire:model.live.debounce.300ms="search"` | `<form id="filters" hx-get hx-trigger="input changed delay:300ms from:[name=search], change from:select" hx-target="#list" hx-push-url="true">` |
| `->links()` paginasi | Partial `partials/pagination.php` (salinan markup `livewire/.../tailwind.blade.php`, data dari `Pager::getDetails()`), link diberi `hx-get` + `hx-target="#list"` |
| `wire:click="toggleActive(id)"` | `hx-post="/…/{id}/toggle" hx-include="#filters" hx-target="#list"` → server mengembalikan partial list |
| Modal hapus (`$showDeleteModal`) | Modal Alpine (markup sama), tombol konfirmasi berupa `<form hx-post="/…/delete">` dengan input hidden `id`; respons partial list + header `HX-Trigger: close-modal` |
| `wire:submit="save"` + `@error` | `<form hx-post hx-target="this" hx-swap="outerHTML">`. Jika gagal validasi, server mengembalikan partial form berisi error. Jika sukses, header `HX-Redirect` ke index + flash session |
| `wire:loading` / `wire:loading.attr="disabled"` | `hx-disabled-elt="this"` + kelas `.htmx-indicator` / `.hide-on-request` |
| `wire:model="screenshot"` + `temporaryUrl()` | `hx-encoding="multipart/form-data"`, preview lokal via `URL.createObjectURL` (Alpine), progres dari event `htmx:xhr:progress` |
| `prevMonth` / `nextMonth`, filter kategori | `hx-get="/house?month=&year=&category="` → partial konten dashboard, lalu `renderMonthlyChart()` dipanggil pada `htmx:afterSettle` |
| Line items invoice (`wire:model.live`) | Komponen Alpine `documentForm()`: add/remove item, isi dari produk (JSON), total dihitung live di klien. Server menghitung ulang saat simpan (sumber kebenaran) |
| `wire:navigate` | Link biasa (Fase 1). Opsional `hx-boost` di Fase 4 |

Semua endpoint tetap berfungsi tanpa JavaScript (progressive enhancement): jika header `HX-Request` tidak ada, controller merender halaman penuh atau melakukan redirect.

### 4.3 Struktur Direktori Target

```
finance-app/
├── CLAUDE.md
├── docs/ (PRD.md, TASKS.md)
├── app/
│   ├── Config/        Routes.php, Filters.php, App.php, Security.php, Pager.php, Validation.php
│   ├── Controllers/   BaseController.php, AuthController.php, DashboardController.php,
│   │                  SettingsController.php, PlaceholderController.php,
│   │                  House/{DashboardController, BillController, PaymentController}.php,
│   │                  Debt/{ContactController, DebtController, ReceivableController}.php,
│   │                  Business/{DashboardController, ClientController, ProductController,
│   │                            QuotationController, InvoiceController, ReceiptController}.php
│   ├── Models/        *Model.php (1 per tabel)
│   ├── Entities/      User, UtilityBill, BillPayment, Contact, Debt, Receivable, ... (getter enum)
│   ├── Enums/         Salinan app/Enums Laravel (PHP 8.1 native enum)
│   ├── Services/      DebtLedgerService, BillPaymentService, ImageCompressor,
│   │                  DocumentNumberService, DocumentCalculator, AuthService
│   ├── Filters/       AuthFilter.php, GuestFilter.php
│   ├── Helpers/       app_helper.php (rupiah, month_name, is_htmx, initials, field_error, ...)
│   ├── Language/id/   Validation.php (pesan default berbahasa Indonesia)
│   ├── Database/      Migrations/ (identik skema Laravel), Seeds/
│   └── Views/
│       ├── layouts/   app.php, auth.php
│       ├── partials/  head_assets.php, tailwind_css.php, sidebar.php, flash.php,
│       │              delete_modal.php, form_error.php, pagination.php
│       ├── dashboard/ index.php
│       ├── house/     dashboard/{index,_content}.php, bills/{index,_list,form,_form}.php,
│       │              payments/{index,_list,form,_form,show}.php
│       ├── debt/      contacts/…, debts/{index,_list,form,_form,show,_detail}.php, receivables/…
│       ├── business/  …
│       ├── settings/  profile.php, password.php
│       └── placeholder.php
├── public/
│   ├── index.php, .htaccess, manifest.json, sw.js, favicon.ico, robots.txt
│   ├── img/ (logo.jpg, icons/*)
│   └── js/app.js        (port resources/js/app.js: grafik, modal, PWA, hook htmx)
└── writable/uploads/bill-payments/
```

### 4.4 Routing (URL dipertahankan)

URL sama persis dengan Laravel; nama route memakai opsi `as` CI4 dengan nama yang sama (`house.bills.index`, dll.) sehingga `url_to('house.bills.index')` setara dengan `route()`.

Route mutasi baru (pengganti aksi Livewire), semuanya `POST` dan dilindungi CSRF:

```
POST  login | logout | register
POST  house/bills                    (store)      POST house/bills/(:num)          (update)
POST  house/bills/(:num)/toggle      POST house/bills/delete
POST  house/payments                 POST house/payments/(:num)    POST house/payments/delete
GET   house/payments/(:num)/screenshot
POST  debt/contacts | debt/contacts/(:num) | debt/contacts/delete
POST  debt/debts | debt/debts/(:num) | debt/debts/delete
POST  debt/debts/(:num)/payments     POST debt/debts/(:num)/payments/delete
      (sama untuk debt/receivables)
POST  settings/profile | settings/password | settings/delete-account
      (Fase 2: business/clients|products|quotes|invoices|receipts, sama polanya + toggle)
```

Redirect legacy dipertahankan: `/` → `/dashboard`, `/debts` → `/debt/debts`, `/receivables`, `/contacts`, `/profile`, `/settings` → `/settings/profile`.

---

## 5. Persyaratan Fungsional

> Rincian perilaku per halaman ada di §2.3–2.4. Bagian ini menetapkan persyaratan yang bisa diuji.

### 5.1 Umum
- FR-G1: Semua halaman selain `/login` (dan `/register` jika aktif) wajib login. Jika belum login, redirect ke `/login`, lalu kembali ke URL tujuan (intended) setelah login.
- FR-G2: Semua query data domain difilter `user_id` sesi. Akses ID milik user lain → 404.
- FR-G3: Flash message sukses tampil dengan markup yang sama (`✓ {pesan}`, hijau) dan teks pesan yang sama dengan Laravel.
- FR-G4: Format angka `Rp 1.234.567` (`number_format($n, 0, ',', '.')`). Format tanggal mengikuti Blade sumber (mis. `d M Y` dengan nama bulan Inggris seperti di Carbon `format()`; nama bulan Indonesia hanya di tempat yang memakai array `$MONTHS`).
- FR-G5: Semua pesan validasi berbahasa Indonesia, dengan teks sama seperti `$messages` Livewire.
- FR-G6: Paginasi 15 baris per halaman. Search/filter mereset ke halaman 1. State (search, filter, page) tercermin di URL.

### 5.2 Auth & Settings
- FR-A1: Login dengan validasi (email wajib & valid, password wajib) dan pesan gagal "Email atau password salah."
- FR-A2: Rate-limit 5 percobaan gagal per `lower(email)|ip`. Lewat batas → pesan dengan sisa detik.
- FR-A3: "Ingat saya": cookie token persisten (hash token disimpan di `users.remember_token`).
- FR-A4: Logout (POST) → invalidasi sesi → redirect `/`.
- FR-A5: Profil: ubah nama & email (unique, lowercase). Jika email berubah, `email_verified_at = null`.
- FR-A6: Password: password lama harus cocok, password baru minimal 8 karakter + konfirmasi. Jika gagal, ketiga field dikosongkan.
- FR-A7: Hapus akun dengan konfirmasi password, lalu logout dan redirect `/`.

### 5.3 Dashboard Utama
- FR-D1: Kartu & panel sama dengan `livewire/dashboard.blade.php`: tagihan lunas/total, periode aktif, dibayar bulan ini, jumlah belum dibayar, total utang & piutang (status unpaid+partial), 3 tagihan belum bayar + "+N lainnya", persentase progres.

### 5.4 Operasional Rumah
- FR-H1: Dashboard tagihan dengan navigasi bulan tanpa reload. Tombol "next" dinonaktifkan di bulan berjalan dan server juga menolak bulan di masa depan.
- FR-H2: Grafik 12 bulan + filter kategori tanpa reload. Klik batang membuka modal rincian per kategori. Di mobile (≤640px) grafik bisa di-scroll horizontal (min-width 850px) dan otomatis di-scroll ke kanan.
- FR-H3: Peringatan jatuh tempo ≤3 hari (hanya bulan berjalan).
- FR-H4: List "Belum Dibayar" (tombol "✓ Bayar" → form dengan prefill tagihan) dan "Sudah Dibayar" (tanggal, nominal, tombol "📷 Bukti" jika ada screenshot).
- FR-H5: Master tagihan: CRUD, search nama tanpa reload, toggle aktif tanpa reload, hapus via modal (pembayaran ikut terhapus oleh cascade, file screenshot ikut dihapus). Mobile memakai card, desktop memakai tabel.
- FR-H6: Riwayat pembayaran: filter tagihan/bulan/tahun (default bulan & tahun berjalan; `?bill_id=` mem-prefill) tanpa reload, hapus via modal (file ikut dihapus).
- FR-H7: Form pembayaran: daftar tagihan aktif, bulan (nama Indonesia), tahun (tahun ini sampai 3 tahun lalu), tanggal, nominal berformat titik ribuan (nilai yang dikirim berupa digit), metode, referensi, screenshot + preview + teks "⏳ Mengupload...", catatan. Aturan unik periode & kompresi sesuai §2.4.
- FR-H8: Detail pembayaran: data lengkap + gambar bukti dari route ber-auth.

### 5.5 Utang & Piutang
- FR-U1: Kontak: CRUD, search (nama/telepon/email) tanpa reload, jumlah utang & piutang per kontak, hapus via modal. Jika kontak masih punya utang/piutang, tolak dengan pesan (BUG-4).
- FR-U2: Utang/Piutang: CRUD, search, filter status, total belum lunas, hapus (cicilan ikut terhapus) via modal.
- FR-U3: Detail: ringkasan (jumlah, sisa, status, progres), form cicilan toggle tanpa reload, list cicilan, hapus cicilan via modal. Semua angka di halaman ter-update tanpa reload.
- FR-U4: Seluruh perubahan saldo/status dijalankan di dalam transaksi DB (`DebtLedgerService`).

### 5.6 Manajemen Bisnis (Fase 2)
- FR-B1: Dashboard bisnis: klien aktif, invoice bulan ini, pendapatan bulan ini (paid+partial), outstanding, overdue, 5 invoice terdekat jatuh tempo.
- FR-B2: Klien & Produk: CRUD, search, toggle aktif, hapus via modal (klien dengan dokumen: tolak dengan pesan).
- FR-B3: Penawaran & Invoice: list (search nomor/nama klien, filter status, ringkasan untuk invoice), form dengan line items dinamis Alpine, pilih produk mengisi nama/satuan/harga, total live, simpan dalam transaksi (header + items). Edit mengganti seluruh item.
- FR-B4: Konversi penawaran ke invoice via `GET /business/invoices/create?from_quotation={id}`.
- FR-B5: Kwitansi: list + search + hapus.
- FR-B6: Menu bisnis tetap **tidak tampil** di sidebar (sama dengan versi sekarang), tetapi halamannya bisa diakses via URL. Visibilitas diatur lewat flag konfigurasi `Config\App::$showBusinessMenu = false`.

### 5.7 Placeholder (Fase 3)
- FR-P1: `/accounts`, `/transactions`, `/categories`, `/budgets`, `/vehicles`, `/services`, `/fuel` merender halaman "🚧 Sedang Dalam Pengembangan" dengan gaya Tailwind biasa (komponen `flux:` diganti markup setara).

### 5.8 PWA
- FR-W1: `manifest.json`, ikon, meta tag, registrasi `sw.js`, dan banner "Install MyFinance" tetap ada.
- FR-W2: `sw.js` diperbarui: hapus aturan `/livewire-` & `/build/`, **lewati request dengan header `HX-Request`** (partial tidak boleh di-cache sebagai halaman), lewati `/house/payments/*/screenshot`.

---

## 6. Persyaratan Non-Fungsional

| ID | Persyaratan |
|---|---|
| NFR-1 | PHP ≥ 8.2 (lokal 8.3), ekstensi intl, mbstring, gd, mysqlnd, fileinfo (sudah tersedia) |
| NFR-2 | Tanpa `package.json`, `node_modules`, atau build step. Semua library frontend dari CDN dengan versi mayor terkunci (`@tailwindcss/browser@4`, `alpinejs@3`, `htmx.org@2`) |
| NFR-3 | Keamanan: CSRF aktif untuk semua POST (token dikirim htmx via header `X-CSRF-TOKEN`), output di-escape `esc()`, query builder ter-binding, cek kepemilikan di setiap aksi, `session()->regenerate()` setelah login |
| NFR-4 | Respons partial htmx < 300ms di lokal untuk dataset ±1.000 baris per tabel |
| NFR-5 | Mencegah FOUC: script Tailwind CDN di `<head>`, `[x-cloak]{display:none!important}`, dan `.htmx-indicator` didefinisikan |
| NFR-6 | Timezone `Asia/Jakarta`, locale `id` |
| NFR-7 | Tes fitur (PHPUnit CI4) untuk: auth, isolasi user, logika utang/piutang, keunikan periode tagihan, kalkulasi dokumen, kompresi gambar |
| NFR-8 | Header `Vary: HX-Request` pada respons yang bisa berupa partial atau halaman penuh |

---

## 7. Model Data

Skema **identik** dengan migrasi Laravel (`database/migrations/*`). Ringkasan tabel yang dipakai:

| Tabel | Kolom kunci | Relasi / Constraint |
|---|---|---|
| users | name, email (unique), email_verified_at, password, remember_token | — |
| utility_bills | user_id, name, category (enum 8), provider_name, customer_number, estimated_amount, due_day, is_active, notes | user cascade |
| bill_payments | user_id, utility_bill_id, period_month, period_year, payment_date, amount_paid, payment_method (enum 6), reference_number, screenshot_path, notes, transaction_id | unique(bill, month, year); bill cascade |
| contacts | user_id, name, phone, email, address, notes | — |
| debts / receivables | user_id, contact_id, amount, remaining_amount, interest_rate (nullable), borrowed_date / lent_date, due_date, status (unpaid/partial/paid), purpose, notes | contact RESTRICT |
| debt_payments / receivable_payments | debt_id / receivable_id, transaction_id, amount, payment_date, notes | parent cascade |
| business_settings | user_id (unique), profil usaha, prefix & counter QUO/INV/RCP | — |
| clients, products | user_id, code, name, …, is_active | — |
| quotations (+items), invoices (+items, payments), receipts | lihat migrasi sumber | client RESTRICT, items cascade |
| accounts, categories, transactions, transaction_attachments, budgets, recurring_transactions, vehicles, service_schedules, service_histories, service_attachments, odometer_logs, fuel_logs | dibuat untuk kompatibilitas (Fase 3) | FK sesuai sumber |

Tabel khusus Laravel yang **tidak** dimigrasi: `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations`.

**Enum** disalin dari `app/Enums` (label, ikon, warna, `badgeClass`) karena kompatibel dengan PHP 8.1+. Entity CI4 menyediakan getter yang mengembalikan enum (mis. `$bill->category->icon()`), sehingga kode view hampir sama dengan Blade. Model tetap menyimpan nilai string mentah.

**Seeder:** UserSeeder (demo@myfinance.app / admin@myfinance.app, password `password`), CategorySeeder, UtilityBillSeeder (porting dari sumber).

---

## 8. Strategi Migrasi Data

1. Jalankan migrasi CI4 ke database baru (mis. `finance_app`).
2. `mysqldump --no-create-info --complete-insert myfinance users utility_bills bill_payments contacts debts debt_payments receivables receivable_payments business_settings clients products quotations quotation_items invoices invoice_items invoice_payments receipts categories > data.sql`, lalu impor.
3. Salin `C:\Laravel-herd\myfinance\storage\app\public\bill-payments\*` ke `writable/uploads/bill-payments/`. Nilai `screenshot_path` tidak perlu diubah.
4. Password bcrypt Laravel langsung valid (`password_verify`).
5. Catatan timezone: `created_at`/`updated_at` lama tersimpan dalam UTC (tidak ditampilkan di UI). Kolom `date` tidak terdampak.

---

## 9. Kriteria Penerimaan (Definition of Done)

- [ ] Seluruh route Fase 1 berfungsi; checklist paritas visual (desktop 1440px & mobile 390px) lolos untuk setiap halaman.
- [ ] Search, filter, paginasi, toggle, hapus, navigasi bulan, filter grafik, simpan & hapus cicilan, dan validasi form bekerja tanpa full reload (diverifikasi di DevTools Network: request ber-header `HX-Request`).
- [ ] Bug BUG-1 s/d BUG-8 tertutup dan memiliki tes.
- [ ] Tidak ada file `package.json` / `node_modules` / `vite.config.js`.
- [ ] `php spark test` hijau.
- [ ] Data dump dari Laravel berhasil diimpor dan tampil benar.

---

## 10. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| Tailwind Browser CDN mengompilasi di runtime → kedip (FOUC) & beban CPU | Script di `<head>` sebelum CSS lain; `[x-cloak]`; dokumentasikan bahwa CDN tidak direkomendasikan Tailwind untuk produksi (keputusan sadar sesuai permintaan) |
| Kelas Tailwind dinamis dari enum (`badgeClass()`) | Browser CDN memindai DOM dan memantau perubahan, sehingga konten hasil swap htmx ikut ter-style. Tetap verifikasi manual |
| Token CSRF berotasi → request htmx kedua gagal | `Config\Security::$regenerate = false` |
| Cascade layer berubah jika CSS kustom diletakkan di file `.css` biasa | Wajib di `<style type="text/tailwindcss">` (lihat CLAUDE.md) |
| Service worker men-cache partial htmx | Lewati `HX-Request` di `sw.js` + `Vary: HX-Request` |
| Race condition nomor dokumen | Transaksi + `SELECT … FOR UPDATE` pada `business_settings` |
| Perbedaan output tanggal Carbon vs PHP | Pakai `date()` / `Time::format()` dengan pola yang sama, bukan `toLocalizedString()` |

---

## 11. Pertanyaan Terbuka (default dipakai jika tidak dijawab)

| ID | Pertanyaan | Default |
|---|---|---|
| Q-1 | Register diaktifkan? (view sumber kosong, tidak ada link dari login) | **Nonaktif** (`Config\App::$allowRegistration = false`). Jika diaktifkan, buat form dengan gaya halaman login |
| Q-2 | Menu Bisnis ditampilkan di sidebar? | Tidak (sama seperti sekarang) |
| Q-3 | Nama database CI4 | `finance_app` |
| Q-4 | Aktifkan `hx-boost` untuk navigasi ala SPA (pengganti `wire:navigate`)? | Tidak di Fase 1; dievaluasi di Fase 4 |
