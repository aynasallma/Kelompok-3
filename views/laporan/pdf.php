<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 34px 38px; }
        body { color: #23364e; font: 10px DejaVu Sans, sans-serif; }
        .brand { color: #166c70; font-size: 10px; font-weight: bold; letter-spacing: 1px; }
        h1 { margin: 7px 0 3px; color: #102c55; font-size: 21px; }
        .meta { margin-bottom: 20px; color: #62738b; font-size: 9px; }
        h2 { margin: 20px 0 8px; color: #102c55; font-size: 13px; }
        table { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        th, td { padding: 7px 8px; border-bottom: 1px solid #dce5ed; text-align: left; }
        th { background: #edf3f7; color: #31465f; font-size: 9px; }
        .summary td:first-child { width: 72%; }
        .foot { margin-top: 24px; padding-top: 9px; border-top: 1px solid #dce5ed; color: #718198; font-size: 8px; }
    </style>
</head>
<body>
    <div class="brand">KLINIK SEHAT</div>
    <h1>Laporan Klinik</h1>
    <div class="meta">Periode <?= e(tgl($from)) ?> sampai <?= e(tgl($to)) ?> · Dicetak <?= e(date('d/m/Y H:i')) ?></div>

    <table class="summary">
        <tbody>
            <tr><td>Pasien baru</td><td><?= (int) $report['newPatients'] ?></td></tr>
            <tr><td>Kunjungan</td><td><?= (int) $report['visits'] ?></td></tr>
            <tr><td>Rekam medis</td><td><?= (int) $report['medicalRecords'] ?></td></tr>
            <tr><td>Resep diterbitkan</td><td><?= (int) $report['prescriptions'] ?></td></tr>
            <tr><td>Perkiraan nilai total resep</td><td><?= e(rupiah($report['prescriptionValue'])) ?></td></tr>
        </tbody>
    </table>

    <h2>Kunjungan berdasarkan status</h2>
    <table><thead><tr><th>Status</th><th>Jumlah</th></tr></thead><tbody>
        <?php foreach ($report['visitsByStatus'] as $row): ?>
            <tr><td><?= e($row['status']) ?></td><td><?= (int) $row['jumlah'] ?></td></tr>
        <?php endforeach; ?>
    </tbody></table>

    <h2>Kunjungan berdasarkan poli</h2>
    <table><thead><tr><th>Poli</th><th>Jumlah</th></tr></thead><tbody>
        <?php foreach ($report['visitsByPoli'] as $row): ?>
            <tr><td><?= e($row['poli']) ?></td><td><?= (int) $row['jumlah'] ?></td></tr>
        <?php endforeach; ?>
    </tbody></table>
    <p class="foot">Nilai resep bukan laporan pembayaran atau pendapatan.</p>
</body>
</html>