<?php require __DIR__ . '/../layouts/public_header.php'; ?>

<main class="application-page status-page">
    <section class="status-card">
        <?php if ($confirmation): ?>
            <p class="eyebrow">Pengajuan diterima</p>
            <h1>Terima kasih. Data Anda sedang ditinjau.</h1>
            <p>Belum ada data pasien atau janji temu yang dibuat. Simpan nomor pendaftaran ini untuk melihat status pengajuan.</p>
            <div class="reference-code"><span>Nomor pendaftaran</span><code><?= e($confirmation) ?></code></div>
            <p class="status-warning">Kode ini hanya dapat dilihat saat pengiriman. Simpan di tempat pribadi.</p>
        <?php elseif ($lookupStatus === 'Disetujui' && $prescriptionStatus === 'Menunggu'): ?>
            <p class="eyebrow">Resep sedang disiapkan</p>
            <h1>Mohon menunggu di area apotek.</h1>
            <p>Dokter telah membuat resep Anda. Petugas apotek sedang menyiapkan obat sesuai resep.</p>
        <?php elseif ($lookupStatus === 'Disetujui' && $prescriptionStatus === 'Diserahkan'): ?>
            <p class="eyebrow">Resep siap</p>
            <h1>Obat sudah diserahkan.</h1>
            <p>Resep Anda sudah diproses oleh petugas apotek.</p>
        <?php elseif ($lookupStatus === 'Disetujui' && $prescriptionStatus === 'Batal'): ?>
            <p class="eyebrow">Perlu bantuan petugas</p>
            <h1>Resep belum dapat diserahkan.</h1>
            <p>Silakan temui petugas klinik untuk informasi lebih lanjut.</p>
        <?php elseif ($lookupStatus): ?>
            <p class="eyebrow">Status pengajuan</p>
            <h1><?= $lookupStatus === 'Menunggu' ? 'Pengajuan sedang ditinjau.' : ($lookupStatus === 'Disetujui' ? 'Pengajuan Anda disetujui.' : 'Pengajuan belum dapat disetujui.') ?></h1>
            <?php if ($lookupStatus === 'Disetujui'): ?>
                <p>Kunjungan Anda terjadwal <?= e(tgl($appointmentDate)) ?> di <?= e($appointmentPoli) ?>. Dokter sedang menyiapkan pemeriksaan.</p>
            <?php else: ?>
                <p><?= $lookupStatus === 'Ditolak' ? 'Untuk informasi lebih lanjut, silakan hubungi resepsionis.' : 'Resepsionis akan menghubungi nomor yang Anda daftarkan setelah proses peninjauan.' ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p class="eyebrow">Lacak pendaftaran</p>
            <h1>Periksa status pengajuan Anda.</h1>
            <p>Masukkan kode pendaftaran yang ditampilkan setelah formulir dikirim.</p>
        <?php endif; ?>

        <?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>
        <form method="post" action="<?= route('pendaftaran/lacak') ?>" class="tracking-form">
            <?= csrf_field() ?>
            <label for="reference">Nomor pendaftaran</label>
            <input id="reference" type="text" name="reference" minlength="36" maxlength="64" pattern="(REG-[A-F0-9]{32}|[a-f0-9]{64})" autocomplete="off" required>
            <button class="button button-primary" type="submit">Periksa status</button>
        </form>
        <a class="text-link" href="<?= route('pendaftaran') ?>">Kembali ke formulir pendaftaran</a>
    </section>
</main>

<?php require __DIR__ . '/../layouts/public_footer.php'; ?>