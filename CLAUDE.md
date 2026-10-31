# CLAUDE.md — MyFinance (CodeIgniter 4)

Aplikasi keuangan pribadi (tagihan rumah, utang/piutang, bisnis kecil) hasil **migrasi 1:1** dari Laravel 12 + Livewire 3.
Dokumen wajib: `docs/PRD.md` (apa & kenapa), `docs/TASKS.md` (urutan kerja — centang task setelah selesai).

## Sumber Kebenaran

- **Kode Laravel asli: `C:\Laravel-herd\myfinance`** (read-only, jangan diubah). Sebelum mengerjakan halaman/fitur, **baca file sumbernya**:
  - Logika: `app/Livewire/**`, `app/Models/**`, `app/Enums/**`, `database/migrations/**`
  - Tampilan: `resources/views/livewire/**`, `resources/views/layouts/app.blade.php`, `resources/views/components/sidebar.blade.php`, `resources/views/components/layouts/auth.blade.php`
  - CSS/JS: `resources/css/app.css`, `resources/js/app.js`
  - Pagination: `vendor/livewire/livewire/src/Features/SupportPagination/views/tailwind.blade.php`
- Teks UI, pesan validasi, pesan flash, urutan data, dan default form harus **sama persis** dengan sumber. UI berbahasa Indonesia.

## Aturan Mutlak

1. **UI identik.** Salin markup HTML dan kelas Tailwind dari Blade apa adanya. Jangan redesain, jangan merapikan kelas, jangan mengganti ikon/emoji. Yang boleh diubah hanya sintaks template (Blade → PHP) dan atribut `wire:*` → htmx/Alpine.
2. **Tanpa npm/Node.** Jangan membuat `package.json`, `vite.config.js`, atau build step apa pun. Library frontend hanya lewat CDN:
   ```html
   <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
   <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/htmx.org@2/dist/htmx.min.js"></script>
   ```
   Tailwind **harus v4** (sumber memakai v4). Jangan pakai `cdn.tailwindcss.com` (v3).
3. **Interaksi tanpa reload** untuk semua yang dulu memakai Livewire: htmx untuk round-trip ke server (partial HTML), Alpine untuk state murni di klien. Lihat "Pola htmx".
4. **Isolasi user.** Setiap query domain wajib difilter `user_id = user_id()`. Search dengan banyak kolom **wajib** dibungkus `groupStart()/groupEnd()`. ID milik user lain → `PageNotFoundException`.
5. **Skema DB identik dengan Laravel.** Jangan rename kolom/tabel. Data lama harus bisa diimpor langsung.
6. Composer boleh dipakai, tetapi jangan menambah paket tanpa alasan kuat (stack inti cukup CI4).

## Perintah

```bash
php spark serve                      # http://localhost:8080
php spark migrate                    # jalankan migrasi
php spark migrate:refresh            # reset + migrate ulang
php spark db:seed DatabaseSeeder     # user demo: demo@myfinance.app / password
php spark routes                     # cek route & nama
vendor/bin/phpunit                   # semua tes (DB grup "tests" → MySQL finance_app_test)
vendor/bin/phpunit --filter DebtLedgerServiceTest
```

## Struktur

```
app/Controllers/{House,Debt,Business}/…   1 controller per resource (index, create, store, edit, update, delete, + aksi)
app/Models/*Model.php                     returnType = Entity, allowedFields lengkap, useTimestamps
app/Entities/*.php                        getter enum/tanggal + method domain (isDueSoon, initials, …)
app/Enums/*.php                           salinan app/Enums Laravel (jangan diubah)
app/Services/*.php                        logika multi-tabel (DebtLedgerService, ImageCompressor, DocumentNumberService, DocumentCalculator, AuthService)
app/Helpers/app_helper.php                rupiah(), month_name(), is_htmx(), user_id(), field_error(), …
app/Views/layouts/{app,auth}.php
app/Views/partials/                       head_assets, tailwind_css, sidebar, flash, delete_modal, pagination
app/Views/<modul>/<resource>/             index.php (halaman) + _list.php (partial htmx) + form.php + _form.php (+ show.php/_detail.php)
public/js/app.js                          grafik native, modal detail grafik, PWA banner, documentForm(), hook htmx
writable/uploads/bill-payments/           screenshot (disajikan lewat controller, bukan publik)
```

