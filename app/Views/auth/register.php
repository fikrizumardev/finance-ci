<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Daftar — MyFinance<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php /* View register Laravel kosong; halaman ini mengikuti gaya halaman login (PRD Q-1). */ ?>
<div class="login-shell">
<?= view('auth/_styles') ?>

    <div class="mb-8">
        <p class="login-kicker mb-3">Buat akun</p>
        <h2 class="login-heading">Mulai kelola keuangan</h2>
        <p class="login-subtitle mt-3 max-w-sm">Daftar untuk membuat ruang kerja MyFinance Anda sendiri.</p>
    </div>

    <?= view('auth/_register_form') ?>
</div>
<?= $this->endSection() ?>
