<?php
require __DIR__ . '/../layouts/header.php';
$v = function ($key, $default = '') use ($item) {
    return e(old($key, $item[$key] ?? $default));
};
?>

<div class="panel">
    <h2><?= e($title) ?></h2>
    <p class="muted" style="margin-bottom: 20px;">Kelola akun staf klinik, hak akses peranan, dan masa aktif login.</p>

    <form method="post" action="<?= $action ?>" class="user-form" novalidate>
        <?= csrf_field() ?>

        <div>
            <label for="nama">Nama Lengkap Pengguna <span class="required">*</span></label>
            <input id="nama" type="text" name="nama" value="<?= $v('nama') ?>" maxlength="100" placeholder="Nama lengkap beserta gelar jika ada" required>
        </div>

        <div>
            <label for="username">Username Login <span class="required">*</span></label>
            <input id="username" type="text" name="username" value="<?= $v('username') ?>" maxlength="50" autocomplete="username" placeholder="Contoh: dr_budi" required>
            <small class="muted">Hanya huruf, angka, dan underscore (_)</small>
        </div>

        <div>
            <label for="password">Password <?= $item ? '(kosongkan jika tidak ingin mengubah)' : '<span class="required">*</span>' ?></label>
            <input id="password" type="password" name="password" minlength="12" autocomplete="new-password" placeholder="Minimal 12 karakter aman" <?= $item ? '' : 'required' ?>>
            <small class="muted">Standar keamanan: minimal 12 karakter</small>
        </div>

        <div>
            <label for="role">Hak Akses (Role) <span class="required">*</span></label>
            <select id="role" name="role" required>
                <?php
                $curRole = $v('role', 'admin');
                $roleList = [
                    'admin' => 'Admin (Pengelola Sistem)',
                    'resepsionis' => 'Resepsionis (Pendaftaran & Antrean)',
                    'dokter' => 'Dokter (Pemeriksaan & Resep)',
                    'apoteker' => 'Apoteker (Farmasi & Penyerahan Obat)'
                ];
                foreach ($roleList as $val => $lbl):
                ?>
                    <option value="<?= e($val) ?>" <?= $curRole === $val ? 'selected' : '' ?>><?= e($lbl) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="aktif_mulai">Masa Aktif Mulai <span class="required">*</span></label>
            <input id="aktif_mulai" type="date" name="aktif_mulai" value="<?= $v('aktif_mulai', $activeFrom) ?>" required>
        </div>

        <div>
            <label for="aktif_sampai">Masa Aktif Sampai <span class="required">*</span></label>
            <input id="aktif_sampai" type="date" name="aktif_sampai" value="<?= $v('aktif_sampai', $activeUntil) ?>" required>
        </div>

        <div class="form-actions" style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
            <a class="btn" href="<?= route('pengguna') ?>">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
