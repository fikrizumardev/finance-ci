# TASKS — Migrasi MyFinance ke CodeIgniter 4

> Acuan: `docs/PRD.md`. Kode sumber: `C:\Laravel-herd\myfinance` (disingkat **SRC**).
> Tandai `[x]` jika selesai. Setiap task menyebut file SRC yang harus dibaca sebelum dikerjakan.
> Urutan fase wajib diikuti; task di dalam satu fase boleh paralel jika tidak saling bergantung.

Legenda: **P0** = wajib Fase 1 · **P1** = Fase 2 · **P2** = Fase 3/opsional

---

## Fase 0 — Setup Proyek

- [x] **T0.1 Install CodeIgniter 4** (P0)
  - Folder ini tidak kosong (ada `CLAUDE.md`, `docs/`), sehingga `create-project .` akan gagal. Jalankan:
    ```bash
    cd D:/project-app
    composer create-project codeigniter4/appstarter finance-app-tmp
    # pindahkan seluruh isi (termasuk file tersembunyi) ke finance-app, lalu hapus folder tmp
    ```
  - Salin `env` ke `.env`. Set `CI_ENVIRONMENT = development`, `app.baseURL = 'http://localhost:8080/'`, `database.default.*` (MySQL, db `finance_app`).
  - ✅ `php spark serve` menampilkan halaman welcome CI4.
- [x] **T0.2 Inisialisasi git** + `.gitignore` (bawaan CI4 + `writable/uploads/*`, `.env`). (P0)
- [x] **T0.3 Konfigurasi dasar** (P0)
  - `app/Config/App.php`: `$appTimezone = 'Asia/Jakarta'`, `$defaultLocale = 'id'`, `$supportedLocales = ['id']`, `$indexPage = ''`, tambahkan properti `public bool $allowRegistration = false; public bool $showBusinessMenu = false;`.
  - `app/Config/Security.php`: `$csrfProtection = 'session'`, `$headerName = 'X-CSRF-TOKEN'`, `$regenerate = false`, `$redirect = false` (agar request htmx yang gagal CSRF mengembalikan 403, bukan redirect).
  - `app/Config/Filters.php`: aktifkan `csrf` global (kecuali GET), daftarkan alias `auth` & `guest`.
  - `app/Config/Autoload.php`: helper `['url', 'form', 'app']`.
  - ✅ Konfigurasi termuat tanpa error.

## Fase 1 — Fondasi

### 1A. Database
- [x] **T1.1 Migrasi skema** (P0). SRC: `database/migrations/*.php`
  - Buat migrasi CI4 dengan nama, tipe, panjang, default, nullable, index, unique, dan FK (`CASCADE` / `RESTRICT` / `SET NULL`) **identik**. `enum` → `ENUM` MySQL dengan nilai yang sama. `decimal(15,2)`, `softDeletes` → kolom `deleted_at`.
  - Gabungkan 2 migrasi "fix nullable interest_rate" langsung ke definisi tabel (`interest_rate` nullable default NULL).
  - Urutan migrasi mengikuti dependensi FK. Tabel Laravel-only (sessions, cache, jobs, …) dilewati.
  - ✅ `php spark migrate` sukses; `SHOW CREATE TABLE` setiap tabel cocok dengan DB Laravel (bandingkan via `mysqldump --no-data`).
- [x] **T1.2 Seeder** (P0). SRC: `database/seeders/*`
  - `UserSeeder` (2 user, password `password`, `password_hash(PASSWORD_BCRYPT)`), `CategorySeeder`, `UtilityBillSeeder`, `DatabaseSeeder` (memanggil ketiganya).
  - ✅ `php spark db:seed DatabaseSeeder` sukses dan bisa login dengan demo@myfinance.app.

