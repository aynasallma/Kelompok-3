<?php
$activeNav = 'beranda';
require __DIR__ . '/../layouts/public_header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero" id="beranda">
        <div class="section-shell hero-inner">
            <div class="hero-copy">
                <span class="hero-badge">Klinik Pratama &amp; Rawat Jalan</span>
                <h1>Pelayanan Medis Terpadu &amp; Ramah untuk Seluruh Keluarga</h1>
                <p class="hero-lede">
                    Daftar kunjungan online dengan mudah, cek jadwal praktik dokter yang selalu diperbarui, dan nikmati alur rekam medis elektronik terintegrasi dari ruang periksa hingga apotek tanpa antrean berbelit.
                </p>
                <div class="hero-actions">
                    <a class="button button-primary" href="<?= route('pendaftaran') ?>">
                        Daftar Pasien Baru <?= icon('arrow-right', '', 16) ?>
                    </a>
                    <a class="button button-quiet" href="<?= route('jadwal') ?>">
                        <?= icon('calendar', '', 16) ?> Lihat Jadwal Praktik
                    </a>
                </div>
                <div class="hero-status-shortcut">
                    <a href="<?= route('pendaftaran/status') ?>" class="text-link">Sudah mendaftar sebelumnya? Lacak status verifikasi &amp; antrean Anda &rarr;</a>
                </div>
                <div class="hero-features">
                    <div class="feature-item">
                        <span class="feature-icon"><?= icon('shield-check', '', 16) ?></span>
                        <span>Dokter Resmi Ber-SIP &amp; STR</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon"><?= icon('clipboard-list', '', 16) ?></span>
                        <span>Rekam Medis Elektronik Terpadu</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon"><?= icon('pill', '', 16) ?></span>
                        <span>Apotek &amp; Resep Siap Ambil</span>
                    </div>
                </div>
            </div>

            <div class="hero-card-side">
                <div class="hero-info-card">
                    <div class="info-card-header">
                        <div class="info-card-status-row">
                            <span class="badge-status-open">Operasional Buka</span>
                        </div>
                        <strong class="info-card-title">Pelayanan Rawat Jalan Hari Ini</strong>
                    </div>
                    <div class="info-card-hours">
                        <?= icon('clock', '', 15) ?>
                        <span>Senin &ndash; Sabtu: <strong>08.00 &ndash; 20.00 WIB</strong></span>
                    </div>
                    <p class="info-card-desc">Pelayanan konsultasi medis dan instalasi farmasi aktif melayani pasien.</p>
                    <div class="info-card-grid">
                        <div class="info-pill">
                            <span class="info-pill-title">Poli Umum</span>
                            <span class="info-pill-val">Senin &ndash; Sabtu</span>
                        </div>
                        <div class="info-pill">
                            <span class="info-pill-title">Poli Gigi</span>
                            <span class="info-pill-val">Pemeriksaan &amp; Tindakan</span>
                        </div>
                        <div class="info-pill">
                            <span class="info-pill-title">Poli KIA &amp; Anak</span>
                            <span class="info-pill-val">Tumbuh Kembang &amp; Imunisasi</span>
                        </div>
                        <div class="info-pill">
                            <span class="info-pill-title">Instalasi Apotek</span>
                            <span class="info-pill-val">Obat Lengkap &amp; Konseling</span>
                        </div>
                    </div>
                    <div class="info-card-action">
                        <a href="<?= route('pendaftaran/status') ?>" class="text-link">Cek status pendaftaran atau antrean &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section" id="layanan">
        <div class="section-shell">
            <div class="section-heading">
                <div>
                    <span class="section-badge">Pelayanan Medis</span>
                    <h2>Fasilitas dan Poliklinik Rawat Jalan</h2>
                </div>
                <p class="section-lede">
                    Standar pelayanan kesehatan primer yang komprehensif, ditangani langsung oleh dokter dan tenaga medis berlisensi resmi.
                </p>
            </div>
            <div class="services-grid">
                <article class="service-card">
                    <div class="service-card-icon">
                        <?= icon('stethoscope', '', 22) ?>
                    </div>
                    <h3>Poli Umum &amp; MCU Rutin</h3>
                    <p>Pemeriksaan kesehatan menyeluruh, penanganan keluhan penyakit harian (demam, flu, lambung, hipertensi), serta penerbitan Surat Keterangan Sehat resmi.</p>
                    <ul class="service-subitems">
                        <li>Konsultasi dokter umum</li>
                        <li>Pemeriksaan tensi &amp; fisik rutin</li>
                        <li>Surat keterangan sehat</li>
                    </ul>
                </article>

                <article class="service-card">
                    <div class="service-card-icon">
                        <?= icon('activity', '', 22) ?>
                    </div>
                    <h3>Poli Gigi &amp; Mulut</h3>
                    <p>Perawatan kesehatan gigi keluarga oleh dokter gigi profesional mencakup pembersihan karang gigi (scaling), penambalan estetis, dan pencabutan gigi.</p>
                    <ul class="service-subitems">
                        <li>Scaling / pembersihan karang</li>
                        <li>Penambalan &amp; perawatan saraf gigi</li>
                        <li>Pencabutan gigi sulung &amp; dewasa</li>
                    </ul>
                </article>

                <article class="service-card">
                    <div class="service-card-icon">
                        <?= icon('heart', '', 22) ?>
                    </div>
                    <h3>Kesehatan Ibu, Anak (KIA) &amp; Imunisasi</h3>
                    <p>Pemantauan tumbuh kembang bayi dan balita, imunisasi dasar lengkap, serta konsultasi nutrisi ibu dan pendampingan kesehatan anak.</p>
                    <ul class="service-subitems">
                        <li>Imunisasi wajib &amp; tambahan</li>
                        <li>Penimbangan &amp; evaluasi gizi anak</li>
                        <li>Konsultasi kesehatan ibu hamil</li>
                    </ul>
                </article>

                <article class="service-card">
                    <div class="service-card-icon">
                        <?= icon('pill', '', 22) ?>
                    </div>
                    <h3>Instalasi Farmasi &amp; Apotek</h3>
                    <p>Penyediaan obat resep dan generik berstandar resmi BPOM, disiapkan secara cermat oleh apoteker berijazah dengan penjelasan aturan pakai yang jelas.</p>
                    <ul class="service-subitems">
                        <li>Penebusan resep dokter klinik</li>
                        <li>Konseling cara penggunaan obat</li>
                        <li>Pencegahan risiko interaksi obat</li>
                    </ul>
                </article>

                <article class="service-card">
                    <div class="service-card-icon">
                        <?= icon('clipboard-list', '', 22) ?>
                    </div>
                    <h3>Laboratorium Cek Cepat</h3>
                    <p>Pemeriksaan skrining penunjang sederhana dengan hasil langsung di hari yang sama untuk membantu ketepatan penegakan diagnosis dokter.</p>
                    <ul class="service-subitems">
                        <li>Cek gula darah puasa / sewaktu</li>
                        <li>Cek kolesterol total &amp; asam urat</li>
                        <li>Pemeriksaan urine rutin</li>
                    </ul>
                </article>

                <article class="service-card">
                    <div class="service-card-icon">
                        <?= icon('shield-plus', '', 22) ?>
                    </div>
                    <h3>Tindakan Medis &amp; Rawat Luka</h3>
                    <p>Penanganan luka terbuka, perawatan luka bedah steril, ekstraksi benda asing sederhana, dan pertolongan pertama kedaruratan medis non-kritis.</p>
                    <ul class="service-subitems">
                        <li>Ganti balut steril (wound care)</li>
                        <li>Penjahitan luka robek sederhana</li>
                        <li>Pertolongan pertama gawat darurat</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <!-- Patient Journey / Flow Section -->
    <section class="medical-flow-section" id="alur">
        <div class="section-shell">
            <div class="section-heading">
                <div>
                    <span class="section-badge">Alur Pelayanan Pasien</span>
                    <h2>Empat Langkah Praktis Menuju Pelayanan Medis</h2>
                </div>
                <p class="section-lede">Sistem terintegrasi kami memangkas waktu tunggu sehingga Anda dapat berkonsultasi dengan tertib dan nyaman.</p>
            </div>
            <div class="medical-flow">
                <article class="medical-step">
                    <span class="step-number">1</span>
                    <h3>Pendaftaran Online Mandiri</h3>
                    <p>Pasien mengisi formulir online dari rumah melalui ponsel atau komputer dengan data KTP/identitas tanpa antre loket manual.</p>
                    <span class="step-tag">Praktis dari Rumah</span>
                </article>
                <article class="medical-step">
                    <span class="step-number">2</span>
                    <h3>Verifikasi Petugas &amp; Antrean</h3>
                    <p>Resepsionis memvalidasi data pengajuan secara digital, menetapkan nomor antrean kunjungan, dan konfirmasi poli tujuan.</p>
                    <span class="step-tag">Teratur &amp; Tepat Waktu</span>
                </article>
                <article class="medical-step">
                    <span class="step-number">3</span>
                    <h3>Pemeriksaan &amp; Konsultasi Dokter</h3>
                    <p>Dokter memeriksa keluhan fisik, mencatat riwayat diagnosis pada Rekam Medis Elektronik (RME), dan meresepkan terapi yang tepat.</p>
                    <span class="step-tag">RME Terintegrasi</span>
                </article>
                <article class="medical-step">
                    <span class="step-number">4</span>
                    <h3>Pengambilan Obat di Apotek</h3>
                    <p>Resep dokter langsung tersinkronisasi ke bagian apotek. Pasien menerima obat beserta edukasi aturan pakai tanpa antre tebus luar.</p>
                    <span class="step-tag">Siap Ambil di Tempat</span>
                </article>
            </div>
        </div>
    </section>

    <!-- Standards & Patient Safety Section -->
    <section class="standards-section" id="standar">
        <div class="section-shell">
            <div class="section-heading">
                <div>
                    <span class="section-badge">Standar Mutu Pelayanan</span>
                    <h2>Komitmen Keamanan dan Kenyamanan Pasien</h2>
                </div>
                <p class="section-lede">Pelayanan di Klinik Sehat berpegang teguh pada prinsip keselamatan pasien (patient safety) dan etika profesi kedokteran.</p>
            </div>
            <div class="standards-grid">
                <div class="standard-item">
                    <div class="standard-icon"><?= icon('shield-check', '', 24) ?></div>
                    <h3>Tenaga Medis Berizin Resmi</h3>
                    <p>Seluruh dokter umum, dokter gigi, perawat, dan apoteker memiliki Surat Izin Praktik (SIP) dan Surat Tanda Registrasi (STR) aktif dari dinas kesehatan terkait.</p>
                </div>
                <div class="standard-item">
                    <div class="standard-icon"><?= icon('clipboard-list', '', 24) ?></div>
                    <h3>Rekam Medis Elektronik Terpadu</h3>
                    <p>Riwayat diagnosis, pengobatan terdahulu, dan alergi obat tersimpan rapi dalam sistem terpusat, mencegah duplikasi terapi dan risiko reaksi obat.</p>
                </div>
                <div class="standard-item">
                    <div class="standard-icon"><?= icon('building-2', '', 24) ?></div>
                    <h3>Fasilitas Higienis &amp; Steril</h3>
                    <p>Ruang pemeriksaan, instrumen medis, dan ruang tunggu disterilisasi rutin mengikuti panduan Pencegahan &amp; Pengendalian Infeksi (PPI) faskes.</p>
                </div>
                <div class="standard-item">
                    <div class="standard-icon"><?= icon('check-circle-2', '', 24) ?></div>
                    <h3>Transparansi Biaya &amp; Pelayanan Santun</h3>
                    <p>Biaya konsultasi, tindakan medis, dan tarif obat tertera jelas dan wajar tanpa biaya tersembunyi, didukung oleh staf yang ramah dan siap membantu.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Doctor Section -->
    <section class="doctor-section" id="dokter">
        <div class="section-shell">
            <div class="section-heading">
                <div>
                    <span class="section-badge">Tim Medis</span>
                    <h2>Dokter Profesional yang Siap Mendampingi Anda</h2>
                </div>
                <p class="section-lede">Kenali para dokter berpengalaman kami yang siap memberikan diagnosis terarah dan penanganan yang menenangkan.</p>
            </div>
            <div class="doctor-grid">
                <?php if (!$doctorList): ?>
                    <p class="muted">Belum ada data dokter yang tersedia.</p>
                <?php else: ?>
                    <?php foreach ($doctorList as $doc): ?>
                        <article class="doctor-card">
                            <div class="doctor-card-top">
                                <span class="doctor-poli-tag">Poli <?= e(ucfirst($doc['poli'] === 'Berinunci' ? 'Umum' : $doc['poli'])) ?></span>
                                <span class="doctor-status <?= $doc['is_active'] ? 'available' : 'inactive' ?>">
                                    <?= $doc['is_active'] ? 'Praktik Aktif' : 'Nonaktif' ?>
                                </span>
                            </div>

                            <div class="doctor-avatar">
                                <span class="avatar-initial"><?= strtoupper(substr($doc['nama'], 0, 1)) ?></span>
                            </div>

                            <div class="doctor-card-body">
                                <h3 class="doctor-name"><?= e($doc['nama']) ?></h3>
                                <p class="doctor-role">Dokter <?= e(ucfirst($doc['poli'] === 'Berinunci' ? 'Umum' : $doc['poli'])) ?></p>

                                <div class="doctor-schedule-box">
                                    <div class="schedule-row">
                                        <span class="label">Hari Praktik:</span>
                                        <span class="val"><?= e($doc['hari'] === '-' ? 'Sesuai Reservasi' : $doc['hari']) ?></span>
                                    </div>
                                    <div class="schedule-row">
                                        <span class="label">Jam:</span>
                                        <span class="val"><?= e($doc['jam_mulai'] !== '-' ? $doc['jam_mulai'] . ' – ' . $doc['jam_selesai'] . ' WIB' : 'Jadwal Mengikuti Sesi') ?></span>
                                    </div>
                                    <div class="schedule-row">
                                        <span class="label">Ruang:</span>
                                        <span class="val"><?= e($doc['lokasi'] === '-' ? 'Ruang Pemeriksaan 1' : $doc['lokasi']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="doctor-card-footer">
                                <a class="button button-quiet btn-block" href="<?= route('jadwal') ?>">Lihat Jadwal Lengkap</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Schedule Preview Section -->
    <section class="schedule-section" id="jadwal">
        <div class="section-shell">
            <div class="section-heading">
                <div>
                    <span class="section-badge">Jadwal Praktik Dokter</span>
                    <h2>Waktu Pelayanan Praktik Terdekat</h2>
                    <p class="muted" style="margin-top: 6px;">Tanggal Tersedia: <strong><?= e(tgl($bookingDate)) ?></strong></p>
                </div>
                <a class="text-link" href="<?= route('jadwal') ?>">Buka seluruh jadwal dokter &rarr;</a>
            </div>

            <?php if ($jadwal): ?>
                <div class="schedule-preview">
                    <?php foreach ($jadwal as $item): ?>
                        <article class="schedule-preview-item">
                            <span class="preview-day"><?= e($item['hari']) ?></span>
                            <div class="preview-info">
                                <strong><?= e($item['nama_dokter']) ?></strong>
                                <small>Poli <?= e($item['poli']) ?> &bull; <?= e($item['lokasi']) ?></small>
                            </div>
                            <time class="preview-time"><?= e(substr($item['jam_mulai'], 0, 5)) ?> &ndash; <?= e(substr($item['jam_selesai'], 0, 5)) ?> WIB</time>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="schedule-empty">
                    <p>Jadwal praktik untuk tanggal ini sedang disesuaikan oleh tim administrasi medis klinik.</p>
                    <a class="text-link" href="<?= route('jadwal') ?>">Lihat jadwal hari lain &rarr;</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section" id="faq">
        <div class="section-shell">
            <div class="section-heading">
                <div>
                    <span class="section-badge">Tanya Jawab Pasien</span>
                    <h2>Pertanyaan yang Sering Diajukan (FAQ)</h2>
                </div>
                <p class="section-lede">Informasi seputar pendaftaran online, berkas persyaratan, dan pengambilan obat di klinik.</p>
            </div>
            <div class="faq-grid">
                <article class="faq-card">
                    <h3>Apakah saya wajib mendaftar online sebelum datang ke klinik?</h3>
                    <p>Pendaftaran online sangat dianjurkan agar Anda mendapat kepastian kuota dokter dan nomor antrean lebih awal. Namun, klinik kami tetap melayani pendaftaran langsung di loket resepsionis selama kuota harian dokter masih tersedia.</p>
                </article>
                <article class="faq-card">
                    <h3>Berapa lama proses verifikasi pendaftaran online oleh staf klinik?</h3>
                    <p>Petugas resepsionis memverifikasi data formulir pasien dalam waktu 15&ndash;30 menit pada jam operasional klinik. Anda dapat mengecek status persetujuan secara mandiri melalui menu Lacak Status Pendaftaran.</p>
                </article>
                <article class="faq-card">
                    <h3>Dokumen apa saja yang perlu saya bawa saat hari kunjungan?</h3>
                    <p>Cukup bawa kartu identitas resmi yang masih berlaku (KTP / KK / KIA / SIM) dan tunjukkan kode nomor pendaftaran online yang telah dikonfirmasi ke petugas loket kami saat tiba di klinik.</p>
                </article>
                <article class="faq-card">
                    <h3>Apakah obat resep dari dokter bisa langsung diambil di klinik?</h3>
                    <p>Ya. Instalasi farmasi kami terintegrasi langsung dengan komputer dokter pemeriksa. Begitu sesi konsultasi selesai, resep obat langsung diterima apoteker dan disiapkan sehingga Anda tidak perlu menebus obat di luar.</p>
                </article>
                <article class="faq-card">
                    <h3>Bagaimana jika dokter berhalangan hadir atau ada perubahan jadwal?</h3>
                    <p>Setiap perubahan jadwal dan hari libur operasional klinik selalu diperbarui secara real-time pada halaman Jadwal Dokter di situs ini, atau Anda dapat menghubungi nomor layanan WhatsApp kami untuk konfirmasi cepat.</p>
                </article>
                <article class="faq-card">
                    <h3>Apakah klinik melayani pemeriksaan untuk anak-anak dan lansia?</h3>
                    <p>Ya, klinik kami memiliki Poli KIA &amp; Anak serta layanan Poli Umum yang ramah bagi seluruh kelompok usia, didukung oleh fasilitas ruang tunggu yang nyaman dan akses ramah disabilitas.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- Operational Hours, Location & Contact -->
    <section class="contact-info-section" id="kontak">
        <div class="section-shell">
            <div class="section-heading">
                <div>
                    <span class="section-badge">Informasi Operasional &amp; Lokasi</span>
                    <h2>Kunjungi Klinik Kami atau Hubungi Layanan Bantuan</h2>
                </div>
                <p class="section-lede">Lokasi strategis di pusat kota dengan akses mudah, fasilitas parkir memadai, dan kontak bantuan yang responsif.</p>
            </div>

            <div class="contact-cards-grid">
                <div class="contact-card">
                    <div class="contact-card-icon"><?= icon('clock', '', 22) ?></div>
                    <h3>Jam Pelayanan Klinik</h3>
                    <div class="contact-detail-list">
                        <div class="detail-row">
                            <span class="detail-label">Senin &ndash; Jumat</span>
                            <span class="detail-value">08.00 &ndash; 20.00 WIB</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Sabtu</span>
                            <span class="detail-value">08.00 &ndash; 17.00 WIB</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Minggu &amp; Libur Nasional</span>
                            <span class="detail-value text-muted">Tutup / Layanan Terjadwal</span>
                        </div>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-card-icon"><?= icon('building-2', '', 22) ?></div>
                    <h3>Lokasi &amp; Aksesibilitas</h3>
                    <div class="contact-detail-list">
                        <p class="address-text"><strong>Klinik Sehat Pratama</strong><br>Jl. Sehat Utama No. 12, Pusat Kota, Sejahtera 10110</p>
                        <p class="accessibility-note">Akses mudah dari jalan protokol, tersedia area parkir motor/mobil luas, serta ramah pengguna kursi roda.</p>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-card-icon"><?= icon('phone', '', 22) ?></div>
                    <h3>Kontak &amp; Layanan Pasien</h3>
                    <div class="contact-detail-list">
                        <div class="detail-row">
                            <span class="detail-label">Telepon Loket</span>
                            <span class="detail-value">(021) 555-0192</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">WhatsApp Petugas</span>
                            <span class="detail-value">0812-3456-7890</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email Informasi</span>
                            <span class="detail-value">layanan@kliniksehat.local</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final Call to Action -->
    <section class="cta-banner-section">
        <div class="section-shell cta-inner">
            <div class="cta-copy">
                <h2>Siap Melakukan Pemeriksaan Kesehatan?</h2>
                <p>Kirimkan data pendaftaran online Anda sekarang untuk mengamankan nomor antrean dan mempermudah kedatangan di klinik.</p>
            </div>
            <div class="cta-buttons">
                <a class="button button-primary" href="<?= route('pendaftaran') ?>">
                    Daftar Pasien Baru <?= icon('arrow-right', '', 16) ?>
                </a>
                <a class="button button-quiet" href="<?= route('pendaftaran/status') ?>">
                    Lacak Status Antrean
                </a>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/public_footer.php'; ?>
