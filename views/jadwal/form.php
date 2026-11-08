<?php
require __DIR__ . '/../layouts/header.php';
$v = function ($key, $default = '') use ($item) {
    return e(old($key, $item[$key] ?? $default));
};
$selectedDoctor = (int) old('id_user', $item['id_user'] ?? 0);
$selectedDay = (string) old('hari', $item['hari'] ?? '');
?>

<div class="panel">
    <h2><?= e($title) ?></h2>
    <p class="muted" style="margin-bottom: 20px;">Atur hari dan jam praktik dokter spesialis dan umum pada klinik.</p>

    <form method="post" action="<?= $action ?>" class="user-form schedule-form" novalidate>
        <?= csrf_field() ?>

        <div>
            <label for="id_user">Dokter Praktik <span class="required">*</span></label>
            <select id="id_user" name="id_user" required>
                <option value="">-- Pilih Dokter --</option>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= (int) $doctor['id_user'] ?>" <?= $selectedDoctor === (int) $doctor['id_user'] ? 'selected' : '' ?>>
                        <?= e($doctor['nama']) ?>
                    </option>
                <?php endforeach; ?>
                <?php if ($item && !in_array((int) $item['id_user'], array_map(function ($doctor) { return (int) $doctor['id_user']; }, $doctors), true)): ?>
                    <option value="<?= (int) $item['id_user'] ?>" selected><?= e($item['nama_dokter']) ?> (akun nonaktif)</option>
                <?php endif; ?>
            </select>
        </div>

        <div>
            <label for="hari">Hari Praktik <span class="required">*</span></label>
            <select id="hari" name="hari" required>
                <option value="">-- Pilih Hari --</option>
                <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day): ?>
                    <option value="<?= e($day) ?>" <?= $selectedDay === $day ? 'selected' : '' ?>><?= e($day) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="jam_mulai">Jam Mulai <span class="required">*</span></label>
            <input id="jam_mulai" type="time" name="jam_mulai" value="<?= $v('jam_mulai', isset($item['jam_mulai']) ? substr($item['jam_mulai'], 0, 5) : '') ?>" required>
        </div>

        <div>
            <label for="jam_selesai">Jam Selesai <span class="required">*</span></label>
            <input id="jam_selesai" type="time" name="jam_selesai" value="<?= $v('jam_selesai', isset($item['jam_selesai']) ? substr($item['jam_selesai'], 0, 5) : '') ?>" required>
        </div>

        <div>
            <label for="poli">Poliklinik <span class="required">*</span></label>
            <input id="poli" type="text" name="poli" value="<?= $v('poli') ?>" maxlength="100" placeholder="Contoh: Poli Umum, Poli Gigi" required>
        </div>

        <div>
            <label for="lokasi">Lokasi Ruang Praktik <span class="required">*</span></label>
            <input id="lokasi" type="text" name="lokasi" value="<?= $v('lokasi', 'Klinik Utama - Ruang 1') ?>" maxlength="150" required>
        </div>

        <div class="schedule-active-field">
            <label><input type="checkbox" name="is_active" value="1" <?= !isset($item['is_active']) || (int) $item['is_active'] === 1 ? 'checked' : '' ?>> Tampilkan jadwal ini ke publik di website</label>
        </div>

        <div class="form-actions" style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary" <?= !$doctors && !$item ? 'disabled' : '' ?>>Simpan Jadwal</button>
            <a class="btn" href="<?= route('jadwal') ?>">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
