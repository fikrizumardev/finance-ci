<?php
/**
 * Modal konfirmasi hapus — markup sama dengan modal Livewire ($showDeleteModal).
 * State dikelola Alpine pada elemen induk: x-data="{ deleteOpen: false, deleteId: null }".
 * Tombol "Hapus" pada baris cukup: @click="deleteId = 12; deleteOpen = true".
 * Respons server memicu event "close-modal" (header HX-Trigger) untuk menutup modal.
 *
 * @var string      $title    Judul, mis. "Hapus Tagihan?"
 * @var string      $message  Teks penjelasan
 * @var string      $action   URL POST hapus
 * @var string      $target   Selector container yang diganti, mis. "#list"
 * @var string|null $include  Selector input tambahan (filter & halaman aktif)
 * @var string|null $swap     hx-swap (default innerHTML)
 * @var string|null $variant  "default" (Rumah/Relasi) atau "business" (overlay tanpa px-4, kotak mx-4)
 */
$business = ($variant ?? 'default') === 'business';
?>
<div x-show="deleteOpen" x-cloak @close-modal.window="deleteOpen = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40<?= $business ? '' : ' px-4' ?>">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm<?= $business ? ' mx-4' : '' ?>">
        <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= esc($title) ?></h3>
        <p class="text-sm text-gray-600 mb-5"><?= esc($message) ?></p>
        <form class="flex justify-end gap-2"
              method="POST" action="<?= esc($action, 'attr') ?>"
              hx-post="<?= esc($action, 'attr') ?>"
              hx-target="<?= esc($target, 'attr') ?>"
              <?php if (! empty($swap)): ?>hx-swap="<?= esc($swap, 'attr') ?>"<?php endif ?>
              <?php if (! empty($include)): ?>hx-include="<?= esc($include, 'attr') ?>"<?php endif ?>
              hx-disabled-elt="find button[type=submit]">
            <?= csrf_field() ?>
            <input type="hidden" name="id" :value="deleteId">
            <button type="button" @click="deleteOpen = false"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Batal</button>
            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-red-600 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-60 transition">
                <span class="hide-on-request">Hapus</span>
                <span class="htmx-indicator">Menghapus...</span>
            </button>
        </form>
    </div>
</div>
