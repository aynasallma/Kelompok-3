<?php
$activeNav = $activeNav ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f0f7ff">
    <title><?= e($title ?? 'Klinik Sehat') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/landing.css') ?>">
</head>
<body class="public-site">
<header class="site-header">
    <div class="site-nav-shell">
        <a class="site-brand" href="<?= route('beranda') ?>">
            <span class="brand-emblem-svg"><?= icon('shield-plus', '', 22) ?></span>
            <span class="site-brand-text">Klinik Sehat <span>Pelayanan kesehatan keluarga</span></span>
        </a>
        <nav class="public-navigation" id="public-navigation" aria-label="Navigasi utama">
            <a class="<?= ($activeNav ?? '') === 'beranda' ? 'current' : '' ?>" href="<?= route('beranda') ?>#beranda" data-nav="beranda">Beranda</a>
            <a href="<?= route('beranda') ?>#layanan" data-nav="layanan">Layanan</a>
            <a href="<?= route('beranda') ?>#dokter" data-nav="dokter">Tim Dokter</a>
            <a class="<?= ($activeNav ?? '') === 'jadwal' ? 'current' : '' ?>" href="<?= route('jadwal') ?>">Jadwal Praktik</a>
            <a href="<?= route('beranda') ?>#faq" data-nav="faq">FAQ</a>
            <a class="<?= ($activeNav ?? '') === 'status' ? 'current' : '' ?>" href="<?= route('pendaftaran/status') ?>">Cek Status</a>
        </nav>
        <div class="header-actions">
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="public-navigation">Menu</button>
            <a class="header-registration <?= ($activeNav ?? '') === 'pendaftaran' ? 'is-active' : '' ?>" href="<?= route('pendaftaran') ?>">Daftar Pasien</a>
            <a class="staff-login" href="<?= route('auth/login') ?>">Login Staf</a>
        </div>
    </div>
</header>
