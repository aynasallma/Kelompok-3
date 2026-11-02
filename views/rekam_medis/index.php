<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="toolbar">
    <form method="get" action="<?= BASE_URL ?>/index.php" class="search">
        <input type="hidden" name="url" value="rekam_medis">
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari pasien / No. RM / diagnosa">
        <button class="btn">Cari</button>
    </form>
    <a class="btn btn-primary" href="<?= route('rekam_medis/create') ?>">+ Tambah Rekam Medis</a>
</div>

<div class="panel">
<div class="table-wrap">
<table>
    <thead>
        <tr><th>Tanggal</th><th>No. RM</th><th>Pasien</th><th>Poli</th><th>Diagnosa</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php if (!$items): ?>
        <tr><td colspan="6" class="empty">Belum ada rekam medis.</td></tr>
    <?php endif; ?>
    <?php foreach ($items as $rm): ?>
        <tr>
            <td><?= tgl($rm['tanggal_kunjungan']) ?></td>
            <td><?= e($rm['no_rm']) ?></td>
            <td><?= e($rm['nama_pasien']) ?></td>
            <td><?= e($rm['poli']) ?></td>
            <td><?= e($rm['diagnosa']) ?></td>
            <td class="actions">
                <a class="btn btn-small" href="<?= route('rekam_medis/show/' . (int) $rm['id_rekam_medis']) ?>">Detail</a>
                <a class="btn btn-small" href="<?= route('rekam_medis/edit/' . (int) $rm['id_rekam_medis']) ?>">Edit</a>
                <?php if (has_role('admin')): ?>
                    <form method="post" action="<?= route('rekam_medis/delete/' . (int) $rm['id_rekam_medis']) ?>" class="inline"
                          data-confirm="Hapus rekam medis ini beserta resepnya?">
                        <?= csrf_field() ?>
                        <button class="btn btn-small btn-danger">Hapus</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>