<?php

namespace App\Controllers;

/**
 * Halaman "Sedang Dalam Pengembangan" (pengganti resources/views/_placeholder.blade.php).
 * Judul diambil dari nama route yang cocok.
 */
class PlaceholderController extends BaseController
{
    private const TITLES = [
        'accounts.index'     => 'Akun & Dompet',
        'transactions.index' => 'Transaksi',
        'categories.index'   => 'Kategori',
        'budgets.index'      => 'Budget',
        'vehicles.index'     => 'Kendaraan',
        'services.index'     => 'Servis',
        'fuel.index'         => 'BBM',
    ];

    public function index(): string
    {
        $route = service('router')->getMatchedRouteOptions()['as'] ?? '';

        return view('placeholder', ['title' => self::TITLES[$route] ?? 'Halaman']);
    }
}
