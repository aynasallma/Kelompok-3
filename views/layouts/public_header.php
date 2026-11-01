<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6fbff">
    <title><?= e($title ?? 'Klinik Sehat') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/landing.css') ?>">
</head>
<body class="public-site">
<header class="site-header">
    <div class="site-nav-shell">
        <a class="site-brand" href="<?= route('beranda') ?>">Klinik Sehat <span>Pelayanan kesehatan keluarga</span></a>
        <nav class="public-navigation" id="public-navigation" aria-label="Navigasi utama">
            <a class="<?= ($activeNav ?? '') === 'beranda' ? 'current' : '' ?>" href="<?= route('beranda') ?>">Beranda</a>
            <a href="<?= route('beranda') ?>#layanan">Layanan</a>
            <a class="<?= ($activeNav ?? '') === 'jadwal' ? 'current' : '' ?>" href="<?= route('jadwal') ?>">Jadwal Dokter</a>
            <a href="<?= route('beranda') ?>#tentang">Tentang</a>
            <a href="<?= route('beranda') ?>#kontak">Kontak</a>
            <a href="<?= route('pendaftaran/status') ?>">Cek Status</a>
        </nav>
        <div class="header-actions">
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="public-navigation">Menu</button>
            <a class="header-registration" href="<?= route('pendaftaran') ?>">Daftar Pasien</a>
            <a class="staff-login" href="<?= route('auth/login') ?>">Login Staf</a>
        </div>
    </div>
</header>