<?php
require __DIR__ . '/../layouts/header.php';
$v = function ($key) use ($item) {
    return e(old($key, $item[$key] ?? ''));
};
?>

<div class="panel">
    <h2><?= e($title) ?></h2>
    <p class="muted" style="margin-bottom: 20px;">Lengkapi data diri pasien secara akurat sesuai kartu identitas resmi (KTP / KK / SIM).</p>

    <form method="post" action="<?= $action ?>" class="staff-form" novalidate>
        <?= csrf_field() ?>

        <div class="staff-form-grid">
            <?php if ($item): ?>
                <div>
                    <label>No. Rekam Medis</label>
                    <input type="text" value="<?= e($item['no_rm']) ?>" disabled style="background: #f1f5f9; font-weight: 600;">
                </div>
            <?php endif; ?>

            <div>
                <label>Nama Pasien <span class="required">*</span></label>
                <input type="text" name="nama_pasien" value="<?= $v('nama_pasien') ?>" placeholder="Nama lengkap pasien" required>
            </div>

            <div>
                <label>Jenis Kelamin <span class="required">*</span></label>
                <select name="jenis_kelamin" required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L" <?= $v('jenis_kelamin') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= $v('jenis_kelamin') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>

            <div>
                <label>Tanggal Lahir <span class="required">*</span></label>
                <input type="date" name="tanggal_lahir" value="<?= $v('tanggal_lahir') ?>" max="<?= date('Y-m-d') ?>" required>
            </div>

            <div>
                <label>Nomor HP / WhatsApp <span class="required">*</span></label>
                <input type="tel" name="no_hp" value="<?= $v('no_hp') ?>" placeholder="Contoh: 081234567890" required>
                <small class="muted">Nomor kontak aktif untuk informasi kunjungan</small>
            </div>

            <div class="field-wide">
                <label>Alamat Lengkap Pasien</label>
                <textarea name="alamat" rows="3" placeholder="Alamat domisili atau sesuai KTP"><?= $v('alamat') ?></textarea>
            </div>
        </div>

        <div class="form-actions" style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Simpan Pasien</button>
            <a class="btn" href="<?= route('pasien') ?>">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
