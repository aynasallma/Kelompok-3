<?php
require __DIR__ . '/../layouts/header.php';
$v = function ($key, $default = '') use ($item) {
    return e(old($key, $item[$key] ?? $default));
};
?>

<div class="panel">
    <h2><?= e($title) ?></h2>
    <form method="post" action="<?= $action ?>" class="staff-form" novalidate>
        <?= csrf_field() ?>

        <div class="staff-form-grid">
            <div>
                <label>Kode Obat <span class="required">*</span></label>
                <input type="text" name="kode_obat" value="<?= $v('kode_obat') ?>" placeholder="Contoh: OBT-001" required>
                <small class="muted">Kode unik pengenal obat</small>
            </div>
            <div>
                <label>Nama Obat <span class="required">*</span></label>
                <input type="text" name="nama_obat" value="<?= $v('nama_obat') ?>" placeholder="Nama merk atau generik" required>
            </div>
            <div>
                <label>Kategori</label>
                <input type="text" name="kategori" value="<?= $v('kategori') ?>" placeholder="Contoh: Antibiotik, Analgesik, Vitamin">
            </div>
            <div>
                <label>Satuan Takaran <span class="required">*</span></label>
                <input type="text" name="satuan" value="<?= $v('satuan') ?>" placeholder="Contoh: tablet, botol, strip, kapsul, tube" required>
            </div>
            <div>
                <label>Harga Jual (Rp) <span class="required">*</span></label>
                <input type="number" name="harga" min="0" step="100" value="<?= $v('harga', '0') ?>" required>
            </div>
            <div>
                <label>Stok Saat Ini <span class="required">*</span></label>
                <input type="number" name="stok" min="0" value="<?= $v('stok', '0') ?>" required>
            </div>
            <div>
                <label>Ambang Batas Stok Minimum <span class="required">*</span></label>
                <input type="number" name="stok_minimum" min="0" value="<?= $v('stok_minimum', '10') ?>" required>
                <small class="muted">Peringatan akan muncul jika stok &le; angka ini</small>
            </div>
        </div>

        <div class="form-actions" style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Simpan Data Obat</button>
            <a class="btn" href="<?= route('obat') ?>">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
