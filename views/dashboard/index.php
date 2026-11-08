<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- Greeting Banner -->
<div class="dashboard-greeting" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="margin: 0; font-size: 20px; font-weight: 700;">Selamat datang, <?= e($user['nama']) ?></h2>
            <p class="muted" style="margin: 4px 0 0; font-size: 14px;">
                Anda masuk sebagai <span class="badge badge-primary"><?= e(ucfirst($role)) ?></span> &bull; <?= date('l, d F Y') ?>
            </p>
        </div>
        <div class="live-clock muted small">
            <span>Zona Waktu: <strong>WIB (Asia/Jakarta)</strong></span>
        </div>
    </div>
</div>

<?php if ($role === 'admin'): ?>
    <!-- ==================== DASHBOARD ADMIN ==================== -->
    <div class="cards dashboard-cards">
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Total Pasien</span>
                <span class="stat-badge stat-badge-blue">Database</span>
            </div>
            <strong class="stat-value"><?= (int) $totalPasien ?></strong>
            <span class="stat-hint">Pasien terdaftar</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Total Dokter</span>
                <span class="stat-badge stat-badge-teal">Aktif</span>
            </div>
            <strong class="stat-value"><?= (int) $totalDokter ?></strong>
            <span class="stat-hint">Tenaga medis aktif</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Total Apoteker</span>
                <span class="stat-badge stat-badge-purple">Farmasi</span>
            </div>
            <strong class="stat-value"><?= (int) $totalApoteker ?></strong>
            <span class="stat-hint">Staf farmasi aktif</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Total Obat</span>
                <span class="stat-badge stat-badge-cyan">Inventori</span>
            </div>
            <strong class="stat-value"><?= (int) $totalObat ?></strong>
            <span class="stat-hint">Item obat terdaftar</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Total Kunjungan</span>
                <span class="stat-badge"><?= (int) $kunjunganHariIni ?> hari ini</span>
            </div>
            <strong class="stat-value"><?= (int) $totalKunjungan ?></strong>
            <span class="stat-hint">Akumulasi kunjungan</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Resep Menunggu</span>
                <span class="stat-badge stat-badge-amber">Antre</span>
            </div>
            <strong class="stat-value"><?= (int) $resepMenunggu ?></strong>
            <span class="stat-hint">Belum diserahkan</span>
        </div>
    </div>

    <div class="info-grid-2col" style="margin-top: 24px;">
        <!-- Statistik Kunjungan Pasien -->
        <div class="panel">
            <div class="toolbar" style="margin-bottom: 14px;">
                <h3 style="margin: 0;">Statistik Kunjungan (7 Hari Terakhir)</h3>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($statistikMingguan)): ?>
                        <tr><td colspan="2" class="empty">Belum ada data kunjungan dalam 7 hari terakhir.</td></tr>
                    <?php else: ?>
                        <?php foreach ($statistikMingguan as $stat): ?>
                            <tr>
                                <td><?= tgl($stat['tanggal_kunjungan']) ?> <?= $stat['tanggal_kunjungan'] === date('Y-m-d') ? '<span class="badge badge-primary">Hari ini</span>' : '' ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <strong><?= (int) $stat['jumlah'] ?> pasien</strong>
                                        <div style="flex: 1; max-width: 120px; background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden;">
                                            <div style="width: <?= min(100, (int) $stat['jumlah'] * 10) ?>%; background: var(--primary); height: 100%;"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Aktivitas Terbaru Sistem (Audit Trail) -->
        <div class="panel">
            <div class="toolbar" style="margin-bottom: 14px;">
                <h3 style="margin: 0;">Aktivitas Terbaru Sistem</h3>
            </div>
            <?php if (empty($audit)): ?>
                <p class="muted empty">Belum ada rekaman aktivitas.</p>
            <?php else: ?>
                <div class="rm-timeline" style="margin-top: 8px;">
                    <?php foreach ($audit as $a): ?>
                        <div class="timeline-node">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div><strong><?= e($a['aktivitas']) ?></strong></div>
                                <div class="muted small"><?= e($a['nama'] ?? 'Sistem') ?> &bull; <?= e(date('d/m/Y H:i', strtotime($a['waktu']))) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php elseif ($role === 'dokter'): ?>
    <!-- ==================== DASHBOARD DOKTER ==================== -->
    <div class="cards dashboard-cards">
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Kunjungan Hari Ini</span>
                <span class="stat-badge stat-badge-blue"><?= date('d M') ?></span>
            </div>
            <strong class="stat-value"><?= (int) $kunjunganHariIni ?></strong>
            <span class="stat-hint">Pasien ditugaskan ke Anda</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Sudah Diperiksa</span>
                <span class="stat-badge stat-badge-teal" style="display: inline-flex; align-items: center; gap: 4px;"><?= icon('check', '', 13) ?> Selesai</span>
            </div>
            <strong class="stat-value"><?= (int) $pasienSelesai ?></strong>
            <span class="stat-hint">Pemeriksaan tuntas</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Menunggu Pemeriksaan</span>
                <span class="stat-badge stat-badge-amber">Antre</span>
            </div>
            <strong class="stat-value"><?= (int) $pasienMenunggu ?></strong>
            <span class="stat-hint">Menunggu giliran</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Resep Medis Dibuat</span>
                <span class="stat-badge stat-badge-purple">Total</span>
            </div>
            <strong class="stat-value"><?= (int) $totalResep ?></strong>
            <span class="stat-hint">Resep oleh akun Anda</span>
        </div>
    </div>

    <!-- Antrean Pasien Dokter Hari Ini -->
    <div class="panel" style="margin-top: 24px;">
        <div class="toolbar" style="margin-bottom: 14px;">
            <div>
                <h3 style="margin: 0;">Daftar Kunjungan Pasien Hari Ini</h3>
                <p class="muted small">Tanggal: <?= date('d F Y') ?></p>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Antrian</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Poli</th>
                        <th>Status</th>
                        <th>Aksi Medis</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($daftarKunjungan)): ?>
                    <tr><td colspan="6" class="empty">Tidak ada pasien yang dijadwalkan untuk Anda hari ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($daftarKunjungan as $k): ?>
                        <tr>
                            <td><span class="queue-number"><?= (int) $k['no_antrian'] ?></span></td>
                            <td><code><?= e($k['no_rm']) ?></code></td>
                            <td><strong><?= e($k['nama_pasien']) ?></strong></td>
                            <td><span class="badge"><?= e($k['poli']) ?></span></td>
                            <td><span class="status status-<?= strtolower(e($k['status'])) ?>"><?= e($k['status']) ?></span></td>
                            <td class="actions">
                                <?php if (!empty($k['id_rekam_medis'])): ?>
                                    <a class="icon-btn icon-btn-primary" href="<?= route('rekam_medis/show/' . (int) $k['id_rekam_medis']) ?>" title="Lihat Rekam Medis">
                                        <?= icon('eye', '', 15) ?>
                                    </a>
                                    <a class="icon-btn icon-btn-success" href="<?= route('resep/create/' . (int) $k['id_rekam_medis']) ?>" title="Buat Resep Obat">
                                        <?= icon('pill', '', 15) ?>
                                    </a>
                                <?php else: ?>
                                    <a class="icon-btn icon-btn-primary" href="<?= route('rekam_medis/create?kunjungan=' . (int) $k['id_kunjungan']) ?>" title="Mulai Periksa Pasien">
                                        <?= icon('stethoscope', '', 15) ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Pemeriksaan Terbaru -->
    <div class="panel" style="margin-top: 24px;">
        <div class="toolbar" style="margin-bottom: 14px;">
            <h3 style="margin: 0;">Riwayat Pemeriksaan Medis Terbaru</h3>
            <a class="btn btn-small" href="<?= route('rekam_medis') ?>">Semua Rekam Medis &rarr;</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. RM</th>
                        <th>Pasien</th>
                        <th>Poli</th>
                        <th>Diagnosa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($riwayatPemeriksaan)): ?>
                    <tr><td colspan="6" class="empty">Belum ada riwayat rekam medis.</td></tr>
                <?php else: ?>
                    <?php foreach ($riwayatPemeriksaan as $rp): ?>
                        <tr>
                            <td><?= tgl($rp['tanggal_kunjungan']) ?></td>
                            <td><code><?= e($rp['no_rm']) ?></code></td>
                            <td><strong><?= e($rp['nama_pasien']) ?></strong></td>
                            <td><span class="badge badge-light"><?= e($rp['poli']) ?></span></td>
                            <td><?= e($rp['diagnosa']) ?></td>
                            <td class="actions">
                                <a class="icon-btn icon-btn-primary" href="<?= route('rekam_medis/show/' . (int) $rp['id_rekam_medis']) ?>" title="Detail Rekam Medis">
                                    <?= icon('eye', '', 15) ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($role === 'apoteker'): ?>
    <!-- ==================== DASHBOARD APOTEKER ==================== -->
    <div class="cards dashboard-cards">
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Resep Menunggu</span>
                <span class="stat-badge stat-badge-amber">Perlu Diproses</span>
            </div>
            <strong class="stat-value" style="color: #b45309;"><?= (int) $resepMenunggu ?></strong>
            <span class="stat-hint">Menunggu penyerahan obat</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Resep Diserahkan</span>
                <span class="stat-badge stat-badge-teal" style="display: inline-flex; align-items: center; gap: 4px;"><?= icon('check', '', 13) ?> Selesai</span>
            </div>
            <strong class="stat-value"><?= (int) $resepDiserahkan ?></strong>
            <span class="stat-hint">Obat telah diserahkan</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Total Resep Masuk</span>
                <span class="stat-badge stat-badge-blue">Semua</span>
            </div>
            <strong class="stat-value"><?= (int) $totalResep ?></strong>
            <span class="stat-hint">Total resep tercatat</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Stok Menipis</span>
                <span class="stat-badge stat-badge-rose">Perhatian</span>
            </div>
            <strong class="stat-value" style="color: #be123c;"><?= count($stokMenipis) ?></strong>
            <span class="stat-hint">Obat &le; batas minimum</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Total Jenis Obat</span>
                <span class="stat-badge">Katalog</span>
            </div>
            <strong class="stat-value"><?= (int) $totalObat ?></strong>
            <span class="stat-hint">Katalog obat klinik</span>
        </div>
    </div>

    <!-- Antrean Resep yang Menunggu Penyerahan -->
    <div class="panel" style="margin-top: 24px;">
        <div class="toolbar" style="margin-bottom: 14px;">
            <div>
                <h3 style="margin: 0;">Antrean Resep Menunggu Penyerahan</h3>
                <p class="muted small">Lakukan verifikasi obat dan serahkan kepada pasien</p>
            </div>
            <a class="btn btn-small" href="<?= route('resep') ?>&status=Menunggu">Lihat Semua Antrean &rarr;</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Resep</th>
                        <th>Tanggal</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Total</th>
                        <th>Aksi Penyerahan</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($antreanResep)): ?>
                    <tr><td colspan="6" class="empty">Tidak ada antrean resep yang menunggu saat ini. Seluruh resep telah diserahkan!</td></tr>
                <?php else: ?>
                    <?php foreach (array_slice($antreanResep, 0, 6) as $r): ?>
                        <tr>
                            <td><strong>#<?= (int) $r['id_resep'] ?></strong></td>
                            <td><?= tgl($r['tanggal_resep']) ?></td>
                            <td>
                                <strong><?= e($r['nama_pasien']) ?></strong>
                                <div class="muted small">RM: <?= e($r['no_rm']) ?></div>
                            </td>
                            <td><?= e($r['nama_dokter'] ?? '-') ?></td>
                            <td><strong><?= rupiah($r['total']) ?></strong></td>
                            <td class="actions">
                                <a class="icon-btn icon-btn-primary" href="<?= route('resep/show/' . (int) $r['id_resep']) ?>" title="Detail Resep">
                                    <?= icon('eye', '', 15) ?>
                                </a>
                                <form method="post" action="<?= route('resep/serahkan/' . (int) $r['id_resep']) ?>" class="inline"
                                      data-confirm="Serahkan obat untuk Resep #<?= (int) $r['id_resep'] ?> (<?= e($r['nama_pasien']) ?>) dan kurangi stok obat?">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="icon-btn icon-btn-success" title="Serahkan Obat">
                                        <?= icon('check-circle', '', 15) ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Obat dengan Stok Menipis & Penyerahan Terbaru -->
    <div class="info-grid-2col" style="margin-top: 24px;">
        <!-- Card Stok Menipis -->
        <div class="panel">
            <div class="toolbar" style="margin-bottom: 14px;">
                <h3 style="margin: 0; color: #b91c1c; display: flex; align-items: center; gap: 8px;"><?= icon('alert-triangle', '', 20) ?> Peringatan Stok Obat Menipis</h3>
                <a class="btn btn-small" href="<?= route('obat') ?>">Katalog Obat &rarr;</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Obat</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($stokMenipis)): ?>
                        <tr><td colspan="4" class="empty">Semua stok obat dalam kondisi aman (&gt; batas minimum).</td></tr>
                    <?php else: ?>
                        <?php foreach (array_slice($stokMenipis, 0, 5) as $sm): ?>
                            <tr>
                                <td><code><?= e($sm['kode_obat']) ?></code></td>
                                <td><strong><?= e($sm['nama_obat']) ?></strong></td>
                                <td>
                                    <span class="badge badge-danger"><?= (int) $sm['stok'] ?> <?= e($sm['satuan']) ?></span>
                                    <small class="muted">(Min: <?= (int) $sm['stok_minimum'] ?>)</small>
                                </td>
                                <td class="actions">
                                    <a class="icon-btn icon-btn-success" href="<?= route('obat/stok/' . (int) $sm['id_obat']) ?>" title="Tambah Stok">
                                        <?= icon('package-plus', '', 15) ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Aktivitas Penyerahan Obat Terbaru -->
        <div class="panel">
            <div class="toolbar" style="margin-bottom: 14px;">
                <h3 style="margin: 0;">Penyerahan Obat Terkini</h3>
                <a class="btn btn-small" href="<?= route('resep') ?>&status=Diserahkan">Riwayat Resep &rarr;</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No. Resep</th>
                            <th>Pasien</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($penyerahanTerbaru)): ?>
                        <tr><td colspan="4" class="empty">Belum ada penyerahan obat terbaru.</td></tr>
                    <?php else: ?>
                        <?php foreach ($penyerahanTerbaru as $pt): ?>
                            <tr>
                                <td><strong>#<?= (int) $pt['id_resep'] ?></strong></td>
                                <td><?= e($pt['nama_pasien']) ?></td>
                                <td><?= tgl($pt['tanggal_resep']) ?></td>
                                <td><span class="status status-selesai"><?= rupiah($pt['total']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ==================== DASHBOARD RESEPSIONIS ==================== -->
    <div class="cards dashboard-cards">
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Kunjungan Hari Ini</span>
                <span class="stat-badge stat-badge-blue"><?= date('d M') ?></span>
            </div>
            <strong class="stat-value"><?= (int) $kunjunganHariIni ?></strong>
            <span class="stat-hint">Pasien terjadwal</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Menunggu Pelayanan</span>
                <span class="stat-badge stat-badge-amber">Antre</span>
            </div>
            <strong class="stat-value"><?= (int) $kunjunganMenunggu ?></strong>
            <span class="stat-hint">Belum selesai</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Pendaftaran Online</span>
                <span class="stat-badge stat-badge-purple">Verifikasi</span>
            </div>
            <strong class="stat-value"><?= (int) $pendaftaranMenunggu ?></strong>
            <span class="stat-hint">Menunggu tinjauan staf</span>
        </div>
        <div class="card stat-card">
            <div class="stat-card-header">
                <span class="card-label">Total Pasien Terdaftar</span>
                <span class="stat-badge stat-badge-teal">Master Data</span>
            </div>
            <strong class="stat-value"><?= (int) $totalPasien ?></strong>
            <span class="stat-hint">Database pasien</span>
        </div>
    </div>

    <!-- Antrean Kunjungan Hari Ini -->
    <div class="panel" style="margin-top: 24px;">
        <div class="toolbar" style="margin-bottom: 14px;">
            <div>
                <h3 style="margin: 0;">Antrean Kunjungan Pasien Hari Ini</h3>
                <p class="muted small"><?= date('d F Y') ?></p>
            </div>
            <a class="btn btn-primary" href="<?= route('pasien') ?>">+ Daftarkan Kunjungan Baru</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Antrian</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Poli</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($daftarKunjungan)): ?>
                    <tr><td colspan="6" class="empty">Belum ada kunjungan terdaftar hari ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($daftarKunjungan as $k): ?>
                        <tr>
                            <td><span class="queue-number"><?= (int) $k['no_antrian'] ?></span></td>
                            <td><code><?= e($k['no_rm']) ?></code></td>
                            <td><strong><?= e($k['nama_pasien']) ?></strong></td>
                            <td><span class="badge"><?= e($k['poli']) ?></span></td>
                            <td><span class="status status-<?= strtolower(e($k['status'])) ?>"><?= e($k['status']) ?></span></td>
                            <td class="actions">
                                <a class="icon-btn icon-btn-primary" href="<?= route('pasien/show/' . (int) $k['id_pasien']) ?>" title="Profil Pasien">
                                    <?= icon('user', '', 15) ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pendaftaran Online Menunggu & Jadwal Dokter Aktif -->
    <div class="info-grid-2col" style="margin-top: 24px;">
        <div class="panel">
            <div class="toolbar" style="margin-bottom: 14px;">
                <h3 style="margin: 0;">Pendaftaran Online Menunggu Verifikasi</h3>
                <a class="btn btn-small" href="<?= route('pendaftaran/verifikasi') ?>">Lihat Semua &rarr;</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No. Reg</th>
                            <th>Nama Pasien</th>
                            <th>Poli</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($pendingPendaftaran)): ?>
                        <tr><td colspan="4" class="empty">Tidak ada pendaftaran online yang menunggu verifikasi.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pendingPendaftaran as $p): ?>
                            <tr>
                                <td><code><?= e($p['nomor_pendaftaran']) ?></code></td>
                                <td><strong><?= e($p['nama_pasien']) ?></strong></td>
                                <td><span class="badge badge-light"><?= e($p['poli']) ?></span></td>
                                <td>
                                    <a class="btn btn-small btn-primary" href="<?= route('pendaftaran/verifikasi') ?>">Verifikasi</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="toolbar" style="margin-bottom: 14px;">
                <h3 style="margin: 0;">Dokter Praktik Hari Ini</h3>
                <a class="btn btn-small" href="<?= route('jadwal') ?>">Jadwal Lengkap &rarr;</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Dokter</th>
                            <th>Poli</th>
                            <th>Jam Praktik</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($jadwalDokterHariIni)): ?>
                        <tr><td colspan="3" class="empty">Tidak ada jadwal dokter aktif untuk hari ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($jadwalDokterHariIni as $j): ?>
                            <tr>
                                <td><strong><?= e($j['nama_dokter']) ?></strong></td>
                                <td><span class="badge"><?= e($j['poli']) ?></span></td>
                                <td><?= e(substr($j['jam_mulai'], 0, 5)) ?> &ndash; <?= e(substr($j['jam_selesai'], 0, 5)) ?> WIB</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
