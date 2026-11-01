<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="panel">
    <dl class="detail">
        <dt>Pasien</dt>
        <dd><a href="<?= route('pasien/show/' . (int) $item['id_pasien']) ?>"><?= e($item['no_rm'] . ' - ' . $item['nama_pasien']) ?></a></dd>
        <dt>Tanggal Kunjungan</dt><dd><?= tgl($item['tanggal_kunjungan']) ?></dd>
        <dt>Poli</dt><dd><?= e($item['poli']) ?> (antrian <?= (int) $item['no_antrian'] ?>)</dd>
        <dt>Dokter</dt><dd><?= e($item['nama_dokter'] ?? '-') ?></dd>
        <dt>Keluhan</dt><dd><?= nl2br(e($item['keluhan'])) ?></dd>
        <dt>Diagnosa</dt><dd><?= nl2br(e($item['diagnosa'])) ?></dd>
        <dt>Tindakan</dt><dd><?= nl2br(e($item['tindakan'])) ?></dd>
        <dt>Catatan</dt><dd><?= nl2br(e($item['catatan'])) ?></dd>
    </dl>
    <a class="btn" href="<?= route('rekam_medis') ?>">&laquo; Kembali</a>
    <a class="btn" href="<?= route('rekam_medis/edit/' . (int) $item['id_rekam_medis']) ?>">Edit</a>
    <a class="btn" href="<?= route('rekam_medis/pdf/' . (int) $item['id_rekam_medis']) ?>">Unduh PDF</a>
    <a class="btn btn-primary" href="<?= route('resep/create/' . (int) $item['id_rekam_medis']) ?>">+ Buat Resep</a>
</div>

<div class="panel">
    <h2>Resep</h2>
    <div class="table-wrap">
    <table>
        <thead><tr><th>#</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php if (!$resep): ?>
            <tr><td colspan="5" class="empty">Belum ada resep.</td></tr>
        <?php endif; ?>
        <?php foreach ($resep as $r): ?>
            <tr>
                <td><?= (int) $r['id_resep'] ?></td>
                <td><?= tgl($r['tanggal_resep']) ?></td>
                <td><?= rupiah($r['total']) ?></td>
                <td><span class="status status-<?= strtolower(e($r['status'])) ?>"><?= e($r['status']) ?></span></td>
                <td><a class="btn btn-small" href="<?= route('resep/show/' . (int) $r['id_resep']) ?>">Detail</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
