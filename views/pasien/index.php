<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Data Pasien</h2>
        <p class="muted">Daftar seluruh pasien terdaftar, riwayat registrasi, dan pencarian identitas.</p>
    </div>
    <?php if (has_role('admin', 'resepsionis')): ?>
        <div class="page-header-actions">
            <a class="btn btn-primary" href="<?= route('pasien/create') ?>">
                <?= icon('plus', '', 16) ?> Tambah Pasien Baru
            </a>
        </div>
    <?php endif; ?>
</div>

<div class="toolbar">
    <form method="get" action="<?= BASE_URL ?>/index.php" class="patient-filters">
        <input type="hidden" name="url" value="pasien">
        <div class="filter-field">
            <label for="patient-search">Cari pasien</label>
            <input id="patient-search" type="text" name="q" value="<?= e($search) ?>" placeholder="Nama / No. RM">
        </div>
        <div class="filter-field">
            <label for="date-from">Terdaftar dari</label>
            <input id="date-from" type="date" name="from" value="<?= e($dateFrom) ?>">
        </div>
        <div class="filter-field">
            <label for="date-to">Sampai</label>
            <input id="date-to" type="date" name="to" value="<?= e($dateTo) ?>">
        </div>
        <button class="btn btn-primary">
            <?= icon('search', '', 14) ?> Terapkan
        </button>
        <?php if ($search !== '' || $dateFrom !== '' || $dateTo !== ''): ?>
            <a href="<?= route('pasien') ?>" class="btn">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="panel">
<div class="table-wrap">
<table>
    <thead>
        <tr><th>No. RM</th><th>Nama</th><th>JK</th><th>Tgl Lahir</th><th>Terdaftar</th><th>No. HP</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php if (!$items): ?>
        <tr><td colspan="7" class="empty">Data pasien tidak ditemukan.</td></tr>
    <?php endif; ?>
    <?php foreach ($items as $p): ?>
        <tr>
            <td><?= e($p['no_rm']) ?></td>
            <td><?= e($p['nama_pasien']) ?></td>
            <td><?= e($p['jenis_kelamin']) ?></td>
            <td><?= tgl($p['tanggal_lahir']) ?></td>
            <td><?= tgl($p['created_at']) ?></td>
            <td><?= e($p['no_hp']) ?></td>
            <td class="actions">
                <a class="icon-btn icon-btn-primary" href="<?= route('pasien/show/' . (int) $p['id_pasien']) ?>" title="Detail Pasien">
                    <?= icon('eye', '', 15) ?>
                </a>
                <?php if (has_role('admin', 'resepsionis')): ?>
                    <a class="icon-btn icon-btn-edit" href="<?= route('pasien/edit/' . (int) $p['id_pasien']) ?>" title="Edit Pasien">
                        <?= icon('edit-3', '', 15) ?>
                    </a>
                <?php endif; ?>
                <?php if (has_role('admin')): ?>
                    <form method="post" action="<?= route('pasien/delete/' . (int) $p['id_pasien']) ?>" class="inline"
                          data-confirm="Hapus pasien beserta seluruh riwayatnya?">
                        <?= csrf_field() ?>
                        <button class="icon-btn icon-btn-danger" type="submit" title="Hapus Pasien">
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
