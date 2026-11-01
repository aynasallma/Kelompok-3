<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="toolbar">
    <form method="get" action="<?= BASE_URL ?>/index.php" class="search">
        <input type="hidden" name="url" value="obat">
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari kode / nama / kategori">
        <button class="btn">Cari</button>
    </form>
    <?php if (has_role('admin', 'apoteker')): ?>
        <a class="btn btn-primary" href="<?= route('obat/create') ?>">+ Tambah Obat</a>
    <?php endif; ?>
</div>

<div class="panel">
<div class="table-wrap">
<table>
    <thead>
        <tr><th>Kode</th><th>Nama Obat</th><th>Kategori</th><th>Satuan</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php if (!$items): ?>
        <tr><td colspan="7" class="empty">Data obat tidak ditemukan.</td></tr>
    <?php endif; ?>
    <?php foreach ($items as $o): ?>
        <tr>
            <td><?= e($o['kode_obat']) ?></td>
            <td><?= e($o['nama_obat']) ?></td>
            <td><?= e($o['kategori']) ?></td>
            <td><?= e($o['satuan']) ?></td>
            <td><?= rupiah($o['harga']) ?></td>
            <td>
                <span class="status <?= $o['stok'] <= $o['stok_minimum'] ? 'status-batal' : 'status-selesai' ?>">
                    <?= (int) $o['stok'] ?>
                </span>
            </td>
            <td class="actions">
                <?php if (has_role('admin', 'apoteker')): ?>
                    <a class="btn btn-small" href="<?= route('obat/stok/' . (int) $o['id_obat']) ?>">Stok</a>
                    <a class="btn btn-small" href="<?= route('obat/edit/' . (int) $o['id_obat']) ?>">Edit</a>
                <?php endif; ?>
                <?php if (has_role('admin')): ?>
                    <form method="post" action="<?= route('obat/delete/' . (int) $o['id_obat']) ?>" class="inline"
                          data-confirm="Hapus obat ini?">
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
