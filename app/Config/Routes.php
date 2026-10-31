<?php

use CodeIgniter\Router\RouteCollection;

/**
 * URL & nama route sama dengan versi Laravel (routes/web.php, routes/auth.php),
 * sehingga url_to('house.bills.index') setara route('house.bills.index').
 *
 * Halaman yang belum dimigrasi sementara diarahkan ke PlaceholderController
 * dan diganti pada fase masing-masing (lihat docs/TASKS.md).
 *
 * @var RouteCollection $routes
 */
$routes->get('/', static fn () => redirect()->route('dashboard'));

// ─── Auth ───────────────────────────────────────────────────────────
$routes->group('', ['filter' => 'guest'], static function (RouteCollection $routes): void {
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::attempt', ['as' => 'login.attempt']);

    if (config('App')->allowRegistration) {
        $routes->get('register', 'AuthController::register', ['as' => 'register']);
        $routes->post('register', 'AuthController::store', ['as' => 'register.store']);
    }
});
$routes->post('logout', 'AuthController::logout', ['as' => 'logout']);

// ─── Protected Routes ───────────────────────────────────────────────
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    // Dashboard
    $routes->get('dashboard', 'PlaceholderController::index', ['as' => 'dashboard']);

    // ─── Operasional Rumah ───────────────────────────────────────
    $routes->group('house', static function (RouteCollection $routes): void {
        $routes->get('/', 'PlaceholderController::index', ['as' => 'house.dashboard']);
        $routes->get('bills', 'PlaceholderController::index', ['as' => 'house.bills.index']);
        $routes->get('bills/create', 'PlaceholderController::index', ['as' => 'house.bills.create']);
        $routes->get('bills/(:num)/edit', 'PlaceholderController::index', ['as' => 'house.bills.edit']);
        $routes->get('payments', 'PlaceholderController::index', ['as' => 'house.payments.index']);
        $routes->get('payments/create', 'PlaceholderController::index', ['as' => 'house.payments.create']);
        $routes->get('payments/(:num)', 'PlaceholderController::index', ['as' => 'house.payments.show']);
        $routes->get('payments/(:num)/edit', 'PlaceholderController::index', ['as' => 'house.payments.edit']);
    });

    // ─── Manajemen Bisnis ───────────────────────────────────────
    $routes->group('business', static function (RouteCollection $routes): void {
        $routes->get('/', 'PlaceholderController::index', ['as' => 'business.dashboard']);

        $routes->get('clients', 'PlaceholderController::index', ['as' => 'business.clients.index']);
        $routes->get('clients/create', 'PlaceholderController::index', ['as' => 'business.clients.create']);
        $routes->get('clients/(:num)/edit', 'PlaceholderController::index', ['as' => 'business.clients.edit']);

        $routes->get('products', 'PlaceholderController::index', ['as' => 'business.products.index']);
        $routes->get('products/create', 'PlaceholderController::index', ['as' => 'business.products.create']);
        $routes->get('products/(:num)/edit', 'PlaceholderController::index', ['as' => 'business.products.edit']);

        $routes->get('quotes', 'PlaceholderController::index', ['as' => 'business.quotes.index']);
        $routes->get('quotes/create', 'PlaceholderController::index', ['as' => 'business.quotes.create']);
        $routes->get('quotes/(:num)/edit', 'PlaceholderController::index', ['as' => 'business.quotes.edit']);

        $routes->get('invoices', 'PlaceholderController::index', ['as' => 'business.invoices.index']);
        $routes->get('invoices/create', 'PlaceholderController::index', ['as' => 'business.invoices.create']);
        $routes->get('invoices/(:num)/edit', 'PlaceholderController::index', ['as' => 'business.invoices.edit']);

        $routes->get('receipts', 'PlaceholderController::index', ['as' => 'business.receipts.index']);
    });

    // ─── Finance Module (placeholder) ───────────────────────────
    $routes->get('accounts', 'PlaceholderController::index', ['as' => 'accounts.index']);
    $routes->get('transactions', 'PlaceholderController::index', ['as' => 'transactions.index']);
    $routes->get('categories', 'PlaceholderController::index', ['as' => 'categories.index']);
    $routes->get('budgets', 'PlaceholderController::index', ['as' => 'budgets.index']);

    // ─── Debt Module ────────────────────────────────────────────
    $routes->group('debt', static function (RouteCollection $routes): void {
        $routes->get('contacts', 'PlaceholderController::index', ['as' => 'debt.contacts.index']);
        $routes->get('contacts/create', 'PlaceholderController::index', ['as' => 'debt.contacts.create']);
        $routes->get('contacts/(:num)/edit', 'PlaceholderController::index', ['as' => 'debt.contacts.edit']);

        $routes->get('debts', 'PlaceholderController::index', ['as' => 'debt.debts.index']);
        $routes->get('debts/create', 'PlaceholderController::index', ['as' => 'debt.debts.create']);
        $routes->get('debts/(:num)', 'PlaceholderController::index', ['as' => 'debt.debts.show']);
        $routes->get('debts/(:num)/edit', 'PlaceholderController::index', ['as' => 'debt.debts.edit']);

        $routes->get('receivables', 'PlaceholderController::index', ['as' => 'debt.receivables.index']);
        $routes->get('receivables/create', 'PlaceholderController::index', ['as' => 'debt.receivables.create']);
        $routes->get('receivables/(:num)', 'PlaceholderController::index', ['as' => 'debt.receivables.show']);
        $routes->get('receivables/(:num)/edit', 'PlaceholderController::index', ['as' => 'debt.receivables.edit']);
    });

    // Legacy redirects agar link lama tidak 404
    $routes->get('debts', static fn () => redirect()->route('debt.debts.index'), ['as' => 'debts.index']);
    $routes->get('receivables', static fn () => redirect()->route('debt.receivables.index'), ['as' => 'receivables.index']);
    $routes->get('contacts', static fn () => redirect()->route('debt.contacts.index'), ['as' => 'contacts.index']);

    // ─── Vehicle Module (placeholder) ───────────────────────────
    $routes->get('vehicles', 'PlaceholderController::index', ['as' => 'vehicles.index']);
    $routes->get('services', 'PlaceholderController::index', ['as' => 'services.index']);
    $routes->get('fuel', 'PlaceholderController::index', ['as' => 'fuel.index']);

    // ─── Settings ───────────────────────────────────────────────
    $routes->get('settings/profile', 'PlaceholderController::index', ['as' => 'settings.profile']);
    $routes->get('settings/password', 'PlaceholderController::index', ['as' => 'settings.password']);
    $routes->get('settings/appearance', 'PlaceholderController::index', ['as' => 'settings.appearance']);
    // Legacy aliases
    $routes->get('profile', static fn () => redirect()->route('settings.profile'), ['as' => 'profile']);
    $routes->get('settings', static fn () => redirect()->route('settings.profile'), ['as' => 'settings']);
});
