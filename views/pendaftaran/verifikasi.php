<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Verifikasi Pendaftaran Online</h2>
        <p class="muted">Tinjau dan verifikasi pengajuan pendaftaran mandiri dari pasien baru.</p>
    </div>
    <div class="page-header-actions">
        <span class="status status-menunggu" style="font-size: 13px; padding: 6px 14px;">
            <?= count($pending) ?> pengajuan menunggu
        </span>
    </div>
</div>

<div class="panel">
    <h2>Antrean Verifikasi Pendaftaran</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No. Pendaftaran</th>
                    <th>Diajukan Pada</th>
                    <th>Nama Pasien</th>
                    <th>JK</th>
                    <th>Tanggal Lahir</th>
                    <th>Poli</th>
                    <th>No. Telepon</th>
                    <th>Alamat</th>
                    <th>Keputusan &amp; Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$pending): ?>
                    <tr><td colspan="9" class="empty">Tidak ada pengajuan yang menunggu verifikasi.</td></tr>
                <?php endif; ?>
                <?php foreach ($pending as $item): ?>
                    <tr>
                        <td><strong><?= e($item['nomor_pendaftaran']) ?></strong></td>
                        <td><span class="small"><?= e(date('d/m/Y H:i', strtotime($item['diajukan_pada']))) ?></span></td>
                        <td><strong><?= e($item['nama_pasien']) ?></strong></td>
                        <td><?= $item['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                        <td><?= tgl($item['tanggal_lahir']) ?></td>
                        <td><span class="badge"><?= e($item['poli']) ?></span></td>
                        <td><?= e($item['no_hp']) ?></td>
                        <td><?= e($item['alamat'] ?: '-') ?></td>
                        <td class="review-actions">
                            <form method="post" action="<?= route('pendaftaran/setujui/' . (int) $item['id_pendaftaran']) ?>" class="review-form" data-confirm="Setujui dan daftarkan pasien ke jadwal kunjungan?">
                                <?= csrf_field() ?>
                                <label for="doctor-<?= (int) $item['id_pendaftaran'] ?>">Dokter Penanggung Jawab:</label>
                                <select id="doctor-<?= (int) $item['id_pendaftaran'] ?>" name="id_dokter" required <?= empty($doctorsByRequest[$item['id_pendaftaran']]) ? 'disabled' : '' ?>>
                                    <option value="">Pilih dokter</option>
                                    <?php foreach ($doctorsByRequest[$item['id_pendaftaran']] ?? [] as $doctor): ?>
                                        <option value="<?= (int) $doctor['id_user'] ?>"><?= e($doctor['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="note-approve-<?= (int) $item['id_pendaftaran'] ?>">Catatan Staf (opsional):</label>
                                <textarea id="note-approve-<?= (int) $item['id_pendaftaran'] ?>" name="catatan_staf" rows="2" maxlength="500" placeholder="Catatan internal"></textarea>
                                <button class="btn btn-small btn-primary" <?= empty($doctorsByRequest[$item['id_pendaftaran']]) ? 'disabled title="Tidak ada dokter aktif di poli ini"' : '' ?>>Setujui</button>
                            </form>
                            <form method="post" action="<?= route('pendaftaran/tolak/' . (int) $item['id_pendaftaran']) ?>" class="review-form-reject" data-confirm="Tolak pengajuan pendaftaran ini?">
                                <?= csrf_field() ?>
                                <label for="note-reject-<?= (int) $item['id_pendaftaran'] ?>">Alasan Penolakan:</label>
                                <textarea id="note-reject-<?= (int) $item['id_pendaftaran'] ?>" name="catatan_staf" rows="2" maxlength="500" placeholder="Alasan internal penolakan"></textarea>
                                <button class="btn btn-small btn-danger">Tolak</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <h2>Pengajuan yang Sudah Ditinjau</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Waktu Review</th>
                    <th>Poli</th>
                    <th>Dokter</th>
                    <th>Nama Pasien</th>
                    <th>Status</th>
                    <th>No. RM</th>
                    <th>Petugas</th>
                    <th>Catatan Staf</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$reviewed): ?>
                    <tr><td colspan="8" class="empty">Belum ada riwayat peninjauan pengajuan.</td></tr>
                <?php endif; ?>
                <?php foreach ($reviewed as $item): ?>
                    <tr>
                        <td><span class="small"><?= e(date('d/m/Y H:i', strtotime($item['direview_pada']))) ?></span></td>
                        <td><?= e($item['poli']) ?></td>
                        <td><?= e($item['nama_dokter'] ?? '-') ?></td>
                        <td><strong><?= e($item['nama_pasien']) ?></strong></td>
                        <td><span class="status <?= $item['status'] === 'Disetujui' ? 'status-selesai' : 'status-batal' ?>"><?= e($item['status']) ?></span></td>
                        <td><?= e($item['no_rm'] ?? '-') ?></td>
                        <td><?= e($item['nama_reviewer'] ?? '-') ?></td>
                        <td><?= e($item['catatan_staf'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
