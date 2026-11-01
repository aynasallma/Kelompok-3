<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="panel password-panel">
    <h2>Ubah password</h2>
    <p class="muted">Gunakan password yang hanya Anda ketahui. Admin tidak dapat melihat password akun.</p>
    <form method="post" action="<?= route('akun/password') ?>" class="password-form" autocomplete="off">
        <?= csrf_field() ?>
        <label for="password_lama">Password saat ini</label>
        <input id="password_lama" type="password" name="password_lama" autocomplete="current-password" required>
        <label for="password_baru">Password baru</label>
        <input id="password_baru" type="password" name="password_baru" minlength="12" maxlength="72" autocomplete="new-password" required>
        <label for="konfirmasi_password">Ulangi password baru</label>
        <input id="konfirmasi_password" type="password" name="konfirmasi_password" minlength="12" maxlength="72" autocomplete="new-password" required>
        <div class="form-actions">
            <button class="btn btn-primary">Simpan password</button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>