Route halaman yang belum dimigrasi sementara menunjuk `PlaceholderController::index` (lihat `app/Config/Routes.php`); ganti handler-nya saat fase terkait dikerjakan.

Konvensi: controller `PascalCase` + suffix `Controller`; view `snake_case`; partial diawali `_`. Nama route disamakan dengan Laravel (`'as' => 'house.bills.index'`) dan dipakai lewat `url_to('house.bills.index')`.

## Blade → View CI4

| Blade | CI4 |
|---|---|
| `{{ $x }}` | `<?= esc($x) ?>` |
| `{!! $html !!}` | `<?= $html ?>` (hanya HTML tepercaya) |
| `@if / @foreach / @php` | `<?php if (…): ?> … <?php endif ?>` / `foreach … endforeach` |
| `route('a.b', $id)` | `url_to('a.b', $id)` |
| `route('x', ['bill' => 1])` (query) | `url_to('x') . '?bill=1'` |
| `@csrf` | `<?= csrf_field() ?>` |
| `@error('f') … @enderror` | `<?= field_error('f') ?>` (markup: `<p class="mt-1 text-xs text-red-600">`) |
| `$errors->has('f') ? 'border-red-400' : 'border-gray-300'` | `has_error('f') ? … : …` |
| `session('success')` | `session('success')` (diset via `session()->setFlashdata()`) |
| `number_format($n,0,',','.')` | `rupiah($n)` (sudah termasuk prefix `Rp `) atau `number_format` langsung jika sumber tidak memakai "Rp" |
| `$date->format('d M Y')` | `$date->format('d M Y')` (getter Entity mengembalikan `Time`). **Jangan** pakai `toLocalizedString()` karena output akan berbeda |
| `@class([...])`, `@disabled($x)` | ekspresi PHP biasa |
| `auth()->user()` | `current_user()` |
| `wire:navigate` | hapus (link biasa) |
| `<x-layouts.app>` / `#[Layout]` | `<?= $this->extend('layouts/app') ?><?= $this->section('content') ?>…<?= $this->endSection() ?>` |

Form view menerima `$form` (array nilai: dari entity saat edit, dari POST saat validasi gagal, default saat create) dan `$errors` (array). Jangan mengandalkan `old()` untuk alur htmx.

## Tailwind CDN & CSS Kustom

- Isi `resources/css/app.css` sumber (**tanpa** `@import 'tailwindcss'` dan `@source`) ditaruh di `app/Views/partials/tailwind_css.php` sebagai `<style type="text/tailwindcss">…</style>`. Ini menjaga urutan cascade `@layer base/utilities` vs CSS tanpa layer persis seperti hasil build Vite. **Jangan** dipindah ke file `.css` biasa.
- `<style>` biasa milik sidebar dan layout auth/login disalin apa adanya (bukan `text/tailwindcss`).
- Wajib ada di `head_assets.php` (Livewire dulu menyuntikkan sebagian ini otomatis):
  ```css
  [x-cloak]{display:none!important}
  .htmx-indicator{display:none}
  .htmx-request .htmx-indicator,.htmx-request.htmx-indicator{display:inline-flex}
  .htmx-request .hide-on-request,.htmx-request.hide-on-request{display:none}
  ```
  serta `<meta name="htmx-config" content='{"includeIndicatorStyles":false}'>`.
- Kelas dari enum (`badgeClass()`, dll.) aman karena Tailwind browser memindai DOM, termasuk konten hasil swap htmx.

## Pola htmx (pengganti Livewire)

**Deteksi & respons** (di `BaseController`):
```php
protected function isHtmx(): bool { return $this->request->getHeaderLine('HX-Request') === 'true'; }
protected function hxRedirect(string $url) { return $this->response->setHeader('HX-Redirect', $url); }
```
Semua endpoint juga harus berfungsi tanpa JS: non-htmx → render halaman penuh atau `redirect()`.

**CSRF:** `<body hx-headers='{"X-CSRF-TOKEN":"<?= csrf_hash() ?>"}'>`, `Config\Security::$regenerate = false`.

