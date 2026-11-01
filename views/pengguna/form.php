<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="panel">
    <form method="post" action="<?= $action ?>" class="user-form">
        <?= csrf_field() ?>
        <div>
            <label for="nama">Nama</label>
            <input id="nama" type="text" name="nama" value="<?= e($item['nama'] ?? '') ?>" maxlength="100" required>
        </div>
        <div>
            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="<?= e($item['username'] ?? '') ?>" maxlength="50" autocomplete="username" required>
        </div>
        <div>
            <label for="password">Password<?= $item ? ' (kosongkan jika tidak diubah)' : '' ?></label>
            <input id="password" type="password" name="password" minlength="12" autocomplete="new-password" <?= $item ? '' : 'required' ?>>
        </div>
        <div>
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <?php foreach (['admin' => 'Admin', 'resepsionis' => 'Resepsionis', 'dokter' => 'Dokter', 'apoteker' => 'Apoteker'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= ($item['role'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="aktif_mulai">Masa aktif mulai</label>
            <input id="aktif_mulai" type="date" name="aktif_mulai" value="<?= e($activeFrom) ?>" required>
        </div>
        <div>
            <label for="aktif_sampai">Masa aktif sampai</label>
            <input id="aktif_sampai" type="date" name="aktif_sampai" value="<?= e($activeUntil) ?>" required>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary">Simpan</button>
            <a class="btn" href="<?= route('pengguna') ?>">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>