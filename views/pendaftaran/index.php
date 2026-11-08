<?php
$activeNav = 'pendaftaran';
require __DIR__ . '/../layouts/public_header.php';
?>

<main class="application-page">
    <div class="application-shell">
        <section class="application-intro">
            <span class="section-badge">Pendaftaran Pasien Online</span>
            <h1>Mulai pelayanan kesehatan keluarga Anda</h1>
            <p class="application-desc">Lengkapi formulir registrasi mandiri pasien baru atau kunjungan rutin rawat jalan. Tim medis dan resepsionis kami segera menyiapkan rekam medis dan antrean Anda.</p>
            
            <div class="application-process">
                <div class="process-step">
                    <div class="process-icon-wrap"><?= icon('clipboard-list', '', 20) ?></div>
                    <strong>1. Isi Data Pasien</strong>
                    <small>Lengkapi identitas, jadwal kunjungan, dan poli tujuan</small>
                </div>
                <div class="process-step">
                    <div class="process-icon-wrap"><?= icon('shield-check', '', 20) ?></div>
                    <strong>2. Verifikasi Resepsionis</strong>
                    <small>Petugas meninjau kelengkapan &amp; kuota jadwal dokter</small>
                </div>
                <div class="process-step">
                    <div class="process-icon-wrap"><?= icon('activity', '', 20) ?></div>
                    <strong>3. Pelayanan Medis</strong>
                    <small>Hadir langsung di klinik sesuai jadwal yang disetujui</small>
                </div>
            </div>
        </section>

        <section class="application-form-section">
            <div class="application-form-heading">
                <h2>Formulir Data Pasien</h2>
                <p>Pastikan informasi yang dimasukkan sudah sesuai dengan identitas resmi.</p>
            </div>

            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-error"><?= e($msg) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= route('pendaftaran/kirim') ?>" class="application-form" data-options-url="<?= route('pendaftaran/pilihan') ?>" autocomplete="on">
                <?= csrf_field() ?>
                <div class="website-field" aria-hidden="true" style="display: none;">
                    <label for="website">Website</label>
                    <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="application-fields">
                    <div class="field-wide">
                        <label for="nama_pasien">Nama Lengkap Pasien *</label>
                        <input id="nama_pasien" type="text" name="nama_pasien" value="<?= e(old('nama_pasien')) ?>" maxlength="100" placeholder="Masukkan nama sesuai KTP / identitas" autocomplete="name" required>
                    </div>

                    <div>
                        <label for="tanggal_kunjungan">Tanggal Kunjungan *</label>
                        <input id="tanggal_kunjungan" type="date" name="tanggal_kunjungan" value="<?= e(old('tanggal_kunjungan', $bookingDate)) ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div>
                        <label for="poli">Poli Tujuan *</label>
                        <select id="poli" name="poli" required <?= !$poliOptions ? 'disabled' : '' ?>>
                            <option value="">Pilih poli</option>
                            <?php foreach ($poliOptions as $poli): ?>
                                <option value="<?= e($poli) ?>" <?= old('poli') === $poli ? 'selected' : '' ?>><?= e($poli) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="field-help" id="registration-closed" <?= $closedMessage ? '' : 'hidden' ?>><?= e($closedMessage) ?></small>
                    </div>

                    <div>
                        <label for="jenis_kelamin">Jenis Kelamin *</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L" <?= old('jenis_kelamin') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= old('jenis_kelamin') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label for="tanggal_lahir">Tanggal Lahir *</label>
                        <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="<?= e(old('tanggal_lahir')) ?>" max="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="field-wide">
                        <label for="no_hp">Nomor Telepon / WhatsApp Aktif *</label>
                        <input id="no_hp" type="tel" name="no_hp" value="<?= e(old('no_hp')) ?>" maxlength="20" placeholder="Contoh: 081234567890" autocomplete="tel" inputmode="tel" required>
                        <small class="field-help">Nomor ini digunakan untuk konfirmasi status antrean oleh resepsionis.</small>
                    </div>

                    <div class="field-wide">
                        <label for="alamat">Alamat Lengkap</label>
                        <textarea id="alamat" name="alamat" rows="3" maxlength="1000" placeholder="Alamat domisili pasien" autocomplete="street-address"><?= e(old('alamat')) ?></textarea>
                    </div>
                </div>

                <label class="consent-field" for="consent-checkbox">
                    <input type="checkbox" id="consent-checkbox" name="persetujuan" value="1" required>
                    <span class="consent-text">Saya menyatakan data yang diisi benar dan menyetujui penggunaannya untuk pendaftaran klinik.</span>
                </label>

                <div class="application-submit">
                    <button class="button button-primary" type="submit" <?= !$poliOptions ? 'disabled' : '' ?>>Kirim Pendaftaran</button>
                    <a class="text-link" href="<?= route('pendaftaran/status') ?>">Sudah mendaftar sebelumnya? Cek status di sini &rarr;</a>
                </div>
            </form>
        </section>
    </div>
</main>

<?php require __DIR__ . '/../layouts/public_footer.php'; ?>