### 1B. Domain Layer
- [x] **T1.3 Enums** (P0). SRC: `app/Enums/*.php`. Salin ke `app/Enums` dengan namespace `App\Enums`, tanpa perubahan logika.
- [x] **T1.4 Entities** (P0). SRC: `app/Models/*.php`
  - Entity untuk User, UtilityBill, BillPayment, Contact, Debt, DebtPayment, Receivable, ReceivablePayment ✔ (entity bisnis dibuat di Fase 5: Client, Product, Quotation, QuotationItem, Invoice, InvoiceItem, InvoicePayment, Receipt, BusinessSetting).
  - Getter enum: `getCategory()`, `getPaymentMethod()`, `getStatus()` mengembalikan enum (`BillCategory::from(...)`), dan getter tanggal mengembalikan `CodeIgniter\I18n\Time`.
  - Port method: `User::initials()`, `UtilityBill::isDueSoon(int $days = 3)`, `BillPayment::getPeriodLabel()` / `hasScreenshot()`, `Invoice::isOverdue()`.
  - ✅ Unit test: `isDueSoon` (termasuk kasus akhir bulan), `initials`, `getPeriodLabel`.
- [x] **T1.5 Models** (P0). Satu model per tabel: `$returnType` entity, `$allowedFields`, `$useTimestamps = true`, `$useSoftDeletes` untuk accounts/transactions. Tambahkan method scope, mis. `forUser(int $id)`, `active()`.
- [x] **T1.6 Helper `app_helper.php`** (P0)
  - `rupiah($n)` → `'Rp ' . number_format((float)$n, 0, ',', '.')`
  - `month_name(int $m)` → array bulan Indonesia (Januari…Desember)
  - `is_htmx()`, `current_user()`, `user_id()`
  - `field_error(string $field)` → `<p class="mt-1 text-xs text-red-600">…</p>` (pengganti `@error`)
  - `has_error(string $field)`, `old_or(string $field, $default)`
  - ✅ Unit test helper.
- [x] **T1.7 Bahasa validasi** `app/Language/id/Validation.php`: pesan default berbahasa Indonesia. (P0)

### 1C. Layout & Aset (paritas visual)
- [x] **T1.8 Partial `head_assets.php`** (P0). SRC: `resources/views/partials/head.blade.php`, `layouts/app.blade.php` (head)
  - Meta, favicon, PWA meta, font Inter (bunny.net).
  - `<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>`
  - `<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>`
  - `<script src="https://cdn.jsdelivr.net/npm/htmx.org@2/dist/htmx.min.js"></script>`
  - `<meta name="htmx-config" content='{"includeIndicatorStyles":false}'>`
  - Style dasar: `[x-cloak]{display:none!important}` + aturan `.htmx-indicator` / `.hide-on-request` (lihat CLAUDE.md).
- [x] **T1.9 Partial `tailwind_css.php`** (P0). SRC: `resources/css/app.css`
  - Isi `app.css` **tanpa** baris `@import 'tailwindcss'` dan `@source …`, dibungkus `<style type="text/tailwindcss">…</style>`.
  - ✅ `.mf-panel`, `.mf-eyebrow`, `.app-header`, `.app-main`, dan grafik tampil sama.
- [x] **T1.10 Layout `layouts/app.php`** (P0). SRC: `resources/views/layouts/app.blade.php`
  - Salin markup 1:1: wrapper Alpine `sidebarOpen`, overlay, sidebar, header (hamburger, logo mobile, eyebrow, lonceng, dropdown user), `<main class="app-main …">`.
  - `<body hx-headers='{"X-CSRF-TOKEN":"<?= csrf_hash() ?>"}'>`.
  - Ganti `@livewireScripts` dengan `<script src="/js/app.js"></script>`; skrip registrasi SW dipertahankan. Skrip "tutup sidebar saat livewire:navigated" dihapus.
  - Section: `$this->renderSection('content')`, `title`.
- [x] **T1.11 Partial `sidebar.php`** (P0). SRC: `resources/views/components/sidebar.blade.php`
  - Markup, `<style>`, dan skrip `scrollSidebarToActive` 1:1. Status aktif berdasarkan prefix URL (`url_is('house*')`, dll.) dengan logika sama seperti `request()->routeIs`.
  - Menu bisnis hanya dirender jika `config('App')->showBusinessMenu`.
