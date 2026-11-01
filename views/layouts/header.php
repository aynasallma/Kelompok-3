<?php
$user = current_user();
$activePage = explode('/', trim($_GET['url'] ?? 'dashboard', '/'))[0];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Klinik App') ?> - Klinik App</title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">Klinik App</div>
        <nav>
            <a href="<?= route('beranda') ?>">Beranda</a>
            <a href="<?= route('dashboard') ?>" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" <?= $activePage === 'dashboard' ? 'aria-current="page"' : '' ?>>Dashboard</a>
            <?php if (has_role('admin', 'dokter', 'resepsionis')): ?>
                <a href="<?= route('jadwal') ?>" class="<?= $activePage === 'jadwal' ? 'active' : '' ?>" <?= $activePage === 'jadwal' ? 'aria-current="page"' : '' ?>>Jadwal Dokter</a>
            <?php endif; ?>
            <?php if (has_role('admin', 'dokter', 'resepsionis')): ?>
                <a href="<?= route('pasien') ?>" class="<?= $activePage === 'pasien' ? 'active' : '' ?>" <?= $activePage === 'pasien' ? 'aria-current="page"' : '' ?>>Pasien</a>
            <?php endif; ?>
            <?php if (has_role('admin', 'dokter')): ?>
                <a href="<?= route('rekam_medis') ?>" class="<?= $activePage === 'rekam_medis' ? 'active' : '' ?>" <?= $activePage === 'rekam_medis' ? 'aria-current="page"' : '' ?>>Rekam Medis</a>
            <?php endif; ?>
            <?php if (has_role('admin', 'resepsionis')): ?>
                <a href="<?= route('pendaftaran/verifikasi') ?>" class="<?= $activePage === 'pendaftaran' ? 'active' : '' ?>" <?= $activePage === 'pendaftaran' ? 'aria-current="page"' : '' ?>>Pendaftaran Online</a>
            <?php endif; ?>
            <?php if (has_role('admin')): ?>
                <a href="<?= route('pengguna') ?>" class="<?= $activePage === 'pengguna' ? 'active' : '' ?>" <?= $activePage === 'pengguna' ? 'aria-current="page"' : '' ?>>Pengguna</a>
                <a href="<?= route('laporan') ?>" class="<?= $activePage === 'laporan' ? 'active' : '' ?>" <?= $activePage === 'laporan' ? 'aria-current="page"' : '' ?>>Laporan</a>
                <a href="<?= route('hari_libur') ?>" class="<?= $activePage === 'hari_libur' ? 'active' : '' ?>" <?= $activePage === 'hari_libur' ? 'aria-current="page"' : '' ?>>Hari Libur</a>
            <?php endif; ?>
            <a href="<?= route('akun') ?>" class="<?= $activePage === 'akun' ? 'active' : '' ?>" <?= $activePage === 'akun' ? 'aria-current="page"' : '' ?>>Akun</a>
            <?php if (has_role('admin', 'dokter', 'apoteker')): ?>
                <a href="<?= route('obat') ?>" class="<?= $activePage === 'obat' ? 'active' : '' ?>" <?= $activePage === 'obat' ? 'aria-current="page"' : '' ?>>Obat</a>
                <a href="<?= route('resep') ?>" class="<?= $activePage === 'resep' ? 'active' : '' ?>" <?= $activePage === 'resep' ? 'aria-current="page"' : '' ?>>Resep</a>
            <?php endif; ?>
        </nav>
        <div class="sidebar-user">
            <strong><?= e($user['nama']) ?></strong>
            <span class="badge"><?= e(ucfirst($user['role'])) ?></span>
            <form method="post" action="<?= route('auth/logout') ?>">
                <?= csrf_field() ?>
                <button class="sidebar-logout">Keluar</button>
            </form>
        </div>
    </aside>
    <main class="content">
        <h1><?= e($title ?? '') ?></h1>
        <?php if ($msg = flash('success')): ?>
            <div class="alert alert-success"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error"><?= e($msg) ?></div>
        <?php endif; ?>