**1. Halaman list (search/filter/paginasi):**
```php
public function index()
{
    $data = $this->listData($this->request->getGet());
    return view($this->isHtmx() ? 'house/bills/_list' : 'house/bills/index', $data);
}
private function listData(array $q): array
{
    $search = trim($q['search'] ?? '');
    $builder = $this->bills->where('user_id', user_id());
    if ($search !== '') {
        $builder->groupStart()->like('name', $search)->groupEnd();
    }
    $items = $builder->orderBy('is_active', 'DESC')->orderBy('due_day')->orderBy('name')
                     ->paginate(15, 'default', (int) ($q['page'] ?? 1));
    return ['bills' => $items, 'pager' => $this->bills->pager->only(['search']), 'search' => $search];
}
```
```html
<form id="filters" hx-get="<?= url_to('house.bills.index') ?>" hx-target="#list" hx-push-url="true"
      hx-trigger="input changed delay:300ms from:[name=search], change from:select, submit">
  <input type="search" name="search" value="<?= esc($search) ?>" …kelas sumber…>
</form>
<div id="list"><?= $this->include('house/bills/_list') ?></div>
```
Search/filter tidak mengirim `page`, sehingga otomatis kembali ke halaman 1. Paginasi: `view('partials/pagination', ['pager' => $pager, 'baseUrl' => url_to('house.bills.index'), 'query' => ['search' => $search], 'target' => '#list'])` (markup 1:1 view tailwind Livewire, algoritma UrlWindow Laravel). URL halaman dibangun dari `baseUrl` + `query`, **bukan** `getPageURI()`, karena respons aksi POST (toggle/hapus) punya current URL = URL aksi. Seperti Blade, panggil hanya jika `$pager->getPageCount() > 1`.

**2. Aksi baris (toggle):** `<button hx-post="<?= url_to('house.bills.toggle', $id) ?>" hx-include="#filters, [name=page]" hx-target="#list">`. Controller menjalankan aksi lalu `return view('…/_list', $this->listData($this->request->getPost()))`. Partial `_list` menyertakan `<input type="hidden" name="page" value="…">`.

**3. Modal hapus:** pakai `partials/delete_modal` (markup sama dengan modal Livewire; `variant: 'business'` untuk varian overlay tanpa `px-4` + kotak `mx-4`). Pembungkus halaman: `x-data="{ deleteOpen: false, deleteId: null }"`; tombol "Hapus" pada baris: `@click="deleteId = <?= $id ?>; deleteOpen = true"`.
```php
<?= view('partials/delete_modal', ['title' => 'Hapus Tagihan?', 'message' => '…teks sumber…',
    'action' => url_to('house.bills.delete'), 'target' => '#list', 'include' => '#filters, [name=page]']) ?>
```
Controller: cek pemilik → hapus → kembalikan `_list` dengan `message` flash → `$this->hxTrigger('close-modal')`. Atribut `hx-*` tidak boleh dibuat dinamis dengan Alpine (`:hx-post`) karena htmx membaca atributnya saat elemen diproses.

**Flash:** `view('partials/flash', ['message' => …, 'class' => …kelas varian sumber…])`. Pada respons partial htmx, kirim pesan langsung lewat `message` (jangan `setFlashdata`, karena pesannya akan muncul lagi di request berikutnya). `setFlashdata('success', …)` hanya untuk alur redirect / `HX-Redirect`.

**4. Form create/edit:**
```html
<form hx-post="<?= $action ?>" hx-target="this" hx-swap="outerHTML" hx-disabled-elt="find button[type=submit]">
```
Validasi gagal → `return view('…/_form', ['form' => $post, 'errors' => $this->validator->getErrors(), …])` (status 200). Sukses → `session()->setFlashdata('success', 'Tagihan berhasil ditambahkan.')` lalu `return $this->hxRedirect(url_to('house.bills.index'))`.
Validasi: `$this->validateData($post, $rules, $messages)`. Salin rules & `$messages` dari komponen Livewire, terjemahkan ke rule CI4 (`exists` → cek manual + user_id, `after_or_equal:x` → rule kustom).

**5. Upload file:** `hx-encoding="multipart/form-data"`. Preview di klien (`URL.createObjectURL`, Alpine). Teks "⏳ Mengupload..." ditampilkan via class `.htmx-indicator`. Rule file hanya ditambahkan jika `$file->getError() !== UPLOAD_ERR_NO_FILE`. Kompresi lewat `ImageCompressor` (GD, maks 1600px, JPEG 80, latar putih) → `writable/uploads/bill-payments/{uuid}.jpg`; kolom DB menyimpan `bill-payments/{uuid}.jpg`.