- [x] **T1.12 Layout `layouts/auth.php`** (P0). SRC: `resources/views/components/layouts/auth.blade.php` (font Plus Jakarta Sans + `<style>` panjang, 1:1).
- [x] **T1.13 Partial umum** (P0): `flash.php` (markup flash dari `house/bills/index.blade.php`), `delete_modal.php` (modal Alpine, markup sama seperti modal Livewire, menerima judul/teks/URL/target), `pagination.php` (port markup `vendor/livewire/livewire/src/Features/SupportPagination/views/tailwind.blade.php`; URL dibangun dari `baseUrl` + filter aktif (bukan `getPageURI()`), algoritma UrlWindow Laravel; setiap link diberi `hx-get` + `hx-target` + `hx-push-url`; teks "Showing X to Y of Z results" sama seperti sumber).
- [x] **T1.14 `public/js/app.js`** (P0). SRC: `resources/js/app.js`
  - Hapus `import './bootstrap'` (axios tidak dipakai).
  - Grafik & modal detail: salin 1:1. Ganti listener `livewire:init` / `livewire:navigated` dengan `DOMContentLoaded` + `htmx:afterSettle`. MutationObserver `data-chart` boleh dipertahankan.
  - Banner PWA: salin 1:1.
  - Tambahkan listener global: `htmx:configRequest` (sisipkan CSRF), `htmx:responseError` (toast sederhana jika 403/500).
- [x] **T1.15 Salin aset publik** (P0): `public/img/**`, `favicon.ico`, `manifest.json`, `robots.txt`, `sw.js` (diperbarui di T6.3).

### 1D. Auth
- [x] **T1.16 AuthService + Filters** (P0)
  - `attempt(email, password, remember)`: `password_verify`, `session()->regenerate()`, simpan `user_id`.
  - Throttle: `service('throttler')->check(md5(strtolower($email).'|'.$ip), 5, MINUTE)`, lalu `getTokenTime()` untuk pesan detik. Reset saat sukses.
  - Remember-me: token acak, cookie `remember` (30 hari, httponly), hash disimpan di `users.remember_token`; `AuthFilter` melakukan auto-login dari cookie.
  - `AuthFilter`: belum login → simpan `redirect_url` (intended) → redirect `/login`. Untuk request htmx, kirim header `HX-Redirect: /login`.
  - `GuestFilter`: sudah login → `/dashboard`.
- [x] **T1.17 Halaman login** (P0). SRC: `resources/views/livewire/auth/login.blade.php`, `app/Livewire/Auth/Login.php`
  - View 1:1 (termasuk `<style>` login dan toggle password Alpine).
  - Form `hx-post="/login" hx-target="this" hx-swap="outerHTML"`. Gagal → partial form + error. Sukses → `HX-Redirect` ke intended/dashboard.
  - Loading "Memproses..." + spinner via `.htmx-indicator`.
  - ✅ Tes: login sukses, gagal, throttle ke-6, remember-me, redirect intended.
- [x] **T1.18 Logout** (P0): `POST /logout` → destroy session, hapus cookie remember → redirect `/`.
- [x] **T1.19 Register** (P2, default nonaktif): route aktif hanya jika `allowRegistration`.

## Fase 2 — Dashboard Utama & Operasional Rumah

- [ ] **T2.1 Dashboard utama** (P0). SRC: `app/Livewire/Dashboard.php`, `resources/views/livewire/dashboard.blade.php`
  - Query periode terakhir (≤ bulan berjalan), statistik tagihan, total utang/piutang.
  - View 1:1; hapus atribut `wire:navigate`.
  - ✅ Angka sama dengan versi Laravel pada data yang sama.
