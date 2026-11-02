<?php
require __DIR__ . '/../layouts/header.php';
$v = function ($key) use ($item) {
    return e($item[$key] ?? '');
};
?>

<div class="panel">
<form method="post" action="<?= $action ?>" class="staff-form">
    <?= csrf_field() ?>

    <div class="staff-form-grid">
        <?php if ($item): ?>
            <div><label>Pasien</label><input type="text" value="<?= e($item['no_rm'] . ' - ' . $item['nama_pasien']) ?>" disabled></div>
            <div><label>Kunjungan</label><input type="text" value="<?= e(tgl($item['tanggal_kunjungan']) . ' - ' . $item['poli'] . ' (antrian ' . $item['no_antrian'] . ')') ?>" disabled></div>
        <?php else: ?>
            <div class="field-wide">
                <label>Kunjungan</label>
                <select name="id_kunjungan" required>
                    <option value="">Pilih kunjungan</option>
                    <?php foreach ($kunjungan as $k): ?>
                        <option value="<?= (int) $k['id_kunjungan'] ?>" <?= $terpilih === (int) $k['id_kunjungan'] ? 'selected' : '' ?>>
                            <?= e(tgl($k['tanggal_kunjungan']) . ' | ' . $k['poli'] . ' #' . $k['no_antrian'] . ' | ' . $k['no_rm'] . ' - ' . $k['nama_pasien']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$kunjungan): ?><p class="muted small">Tidak ada kunjungan yang ditugaskan dan menunggu rekam medis.</p><?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="field-wide"><label>Keluhan</label><textarea name="keluhan" rows="3" required><?= $v('keluhan') ?></textarea></div>
        <div class="field-wide"><label>Diagnosa</label><textarea name="diagnosa" rows="3" required><?= $v('diagnosa') ?></textarea></div>
        <div class="field-wide"><label>Tindakan</label><textarea name="tindakan" rows="3"><?= $v('tindakan') ?></textarea></div>
        <div class="field-wide"><label>Catatan</label><textarea name="catatan" rows="3"><?= $v('catatan') ?></textarea></div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary">Simpan</button>
        <a class="btn" href="<?= route('rekam_medis') ?>">Batal</a>
    </div>
</form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>