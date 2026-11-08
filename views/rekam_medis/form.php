<?php
require __DIR__ . '/../layouts/header.php';
$v = function ($key) use ($item) {
    return e(old($key, $item[$key] ?? ''));
};
$selectedKunjungan = (int) old('id_kunjungan', $terpilih);
?>

<div class="panel">
    <h2><?= e($title) ?></h2>
    <p class="muted" style="margin-bottom: 20px;">Catat hasil anamnesis, pemeriksaan fisik, diagnosis, dan tindakan medis pasien.</p>

    <form method="post" action="<?= $action ?>" class="staff-form" novalidate>
        <?= csrf_field() ?>

        <div class="staff-form-grid">
            <?php if ($item): ?>
                <div>
                    <label>Pasien</label>
                    <input type="text" value="<?= e($item['no_rm'] . ' - ' . $item['nama_pasien']) ?>" disabled style="background: #f1f5f9; font-weight: 600;">
                </div>
                <div>
                    <label>Kunjungan</label>
                    <input type="text" value="<?= e(tgl($item['tanggal_kunjungan']) . ' - ' . $item['poli'] . ' (Antrian #' . $item['no_antrian'] . ')') ?>" disabled style="background: #f1f5f9;">
                </div>
            <?php else: ?>
                <div class="field-wide">
                    <label for="id_kunjungan">Pilih Kunjungan Pasien <span class="required">*</span></label>
                    <select id="id_kunjungan" name="id_kunjungan" required>
                        <option value="">-- Pilih Pasien &amp; Antrean Kunjungan --</option>
                        <?php foreach ($kunjungan as $k): ?>
                            <option value="<?= (int) $k['id_kunjungan'] ?>" <?= $selectedKunjungan === (int) $k['id_kunjungan'] ? 'selected' : '' ?>>
                                <?= e(tgl($k['tanggal_kunjungan']) . ' | Poli ' . $k['poli'] . ' #' . $k['no_antrian'] . ' | ' . $k['no_rm'] . ' - ' . $k['nama_pasien']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (empty($kunjungan)): ?>
                        <p class="muted small" style="color: #b45309;">Tidak ada kunjungan pasien yang menunggu rekam medis saat ini.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="field-wide">
                <label for="keluhan">Keluhan Pasien (Anamnesis) <span class="required">*</span></label>
                <textarea id="keluhan" name="keluhan" rows="3" placeholder="Keluhan utama dan riwayat penyakit yang dirasakan pasien" required><?= $v('keluhan') ?></textarea>
            </div>

            <div class="field-wide">
                <label for="diagnosa">Diagnosis Medis <span class="required">*</span></label>
                <textarea id="diagnosa" name="diagnosa" rows="3" placeholder="Diagnosis penyakit atau kondisi medis pasien" required><?= $v('diagnosa') ?></textarea>
            </div>

            <div class="field-wide">
                <label for="tindakan">Tindakan Medis / Pemeriksaan Fisik</label>
                <textarea id="tindakan" name="tindakan" rows="3" placeholder="Tindakan medis, pemeriksaan fisik, atau prosedur yang dilakukan (opsional)"><?= $v('tindakan') ?></textarea>
            </div>

            <div class="field-wide">
                <label for="catatan">Catatan Tambahan &amp; Edukasi Pasien</label>
                <textarea id="catatan" name="catatan" rows="3" placeholder="Catatan khusus atau saran perawatan mandiri bagi pasien (opsional)"><?= $v('catatan') ?></textarea>
            </div>
        </div>

        <div class="form-actions" style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Simpan Rekam Medis</button>
            <a class="btn" href="<?= route('rekam_medis') ?>">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
