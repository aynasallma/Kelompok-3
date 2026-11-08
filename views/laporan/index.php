<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Laporan Kunjungan &amp; Pelayanan</h2>
        <p class="muted">Rekapitulasi kunjungan pasien, rekam medis, dan transaksi resep pada rentang tanggal tertentu.</p>
    </div>
</div>

<div class="panel report-filter-panel">
    <form method="get" action="<?= BASE_URL ?>/index.php" class="row-form staff-inline-grid">
        <input type="hidden" name="url" value="laporan">
        <div>
            <label for="report-from">Dari tanggal <span class="required">*</span></label>
            <input id="report-from" type="date" name="from" value="<?= e($from) ?>" required>
        </div>
        <div>
            <label for="report-to">Sampai tanggal <span class="required">*</span></label>
            <input id="report-to" type="date" name="to" value="<?= e($to) ?>" required>
        </div>
        <button class="btn btn-primary">Tampilkan laporan</button>
        <a class="btn" href="<?= route('laporan/pdf') ?>&amp;from=<?= e($from) ?>&amp;to=<?= e($to) ?>">Unduh PDF</a>
    </form>
</div>

<div class="cards report-cards">
    <div class="card"><span class="muted">Pasien baru</span><strong><?= (int) $report['newPatients'] ?></strong></div>
    <div class="card"><span class="muted">Kunjungan</span><strong><?= (int) $report['visits'] ?></strong></div>
    <div class="card"><span class="muted">Rekam medis</span><strong><?= (int) $report['medicalRecords'] ?></strong></div>
    <div class="card"><span class="muted">Resep diterbitkan</span><strong><?= (int) $report['prescriptions'] ?></strong></div>
    <div class="card"><span class="muted">Nilai total resep</span><strong class="report-value"><?= rupiah($report['prescriptionValue']) ?></strong><small class="muted">Bukan laporan pembayaran.</small></div>
</div>

<div class="report-columns">
    <div class="panel">
        <h2>Kunjungan berdasarkan status</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Status</th><th class="right">Jumlah</th></tr></thead>
                <tbody>
                    <?php if (!$report['visitsByStatus']): ?><tr><td colspan="2" class="empty">Tidak ada kunjungan pada periode ini.</td></tr><?php endif; ?>
                    <?php foreach ($report['visitsByStatus'] as $row): ?>
                        <tr><td><?= e($row['status']) ?></td><td class="right"><?= (int) $row['jumlah'] ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel">
        <h2>Kunjungan berdasarkan poli</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Poli</th><th class="right">Jumlah</th></tr></thead>
                <tbody>
                    <?php if (!$report['visitsByPoli']): ?><tr><td colspan="2" class="empty">Tidak ada kunjungan pada periode ini.</td></tr><?php endif; ?>
                    <?php foreach ($report['visitsByPoli'] as $row): ?>
                        <tr><td><?= e($row['poli']) ?></td><td class="right"><?= (int) $row['jumlah'] ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>