<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Rekam Medis Pasien</h2>
        <p class="muted">Catatan riwayat diagnosis, pemeriksaan medis, dan tindakan klinis pasien.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-primary" href="<?= route('rekam_medis/create') ?>">
            <?= icon('plus', '', 16) ?> Tambah Rekam Medis
        </a>
    </div>
</div>

<div class="toolbar">
    <form method="get" action="<?= BASE_URL ?>/index.php" class="search">
        <input type="hidden" name="url" value="rekam_medis">
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari nama pasien, No. RM, atau diagnosa...">
        <button type="submit" class="btn">
            <?= icon('search', '', 15) ?> Cari
        </button>
        <?php if ($search !== ''): ?>
            <a href="<?= route('rekam_medis') ?>" class="btn">Reset</a>
        <?php endif; ?>
    </form>
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
                <a class="icon-btn icon-btn-primary" href="<?= route('rekam_medis/show/' . (int) $rm['id_rekam_medis']) ?>" title="Detail Rekam Medis">
                    <?= icon('eye', '', 15) ?>
                </a>
                <a class="icon-btn icon-btn-edit" href="<?= route('rekam_medis/edit/' . (int) $rm['id_rekam_medis']) ?>" title="Edit Rekam Medis">
                    <?= icon('edit-3', '', 15) ?>
                </a>
                <?php if (has_role('admin')): ?>
                    <form method="post" action="<?= route('rekam_medis/delete/' . (int) $rm['id_rekam_medis']) ?>" class="inline"
                          data-confirm="Hapus rekam medis ini beserta resepnya?">
                        <?= csrf_field() ?>
                        <button class="icon-btn icon-btn-danger" type="submit" title="Hapus Rekam Medis">
                            <?= icon('trash', '', 15) ?>
                        </button>
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