- [ ] **T2.2 House Dashboard** (P0). SRC: `app/Livewire/House/Dashboard.php`, `livewire/house/dashboard.blade.php`
  - `GET /house?month=&year=&category=`; default = periode terakhir. Clamp agar tidak melewati bulan berjalan.
  - `index.php` (shell) + `_content.php` (header periode, navigasi bulan, alert, grafik, kartu, dua list).
  - Tombol prev/next: `hx-get` ke URL bulan target, `hx-target="#house-dashboard"`, `hx-push-url="true"`. Tombol next `disabled` di bulan berjalan.
  - Select kategori: `hx-get` dengan `hx-include` month/year (hidden), `hx-trigger="change"`.
  - `data-chart` = `json_encode($chartData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_IGNORE)` di-escape `esc(..., 'attr')`.
  - Query `monthlyTransactionData()` diporting persis (12 bulan, details per kategori).
  - ✅ Ganti bulan/kategori tanpa reload; grafik ter-render ulang; modal detail berfungsi; scroll mobile berfungsi.
- [ ] **T2.3 Master Tagihan — list** (P0). SRC: `House/Bills/Index.php`, `house/bills/index.blade.php`
  - `index.php` + `_list.php` (empty state, card mobile, tabel desktop, pager).
  - Search `hx-get` debounce 300ms, reset page. Urutan: is_active desc, due_day, name.
  - Toggle: `POST house/bills/{id}/toggle` → partial list (filter & page disertakan via `hx-include`).
  - Hapus: modal → `POST house/bills/delete` → hapus file screenshot pembayaran terkait, lalu record → partial list + flash + `HX-Trigger: close-modal`.
  - Link "Riwayat" → `/house/payments?bill_id={id}`.
  - ✅ Tes: search tidak bocor antar user, toggle, hapus milik user lain → 404.
- [ ] **T2.4 Master Tagihan — form** (P0). SRC: `House/Bills/CreateEdit.php`, `house/bills/create-edit.blade.php`
  - `form.php` + `_form.php`, rules & pesan sama. Kolom kosong disimpan `null`. Edit cek pemilik (BUG-2).
  - ✅ Validasi inline tanpa reload, sukses → redirect + flash "Tagihan berhasil ditambahkan/diperbarui."
- [ ] **T2.5 Riwayat Pembayaran — list** (P0). SRC: `House/Payments/Index.php`, `house/payments/index.blade.php`
  - Filter `bill_id` (dari query, BUG-5), bulan & tahun (default sekarang), tahun = sekarang s/d −3. Urutan: payment_date desc, period_year desc, period_month desc.
  - Hapus via modal (hapus file juga).
- [ ] **T2.6 ImageCompressor service** (P0). SRC: method `compressAndStoreScreenshot()`
  - GD: `imagecreatefromstring`, maks 1600px, kanvas putih, `imagejpeg(…, 80)`, simpan `writable/uploads/bill-payments/{uuid}.jpg`, kembalikan `bill-payments/{uuid}.jpg`. Pesan error sama.
  - ✅ Unit test: PNG transparan → JPEG berlatar putih; 4000×3000 → 1600×1200; file non-gambar → error.
- [ ] **T2.7 Form Pembayaran** (P0). SRC: `House/Payments/CreateEdit.php`, `house/payments/create-edit.blade.php`
  - Prefill `?bill=` (BUG-5); default bulan/tahun/tanggal sekarang.
  - Input nominal Alpine (format titik ribuan) + `<input type="hidden" name="amount_paid">` berisi digit saja.
  - Upload: `hx-encoding="multipart/form-data"`, preview `URL.createObjectURL`, "⏳ Mengupload..." selama `htmx:xhr:progress`, preview lama saat edit dari route screenshot.
  - Validasi: rules sama (`is_image`, `mime_in`, `max_size[screenshot,5120]`) + cek unik periode → error di `period_month`.
  - Saat edit dengan file baru, hapus file lama.
  - ✅ Tes: duplikat periode ditolak, kompresi berjalan, edit milik user lain → 404.
- [ ] **T2.8 Detail Pembayaran + route screenshot** (P0). SRC: `House/Payments/Show.php`, `house/payments/show.blade.php`
  - `GET house/payments/{id}/screenshot`: cek pemilik, kirim file dengan `Content-Type: image/jpeg`, `Cache-Control: private`.

## Fase 3 — Utang & Piutang

