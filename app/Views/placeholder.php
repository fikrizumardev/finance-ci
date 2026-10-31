<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php /* Placeholder - digunakan sementara sebelum modul diimplementasi */ ?>
<div>
    <div class="flex items-center gap-4 mb-6">
        <a href="<?= url_to('dashboard') ?>"
           class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900"><?= esc($title) ?></h1>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
        <p class="text-5xl mb-4">🚧</p>
        <h2 class="text-lg font-semibold text-gray-900">Sedang Dalam Pengembangan</h2>
        <p class="text-sm text-gray-500 mt-1">Fitur ini akan segera tersedia. Saat ini dalam proses implementasi.</p>
        <div class="mt-4">
            <a href="<?= url_to('dashboard') ?>"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 transition">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
