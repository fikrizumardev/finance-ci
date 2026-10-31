<?php
/**
 * Library frontend (CDN, tanpa npm/build) + CSS dasar aplikasi.
 * Dipakai oleh semua layout.
 */
?>
    <meta name="htmx-config" content='{"includeIndicatorStyles":false,"historyCacheSize":0,"refreshOnHistoryMiss":true}'>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/htmx.org@2/dist/htmx.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    <?= view('partials/tailwind_css') ?>
    <style>
        [x-cloak] { display: none !important; }
        .htmx-indicator { display: none; }
        .htmx-request .htmx-indicator, .htmx-request.htmx-indicator { display: inline-flex; }
        .htmx-request .hide-on-request, .htmx-request.hide-on-request { display: none; }
    </style>
