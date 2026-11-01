<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="panel">
    <form method="post" action="<?= $action ?>" class="user-form schedule-form">
        <?= csrf_field() ?>
        <div>
            <label for="id_user">Dokter</label>
            <select id="id_user" name="id_user" required>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= (int) $doctor['id_user'] ?>" <?= (int) ($item['id_user'] ?? 0) === (int) $doctor['id_user'] ? 'selected' : '' ?>><?= e($doctor['nama']) ?></option>
                <?php endforeach; ?>
                <?php if ($item && !in_array((int) $item['id_user'], array_map(function ($doctor) { return (int) $doctor['id_user']; }, $doctors), true)): ?>
                    <option value="<?= (int) $item['id_user'] ?>" selected><?= e($item['nama_dokter']) ?> (akun nonaktif)</option>
                <?php endif; ?>
            </select>
        </div>
        <div>
            <label for="hari">Hari</label>
            <select id="hari" name="hari" required>
                <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day): ?>
                    <option value="<?= e($day) ?>" <?= ($item['hari'] ?? '') === $day ? 'selected' : '' ?>><?= e($day) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="jam_mulai">Jam mulai</label>
            <input id="jam_mulai" type="time" name="jam_mulai" value="<?= e(isset($item['jam_mulai']) ? substr($item['jam_mulai'], 0, 5) : '') ?>" required>
        </div>
        <div>
            <label for="jam_selesai">Jam selesai</label>
            <input id="jam_selesai" type="time" name="jam_selesai" value="<?= e(isset($item['jam_selesai']) ? substr($item['jam_selesai'], 0, 5) : '') ?>" required>
        </div>
        <div>
            <label for="poli">Poli</label>
            <input id="poli" type="text" name="poli" value="<?= e($item['poli'] ?? '') ?>" maxlength="100" required>
        </div>
        <div>
            <label for="lokasi">Lokasi</label>
            <input id="lokasi" type="text" name="lokasi" value="<?= e($item['lokasi'] ?? 'Klinik Utama') ?>" maxlength="150" required>
        </div>
        <div class="schedule-active-field">
            <label><input type="checkbox" name="is_active" value="1" <?= !isset($item['is_active']) || (int) $item['is_active'] === 1 ? 'checked' : '' ?>> Tampilkan ke publik</label>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" <?= !$doctors && !$item ? 'disabled' : '' ?>>Simpan Jadwal</button>
            <a class="btn" href="<?= route('jadwal') ?>">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>