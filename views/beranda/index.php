<?php require __DIR__ . '/../layouts/public_header.php'; ?>

<main>
    <section class="hero" id="beranda">
        <img class="hero-photo" src="https://images.unsplash.com/photo-1594824476967-48c8b964273f?auto=format&amp;fit=crop&amp;w=1900&amp;q=88" alt="Dokter Klinik Sehat siap memberikan pelayanan" fetchpriority="high">
        <div class="hero-inner">
            <div class="hero-copy reveal">
                <p class="eyebrow">Klinik keluarga di dekat Anda</p>
                <h1>Klinik Sehat<span>Perawatan yang dimulai dengan perhatian.</span></h1>
                <p class="hero-lede">Temukan jadwal dokter, kenali layanan klinik, dan kirim pendaftaran pasien baru dari rumah.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="<?= route('pendaftaran') ?>">Daftar sebagai pasien</a>
                    <a class="button button-quiet" href="<?= route('jadwal') ?>">Lihat jadwal dokter</a>
                </div>
                <p class="hero-note">Pengajuan akan ditinjau oleh tim resepsionis sebelum kunjungan.</p>
            </div>
        </div>
    </section>

    <section class="services-section" id="layanan">
        <div class="section-shell">
            <div class="section-heading reveal">
                <div><p class="eyebrow">Layanan klinik</p><h2>Perawatan yang terhubung, dari awal hingga selesai.</h2></div>
                <p class="section-lede">Informasi yang jelas membantu Anda menyiapkan kunjungan dengan lebih tenang.</p>
            </div>
            <div class="service-list">
                <article class="service-item reveal">
                    <span class="service-number">01</span>
                    <div><h3>Pendaftaran pasien baru</h3><p>Kirim data pasien secara online. Resepsionis atau admin akan meninjau pengajuan sebelum membuat data rekam pasien.</p></div>
                    <a href="<?= route('pendaftaran') ?>">Mulai pendaftaran</a>
                </article>
                <article class="service-item reveal">
                    <span class="service-number">02</span>
                    <div><h3>Konsultasi dan layanan poli</h3><p>Pilih jadwal praktik dan poli yang sesuai. Konfirmasi waktu kunjungan setelah pengajuan pasien disetujui.</p></div>
                    <a href="<?= route('jadwal') ?>">Lihat jadwal dokter</a>
                </article>
                <article class="service-item reveal">
                    <span class="service-number">03</span>
                    <div><h3>Rekam medis dan resep</h3><p>Riwayat pemeriksaan dan pengelolaan obat dilakukan oleh petugas klinik melalui sistem internal yang memerlukan login.</p></div>
                    <a href="<?= route('auth/login') ?>">Login staf klinik</a>
                </article>
            </div>
        </div>
    </section>

    <section class="intro-section" id="tentang">
        <div class="section-shell intro-grid">
            <div class="intro-copy reveal">
                <p class="eyebrow">Tentang Klinik Sehat</p>
                <h2>Pelayanan kesehatan yang mendengarkan kebutuhan Anda.</h2>
                <p>Klinik Sehat menyatukan pendaftaran, jadwal dokter, kunjungan, rekam medis, dan resep dalam satu alur pelayanan. Setiap pengajuan pasien baru ditinjau oleh petugas sebelum diteruskan ke proses kunjungan.</p>
                <p>Informasi jadwal praktik dapat dilihat tanpa login. Data pribadi dan catatan kesehatan hanya dapat diakses oleh staf yang memiliki akun dan kewenangan sesuai perannya.</p>
                <a class="text-link" href="<?= route('pendaftaran') ?>">Pelajari alur pendaftaran</a>
            </div>
            <div class="intro-image reveal">
                <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&amp;fit=crop&amp;w=1200&amp;q=85" alt="Ruang pelayanan klinik yang tertata dan nyaman" loading="lazy">
                <p class="image-caption">Ruang pelayanan Klinik Sehat</p>
            </div>
        </div>
    </section>

    <section class="schedule-section" id="jadwal">
        <div class="section-shell">
            <div class="section-heading reveal">
                <div>
                    <p class="eyebrow">Waktu kunjungan</p>
                    <h2>Jadwal praktik berikutnya</h2>
                    <p class="muted"><?= e(tgl($bookingDate)) ?></p>
                </div>
                <a class="text-link" href="<?= route('jadwal') ?>">Lihat semua jadwal</a>
            </div>
            <?php if ($jadwal): ?>
                <div class="schedule-preview">
                    <?php foreach ($jadwal as $item): ?>
                        <article class="schedule-preview-item reveal">
                            <span class="preview-day"><?= e($item['hari']) ?></span>
                            <div><strong><?= e($item['nama_dokter']) ?></strong><small><?= e($item['poli']) ?> · <?= e($item['lokasi']) ?></small></div>
                            <time><?= e(substr($item['jam_mulai'], 0, 5)) ?>–<?= e(substr($item['jam_selesai'], 0, 5)) ?></time>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="schedule-empty reveal"><p>Jadwal praktik sedang diperbarui oleh tim klinik.</p><a href="<?= route('jadwal') ?>">Buka halaman jadwal</a></div>
            <?php endif; ?>
        </div>
    </section>

    <section class="contact-section" id="kontak">
        <div class="section-shell contact-inner reveal">
            <div><p class="eyebrow">Langkah berikutnya</p><h2>Tim resepsionis siap membantu proses pendaftaran Anda.</h2><p>Setelah pengajuan dikirim, simpan kode pendaftaran untuk memeriksa status. Jika disetujui, resepsionis akan membantu melanjutkan proses kunjungan.</p></div>
            <div class="contact-actions"><a class="button button-primary" href="<?= route('pendaftaran') ?>">Daftar pasien baru</a><a class="text-link" href="<?= route('pendaftaran/status') ?>">Lacak status pengajuan</a></div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/public_footer.php'; ?>