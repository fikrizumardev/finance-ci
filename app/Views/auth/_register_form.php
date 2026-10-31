<?php
/**
 * @var array{name?: string, email?: string} $form
 */
$form ??= [];
$fieldError = static fn (string $field): string => ($message = error_message($field)) === null
    ? ''
    : '<p class="flex items-center gap-1.5 text-xs font-semibold text-red-600"><span class="text-sm">!</span>' . esc($message) . '</p>';
$fields = [
    ['name', 'Nama lengkap', 'text', 'name', 'Nama Anda'],
    ['email', 'Alamat email', 'email', 'email', 'nama@email.com'],
    ['password', 'Password', 'password', 'new-password', 'Minimal 8 karakter'],
    ['password_confirmation', 'Konfirmasi password', 'password', 'new-password', 'Ulangi password'],
];
?>
    <form method="POST" action="<?= url_to('register.store') ?>" class="space-y-5"
          hx-post="<?= url_to('register.store') ?>" hx-target="this" hx-swap="outerHTML" hx-disabled-elt="find button[type=submit]">
        <?= csrf_field() ?>
        <?php foreach ($fields as [$name, $label, $type, $autocomplete, $placeholder]): ?>
            <div class="space-y-2">
                <label for="<?= $name ?>" class="login-label block"><?= $label ?></label>
                <input id="<?= $name ?>" type="<?= $type ?>" name="<?= $name ?>"
                       value="<?= $type === 'password' ? '' : esc($form[$name] ?? '') ?>"
                       autocomplete="<?= $autocomplete ?>" placeholder="<?= $placeholder ?>"
                       class="login-field w-full px-4 <?= has_error($name) ? 'has-error' : '' ?>" />
                <?= $fieldError($name) ?>
            </div>
        <?php endforeach ?>

        <button type="submit" class="login-submit relative w-full px-4 text-white focus:outline-none focus:ring-4 focus:ring-blue-500/15">
            <span class="hide-on-request flex items-center justify-center gap-2">Daftar <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h13m-5-5 5 5-5 5"/></svg></span>
            <span class="htmx-indicator inline-flex items-center justify-center gap-2"><svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4Z"/></svg>Memproses...</span>
        </button>

        <p class="text-center text-xs font-semibold text-slate-500">Sudah punya akun? <a href="<?= url_to('login') ?>" class="text-blue-600 hover:text-blue-700">Masuk</a></p>
    </form>
