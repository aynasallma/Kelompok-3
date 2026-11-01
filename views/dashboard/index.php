<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="cards">
    <?php if ($canViewPatients): ?>
        <div class="card"><span class="muted">Total Pasien</span><strong><?= (int) $totalPasien ?></strong></div>
        <div class="card"><span class="muted">Kunjungan Hari Ini</span><strong><?= count($kunjungan) ?></strong></div>
    <?php endif; ?>
    <?php if ($canViewPharmacy): ?>
        <div class="card"><span class="muted">Resep Menunggu</span><strong><?= (int) $resepMenunggu ?></strong></div>
        <div class="card"><span class="muted">Stok Menipis</span><strong><?= count($stokMenipis) ?></strong></div>
    <?php endif; ?>
</div>

<?php if (has_role('admin', 'resepsionis')): ?>
<div class="panel">
    <div class="toolbar">
        <div><h2>Pendaftaran Online</h2><p class="muted">Pengajuan pasien baru yang menunggu verifikasi.</p></div>
        <a class="btn btn-primary" href="<?= route('pendaftaran/verifikasi') ?>">Lihat antrean (<?= (int) $pendaftaranMenunggu ?>)</a>
    </div>
</div>
<?php endif; ?>

<?php if ($canViewPatients): ?>
<div class="panel">
    <h2>Kunjungan Hari Ini (<?= date('d/m/Y') ?>)</h2>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>Poli</th><th>Antrian</th><th>No. RM</th><th>Pasien</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php if (!$kunjungan): ?>
            <tr><td colspan="6" class="empty">Belum ada kunjungan hari ini.</td></tr>
        <?php endif; ?>
        <?php foreach ($kunjungan as $k): ?>
            <tr>
                <td><?= e($k['poli']) ?></td>
                <td><?= (int) $k['no_antrian'] ?></td>
                <td><?= e($k['no_rm']) ?></td>
                <td><?= e($k['nama_pasien']) ?></td>
                <td><span class="status status-<?= strtolower(e($k['status'])) ?>"><?= e($k['status']) ?></span></td>
                <td class="actions">
                <?php if (has_role('admin', 'dokter', 'resepsionis')): ?>
                    <form method="post" action="<?= route('pasien/status/' . (int) $k['id_kunjungan']) ?>" class="inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="back" value="dashboard">
                        <select name="status" onchange="this.form.submit()">
                            <?php foreach (['Menunggu', 'Diperiksa', 'Selesai', 'Batal'] as $s): ?>
                                <option value="<?= $s ?>" <?= $k['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <?php if (has_role('admin', 'dokter') && !$k['id_rekam_medis'] && $k['status'] !== 'Batal'): ?>
                        <a class="btn btn-small" href="<?= route('rekam_medis/create/' . (int) $k['id_kunjungan']) ?>">Isi Rekam Medis</a>
                    <?php endif; ?>
                <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php endif; ?>

<?php if (has_role('dokter')): ?>
<div class="panel">
    <div class="toolbar">
        <div><h2>Pasien baru ditugaskan</h2><p class="muted">Pendaftaran yang disetujui dan ditangani oleh Anda.</p></div>
        <span class="status status-diperiksa"><?= count($assignedPatients) ?> pasien</span>
    </div>
    <?php if (!$assignedPatients): ?>
        <p class="muted">Belum ada pasien yang ditugaskan kepada Anda.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>No. pendaftaran</th><th>No. RM</th><th>Pasien</th><th>Poli</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($assignedPatients as $patient): ?>
                        <tr>
                            <td><?= e($patient['nomor_pendaftaran']) ?></td>
                            <td><?= e($patient['no_rm']) ?></td>
                            <td><?= e($patient['nama_pasien']) ?></td>
                            <td><?= e($patient['poli']) ?></td>
                            <td class="actions">
                                <a class="btn btn-small" href="<?= route('pasien/show/' . (int) $patient['id_pasien']) ?>">Buka pasien</a>
                                <?php if (!$patient['id_rekam_medis']): ?>
                                    <a class="btn btn-small btn-primary" href="<?= route('rekam_medis/create/' . (int) $patient['id_kunjungan']) ?>">Isi rekam medis</a>
                                <?php else: ?>
                                    <a class="btn btn-small btn-primary" href="<?= route('rekam_medis/show/' . (int) $patient['id_rekam_medis']) ?>">Lanjutkan resep</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($canViewPharmacy): ?>
<div class="panel">
    <h2>Obat Stok Menipis</h2>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Kode</th><th>Nama Obat</th><th>Stok</th><th>Minimum</th></tr></thead>
        <tbody>
        <?php if (!$stokMenipis): ?>
            <tr><td colspan="4" class="empty">Semua stok obat aman.</td></tr>
        <?php endif; ?>
        <?php foreach ($stokMenipis as $o): ?>
            <tr>
                <td><?= e($o['kode_obat']) ?></td>
                <td><?= e($o['nama_obat']) ?></td>
                <td><span class="status status-batal"><?= (int) $o['stok'] ?> <?= e($o['satuan']) ?></span></td>
                <td><?= (int) $o['stok_minimum'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php endif; ?>

<?php if (has_role('admin')): ?>
<div class="panel">
    <h2>Audit Trail Terbaru</h2>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Waktu</th><th>User</th><th>Aktivitas</th></tr></thead>
        <tbody>
        <?php if (!$audit): ?>
            <tr><td colspan="3" class="empty">Belum ada aktivitas.</td></tr>
        <?php endif; ?>
        <?php foreach ($audit as $a): ?>
            <tr>
                <td><?= e($a['waktu']) ?></td>
                <td><?= e($a['nama'] ?? '-') ?></td>
                <td><?= e($a['aktivitas']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
