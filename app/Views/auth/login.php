<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Login — MyFinance<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="login-shell">
<?= view('auth/_styles') ?>

    <div class="mb-8">
        <p class="login-kicker mb-3">Akses akun</p>
        <h2 class="login-heading">Selamat datang kembali</h2>
        <p class="login-subtitle mt-3 max-w-sm">Masuk ke ruang kerja MyFinance untuk mengelola keuangan Anda dengan lebih teratur.</p>
    </div>

    <?php if (session('status')): ?>
        <div class="mb-5 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <span><?= esc(session('status')) ?></span>
        </div>
    <?php endif ?>

    <?= view('auth/_login_form') ?>
</div>
<?= $this->endSection() ?>
