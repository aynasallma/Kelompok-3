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
            <div><label>No. RM</label><input type="text" value="<?= $v('no_rm') ?>" disabled></div>
        <?php endif; ?>
        <div><label>Nama Pasien</label><input type="text" name="nama_pasien" value="<?= $v('nama_pasien') ?>" required></div>
        <div>
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" required>
                <option value="L" <?= ($item['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= ($item['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>
        <div><label>Tanggal Lahir</label><input type="date" name="tanggal_lahir" value="<?= $v('tanggal_lahir') ?>" max="<?= date('Y-m-d') ?>" required></div>
        <div><label>No. HP</label><input type="text" name="no_hp" value="<?= $v('no_hp') ?>"></div>
        <div class="field-wide"><label>Alamat</label><textarea name="alamat" rows="4"><?= $v('alamat') ?></textarea></div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary">Simpan</button>
        <a class="btn" href="<?= route('pasien') ?>">Batal</a>
    </div>
</form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
