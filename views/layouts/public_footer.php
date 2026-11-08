<footer class="site-footer">
    <div class="section-shell footer-inner-rich">
        <div class="footer-col-brand">
            <a class="site-brand footer-brand" href="<?= route('beranda') ?>">
                <span class="brand-emblem-svg"><?= icon('shield-plus', '', 20) ?></span>
                <span class="site-brand-text">Klinik Sehat <span>Pelayanan kesehatan keluarga</span></span>
            </a>
            <p class="footer-desc">Klinik pratama terpercaya yang menyediakan pelayanan rawat jalan terpadu, konsultasi dokter berlisensi, instalasi farmasi lengkap, serta rekam medis elektronik aman.</p>
            <div class="footer-badge-item">
                <?= icon('shield-check', '', 15) ?>
                <span>Fasilitas Pelayanan Kesehatan Resmi Terdaftar</span>
            </div>
        </div>

        <div class="footer-col">
            <h4>Poliklinik &amp; Layanan</h4>
            <ul class="footer-links">
                <li><a href="<?= route('beranda') ?>#layanan">Poli Umum &amp; MCU</a></li>
                <li><a href="<?= route('beranda') ?>#layanan">Poli Gigi &amp; Mulut</a></li>
                <li><a href="<?= route('beranda') ?>#layanan">Poli KIA &amp; Imunisasi</a></li>
                <li><a href="<?= route('beranda') ?>#layanan">Instalasi Farmasi &amp; Apotek</a></li>
                <li><a href="<?= route('beranda') ?>#layanan">Laboratorium Cek Cepat</a></li>
                <li><a href="<?= route('beranda') ?>#layanan">Tindakan Medis &amp; Rawat Luka</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Akses Pasien</h4>
            <ul class="footer-links">
                <li><a href="<?= route('pendaftaran') ?>">Pendaftaran Pasien Baru</a></li>
                <li><a href="<?= route('pendaftaran/status') ?>">Lacak Status Pendaftaran</a></li>
                <li><a href="<?= route('jadwal') ?>">Jadwal Praktik Dokter</a></li>
                <li><a href="<?= route('beranda') ?>#alur">Alur Kunjungan Pasien</a></li>
                <li><a href="<?= route('beranda') ?>#faq">Tanya Jawab Pasien (FAQ)</a></li>
                <li><a href="<?= route('auth/login') ?>">Portal Login Staf Medis</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Jam Pelayanan &amp; Kontak</h4>
            <p class="footer-info-text">
                <strong>Senin &ndash; Jumat:</strong> 08.00 &ndash; 20.00 WIB<br>
                <strong>Sabtu:</strong> 08.00 &ndash; 17.00 WIB<br>
                <strong>Minggu &amp; Libur:</strong> Tutup
            </p>
            <p class="footer-info-text" style="margin-top: 10px;">
                <strong>Lokasi:</strong> Jl. Sehat Utama No. 12, Pusat Kota<br>
                <strong>Telepon:</strong> (021) 555-0192<br>
                <strong>WhatsApp:</strong> 0812-3456-7890
            </p>
        </div>
    </div>

    <div class="footer-bottom-bar">
        <div class="section-shell footer-bottom-inner">
            <span>&copy; <?= date('Y') ?> <strong>Klinik Sehat</strong>. Seluruh hak cipta dilindungi undang-undang.</span>
            <div class="footer-bottom-links">
                <a href="<?= route('beranda') ?>">Beranda</a>
                <a href="<?= route('jadwal') ?>">Jadwal Dokter</a>
                <a href="<?= route('auth/login') ?>">Staf Medis</a>
            </div>
        </div>
    </div>
</footer>
<script src="<?= asset('js/landing.js') ?>"></script>
</body>
</html>
