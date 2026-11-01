<?php
require __DIR__ . '/../layouts/header.php';
$v = function ($key, $default = '') use ($item) {
    return e($item[$key] ?? $default);
};
?>

<div class="panel">
<form method="post" action="<?= $action ?>" class="staff-form">
    <?= csrf_field() ?>

    <div class="staff-form-grid">
        <div><label>Kode Obat</label><input type="text" name="kode_obat" value="<?= $v('kode_obat') ?>" required></div>
        <div><label>Nama Obat</label><input type="text" name="nama_obat" value="<?= $v('nama_obat') ?>" required></div>
        <div><label>Kategori</label><input type="text" name="kategori" value="<?= $v('kategori') ?>" placeholder="Contoh: Antibiotik, Analgesik"></div>
        <div><label>Satuan</label><input type="text" name="satuan" value="<?= $v('satuan') ?>" placeholder="Contoh: tablet, botol, strip" required></div>
        <div><label>Harga (Rp)</label><input type="number" name="harga" min="0" step="0.01" value="<?= $v('harga', '0') ?>" required></div>
        <div><label>Stok</label><input type="number" name="stok" min="0" value="<?= $v('stok', '0') ?>" required></div>
        <div><label>Stok Minimum</label><input type="number" name="stok_minimum" min="0" value="<?= $v('stok_minimum', '10') ?>" required></div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary">Simpan</button>
        <a class="btn" href="<?= route('obat') ?>">Batal</a>
    </div>
</form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
