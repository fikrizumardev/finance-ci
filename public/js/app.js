let monthlyChart = null;
let monthlyDetailModal = null;

function rupiah(value) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
}

function closeMonthlyDetailModal() {
    if (!monthlyDetailModal) return;

    monthlyDetailModal.style.display = 'none';
    document.body.style.overflow = '';
}

function openMonthlyDetailModal(monthLabel, total, details) {
    if (!monthlyDetailModal) {
        monthlyDetailModal = document.createElement('div');
        monthlyDetailModal.style.cssText = `
            position:fixed;inset:0;z-index:10000;display:none;align-items:center;justify-content:center;
            padding:16px;background:rgba(15,23,42,.55);backdrop-filter:blur(3px);
        `;
        monthlyDetailModal.innerHTML = `
            <div role="dialog" aria-modal="true" aria-labelledby="house-monthly-detail-title" style="
                width:min(100%, 460px);max-height:min(640px, calc(100vh - 32px));overflow:auto;
                background:#fff;border:1px solid #e2e8f0;border-radius:16px;
                box-shadow:0 20px 45px rgba(15,23,42,.22);
            ">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:20px 20px 14px;border-bottom:1px solid #e2e8f0;">
                    <div>
                        <p style="margin:0 0 4px;font-size:12px;font-weight:600;letter-spacing:.02em;color:#64748b;text-transform:uppercase;">Detail Transaksi</p>
                        <h3 id="house-monthly-detail-title" style="margin:0;font-size:20px;line-height:1.3;font-weight:700;color:#0f172a;"></h3>
                    </div>
                    <button type="button" data-close-monthly-detail aria-label="Tutup" style="
                        display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;
                        border:0;border-radius:8px;background:#f1f5f9;color:#475569;font-size:22px;line-height:1;cursor:pointer;
                    ">&times;</button>
                </div>
                <div style="padding:16px 20px 20px;">
                    <div data-monthly-detail-summary style="padding:12px 14px;margin-bottom:14px;border-radius:10px;background:#eff6ff;border:1px solid #dbeafe;"></div>
                    <div data-monthly-detail-list style="display:flex;flex-direction:column;gap:8px;"></div>
                </div>
            </div>
        `;

        monthlyDetailModal.querySelector('[data-close-monthly-detail]').addEventListener('click', closeMonthlyDetailModal);
        monthlyDetailModal.addEventListener('click', (event) => {
            if (event.target === monthlyDetailModal) closeMonthlyDetailModal();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeMonthlyDetailModal();
        });
        document.body.appendChild(monthlyDetailModal);
    }

    const title = monthlyDetailModal.querySelector('#house-monthly-detail-title');
    const summary = monthlyDetailModal.querySelector('[data-monthly-detail-summary]');
    const list = monthlyDetailModal.querySelector('[data-monthly-detail-list]');
    const entries = Object.values(details || {}).filter((item) => Number(item.total) > 0);

    title.textContent = monthLabel;
    summary.innerHTML = `
        <p style="margin:0 0 3px;font-size:12px;font-weight:600;color:#1d4ed8;">TOTAL PEMBAYARAN</p>
        <p data-monthly-detail-total style="margin:0;font-size:22px;font-weight:700;color:#1e3a8a;"></p>
    `;
    summary.querySelector('[data-monthly-detail-total]').textContent = rupiah(total);
    list.innerHTML = '';

    if (entries.length === 0) {
        const empty = document.createElement('p');
        empty.style.cssText = 'margin:0;color:#64748b;font-size:14px;text-align:center;';
        empty.textContent = 'Tidak ada rincian transaksi untuk periode ini.';
        list.appendChild(empty);
    } else {
        entries.forEach((item) => {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:12px;padding:11px 12px;border:1px solid #e2e8f0;border-radius:10px;';

            const information = document.createElement('div');
            information.style.cssText = 'min-width:0;';

            const label = document.createElement('p');
            label.style.cssText = 'margin:0;color:#1e293b;font-size:14px;font-weight:600;';
            label.textContent = item.label || 'Lainnya';

            const count = document.createElement('p');
            count.style.cssText = 'margin:3px 0 0;color:#64748b;font-size:12px;';
            count.textContent = `${item.count || 0} transaksi`;

            const amount = document.createElement('span');
            amount.style.cssText = 'flex-shrink:0;color:#0f172a;font-size:14px;font-weight:700;text-align:right;';
            amount.textContent = rupiah(item.total);

            information.append(label, count);
            row.append(information, amount);
            list.appendChild(row);
        });
    }

    monthlyDetailModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function renderNativeMonthlyChart(wrap, data) {
    const totals = Array.isArray(data.totals)
        ? data.totals.map((value) => Number(value) || 0)
        : [];
    const categories = Array.isArray(data.categories) ? data.categories : [];
    const maxTotal = Math.max(...totals, 1);

    const scroll = document.createElement('div');
    scroll.className = 'house-monthly-chart-scroll';

    const chart = document.createElement('div');
    chart.className = 'house-monthly-chart';
    chart.style.cssText = 'height:320px;display:flex;flex-direction:column;gap:8px;position:relative;';
    chart.setAttribute('role', 'img');
    chart.setAttribute('aria-label', 'Grafik total transaksi per bulan');

    const tooltip = document.createElement('div');
    tooltip.style.cssText = `
        position:absolute;z-index:10;pointer-events:none;opacity:0;transform:translateY(-4px);
        padding:7px 10px;border-radius:6px;background:#111827;color:#fff;font-size:12px;
        line-height:1.3;white-space:nowrap;box-shadow:0 4px 12px rgba(0,0,0,.18);
        transition:opacity .12s ease, transform .12s ease;
    `;

    const plot = document.createElement('div');
    plot.style.cssText = 'flex:1;min-height:0;display:flex;gap:10px;';

    const axis = document.createElement('div');
    axis.style.cssText = 'width:96px;flex-shrink:0;display:flex;flex-direction:column;justify-content:space-between;align-items:flex-end;padding:4px 0 0;';

    [1, 0.75, 0.5, 0.25, 0].forEach((ratio) => {
        const axisLabel = document.createElement('span');
        axisLabel.style.cssText = 'font-size:11px;font-weight:500;color:#64748b;white-space:nowrap;';
        axisLabel.textContent = rupiah(maxTotal * ratio);
        axis.appendChild(axisLabel);
    });

    const bars = document.createElement('div');
    bars.style.cssText = `
        flex:1;display:grid;grid-template-columns:repeat(${Math.max(categories.length, 1)}, minmax(0, 1fr));
        align-items:end;gap:8px;border-bottom:1px solid #e5e7eb;padding:4px 4px 0;
        background:repeating-linear-gradient(to bottom, transparent 0, transparent calc(25% - 1px), #e2e8f0 25%);
    `;

    const labels = document.createElement('div');
    labels.style.cssText = `
        margin-left:106px;display:grid;grid-template-columns:repeat(${Math.max(categories.length, 1)}, minmax(0, 1fr));
        gap:8px;padding:0 4px;
    `;

    categories.forEach((category, index) => {
        const total = totals[index] || 0;
        const column = document.createElement('div');
        column.style.cssText = 'height:100%;display:flex;align-items:end;justify-content:center;min-width:0;';

        const bar = document.createElement('div');
        bar.dataset.monthlyChartBar = 'true';
        bar.style.cssText = `
            width:min(34px, 100%);height:${total > 0 ? Math.max((total / maxTotal) * 100, 2) : 0}%;
            background:#2563eb;border-radius:5px 5px 0 0;transition:height .2s ease, background .15s ease;cursor:pointer;
        `;

        const showTooltip = (event) => {
            const chartRect = chart.getBoundingClientRect();
            tooltip.textContent = `${category}: ${rupiah(total)}`;
            const tooltipLeft = Math.max(8, Math.min(event.clientX - chartRect.left + 12, chartRect.width - 150));
            tooltip.style.left = `${tooltipLeft}px`;
            tooltip.style.top = `${Math.max(event.clientY - chartRect.top - 42, 0)}px`;
            tooltip.style.opacity = '1';
            tooltip.style.transform = 'translateY(0)';
            bar.style.background = '#1d4ed8';
        };
        const hideTooltip = () => {
            tooltip.style.opacity = '0';
            tooltip.style.transform = 'translateY(-4px)';
            bar.style.background = '#2563eb';
        };

        bar.addEventListener('pointerenter', showTooltip);
        bar.addEventListener('pointermove', showTooltip);
        bar.addEventListener('pointerleave', hideTooltip);
        bar.addEventListener('click', (event) => {
            event.stopPropagation();
            openMonthlyDetailModal(category, total, data.details?.[index] || {});
        });

        column.appendChild(bar);
        bars.appendChild(column);

        const labelCell = document.createElement('span');
        labelCell.style.cssText = 'font-size:12px;font-weight:500;color:#475569;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
        labelCell.textContent = category;
        labels.appendChild(labelCell);
    });

    plot.append(axis, bars);
    chart.append(plot, labels, tooltip);
    scroll.appendChild(chart);
    wrap.appendChild(scroll);

    if (window.matchMedia('(max-width: 640px)').matches) {
        scroll.scrollLeft = scroll.scrollWidth;
    }
}

function renderMonthlyChart() {
    const container = document.getElementById('house-transaction-chart-container');
    if (!container || !container.dataset.chart) return;

    let data;
    try {
        data = JSON.parse(container.dataset.chart);
    } catch (e) {
        return;
    }

    const wrap = document.getElementById('house-transaction-chart');
    if (!wrap) return;

    closeMonthlyDetailModal();

    if (monthlyChart) {
        try { monthlyChart.destroy(); } catch (e) {}
        monthlyChart = null;
    }

    const hasData = Array.isArray(data.totals) && data.totals.some((v) => Number(v) > 0);

    wrap.style.height = '320px';
    wrap.innerHTML = '';

    if (!hasData) {
        wrap.innerHTML = `
            <div style="height:320px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:#9ca3af;">
                <span style="font-size:32px;">📊</span>
                <span style="font-size:14px;font-weight:500;">Belum ada transaksi pembayaran pada periode ini</span>
                <span style="font-size:12px;">Catat pembayaran untuk melihat grafiknya</span>
            </div>`;
        return;
    }

    if (typeof window.Chart === 'undefined') {
        renderNativeMonthlyChart(wrap, data);
        return;
    }

    const canvas = document.createElement('canvas');
    wrap.appendChild(canvas);

    try {
        monthlyChart = new window.Chart(canvas, {
            type: 'bar',
            data: {
                labels: data.categories,
                datasets: [{
                    label: 'Total Transaksi',
                    data: data.totals,
                    backgroundColor: '#2563eb',
                    borderRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => rupiah(ctx.parsed.y),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => rupiah(value),
                        },
                    },
                },
            },
        });
    } catch (e) {
        wrap.innerHTML = '';
        renderNativeMonthlyChart(wrap, data);
    }
}