**6. Area detail yang di-refresh (Utang/Piutang show, dashboard tagihan):** bungkus dengan `<div id="debt-detail">` / `<div id="house-dashboard">`. Aksi (`hx-post` cicilan, `hx-get` bulan/kategori) mengembalikan partial area tersebut. Grafik di-render ulang pada event `htmx:afterSettle` di `public/js/app.js`.

**7. Line items invoice/penawaran:** komponen Alpine `documentForm()` di `app.js`. Data awal di-embed via `<script type="application/json">`. Total dihitung live di klien, tetapi server **selalu menghitung ulang** dengan `DocumentCalculator` saat simpan.

**8. Input nominal Rupiah:** salin `x-data` dari `house/payments/create-edit.blade.php`. Ganti `$wire.set(...)` dengan menulis ke `<input type="hidden" name="amount_paid">` yang hanya berisi digit.

Loading state: `wire:loading.remove` → `class="hide-on-request"`, `wire:loading` → `class="htmx-indicator"`, `wire:loading.attr="disabled"` → `hx-disabled-elt`.

## Logika Bisnis yang Sering Salah

- Periode default dashboard = pembayaran terakhir dengan `(year < now.year) OR (year = now.year AND month <= now.month)`, bukan selalu bulan berjalan. Next-month tidak boleh melewati bulan berjalan (validasi juga di server).
- `bill_payments` unik per `(utility_bill_id, period_month, period_year)` → error di field `period_month`.
- Utang/Piutang: semua perubahan `remaining_amount`/`status` lewat `DebtLedgerService` dalam transaksi. Edit amount: `remaining = max(0, remaining + (baru − lama))`. Cicilan ≤ sisa.
- Urutan status: pakai `CASE status WHEN 'unpaid' THEN 0 WHEN 'partial' THEN 1 ELSE 2 END` (bukan `FIELD()`).
- Hapus kontak/klien yang masih punya relasi `RESTRICT` → tolak dengan pesan, jangan sampai error 500.
- Nomor dokumen `{PREFIX}/{YYYY}/{MM}/{NNN}`: transaksi + `SELECT … FOR UPDATE` pada `business_settings` (auto-create jika belum ada).
- Saat menghapus tagihan atau pembayaran, file screenshot di `writable/uploads` ikut dihapus.
- Timezone `Asia/Jakarta`. Gunakan `Time::now()`, bukan `date()` tanpa konteks.

## Auth

Auth kustom (bukan Shield) di atas tabel `users` Laravel. Hash bcrypt Laravel valid dengan `password_verify()`. Session key `user_id`; `session()->regenerate()` setelah login. Throttle 5 gagal/menit per `md5(lower(email)|ip)` memakai counter `cache()` dengan TTL 60 detik (meniru `RateLimiter` Laravel: hanya dihitung saat gagal). Filter `auth` untuk request htmx mengirim `HX-Redirect: /login`, bukan redirect 302.

## Tes

- PHPUnit CI4 dengan DB MySQL terpisah (ENUM/FK spesifik MySQL, jangan pakai SQLite). Feature test extend `Tests\Support\FeatureTestCase` (migrasi + seed user demo, CSRF dimatikan; `enableCsrf()` untuk tes CSRF, `htmx()` untuk header, `demoUserId()`).
- Di lingkungan testing: `Session::destroy()` tidak melakukan apa-apa (hapus key secara eksplisit), `withHeaders()` berlaku sampai diganti (`withHeaders([])`), cookie request di-set via `service('superglobals')->setCookie()`, dan exception CSRF/404 dilempar (bukan respons 403/404) → pakai `expectException`. `TestResponse::assertSee` mem-parse HTML (fragmen dibungkus `<html><body>`), jadi cek isi mentah via `$result->response()->getBody()`.
- Setiap fitur baru minimal punya: tes isolasi user (user B tidak bisa melihat/mengubah data user A, termasuk via search), tes validasi, dan tes logika service.
- Simulasikan request htmx dengan `->withHeaders(['HX-Request' => 'true'])`, lalu cek respons berupa partial (tanpa `<html>`).

## Definition of Done per Halaman

1. Markup cocok dengan Blade sumber (bandingkan berdampingan di 1440px & 390px).
2. Semua interaksi Livewire di halaman tersebut berjalan tanpa reload (cek tab Network: XHR dengan header `HX-Request`).
3. Teks & pesan sama dengan sumber.
4. Tes lulus; task di `docs/TASKS.md` dicentang.
