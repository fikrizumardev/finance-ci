<?php

use App\Entities\User;
use App\Models\UserModel;

if (! function_exists('rupiah')) {
    /**
     * Format nominal Rupiah: "Rp 1.234.567" (sama dengan number_format($n, 0, ',', '.') di Blade).
     */
    function rupiah(int|float|string|null $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('month_names')) {
    /**
     * Nama bulan berbahasa Indonesia, indeks 1-12.
     *
     * @return array<int, string>
     */
    function month_names(): array
    {
        return [
            1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
            4  => 'April',    5  => 'Mei',      6  => 'Juni',
            7  => 'Juli',     8  => 'Agustus',  9  => 'September',
            10 => 'Oktober',  11 => 'November', 12 => 'Desember',
        ];
    }
}

if (! function_exists('month_name')) {
    /**
     * Nama bulan Indonesia; string kosong jika di luar 1-12.
     */
    function month_name(int $month): string
    {
        return month_names()[$month] ?? '';
    }
}

if (! function_exists('is_htmx')) {
    /**
     * Apakah request saat ini meminta partial htmx (pengganti request Livewire).
     * Request pemulihan history (tombol Back) selalu mendapat halaman penuh.
     */
    function is_htmx(): bool
    {
        $request = service('request');

        return $request->getHeaderLine('HX-Request') === 'true'
            && $request->getHeaderLine('HX-History-Restore-Request') !== 'true';
    }
}

if (! function_exists('user_id')) {
    /**
     * ID user yang sedang login, atau null.
     */
    function user_id(): ?int
    {
        $id = session('user_id');

        return $id === null ? null : (int) $id;
    }
}

if (! function_exists('current_user')) {
    /**
     * User yang sedang login (di-cache per request), atau null.
     */
    function current_user(): ?User
    {
        static $cache = [];

        $id = user_id();

        if ($id === null) {
            return null;
        }

        return $cache[$id] ??= model(UserModel::class)->find($id);
    }
}

if (! function_exists('error_message')) {
    /**
     * Pesan error validasi untuk sebuah field (dari validator request ini atau dari session setelah redirect).
     */
    function error_message(string $field): ?string
    {
        return validation_errors()[$field] ?? null;
    }
}

if (! function_exists('has_error')) {
    function has_error(string $field): bool
    {
        return error_message($field) !== null;
    }
}

if (! function_exists('field_error')) {
    /**
     * Markup error per field, pengganti @error('field') ... @enderror di Blade.
     */
    function field_error(string $field, string $class = 'mt-1 text-xs text-red-600'): string
    {
        $message = error_message($field);

        return $message === null ? '' : '<p class="' . esc($class) . '">' . esc($message) . '</p>';
    }
}

if (! function_exists('old_or')) {
    /**
     * Nilai input lama (setelah redirect withInput) atau nilai default.
     */
    function old_or(string $field, mixed $default = ''): mixed
    {
        return old($field) ?? $default;
    }
}
