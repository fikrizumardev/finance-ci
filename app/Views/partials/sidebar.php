<?php
$user   = current_user();
$active = static fn (bool $isActive): string => $isActive ? 'is-active' : '';
?>
<aside class="mf-sidebar flex flex-col h-full text-white">
    <div class="px-6 pt-7 pb-6">
        <a href="<?= url_to('dashboard') ?>" class="flex items-center gap-3">
            <span class="mf-brand-mark"><img src="/img/logo.jpg" alt="" class="w-full h-full rounded-xl object-cover"></span>
            <span>
                <span class="block text-[17px] font-extrabold tracking-[-.04em] text-white">MyFinance</span>
                <span class="block mt-0.5 text-[10px] font-semibold tracking-[.16em] uppercase text-slate-400">Personal finance</span>
            </span>
        </a>
    </div>

    <nav id="sidebar-nav" class="flex-1 px-4 pb-5 overflow-y-auto">
        <p class="mf-nav-label">Workspace</p>
        <a href="<?= url_to('dashboard') ?>" class="mf-nav-item <?= $active(url_is('dashboard')) ?>">
            <span class="mf-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 21v-7h6v7"/></svg></span>
            <span>Dashboard</span>
        </a>

        <p class="mf-nav-label mt-7">Rumah tangga</p>
        <a href="<?= url_to('house.dashboard') ?>" class="mf-nav-item <?= $active(url_is('house')) ?>">
            <span class="mf-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 11 9-7 9 7M5 10v10h14V10M9 20v-6h6v6"/></svg></span>
            <span>Dashboard tagihan</span>
        </a>
        <a href="<?= url_to('house.bills.index') ?>" class="mf-nav-item <?= $active(url_is('house/bills*')) ?>">
            <span class="mf-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 3h9l3 3v15H6V3Z"/><path stroke-linecap="round" stroke-width="1.8" d="M9 11h6M9 15h6M9 7h3"/></svg></span>
            <span>Master tagihan</span>
        </a>
        <a href="<?= url_to('house.payments.index') ?>" class="mf-nav-item <?= $active(url_is('house/payments*')) ?>">
            <span class="mf-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="5" width="18" height="14" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M3 10h18M7 15h3"/></svg></span>
            <span>Riwayat pembayaran</span>
        </a>

        <p class="mf-nav-label mt-7">Relasi</p>
        <a href="<?= url_to('debts.index') ?>" class="mf-nav-item <?= $active(url_is('debt/debts*') || url_is('debts')) ?>">
            <span class="mf-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12H9m3-9a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z"/></svg></span>
            <span>Utang</span>
        </a>
        <a href="<?= url_to('receivables.index') ?>" class="mf-nav-item <?= $active(url_is('debt/receivables*') || url_is('receivables')) ?>">
            <span class="mf-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M12 8v8m4-4H8"/></svg></span>
            <span>Piutang</span>
        </a>
        <a href="<?= url_to('contacts.index') ?>" class="mf-nav-item <?= $active(url_is('debt/contacts*') || url_is('contacts')) ?>">
            <span class="mf-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="9" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M3 20a6 6 0 0 1 12 0M16 5.5a3 3 0 0 1 0 5.8M17 14a5 5 0 0 1 4 6"/></svg></span>
            <span>Kontak</span>
        </a>

    </nav>

    <div class="px-4 pb-5">
        <div class="mf-user-card">
            <div class="mf-avatar"><?= esc($user->initials()) ?></div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-white truncate"><?= esc($user->name) ?></p>
                <p class="text-[11px] text-slate-400 truncate"><?= esc($user->email) ?></p>
            </div>
            <form method="POST" action="<?= url_to('logout') ?>">
                <?= csrf_field() ?>
                <button type="submit" title="Keluar" class="mf-logout"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H5v14h4M15 8l4 4-4 4m4-4H9"/></svg></button>
            </form>
        </div>
    </div>
</aside>

<style>
    .mf-sidebar { background: linear-gradient(180deg, #142940 0%, #102238 100%); }
    .mf-brand-mark { display: flex; width: 2.65rem; height: 2.65rem; padding: .3rem; background: linear-gradient(145deg, #7398ff, #315fe8); border-radius: .9rem; box-shadow: 0 8px 20px rgba(46,94,225,.3); }
    .mf-nav-label { padding: 0 .75rem; color: #71849e; font-size: .64rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .mf-nav-item { display: flex; align-items: center; gap: .75rem; min-height: 2.65rem; margin-top: .25rem; padding: .6rem .75rem; color: #aebbd0; font-size: .79rem; font-weight: 700; text-decoration: none; border: 1px solid transparent; border-radius: .8rem; transition: .18s ease; }
    .mf-nav-item:hover { color: #fff; background: rgba(255,255,255,.07); }
    .mf-nav-item.is-active { color: #fff; background: linear-gradient(100deg, rgba(67,113,244,.95), rgba(62,93,208,.78)); border-color: rgba(148,174,255,.25); box-shadow: 0 8px 18px rgba(35,75,185,.26); }
    .mf-nav-icon { display: inline-flex; flex: 0 0 auto; align-items: center; justify-content: center; width: 1.2rem; height: 1.2rem; color: #8295b0; }
    .mf-nav-item.is-active .mf-nav-icon, .mf-nav-item:hover .mf-nav-icon { color: #fff; }
    .mf-nav-icon svg { width: 1.15rem; height: 1.15rem; }
    .mf-user-card { display: flex; align-items: center; gap: .7rem; padding: .7rem; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08); border-radius: .9rem; }
    .mf-avatar { display: flex; flex: 0 0 auto; align-items: center; justify-content: center; width: 2.2rem; height: 2.2rem; color: #18336d; background: #dbe7ff; border-radius: .7rem; font-size: .7rem; font-weight: 800; }
    .mf-logout { display: inline-flex; padding: .4rem; color: #8193ac; border: 0; border-radius: .45rem; background: transparent; cursor: pointer; transition: .18s ease; }
    .mf-logout:hover { color: #ff9b9b; background: rgba(255,255,255,.08); }
    .mf-logout svg { width: 1rem; height: 1rem; }
    #sidebar-nav { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.15) transparent; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', scrollSidebarToActive);
    function scrollSidebarToActive() {
        const nav = document.getElementById('sidebar-nav');
        const activeItem = nav ? nav.querySelector('.mf-nav-item.is-active') : null;
        if (nav && activeItem && activeItem.offsetTop > nav.clientHeight) nav.scrollTop = activeItem.offsetTop - 100;
    }
</script>