- [ ] **T3.1 Kontak** (P0). SRC: `Debt/Contacts/*`, `livewire/debt/contacts/*`
  - List + search (nama/telepon/email dalam `groupStart`, BUG-1) + jumlah utang/piutang (subquery count) + hapus (tolak jika punya relasi, BUG-4).
  - Form create/edit. Sukses → redirect `debt/contacts` + flash.
- [ ] **T3.2 DebtLedgerService** (P0). SRC: `Models/Debt.php` (`updateStatus`), `DebtPayment.php` / `ReceivablePayment.php` (`booted`)
  - Generik untuk `debt` dan `receivable`: `create`, `update` (penyesuaian remaining), `addPayment`, `deletePayment`, `delete`, `recalcStatus`. Semua dalam `transStart/transComplete`.
  - ✅ Unit test seluruh skenario status (unpaid → partial → paid → hapus cicilan → partial).
- [ ] **T3.3 Utang — list & form** (P0). SRC: `Debt/Debts/Index.php`, `CreateEdit.php` + views
  - Search kontak/tujuan (join contacts, `groupStart`), filter status, urutan `CASE status WHEN 'unpaid' THEN 0 WHEN 'partial' THEN 1 ELSE 2 END, due_date`, total belum lunas.
  - Validasi: `due_date` ≥ `borrowed_date` (rule kustom `after_or_equal_field`).
- [ ] **T3.4 Utang — detail** (P0). SRC: `Debt/Debts/Show.php`, `debt/debts/show.blade.php`
  - `show.php` + `_detail.php` (ringkasan + form bayar + list cicilan) dalam `#debt-detail`.
  - Toggle form bayar dengan Alpine (`showPayForm`). Submit `hx-post="debt/debts/{id}/payments"` → `_detail` baru (form tertutup + flash) atau `_detail` dengan error (form tetap terbuka).
  - Hapus cicilan via modal → `_detail` baru.
  - ✅ Sisa, status, dan progres ter-update tanpa reload.
- [ ] **T3.5 Piutang** (P0): sama dengan T3.3–T3.4 (kolom `lent_date`, teks "piutang", "Tanggal terima"). Pakai ulang controller dasar/service; view disalin dari SRC `debt/receivables/*`.
- [ ] **T3.6 Redirect legacy** `/debts`, `/receivables`, `/contacts`. (P0)

## Fase 4 — Settings

- [ ] **T4.1 Profil** (P0). SRC: `Settings/Profile.php`, `settings/profile.blade.php`. Email unique (kecuali diri sendiri) & lowercase; reset `email_verified_at` jika email berubah; pesan sukses inline.
- [ ] **T4.2 Hapus akun** (P0). SRC: `Settings/DeleteUser.php`, `settings/delete-user.blade.php` (di-embed di profil). Konfirmasi Alpine + password.
- [ ] **T4.3 Password** (P0). SRC: `Settings/Password.php`, `settings/password.blade.php`. Min 8 karakter + konfirmasi; field direset saat gagal maupun sukses.
- [ ] **T4.4 Redirect** `/profile`, `/settings` → `/settings/profile`; `/settings/appearance` (port view sederhana). (P0)

## Fase 5 — Manajemen Bisnis (P1)

- [ ] **T5.1 Klien** — list (search nama/email/telepon, jumlah invoice/penawaran, toggle, hapus dengan cek relasi) + form. SRC: `Business/Clients/*`.
- [ ] **T5.2 Produk** — list (search nama/kode, toggle, hapus) + form (daftar satuan: pcs, unit, kg, gram, liter, meter, jam, hari, bulan, paket, set). SRC: `Business/Products/*`.
- [ ] **T5.3 DocumentCalculator + DocumentNumberService**
  - Kalkulasi item & dokumen sesuai PRD §2.4.8 (+ unit test angka dari contoh nyata).
  - Penomoran: transaksi, `SELECT … FOR UPDATE` pada `business_settings` (auto-create jika belum ada, BUG-3).
