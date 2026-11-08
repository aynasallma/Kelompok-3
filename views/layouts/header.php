<?php
$user = current_user();
$rawUrl = trim($_GET['url'] ?? 'dashboard', '/');
$urlSegments = explode('/', $rawUrl);
$activePage = $urlSegments[0] ?? 'dashboard';
if (isset($activePage[0]) && $activePage[0] === '/') {
    $activePage = substr($activePage, 1);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Klinik Sehat') ?> - Sistem Informasi Klinik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="layout">
    <!-- Backdrop for mobile drawer sidebar -->
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <a href="<?= route('dashboard') ?>" class="brand-link">
                <span class="brand-badge-icon"><?= icon('shield-plus', '', 20) ?></span>
                <span class="brand-title">Klinik Sehat</span>
                <span class="brand-ribbon">Staf</span>
            </a>
            <button type="button" class="sidebar-close-btn" id="sidebar-close" aria-label="Tutup Menu">&times;</button>
        </div>

        <nav>
            <div class="nav-section-label">UTAMA</div>
            <a href="<?= route('dashboard') ?>" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" <?= $activePage === 'dashboard' ? 'aria-current="page"' : '' ?>>
                <span class="nav-icon"><?= icon('layout-dashboard') ?></span> Dashboard
            </a>

            <?php if (has_role('admin', 'dokter', 'resepsionis')): ?>
                <div class="nav-section-label">PELAYANAN MEDIS</div>
                <a href="<?= route('pasien') ?>" class="<?= $activePage === 'pasien' ? 'active' : '' ?>" <?= $activePage === 'pasien' ? 'aria-current="page"' : '' ?>>
                    <span class="nav-icon"><?= icon('users') ?></span> Data Pasien
                </a>
            <?php endif; ?>

            <?php if (has_role('admin', 'dokter')): ?>
                <a href="<?= route('rekam_medis') ?>" class="<?= $activePage === 'rekam_medis' ? 'active' : '' ?>" <?= $activePage === 'rekam_medis' ? 'aria-current="page"' : '' ?>>
                    <span class="nav-icon"><?= icon('clipboard-list') ?></span> Rekam Medis
                </a>
            <?php endif; ?>

            <?php if (has_role('admin', 'dokter', 'apoteker')): ?>
                <div class="nav-section-label">FARMASI &amp; OBAT</div>
                <a href="<?= route('resep') ?>" class="<?= $activePage === 'resep' ? 'active' : '' ?>" <?= $activePage === 'resep' ? 'aria-current="page"' : '' ?>>
                    <span class="nav-icon"><?= icon('pill') ?></span> Resep &amp; Penyerahan
                </a>
                <a href="<?= route('obat') ?>" class="<?= $activePage === 'obat' ? 'active' : '' ?>" <?= $activePage === 'obat' ? 'aria-current="page"' : '' ?>>
                    <span class="nav-icon"><?= icon('package') ?></span> Data Obat &amp; Stok
                </a>
            <?php endif; ?>

            <?php if (has_role('admin', 'dokter', 'resepsionis')): ?>
                <div class="nav-section-label">JADWAL &amp; REGISTRASI</div>
                <a href="<?= route('jadwal') ?>" class="<?= $activePage === 'jadwal' ? 'active' : '' ?>" <?= $activePage === 'jadwal' ? 'aria-current="page"' : '' ?>>
                    <span class="nav-icon"><?= icon('calendar') ?></span> Jadwal Dokter
                </a>
            <?php endif; ?>

            <?php if (has_role('admin', 'resepsionis')): ?>
                <a href="<?= route('pendaftaran/verifikasi') ?>" class="<?= $activePage === 'pendaftaran' ? 'active' : '' ?>" <?= $activePage === 'pendaftaran' ? 'aria-current="page"' : '' ?>>
                    <span class="nav-icon"><?= icon('file-signature') ?></span> Pendaftaran Online
                </a>
            <?php endif; ?>

            <?php if (has_role('admin')): ?>
                <div class="nav-section-label">ADMINISTRASI SISTEM</div>
                <a href="<?= route('pengguna') ?>" class="<?= $activePage === 'pengguna' ? 'active' : '' ?>" <?= $activePage === 'pengguna' ? 'aria-current="page"' : '' ?>>
                    <span class="nav-icon"><?= icon('user-check') ?></span> Manajemen Pengguna
                </a>
                <a href="<?= route('laporan') ?>" class="<?= $activePage === 'laporan' ? 'active' : '' ?>" <?= $activePage === 'laporan' ? 'aria-current="page"' : '' ?>>
                    <span class="nav-icon"><?= icon('bar-chart-3') ?></span> Laporan Kunjungan
                </a>
                <a href="<?= route('hari_libur') ?>" class="<?= $activePage === 'hari_libur' ? 'active' : '' ?>" <?= $activePage === 'hari_libur' ? 'aria-current="page"' : '' ?>>
                    <span class="nav-icon"><?= icon('calendar-off') ?></span> Kalender Libur
                </a>
            <?php endif; ?>

            <div class="nav-section-label">PENGATURAN</div>
            <a href="<?= route('akun') ?>" class="<?= $activePage === 'akun' ? 'active' : '' ?>" <?= $activePage === 'akun' ? 'aria-current="page"' : '' ?>>
                <span class="nav-icon"><?= icon('settings') ?></span> Akun Saya
            </a>
            <a href="<?= route('beranda') ?>" target="_blank">
                <span class="nav-icon"><?= icon('globe') ?></span> Lihat Web Publik &nearr;
            </a>
        </nav>
    </aside>

    <div class="main-wrapper">
        <!-- Top Navbar: Menempel di atas browser & sidebar (Non-Floating) -->
        <header class="top-navbar">
            <div class="top-nav-left">
                <button type="button" class="hamburger-btn" id="mobile-menu-toggle" aria-label="Buka menu navigasi">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="breadcrumb-trail">
                    <span class="breadcrumb-app">Klinik Sehat</span>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-current"><?= e($title ?? 'Dashboard') ?></span>
                </div>
            </div>

            <div class="top-nav-right">
                <div class="user-profile-menu">
                    <button type="button" class="profile-trigger-btn" id="user-menu-btn" aria-expanded="false" aria-haspopup="true">
                        <span class="avatar-circle"><?= strtoupper(substr($user['nama'], 0, 1)) ?></span>
                        <div class="user-meta">
                            <span class="user-display-name"><?= e($user['nama']) ?></span>
                            <span class="user-display-role"><?= e(ucfirst($user['role'])) ?></span>
                        </div>
                        <svg class="dropdown-chevron" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div class="profile-dropdown-content" id="profile-dropdown" style="display: none;">
                        <div class="dropdown-header">
                            <div class="dropdown-name"><?= e($user['nama']) ?></div>
                            <div class="dropdown-username muted small">@<?= e($user['username']) ?> &bull; <?= e(ucfirst($user['role'])) ?></div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="<?= route('akun') ?>" class="dropdown-item">
                            <?= icon('settings', '', 16) ?>
                            <span>Pengaturan Akun &amp; Password</span>
                        </a>
                        <a href="<?= route('beranda') ?>" target="_blank" class="dropdown-item">
                            <?= icon('globe', '', 16) ?>
                            <span>Halaman Publik</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="post" action="<?= route('auth/logout') ?>" class="dropdown-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="dropdown-logout-btn">
                                <?= icon('log-out', '', 16) ?>
                                <span>Keluar (Logout)</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-body">
            <!-- Flash Notifications -->
            <?php if ($msg = flash('success')): ?>
                <div class="alert alert-success">
                    <div class="alert-icon"><?= icon('check-circle-2', '', 20) ?></div>
                    <div class="alert-content"><?= e($msg) ?></div>
                </div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-error">
                    <div class="alert-icon"><?= icon('alert-triangle', '', 20) ?></div>
                    <div class="alert-content"><?= e($msg) ?></div>
                </div>
            <?php endif; ?>
