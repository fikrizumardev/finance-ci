<?php
/**
 * Form login — di-swap oleh htmx (outerHTML) saat validasi gagal, pengganti wire:submit="login".
 *
 * @var array{email?: string, password?: string, remember?: bool} $form
 */
$form ??= [];
$fieldError = static fn (string $field): string => ($message = error_message($field)) === null
    ? ''
    : '<p class="flex items-center gap-1.5 text-xs font-semibold text-red-600"><span class="text-sm">!</span>' . esc($message) . '</p>';
?>
    <form method="POST" action="<?= url_to('login.attempt') ?>" class="space-y-5"
          hx-post="<?= url_to('login.attempt') ?>" hx-target="this" hx-swap="outerHTML" hx-disabled-elt="find button[type=submit]">
        <?= csrf_field() ?>
        <div class="space-y-2">
            <label for="email" class="login-label block">Alamat email</label>
            <div class="login-field-wrap relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="login-icon h-4 w-4 <?= has_error('email') ? '!text-red-400' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 10.89 13a2 2 0 0 0 2.22 0L21 7.5M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z"/></svg>
                </div>
                <input id="email" type="email" name="email" value="<?= esc($form['email'] ?? '') ?>" autocomplete="email" autofocus placeholder="nama@email.com" class="login-field w-full pl-10 pr-4 <?= has_error('email') ? 'has-error' : '' ?>" />
            </div>
            <?= $fieldError('email') ?>
        </div>

        <div class="space-y-2">
            <label for="password" class="login-label block">Password</label>
            <div class="login-field-wrap relative" x-data="{ show: false }">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="login-icon h-4 w-4 <?= has_error('password') ? '!text-red-400' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 10V7a5 5 0 0 1 10 0v3m-11 0h12a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2Zm5 5v2"/></svg>
                </div>
                <input id="password" name="password" value="<?= esc($form['password'] ?? '') ?>" :type="show ? 'text' : 'password'" type="password" autocomplete="current-password" placeholder="Masukkan password" class="login-field w-full pl-10 pr-11 <?= has_error('password') ? 'has-error' : '' ?>" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition hover:text-blue-600 focus:outline-none" :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                    <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7s-8.27-2.94-9.54-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.58 10.58a2 2 0 0 0 2.83 2.83M9.88 5.08A10.7 10.7 0 0 1 12 5c4.48 0 8.27 2.94 9.54 7a10.3 10.3 0 0 1-3.06 4.44M6.23 6.23A10.4 10.4 0 0 0 2.46 12c1.27 4.06 5.06 7 9.54 7 1.2 0 2.36-.22 3.42-.61"/></svg>
                </button>
            </div>
            <?= $fieldError('password') ?>
        </div>

        <label for="remember" class="flex cursor-pointer select-none items-center gap-2.5 pt-0.5 text-xs font-semibold text-slate-500">
            <input id="remember" type="checkbox" name="remember" value="1" <?= ! empty($form['remember']) ? 'checked' : '' ?> class="h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0" />
            Ingat saya di perangkat ini
        </label>

        <button type="submit" class="login-submit relative w-full px-4 text-white focus:outline-none focus:ring-4 focus:ring-blue-500/15">
            <span class="hide-on-request flex items-center justify-center gap-2">Masuk ke akun <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h13m-5-5 5 5-5 5"/></svg></span>
            <span class="htmx-indicator inline-flex items-center justify-center gap-2"><svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4Z"/></svg>Memproses...</span>
        </button>

        <div class="login-security-note pt-2"><svg class="h-3.5 w-3.5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 5 6v5c0 4.5 3 8.5 7 10 4-1.5 7-5.5 7-10V6l-7-3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>Akses aman dan terlindungi</div>
    </form>
