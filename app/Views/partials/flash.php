<?php
/**
 * Flash sukses. Sumber pesan:
 * - $message: dikirim langsung oleh controller pada respons partial htmx (tidak disimpan ke session,
 *   agar tidak muncul lagi di request berikutnya);
 * - session('success'): flash setelah redirect (HX-Redirect / redirect biasa).
 * $class mengikuti varian markup tiap halaman di Blade sumber.
 *
 * @var string|null $message
 * @var string|null $class
 * @var bool|null   $inline  true = "✓ pesan" dalam satu baris (varian Blade `...">✓ {{ ... }}</div>`)
 */
$message ??= session('success');
$class   ??= 'mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800';
?>
<?php if ($message): ?>
    <?php if (! empty($inline)): ?>
        <div class="<?= esc($class) ?>">✓ <?= esc($message) ?></div>
    <?php else: ?>
        <div class="<?= esc($class) ?>">
            ✓ <?= esc($message) ?>
        </div>
    <?php endif ?>
<?php endif ?>
