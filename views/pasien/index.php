<?php require __DIR__ . '/../layouts/header.php'; ?>

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
        <button class="btn btn-primary">Terapkan</button>
    </form>
    <?php if (has_role('admin', 'resepsionis')): ?>
        <a class="btn btn-primary" href="<?= route('pasien/create') ?>">+ Tambah Pasien</a>
    <?php endif; ?>
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
                <a class="btn btn-small" href="<?= route('pasien/show/' . (int) $p['id_pasien']) ?>">Detail</a>
                <?php if (has_role('admin', 'resepsionis')): ?>
                    <a class="btn btn-small" href="<?= route('pasien/edit/' . (int) $p['id_pasien']) ?>">Edit</a>
                <?php endif; ?>
                <?php if (has_role('admin')): ?>
                    <form method="post" action="<?= route('pasien/delete/' . (int) $p['id_pasien']) ?>" class="inline"
                          data-confirm="Hapus pasien beserta seluruh riwayatnya?">
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