- [ ] **T5.4 Komponen Alpine `documentForm()`** di `public/js/app.js`: state `items`, `products` (JSON), `discount_type`, `discount_value`, `tax_rate`; method `addItem`, `removeItem`, `fillFromProduct`, getter `subtotal`/`discAmt`/`taxAmt`/`total`; input `:name="`items[${i}][name]`"`. Format angka ringkasan sama seperti Blade.
- [ ] **T5.5 Penawaran** — list (search nomor/klien, filter status) + form (valid_until +14 hari). Simpan header + item dalam transaksi; saat edit, hapus item lama lalu insert ulang. SRC: `Business/Quotations/*`.
- [ ] **T5.6 Invoice** — list (search, filter, ringkasan total/outstanding/paid) + form (due_date +30 hari, bank_info, `?from_quotation=`). `paid_amount` dipertahankan saat edit. SRC: `Business/Invoices/*`.
- [ ] **T5.7 Kwitansi** — list + search + hapus. SRC: `Business/Receipts/*`.
- [ ] **T5.8 Dashboard bisnis**. SRC: `Business/Dashboard.php`, `business/dashboard.blade.php`.
- [ ] **T5.9 Menu bisnis** di sidebar di balik flag `showBusinessMenu` (default false).

## Fase 6 — Placeholder, PWA, Polishing

- [ ] **T6.1 PlaceholderController + `placeholder.php`** (P2) untuk accounts, transactions, categories, budgets, vehicles, services, fuel. Markup Tailwind setara `_placeholder.blade.php` (tanpa `flux:`).
- [ ] **T6.2 Migrasi/model modul Keuangan & Kendaraan** (P2): migrasi sudah ada di T1.1; model/entity cukup kerangka.
- [ ] **T6.3 Update `sw.js`** (P0): lewati `HX-Request`, `/house/payments/*/screenshot`, dan non-GET; hapus aturan `/livewire-` & `/build/`; naikkan `CACHE_NAME` ke `myfinance-v2`.
- [ ] **T6.4 Header `Vary: HX-Request`** pada semua respons controller list/dashboard (via `after` filter). (P0)
- [ ] **T6.5 Halaman error** 404/403 dengan gaya aplikasi. (P2)
- [ ] **T6.6 Evaluasi `hx-boost`** untuk navigasi sidebar (pengganti `wire:navigate`): termasuk re-init grafik & state sidebar. (P2)

## Fase 7 — Migrasi Data & QA

- [ ] **T7.1 Skrip impor data** (P0): dokumentasikan perintah `mysqldump` (PRD §8) dan salin folder `bill-payments`. Uji di DB kosong.
- [ ] **T7.2 Feature tests** (P0): auth, filter auth, isolasi user di setiap list/search/aksi (BUG-1, BUG-2), CSRF ditolak tanpa token, hapus kontak dengan relasi (BUG-4), prefill query (BUG-5).
- [ ] **T7.3 Checklist paritas visual** (P0). Buka SRC (`herd`) dan CI4 berdampingan pada 1440px dan 390px untuk setiap halaman:
  - [ ] login (desktop & mobile)  - [ ] dashboard  - [ ] house dashboard (+modal grafik, alert jatuh tempo)
  - [ ] bills list (card & tabel, empty state, modal)  - [ ] bill form (+error)  - [ ] payments list
  - [ ] payment form (+preview, +error)  - [ ] payment detail  - [ ] contacts list/form
  - [ ] debts list/form/detail (+form bayar, modal)  - [ ] receivables list/form/detail
  - [ ] settings profile/password  - [ ] sidebar mobile (overlay, transisi), dropdown user  - [ ] paginasi
- [ ] **T7.4 Checklist tanpa reload** (P0): pastikan di DevTools → Network setiap aksi berikut adalah request XHR `HX-Request`, bukan navigasi dokumen: search, filter, paginasi, toggle, hapus, navigasi bulan, filter grafik, simpan cicilan, hapus cicilan, validasi gagal di semua form.
- [ ] **T7.5 Audit "tanpa npm"** (P0): tidak ada `package.json`, `node_modules`, `vite.config.js`, `resources/css`, `resources/js`.
- [ ] **T7.6 README** (P0): cara install, `.env`, migrate, seed, serve, impor data.
