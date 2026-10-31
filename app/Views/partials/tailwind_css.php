<style type="text/tailwindcss">

:root {
    --mf-ink: #122033;
    --mf-muted: #718096;
    --mf-navy: #122238;
    --mf-blue: #356df3;
    --mf-surface: #ffffff;
    --mf-border: #e7edf5;
    --mf-page: #f6f8fc;
}

/* ─── Mobile auth card (used in auth layout) ───────────── */
@layer utilities {
    .mobile-auth-card {
        margin-top: -1.5rem;
        margin-left: 1rem;
        margin-right: 1rem;
        background-color: white;
        border-radius: 1.5rem;
        box-shadow: 0 20px 50px -10px rgba(30, 58, 138, 0.12);
        border: 1px solid #f1f5f9;
        padding: 1.5rem 1.5rem 1.25rem;
    }
}

/* ─── Base ─────────────────────────────────────────────── */
@layer base {
    *,
    ::after,
    ::before,
    ::backdrop,
    ::file-selector-button {
        border-color: var(--color-gray-200, currentColor);
    }

    body {
        font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        font-optical-sizing: auto;
        font-feature-settings: 'cv11' 1, 'ss01' 1;
        font-synthesis: none;
        background: var(--mf-page);
        color: var(--mf-ink);
        letter-spacing: -0.01em;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        letter-spacing: -0.035em;
    }

    button, input, select, textarea { font: inherit; }
    ::selection { background: #dbe7ff; color: #173b9b; }
}

/* MyFinance visual system */
.app-sidebar-shell {
    background: var(--mf-navy);
    border-color: rgba(255,255,255,.08);
    box-shadow: 12px 0 35px rgba(18,34,56,.08);
}

.app-header {
    height: 4.5rem;
    background: rgba(255,255,255,.88);
    border-color: var(--mf-border);
    backdrop-filter: blur(18px);
}

.app-main {
    background:
        radial-gradient(circle at 92% 0%, rgba(83,127,255,.09), transparent 22rem),
        var(--mf-page);
}

.mf-panel {
    background: var(--mf-surface);
    border: 1px solid var(--mf-border);
    border-radius: 1.25rem;
    box-shadow: 0 10px 30px rgba(36,58,91,.045);
}

.mf-panel-hover { transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
.mf-panel-hover:hover { transform: translateY(-2px); border-color: #cfdbef; box-shadow: 0 16px 34px rgba(36,58,91,.09); }

.mf-eyebrow { color: #8b99ad; font-size: .68rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
.mf-number { font-variant-numeric: tabular-nums; letter-spacing: -.045em; }

@media (max-width: 640px) {
    .app-main { padding: 1rem !important; }
    .mf-panel { border-radius: 1rem; }
}

/* Grafik transaksi tetap terbaca di layar kecil dengan scroll horizontal. */
.house-monthly-chart-scroll {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-color: #94a3b8 transparent;
    scrollbar-width: thin;
}

.house-monthly-chart {
    width: 100%;
    min-width: 100%;
}

@media (max-width: 640px) {
    .house-monthly-chart {
        min-width: 850px;
    }
}
</style>
