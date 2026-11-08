<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Pengaturan Akun</h2>
        <p class="muted">Kelola kredensial dan kata sandi akun staf Anda.</p>
    </div>
</div>

<div class="panel password-panel">
    <h2>Ubah Password Akun</h2>
    <p class="muted" style="margin-bottom: 20px;">
        Jaga keamanan akun Anda dengan memperbarui password secara berkala. Gunakan kombinasi minimal 12 karakter unik.
    </p>

    <form method="post" action="<?= route('akun/password') ?>" class="password-form staff-form" autocomplete="off" novalidate>
        <?= csrf_field() ?>

        <div class="staff-form-grid">
            <div class="field-wide">
                <label for="password_lama">Password Saat Ini <span class="required">*</span></label>
                <input id="password_lama" type="password" name="password_lama" autocomplete="current-password" placeholder="Masukkan password lama" required>
            </div>

            <div>
                <label for="password_baru">Password Baru <span class="required">*</span></label>
                <input id="password_baru" type="password" name="password_baru" minlength="12" maxlength="72" autocomplete="new-password" placeholder="Minimal 12 karakter" required>
                <small class="muted">Gunakan huruf besar, huruf kecil, angka, dan simbol</small>
            </div>

            <div>
                <label for="konfirmasi_password">Ulangi Password Baru <span class="required">*</span></label>
                <input id="konfirmasi_password" type="password" name="konfirmasi_password" minlength="12" maxlength="72" autocomplete="new-password" placeholder="Ketik ulang password baru" required>
            </div>
        </div>

        <div class="form-actions" style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
