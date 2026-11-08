<?php
$activeNav = 'status';
require __DIR__ . '/../layouts/public_header.php';
?>

<main class="application-page status-page">
    <div class="application-shell">
        <section class="status-card">
            <?php if ($confirmation): ?>
                <span class="section-badge">Pengajuan Diterima</span>
                <h1>Terima kasih, data Anda sedang ditinjau</h1>
                <p class="muted">Simpan nomor pendaftaran ini untuk memeriksa status persetujuan kunjungan Anda secara berkala.</p>
                <div class="reference-code">
                    <span>Nomor Registrasi Pasien</span>
                    <code><?= e($confirmation) ?></code>
                </div>
                <p class="status-warning">Gunakan kode di atas pada kolom pencarian di bawah untuk memantau status persetujuan dokter dan resepsionis.</p>
            <?php elseif ($lookupStatus === 'Disetujui' && $prescriptionStatus === 'Menunggu'): ?>
                <span class="section-badge">Resep Sedang Diproses</span>
                <h1>Mohon menunggu di area farmasi</h1>
                <p class="muted">Dokter telah menerbitkan resep Anda. Petugas farmasi saat ini sedang menyiapkan obat.</p>
            <?php elseif ($lookupStatus === 'Disetujui' && $prescriptionStatus === 'Diserahkan'): ?>
                <span class="section-badge">Pelayanan Selesai</span>
                <h1>Obat telah diserahkan</h1>
                <p class="muted">Resep obat Anda telah diserahkan oleh petugas apotek. Semoga lekas sembuh!</p>
            <?php elseif ($lookupStatus === 'Disetujui' && $prescriptionStatus === 'Batal'): ?>
                <span class="section-badge">Perlu Konfirmasi</span>
                <h1>Resep belum dapat diserahkan</h1>
                <p class="muted">Silakan temui petugas resepsionis atau apotek kami untuk bantuan lebih lanjut.</p>
            <?php elseif ($lookupStatus): ?>
                <span class="section-badge">Status Pengajuan</span>
                <h1><?= $lookupStatus === 'Menunggu' ? 'Pengajuan sedang ditinjau' : ($lookupStatus === 'Disetujui' ? 'Pengajuan Anda disetujui!' : 'Pengajuan belum dapat disetujui') ?></h1>
                <?php if ($lookupStatus === 'Disetujui'): ?>
                    <p class="muted">Kunjungan Anda terjadwal tanggal <strong><?= e(tgl($appointmentDate)) ?></strong> di <strong><?= e($appointmentPoli) ?></strong>. Silakan hadir 15 menit sebelum waktu praktik.</p>
                <?php else: ?>
                    <p class="muted"><?= $lookupStatus === 'Ditolak' ? 'Silakan hubungi resepsionis kami untuk informasi persyaratan lebih lanjut.' : 'Petugas kami sedang meninjau pengajuan Anda. Mohon tunggu notifikasi selanjutnya.' ?></p>
                <?php endif; ?>
            <?php else: ?>
                <span class="section-badge">Lacak Status Pendaftaran</span>
                <h1>Periksa status pengajuan pasien</h1>
                <p class="muted">Masukkan kode nomor pendaftaran yang Anda peroleh saat mengirimkan formulir registrasi online.</p>
            <?php endif; ?>

            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-error"><?= e($msg) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= route('pendaftaran/lacak') ?>" class="tracking-form">
                <?= csrf_field() ?>
                <div class="tracking-input-wrapper">
                    <label for="reference">Nomor Pendaftaran</label>
                    <input id="reference" type="text" name="reference" placeholder="Contoh: REG-XXXXXXXXXXXX..." minlength="36" maxlength="64" pattern="(REG-[A-F0-9]{32}|[a-f0-9]{64})" autocomplete="off" required>
                </div>
                <button class="button button-primary" type="submit">Cek Status Sekarang</button>
            </form>

            <div class="status-footer-action">
                <a class="text-link" href="<?= route('pendaftaran') ?>">&larr; Kembali ke formulir pendaftaran baru</a>
            </div>
        </section>
    </div>
</main>

<?php require __DIR__ . '/../layouts/public_footer.php'; ?>
