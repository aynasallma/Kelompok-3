<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Detail Obat: <?= e($item['nama_obat']) ?></h2>
        <p class="muted">Kode Obat: <code><?= e($item['kode_obat']) ?></code> &bull; Kategori: <strong><?= e($item['kategori'] ?: 'Umum') ?></strong></p>
    </div>
    <div class="page-header-actions">
        <a class="btn" href="<?= route('obat') ?>">&laquo; Kembali</a>
        <?php if (has_role('admin', 'apoteker')): ?>
            <a class="btn btn-primary" href="<?= route('obat/stok/' . (int) $item['id_obat']) ?>">
                <?= icon('plus', '', 15) ?> Tambah Stok
            </a>
            <a class="btn" href="<?= route('obat/edit/' . (int) $item['id_obat']) ?>">
                <?= icon('edit-3', '', 15) ?> Edit
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="panel">
    <div class="toolbar" style="margin-bottom: 20px;">
        <div>
            <h3 style="margin: 0;">Informasi Detail &amp; Status Inventori</h3>
        </div>
        <div>
            <?php
            $stok = (int) $item['stok'];
            $min = (int) $item['stok_minimum'];
            if ($stok <= 0) {
                $statusBadge = '<span class="status status-batal">Stok Habis (0)</span>';
            } elseif ($stok <= $min) {
                $statusBadge = '<span class="status status-menunggu">Stok Menipis (' . $stok . ')</span>';
            } else {
                $statusBadge = '<span class="status status-selesai">Stok Tersedia (' . $stok . ')</span>';
            }
            echo $statusBadge;
            ?>
        </div>
    </div>

    <div class="info-grid-2col">
        <div class="card detail-card">
            <h3 class="card-section-title">Spesifikasi &amp; Harga</h3>
            <dl class="detail-list">
                <dt>Kode Obat</dt>
                <dd><code><?= e($item['kode_obat']) ?></code></dd>
                <dt>Nama Obat</dt>
                <dd><strong><?= e($item['nama_obat']) ?></strong></dd>
                <dt>Kategori</dt>
                <dd><?= e($item['kategori'] ?: '-') ?></dd>
                <dt>Satuan Takaran</dt>
                <dd><?= e($item['satuan']) ?></dd>
                <dt>Harga Jual</dt>
                <dd><strong style="color: var(--primary); font-size: 16px;"><?= rupiah($item['harga']) ?></strong></dd>
            </dl>
        </div>

        <div class="card detail-card">
            <h3 class="card-section-title">Status Inventori &amp; Penggunaan</h3>
            <dl class="detail-list">
                <dt>Stok Tersedia</dt>
                <dd><strong><?= (int) $item['stok'] ?> <?= e($item['satuan']) ?></strong></dd>
                <dt>Ambang Batas Minimum</dt>
                <dd><?= (int) $item['stok_minimum'] ?> <?= e($item['satuan']) ?></dd>
                <dt>Riwayat Digunakan di Resep</dt>
                <dd><span class="badge badge-light"><?= (int) $jumlahResep ?> resep</span></dd>
                <dt>Waktu Terdaftar</dt>
                <dd><?= e(date('d/m/Y H:i', strtotime($item['created_at'] ?? 'now'))) ?></dd>
            </dl>
        </div>
    </div>

    <div class="form-actions" style="margin-top: 24px;">
        <a class="btn" href="<?= route('obat') ?>">&laquo; Kembali ke Data Obat</a>
        <?php if (has_role('admin', 'apoteker')): ?>
            <a class="btn btn-primary" href="<?= route('obat/stok/' . (int) $item['id_obat']) ?>">+ Tambah / Kelola Stok</a>
            <a class="btn" href="<?= route('obat/edit/' . (int) $item['id_obat']) ?>">Edit Data Obat</a>
        <?php endif; ?>
        <?php if (has_role('admin')): ?>
            <form method="post" action="<?= route('obat/delete/' . (int) $item['id_obat']) ?>" class="inline"
                  data-confirm="Hapus obat '<?= e($item['nama_obat']) ?>'? Tindakan ini tidak dapat dibatalkan jika obat belum pernah dipakai di resep.">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">Hapus Obat</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="panel">
    <h2>Riwayat Mutasi Stok Terakhir</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Waktu Mutasi</th>
                    <th>Jenis Transaksi</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$riwayat): ?>
                <tr><td colspan="4" class="empty">Belum ada riwayat mutasi stok untuk obat ini.</td></tr>
            <?php endif; ?>
            <?php foreach ($riwayat as $t): ?>
                <tr>
                    <td><?= e(date('d/m/Y H:i', strtotime($t['tanggal_transaksi']))) ?></td>
                    <td>
                        <span class="badge <?= $t['jenis'] === 'Masuk' ? 'badge-success' : ($t['jenis'] === 'Keluar' ? 'badge-danger' : 'badge-light') ?>">
                            <?= e($t['jenis']) ?>
                        </span>
                    </td>
                    <td><strong><?= (int) $t['jumlah'] ?> <?= e($item['satuan']) ?></strong></td>
                    <td><?= e($t['keterangan'] ?: '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

