<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 34px 40px; }
        body { color: #23364e; font: 10px DejaVu Sans, sans-serif; line-height: 1.5; }
        .brand { color: #166c70; font-size: 10px; font-weight: bold; letter-spacing: 1px; }
        h1 { margin: 7px 0 3px; color: #102c55; font-size: 21px; }
        h2 { margin: 20px 0 8px; color: #102c55; font-size: 13px; }
        .meta { margin-bottom: 18px; color: #62738b; font-size: 9px; }
        table { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        th, td { padding: 7px 8px; border-bottom: 1px solid #dce5ed; text-align: left; vertical-align: top; }
        th { width: 24%; background: #edf3f7; color: #31465f; font-size: 9px; }
        .prescription th { width: auto; }
        .foot { margin-top: 24px; padding-top: 9px; border-top: 1px solid #dce5ed; color: #718198; font-size: 8px; }
    </style>
</head>
<body>
    <div class="brand">KLINIK SEHAT</div>
    <h1>Ringkasan Rekam Medis</h1>
    <div class="meta">Dicetak <?= e(date('d/m/Y H:i')) ?></div>
    <table><tbody>
        <tr><th>No. rekam medis</th><td><?= e($item['no_rm']) ?></td></tr>
        <tr><th>Nama pasien</th><td><?= e($item['nama_pasien']) ?></td></tr>
        <tr><th>Tanggal kunjungan</th><td><?= e(tgl($item['tanggal_kunjungan'])) ?></td></tr>
        <tr><th>Poli dan antrean</th><td><?= e($item['poli']) ?>, antrean <?= (int) $item['no_antrian'] ?></td></tr>
        <tr><th>Dokter</th><td><?= e($item['nama_dokter'] ?? '-') ?></td></tr>
        <tr><th>Keluhan</th><td><?= nl2br(e($item['keluhan'])) ?></td></tr>
        <tr><th>Diagnosa</th><td><?= nl2br(e($item['diagnosa'])) ?></td></tr>
        <tr><th>Tindakan</th><td><?= nl2br(e($item['tindakan'] ?: '-')) ?></td></tr>
        <tr><th>Catatan</th><td><?= nl2br(e($item['catatan'] ?: '-')) ?></td></tr>
    </tbody></table>

    <h2>Resep</h2>
    <?php if (!$prescriptions): ?>
        <p>Belum ada resep untuk kunjungan ini.</p>
    <?php else: ?>
        <?php foreach ($prescriptions as $prescription): ?>
            <p>Tanggal <?= e(tgl($prescription['tanggal_resep'])) ?>, status <?= e($prescription['status']) ?>, total <?= e(rupiah($prescription['total'])) ?></p>
            <table class="prescription">
                <thead><tr><th>Obat</th><th>Dosis</th><th>Aturan pakai</th><th>Jumlah</th></tr></thead>
                <tbody>
                    <?php foreach ($prescription['details'] as $detail): ?>
                        <tr><td><?= e($detail['nama_obat']) ?></td><td><?= e($detail['dosis']) ?></td><td><?= e($detail['aturan_pakai']) ?></td><td><?= (int) $detail['jumlah'] ?> <?= e($detail['satuan']) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>
    <p class="foot">Dokumen ini berisi informasi kesehatan yang bersifat rahasia.</p>
</body>
</html>