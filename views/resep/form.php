<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="panel">
<form method="post" action="<?= route('resep/store') ?>" class="staff-form prescription-form">
    <?= csrf_field() ?>

    <div class="staff-form-grid">
        <div class="field-wide">
            <label>Rekam Medis</label>
            <select name="id_rekam_medis" required>
                <option value="">Pilih rekam medis</option>
                <?php foreach ($rekamMedis as $rm): ?>
                    <option value="<?= (int) $rm['id_rekam_medis'] ?>" <?= $terpilih === (int) $rm['id_rekam_medis'] ? 'selected' : '' ?>>
                        <?= e(tgl($rm['tanggal_kunjungan']) . ' | ' . $rm['no_rm'] . ' - ' . $rm['nama_pasien'] . ' | ' . $rm['diagnosa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div><label>Tanggal Resep</label><input type="date" name="tanggal_resep" value="<?= date('Y-m-d') ?>" required></div>
    </div>

    <div class="prescription-items-section">
        <h2>Daftar Obat</h2>
        <div id="resep-items">
            <div class="resep-row">
                <select name="id_obat[]" class="obat-select" required>
                    <option value="">Pilih obat</option>
                    <?php foreach ($obat as $o): ?>
                        <option value="<?= (int) $o['id_obat'] ?>" data-harga="<?= e($o['harga']) ?>">
                            <?= e($o['nama_obat'] . ' (' . $o['satuan'] . ') - stok ' . $o['stok']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="dosis[]" placeholder="Dosis">
                <input type="text" name="aturan_pakai[]" placeholder="Aturan pakai">
                <input type="number" name="jumlah[]" min="1" placeholder="Jumlah" class="jumlah-input">
                <button type="button" class="btn btn-small btn-danger remove-row">Hapus</button>
            </div>
        </div>
        <div class="prescription-actions">
            <button type="button" class="btn" id="add-row">Tambah obat</button>
            <p class="muted">Perkiraan total: <strong id="estimasi-total">Rp 0</strong></p>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary">Simpan Resep</button>
        <a class="btn" href="<?= route('resep') ?>">Batal</a>
    </div>
</form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
