<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="toolbar">
    <p class="muted">Pengajuan baru harus ditinjau sebelum menjadi data pasien.</p>
    <span class="status status-menunggu"><?= count($pending) ?> menunggu</span>
</div>

<div class="panel">
    <h2>Antrean verifikasi</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Diajukan</th><th>Nama</th><th>Jenis kelamin</th><th>Tanggal lahir</th><th>Telepon</th><th>Alamat</th><th>Keputusan</th></tr></thead>
            <thead><tr><th>No. pendaftaran</th><th>Diajukan</th><th>Nama</th><th>JK</th><th>Tanggal lahir</th><th>Poli</th><th>Telepon</th><th>Alamat</th><th>Keputusan</th></tr></thead>
            <tbody>
                <?php if (!$pending): ?><tr><td colspan="9" class="empty">Tidak ada pengajuan yang menunggu.</td></tr><?php endif; ?>
                <?php foreach ($pending as $item): ?>
                    <tr>
                        <td><strong><?= e($item['nomor_pendaftaran']) ?></strong></td>
                        <td><?= e(date('d/m/Y H:i', strtotime($item['diajukan_pada']))) ?></td>
                        <td><?= e($item['nama_pasien']) ?></td>
                        <td><?= $item['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                        <td><?= tgl($item['tanggal_lahir']) ?></td>
                        <td><?= e($item['poli']) ?></td>
                        <td><?= e($item['no_hp']) ?></td>
                        <td><?= e($item['alamat'] ?: '-') ?></td>
                        <td class="review-actions">
                            <form method="post" action="<?= route('pendaftaran/setujui/' . (int) $item['id_pendaftaran']) ?>">
                                <?= csrf_field() ?>
                                <label for="doctor-<?= (int) $item['id_pendaftaran'] ?>">Dokter penanggung jawab</label>
                                <select id="doctor-<?= (int) $item['id_pendaftaran'] ?>" name="id_dokter" required <?= empty($doctorsByRequest[$item['id_pendaftaran']]) ? 'disabled' : '' ?>>
                                    <option value="">Pilih dokter</option>
                                    <?php foreach ($doctorsByRequest[$item['id_pendaftaran']] ?? [] as $doctor): ?>
                                        <option value="<?= (int) $doctor['id_user'] ?>"><?= e($doctor['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="note-approve-<?= (int) $item['id_pendaftaran'] ?>">Catatan internal</label>
                                <textarea id="note-approve-<?= (int) $item['id_pendaftaran'] ?>" name="catatan_staf" rows="2" maxlength="500"></textarea>
                                <button class="btn btn-small btn-primary" <?= empty($doctorsByRequest[$item['id_pendaftaran']]) ? 'disabled title="Tidak ada dokter aktif di poli ini"' : '' ?> data-confirm="Setujui, buat data pasien, dan beri tahu dokter?">Setujui</button>
                            </form>
                            <form method="post" action="<?= route('pendaftaran/tolak/' . (int) $item['id_pendaftaran']) ?>">
                                <?= csrf_field() ?>
                                <label for="note-reject-<?= (int) $item['id_pendaftaran'] ?>">Alasan internal (opsional)</label>
                                <textarea id="note-reject-<?= (int) $item['id_pendaftaran'] ?>" name="catatan_staf" rows="2" maxlength="500"></textarea>
                                <button class="btn btn-small btn-danger" data-confirm="Tolak pengajuan ini?">Tolak</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <h2>Pengajuan yang sudah ditinjau</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Nama</th><th>Status</th><th>No. RM</th><th>Petugas</th><th>Catatan internal</th></tr></thead>
            <thead><tr><th>No. pendaftaran</th><th>Tanggal</th><th>Nama</th><th>Poli</th><th>Dokter</th><th>Status</th><th>No. RM</th><th>Petugas</th></tr></thead>
            <tbody>
                <?php if (!$reviewed): ?><tr><td colspan="8" class="empty">Belum ada riwayat peninjauan.</td></tr><?php endif; ?>
                <?php foreach ($reviewed as $item): ?>
                    <tr>
                        <td><?= e(date('d/m/Y H:i', strtotime($item['direview_pada']))) ?></td>
                                                <td><?= e($item['poli']) ?></td>
                                                <td><?= e($item['nama_dokter'] ?? '-') ?></td>
                        <td><?= e($item['nama_pasien']) ?></td>
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