// Render saat halaman dimuat dan setiap kali htmx mengganti konten dashboard tagihan
// (navigasi bulan / filter kategori) — pengganti hook livewire:init & livewire:navigated.
document.addEventListener('DOMContentLoaded', renderMonthlyChart);
document.addEventListener('htmx:afterSettle', renderMonthlyChart); // aman: keluar jika grafik tidak ada di halaman

/**
 * htmx: kirim token CSRF di setiap request (cadangan jika hx-headers pada <body> tidak terbaca),
 * dan tampilkan pesan sederhana jika server mengembalikan error.
 */
document.addEventListener('htmx:configRequest', (event) => {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token && !event.detail.headers['X-CSRF-TOKEN']) {
        event.detail.headers['X-CSRF-TOKEN'] = token.content;
    }
});

document.addEventListener('htmx:responseError', (event) => {
    const status = event.detail.xhr ? event.detail.xhr.status : 0;
    const message = status === 403
        ? 'Sesi Anda telah berakhir atau akses ditolak. Silakan muat ulang halaman.'
        : status === 404
            ? 'Data tidak ditemukan.'
            : 'Terjadi kesalahan pada server. Silakan coba lagi.';
    showToast(message);
});

document.addEventListener('htmx:sendError', () => {
    showToast('Tidak dapat terhubung ke server. Periksa koneksi Anda.');
});

