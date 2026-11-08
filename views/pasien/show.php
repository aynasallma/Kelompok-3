<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Profil Pasien: <?= e($pasien['nama_pasien']) ?></h2>
        <p class="muted">Informasi data diri, nomor rekam medis, dan pendaftaran kunjungan.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn" href="<?= route('pasien') ?>">&laquo; Kembali</a>
        <?php if (has_role('admin', 'resepsionis')): ?>
            <a class="btn btn-primary" href="<?= route('pasien/edit/' . (int) $pasien['id_pasien']) ?>">
                <?= icon('edit-3', '', 15) ?> Edit Pasien
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="panel">
    <h2><?= e($pasien['nama_pasien']) ?></h2>
    <dl class="detail">
        <dt>No. RM</dt><dd><?= e($pasien['no_rm']) ?></dd>
        <dt>Jenis Kelamin</dt><dd><?= $pasien['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></dd>
        <dt>Tanggal Lahir</dt><dd><?= tgl($pasien['tanggal_lahir']) ?></dd>
        <dt>Alamat</dt><dd><?= nl2br(e($pasien['alamat'])) ?></dd>
        <dt>No. HP</dt><dd><?= e($pasien['no_hp']) ?></dd>
    </dl>
    <a class="btn" href="<?= route('pasien') ?>">&laquo; Kembali</a>
    <?php if (has_role('admin', 'resepsionis')): ?>
        <a class="btn" href="<?= route('pasien/edit/' . (int) $pasien['id_pasien']) ?>">Edit</a>
    <?php endif; ?>
</div>

<?php if (has_role('admin', 'resepsionis')): ?>
<div class="panel">
    <h2>Daftarkan Kunjungan</h2>
    <form method="post" action="<?= route('pasien/kunjungan/' . (int) $pasien['id_pasien']) ?>" class="row-form staff-inline-grid visit-availability-form" data-options-url="<?= route('pasien/pilihan') ?>">
        <?= csrf_field() ?>
        <div>
            <label for="visit-date">Tanggal Kunjungan <span class="required">*</span></label>
            <input id="visit-date" type="date" name="tanggal_kunjungan" value="<?= e($bookingDate) ?>" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div>
            <label for="visit-poli">Poliklinik <span class="required">*</span></label>
            <select id="visit-poli" name="poli" required <?= !$poliOptions ? 'disabled' : '' ?>>
                <option value="">Pilih poli</option>
                <?php foreach ($poliOptions as $poli): ?>
                    <option value="<?= e($poli) ?>"><?= e($poli) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="visit-doctor">Dokter Praktik <span class="required">*</span></label>
            <select id="visit-doctor" name="id_dokter" required <?= !$poliOptions ? 'disabled' : '' ?>>
                <option value="">Pilih dokter</option>
                <?php foreach ($doctorsByPoli[$poliOptions[0] ?? ''] ?? [] as $doctor): ?>
                    <option value="<?= (int) $doctor['id_user'] ?>"><?= e($doctor['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <small class="field-help" data-availability-message><?= e($visitClosedMessage) ?></small>
            <button class="btn btn-primary" <?= !$poliOptions ? 'disabled' : '' ?>>Daftarkan</button>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="panel">
    <h2>Riwayat Kunjungan</h2>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Tanggal</th><th>Poli</th><th>Antrian</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php if (!$kunjungan): ?>
            <tr><td colspan="5" class="empty">Belum ada kunjungan.</td></tr>
        <?php endif; ?>
        <?php foreach ($kunjungan as $k): ?>
            <tr>
                <td><?= tgl($k['tanggal_kunjungan']) ?></td>
                <td><?= e($k['poli']) ?></td>
                <td><?= (int) $k['no_antrian'] ?></td>
                <td><span class="status status-<?= strtolower(e($k['status'])) ?>"><?= e($k['status']) ?></span></td>
                <td class="actions">
                    <form method="post" action="<?= route('pasien/status/' . (int) $k['id_kunjungan']) ?>" class="inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="back" value="pasien/show/<?= (int) $pasien['id_pasien'] ?>">
                        <select name="status" onchange="this.form.submit()">
                            <?php foreach (['Menunggu', 'Diperiksa', 'Selesai', 'Batal'] as $s): ?>
                                <option value="<?= $s ?>" <?= $k['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <?php if (has_role('admin', 'dokter') && !$k['id_rekam_medis'] && $k['status'] !== 'Batal'): ?>
                        <a class="icon-btn icon-btn-primary" href="<?= route('rekam_medis/create/' . (int) $k['id_kunjungan']) ?>" title="Isi Rekam Medis">
                            <?= icon('clipboard-list', '', 15) ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php if (has_role('admin', 'dokter')): ?>
<div class="panel">
    <h2>Riwayat Rekam Medis</h2>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Tanggal</th><th>Poli</th><th>Diagnosa</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php if (!$rekamMedis): ?>
            <tr><td colspan="4" class="empty">Belum ada rekam medis.</td></tr>
        <?php endif; ?>
        <?php foreach ($rekamMedis as $rm): ?>
            <tr>
                <td><?= tgl($rm['tanggal_kunjungan']) ?></td>
                <td><?= e($rm['poli']) ?></td>
                <td><?= e($rm['diagnosa']) ?></td>
                <td class="actions">
                    <a class="icon-btn icon-btn-primary" href="<?= route('rekam_medis/show/' . (int) $rm['id_rekam_medis']) ?>" title="Detail Rekam Medis">
                        <?= icon('eye', '', 15) ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
