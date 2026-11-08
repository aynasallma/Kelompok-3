<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Data Obat &amp; Stok Farmasi</h2>
        <p class="muted">Katalog obat klinik, pemantauan batas stok minimum, dan pengelolaan farmasi.</p>
    </div>
    <?php if (has_role('admin', 'apoteker')): ?>
        <div class="page-header-actions">
            <a class="btn btn-primary" href="<?= route('obat/create') ?>">
                <?= icon('plus', '', 16) ?> Tambah Obat Baru
            </a>
        </div>
    <?php endif; ?>
</div>

<div class="toolbar">
    <form method="get" action="<?= BASE_URL ?>/index.php" class="search">
        <input type="hidden" name="url" value="obat">
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari kode, nama, atau kategori obat...">
        <button type="submit" class="btn">
            <?= icon('search', '', 15) ?> Cari
        </button>
        <?php if ($search !== ''): ?>
            <a href="<?= route('obat') ?>" class="btn">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Obat</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Harga Satuan</th>
                    <th>Stok</th>
                    <th>Status Stok</th>
                    <th style="min-width: 220px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$items): ?>
                <tr>
                    <td colspan="8" class="empty">
                        <div class="empty-state">
                            <p>Data obat tidak ditemukan<?= $search ? ' untuk pencarian "' . e($search) . '"' : '' ?>.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <?php foreach ($items as $o): ?>
                <?php
                $stok = (int) $o['stok'];
                $min = (int) $o['stok_minimum'];
                ?>
                <tr>
                    <td><code><?= e($o['kode_obat']) ?></code></td>
                    <td><strong><?= e($o['nama_obat']) ?></strong></td>
                    <td><span class="badge badge-light"><?= e($o['kategori'] ?: 'Umum') ?></span></td>
                    <td><?= e($o['satuan']) ?></td>
                    <td><?= rupiah($o['harga']) ?></td>
                    <td><strong><?= $stok ?></strong></td>
                    <td>
                        <?php if ($stok <= 0): ?>
                            <span class="status status-batal">Habis</span>
                        <?php elseif ($stok <= $min): ?>
                            <span class="status status-menunggu">Menipis (&le; <?= $min ?>)</span>
                        <?php else: ?>
                            <span class="status status-selesai">Aman</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <a class="icon-btn icon-btn-primary" href="<?= route('obat/show/' . (int) $o['id_obat']) ?>" title="Detail Obat">
                            <?= icon('eye', '', 15) ?>
                        </a>

                        <?php if (has_role('admin', 'apoteker')): ?>
                            <a class="icon-btn icon-btn-success" href="<?= route('obat/stok/' . (int) $o['id_obat']) ?>" title="Tambah Stok">
                                <?= icon('package-plus', '', 15) ?>
                            </a>
                            <a class="icon-btn icon-btn-edit" href="<?= route('obat/edit/' . (int) $o['id_obat']) ?>" title="Edit Obat">
                                <?= icon('edit-3', '', 15) ?>
                            </a>
                        <?php endif; ?>

                        <?php if (has_role('admin')): ?>
                            <form method="post" action="<?= route('obat/delete/' . (int) $o['id_obat']) ?>" class="inline"
                                  data-confirm="Hapus data obat '<?= e($o['nama_obat']) ?>'? (Penghapusan akan ditolak bila sudah pernah digunakan dalam resep)">
                                <?= csrf_field() ?>
                                <button type="submit" class="icon-btn icon-btn-danger" title="Hapus Obat">
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
