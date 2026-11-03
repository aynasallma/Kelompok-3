<?php require __DIR__ . '/../layouts/public_header.php'; ?>

<main class="application-page">
    <div class="application-shell">
        <section class="application-intro">
            <p class="eyebrow">Pendaftaran pasien baru</p>
            <h1>Mulai perawatan Anda dengan langkah yang tepat.</h1>
            <p>Lengkapi data berikut. Tim resepsionis akan meninjau pengajuan sebelum data pasien dibuat. Pengajuan belum menjadi janji temu.</p>
            <div class="application-process">
                <div><span>01</span><strong>Kirim data</strong><small>Isi informasi pasien dengan benar.</small></div>
                <div><span>02</span><strong>Verifikasi staf</strong><small>Resepsionis atau admin memeriksa pengajuan.</small></div>
                <div><span>03</span><strong>Lanjutkan pendaftaran</strong><small>Setelah disetujui, staf membantu jadwal kunjungan.</small></div>
            </div>
        </section>

        <section class="application-form-section">
            <div class="application-form-heading">
                <h2>Data pasien</h2>
                <p>Kolom bertanda wajib diisi diperlukan untuk proses verifikasi.</p>
            </div>
            <?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>
            <form method="post" action="<?= route('pendaftaran/kirim') ?>" class="application-form" data-options-url="<?= route('pendaftaran/pilihan') ?>" autocomplete="on">
                <?= csrf_field() ?>
                <div class="website-field" aria-hidden="true"><label for="website">Website</label><input id="website" type="text" name="website" tabindex="-1" autocomplete="off"></div>
                <div class="application-fields">
                    <div class="field-wide">
                        <label for="nama_pasien">Nama lengkap</label>
                        <input id="nama_pasien" type="text" name="nama_pasien" maxlength="100" autocomplete="name" required>
                    </div>
                    <div>
                        <label for="tanggal_kunjungan">Tanggal kunjungan</label>
                        <input id="tanggal_kunjungan" type="date" name="tanggal_kunjungan" value="<?= e($bookingDate) ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label for="poli">Poli tujuan</label>
                        <select id="poli" name="poli" required <?= !$poliOptions ? 'disabled' : '' ?>>
                            <option value="">Pilih poli</option>
                            <?php foreach ($poliOptions as $poli): ?>
                                <option value="<?= e($poli) ?>"><?= e($poli) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="field-help" id="registration-closed" <?= $closedMessage ? '' : 'hidden' ?>><?= e($closedMessage) ?></small>
                    </div>
                    <div>
                        <label for="jenis_kelamin">Jenis kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="tanggal_lahir">Tanggal lahir</label>
                        <input id="tanggal_lahir" type="date" name="tanggal_lahir" max="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="field-wide">
                        <label for="no_hp">Nomor telepon aktif</label>
                        <input id="no_hp" type="tel" name="no_hp" maxlength="20" autocomplete="tel" inputmode="tel" required>
                        <small class="field-help">Nomor ini digunakan resepsionis untuk menghubungi Anda.</small>
                    </div>
                    <div class="field-wide">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" maxlength="1000" autocomplete="street-address"></textarea>
                    </div>
                </div>
                <label class="consent-field"><input type="checkbox" name="persetujuan" value="1" required><span>Saya menyetujui data ini digunakan untuk memproses pendaftaran pasien.</span></label>
                <div class="application-submit">
                    <button class="button button-primary" type="submit" <?= !$poliOptions ? 'disabled' : '' ?>>Kirim pendaftaran</button>
                    <a href="<?= route('pendaftaran/status') ?>">Sudah mendaftar? Lacak status</a>
                </div>
            </form>
        </section>
    </div>
</main>

<?php require __DIR__ . '/../layouts/public_footer.php'; ?>