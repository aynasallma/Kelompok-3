<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="toolbar">
    <span></span>
    <?php if (has_role('admin', 'dokter')): ?>
        <a class="btn btn-primary" href="<?= route('resep/create') ?>">+ Buat Resep</a>
    <?php endif; ?>
</div>

<div class="panel">
<div class="table-wrap">
<table>
    <thead>
        <tr><th>#</th><th>Tanggal</th><th>No. RM</th><th>Pasien</th><th>Diagnosa</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php if (!$items): ?>
        <tr><td colspan="8" class="empty">Belum ada resep.</td></tr>
    <?php endif; ?>
    <?php foreach ($items as $r): ?>
        <tr>
            <td><?= (int) $r['id_resep'] ?></td>
            <td><?= tgl($r['tanggal_resep']) ?></td>
            <td><?= e($r['no_rm']) ?></td>
            <td><?= e($r['nama_pasien']) ?></td>
            <td><?= e($r['diagnosa']) ?></td>
            <td><?= rupiah($r['total']) ?></td>
            <td><span class="status status-<?= strtolower(e($r['status'])) ?>"><?= e($r['status']) ?></span></td>
            <td><a class="btn btn-small" href="<?= route('resep/show/' . (int) $r['id_resep']) ?>">Detail</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
