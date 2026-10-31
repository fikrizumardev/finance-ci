<?php $user = current_user(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>MyFinance</title>

    <?php /* Favicon */ ?>
    <link rel="icon" type="image/png" sizes="32x32" href="/img/icons/icon-96x96.png">
    <link rel="apple-touch-icon" href="/img/icons/icon-192x192.png">

    <?php /* PWA */ ?>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MyFinance">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
<?= view('partials/head_assets') ?>
</head>
<body class="font-sans antialiased bg-slate-50" hx-headers='{"X-CSRF-TOKEN": "<?= csrf_hash() ?>"}'>

    <?php /* ─── Root wrapper — Alpine manages sidebar open/closed ─────── */ ?>
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        <?php /* ─── OVERLAY (mobile only, closes sidebar on tap) ──────── */ ?>
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-20 bg-black/50 lg:hidden"
            x-cloak
        ></div>

        <?php /*
            ─── SIDEBAR ───────────────────────────────────────────────
            Desktop: always visible, static position.
            Mobile:  off-canvas drawer, slides in from left via transform.
        */ ?>
        <div
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="app-sidebar-shell fixed inset-y-0 left-0 z-30 flex flex-col w-72
                   transform transition-transform duration-200 ease-in-out
                   lg:static lg:translate-x-0 lg:flex"
        >
            <?= view('partials/sidebar') ?>
        </div>

        <?php /* ─── MAIN AREA ───────────────────────────────────────────── */ ?>
        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

            <?php /* ─── TOP HEADER ─────────────────────────────────────── */ ?>
            <header class="app-header shrink-0 flex items-center justify-between px-4 lg:px-8 z-10">

                <?php /* Left: hamburger (mobile) + page title */ ?>
                <div class="flex items-center gap-3">
                    <?php /* Hamburger — visible on mobile only */ ?>
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition lg:hidden"
                        aria-label="Buka menu"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <?php /* Logo image — visible on mobile only when sidebar hidden */ ?>
                    <a href="<?= url_to('dashboard') ?>" class="flex items-center gap-2 lg:hidden">
                        <img src="/img/logo.jpg" alt="MyFinance" class="w-8 h-8 rounded-lg object-cover">
                        <span class="text-base font-extrabold tracking-[-.04em] text-slate-900">MyFinance</span>
                    </a>

                    <div class="hidden lg:block">
                        <p class="mf-eyebrow">MyFinance / Workspace</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-800">Ruang kerja keuangan</p>
                    </div>
                </div>

                <?php /* Right: notification + user menu */ ?>
                <div class="flex items-center gap-1">

                    <?php /* Notification bell */ ?>
                    <button class="p-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>

                    <?php /* User dropdown */ ?>
                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition"
                        >
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-extrabold shrink-0 shadow-sm shadow-blue-500/20">
                                <?= esc($user->initials()) ?>
                            </div>
                            <span class="text-sm font-medium text-gray-700 hidden sm:block max-w-[120px] truncate">
                                <?= esc($user->name) ?>
                            </span>
                            <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-lg py-1 z-50"
                            x-cloak
                        >
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900 truncate"><?= esc($user->name) ?></p>
                                <p class="text-xs text-gray-500 truncate"><?= esc($user->email) ?></p>
                            </div>
                            <a href="<?= url_to('settings.profile') ?>"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profil Saya
                            </a>
                            <a href="<?= url_to('settings.profile') ?>"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Pengaturan
                            </a>
                            <div class="border-t border-gray-100 mt-1"></div>
                            <form method="POST" action="<?= url_to('logout') ?>">
                                <?= csrf_field() ?>
                                <button type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </header>

            <?php /* ─── PAGE CONTENT ───────────────────────────────────── */ ?>
            <main class="app-main flex-1 overflow-y-auto p-4 lg:p-8">
                <?= $this->renderSection('content') ?>
            </main>

        </div><?php /* end main area */ ?>

    </div><?php /* end root wrapper */ ?>

    <script src="/js/app.js?v=<?= @filemtime(FCPATH . 'js/app.js') ?: '1' ?>"></script>
    <?= $this->renderSection('scripts') ?>

    <?php /* PWA: Register Service Worker */ ?>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('[PWA] SW registered:', reg.scope))
                    .catch(err => console.warn('[PWA] SW failed:', err));
            });
        }
    </script>

</body>
</html>