function showToast(message) {
    let toast = document.getElementById('app-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'app-toast';
        toast.setAttribute('role', 'alert');
        toast.style.cssText = `
            position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:10001;
            max-width:min(420px, calc(100% - 32px));padding:12px 16px;border-radius:12px;
            background:#991b1b;color:#fff;font-size:14px;font-weight:500;
            box-shadow:0 10px 30px rgba(15,23,42,.25);transition:opacity .2s ease;
        `;
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.style.opacity = '1';
    clearTimeout(toast._hideTimer);
    toast._hideTimer = setTimeout(() => { toast.style.opacity = '0'; }, 4000);
}

/**
 * PWA Install Prompt
 * Menampilkan tombol "Install App" ketika browser mendukung PWA install.
 */
let deferredPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    // Tampilkan banner install jika belum dismiss
    const dismissed = localStorage.getItem('pwa-install-dismissed');
    if (dismissed) return;

    showInstallBanner();
});

function showInstallBanner() {
    if (document.getElementById('pwa-install-banner')) return;

    const banner = document.createElement('div');
    banner.id = 'pwa-install-banner';
    banner.innerHTML = `
        <div style="
            position: fixed; bottom: 16px; left: 50%; transform: translateX(-50%);
            z-index: 9999; max-width: 380px; width: calc(100% - 32px);
            background: #1e40af; color: white; border-radius: 16px;
            padding: 14px 16px; display: flex; align-items: center; gap: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25); animation: slideUp 0.3s ease;
        ">
            <img src="/img/logo.jpg" alt="" style="width:40px;height:40px;border-radius:10px;object-fit:cover;flex-shrink:0;">
            <div style="flex:1;min-width:0;">
                <p style="margin:0;font-weight:600;font-size:14px;">Install MyFinance</p>
                <p style="margin:0;font-size:12px;opacity:0.85;">Akses lebih cepat seperti aplikasi</p>
            </div>
            <button id="pwa-install-btn" style="
                background:white;color:#1e40af;border:none;border-radius:8px;
                padding:8px 14px;font-size:13px;font-weight:600;cursor:pointer;flex-shrink:0;
            ">Install</button>
            <button id="pwa-dismiss-btn" style="
                background:transparent;border:none;color:rgba(255,255,255,0.7);
                cursor:pointer;padding:4px;flex-shrink:0;font-size:18px;line-height:1;
            ">✕</button>
        </div>
        <style>@keyframes slideUp{from{opacity:0;transform:translateX(-50%) translateY(20px)}to{opacity:1;transform:translateX(-50%) translateY(0)}}</style>
    `;
    document.body.appendChild(banner);

    document.getElementById('pwa-install-btn').addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                banner.remove();
            }
            deferredPrompt = null;
        }
    });

    document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
        banner.remove();
        localStorage.setItem('pwa-install-dismissed', '1');
    });
}

// Deteksi sudah terinstall
window.addEventListener('appinstalled', () => {
    deferredPrompt = null;
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.remove();
    console.log('[PWA] App installed successfully');
});
