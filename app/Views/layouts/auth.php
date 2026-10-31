<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>MyFinance — <?= esc(trim($this->renderSection('title')) ?: 'Login') ?></title>

    <link rel="icon" type="image/png" sizes="32x32" href="/img/icons/icon-96x96.png">
    <link rel="apple-touch-icon" href="/img/icons/icon-192x192.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#152b43">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MyFinance">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
<?= view('partials/head_assets') ?>

    <style>
        :root { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        html, body { min-height: 100%; }
        body { margin: 0; background: #f7f8fb; color: #17243a; font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        .auth-page { display: grid; min-height: 100vh; grid-template-columns: minmax(360px, .9fr) minmax(460px, 1.1fr); }
        .auth-visual { position: relative; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; padding: 3rem clamp(2.5rem, 6vw, 6rem); color: #fff; background: #152b43; }
        .auth-visual::before { content: ''; position: absolute; inset: 0; opacity: .42; background-image: linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px); background-size: 52px 52px; mask-image: linear-gradient(to bottom, black, transparent 80%); }
        .auth-visual::after { content: ''; position: absolute; right: -9rem; bottom: -10rem; width: 32rem; height: 32rem; border: 1px solid rgba(120,164,255,.18); border-radius: 50%; box-shadow: 0 0 0 42px rgba(120,164,255,.035), 0 0 0 84px rgba(120,164,255,.025); }
        .auth-visual-content, .auth-visual-brand, .auth-visual-footer { position: relative; z-index: 1; }
        .auth-visual-brand, .auth-mobile-brand { display: inline-flex; align-items: center; gap: .7rem; }
        .auth-logo { display: inline-flex; align-items: center; justify-content: center; width: 2.7rem; height: 2.7rem; padding: .28rem; background: #fff; border-radius: .75rem; box-shadow: 0 8px 18px rgba(0,0,0,.16); }
        .auth-logo img { width: 100%; height: 100%; border-radius: .5rem; object-fit: cover; }
        .auth-brand-name { font-size: 1rem; font-weight: 800; letter-spacing: -.045em; }
        .auth-brand-caption { margin-top: .12rem; color: #91a8c4; font-size: .57rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; }
        .auth-visual-content { max-width: 30rem; margin: auto 0; padding: 5rem 0; }
        .auth-visual-kicker { color: #8da9ce; font-size: .68rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
        .auth-visual h1 { max-width: 28rem; margin: 1.25rem 0 1.1rem; color: #fff; font-size: clamp(2.2rem, 4vw, 3.6rem); font-weight: 700; letter-spacing: -.07em; line-height: 1.04; }
        .auth-visual-copy { max-width: 25rem; color: #afc0d5; font-size: .88rem; line-height: 1.85; }
        .auth-rule { width: 3rem; height: 2px; margin-top: 2.2rem; background: #6f96f7; }
        .auth-visual-footer { display: flex; align-items: center; justify-content: space-between; color: #7189a7; font-size: .63rem; font-weight: 600; }
        .auth-form-panel { display: flex; align-items: center; justify-content: center; padding: 3rem clamp(2rem, 7vw, 8rem); background: #f7f8fb; }
        .auth-form-wrap { width: 100%; max-width: 27rem; }
        .auth-mobile-brand { display: none; margin-bottom: 2.5rem; }
        .auth-form-footer { margin-top: 2.5rem; color: #9aa6b7; font-size: .62rem; font-weight: 600; text-align: center; }
        @media (max-width: 900px) {
            .auth-page { display: block; min-height: 100dvh; }
            .auth-visual { min-height: 15.5rem; padding: 1.5rem 1.5rem 5rem; }
            .auth-visual-brand { display: none; }
            .auth-visual-content { padding: 1.5rem 0 0; margin: 0; }
            .auth-visual h1 { max-width: 27rem; margin: .7rem 0 .55rem; font-size: 2rem; }
            .auth-visual-copy, .auth-rule, .auth-visual-footer { display: none; }
            .auth-visual::after { right: -12rem; bottom: -18rem; }
            .auth-form-panel { min-height: calc(100dvh - 15.5rem); align-items: flex-start; padding: 2.1rem 1.25rem 1.5rem; border-radius: 1.35rem 1.35rem 0 0; margin-top: -1.35rem; position: relative; }
            .auth-form-wrap { max-width: 28rem; margin: 0 auto; }
            .auth-mobile-brand { display: inline-flex; }
            .auth-mobile-brand .auth-logo { width: 2.25rem; height: 2.25rem; }
            .auth-form-footer { margin-top: 2rem; }
        }
        @media (max-width: 480px) {
            .auth-visual { min-height: 13rem; }
            .auth-visual h1 { font-size: 1.7rem; }
            .auth-form-panel { min-height: calc(100dvh - 13rem); }
        }
    </style>
</head>
<body hx-headers='{"X-CSRF-TOKEN": "<?= csrf_hash() ?>"}'>
    <div class="auth-page">
        <aside class="auth-visual">
            <a href="<?= url_to('dashboard') ?>" class="auth-visual-brand">
                <span class="auth-logo"><img src="/img/logo.jpg" alt="MyFinance"></span>
                <span><span class="auth-brand-name block">MyFinance</span><span class="auth-brand-caption block">Personal finance</span></span>
            </a>

            <div class="auth-visual-content">
                <p class="auth-visual-kicker">Ruang kerja keuangan pribadi</p>
                <h1>Keuangan rapi, keputusan lebih pasti.</h1>
                <p class="auth-visual-copy">Satu ruang kerja untuk memantau tagihan rumah, utang, dan piutang—tanpa membuat pengelolaan keuangan terasa rumit.</p>
                <div class="auth-rule"></div>
            </div>

            <div class="auth-visual-footer"><span>Private financial workspace</span><span>MyFinance © <?= date('Y') ?></span></div>
        </aside>

        <main class="auth-form-panel">
            <div class="auth-form-wrap">
                <a href="<?= url_to('dashboard') ?>" class="auth-mobile-brand">
                    <span class="auth-logo"><img src="/img/logo.jpg" alt="MyFinance"></span>
                    <span><span class="auth-brand-name block text-slate-800">MyFinance</span><span class="auth-brand-caption block text-slate-400">Personal finance</span></span>
                </a>
                <?= $this->renderSection('content') ?>
                <p class="auth-form-footer">Data Anda tersimpan dengan aman dan hanya dapat diakses oleh Anda.</p>
            </div>
        </main>
    </div>

    <script src="/js/app.js?v=<?= @filemtime(FCPATH . 'js/app.js') ?: '1' ?>"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
        }
    </script>
</body>
</html>
