<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Resep &amp; Penyerahan Obat</h2>
        <p class="muted">Daftar peresepan obat dokter dan status penyerahan farmasi kepada pasien.</p>
    </div>
    <?php if (has_role('admin', 'dokter')): ?>
        <div class="page-header-actions">
            <a class="btn btn-primary" href="<?= route('resep/create') ?>">
                <?= icon('plus', '', 16) ?> Buat Resep Baru
            </a>
        </div>
    <?php endif; ?>
</div>

<div class="toolbar resep-toolbar">
    <div class="filter-tabs">
        <a href="<?= route('resep') ?>" class="tab-pill <?= empty($statusFilter) ? 'active' : '' ?>">Semua</a>
        <a href="<?= route('resep') ?>&status=Menunggu" class="tab-pill <?= $statusFilter === 'Menunggu' ? 'active' : '' ?>">
            Menunggu Penyerahan
            <?php if (!empty($countMenunggu)): ?>
                <span class="badge-count"><?= (int) $countMenunggu ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= route('resep') ?>&status=Diserahkan" class="tab-pill <?= $statusFilter === 'Diserahkan' ? 'active' : '' ?>">Diserahkan</a>
        <a href="<?= route('resep') ?>&status=Batal" class="tab-pill <?= $statusFilter === 'Batal' ? 'active' : '' ?>">Batal</a>
    </div>
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No. Resep</th>
                    <th>Nama Pasien</th>
                    <th>Dokter</th>
                    <th>Tanggal Resep</th>
                    <th>Jumlah Item</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$items): ?>
                <tr>
                    <td colspan="8" class="empty">
                        <div class="empty-state">
                            <p>Tidak ada data resep<?= $statusFilter ? ' dengan status "' . e($statusFilter) . '"' : '' ?>.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <?php foreach ($items as $r): ?>
                <tr>
                    <td><strong>#<?= (int) $r['id_resep'] ?></strong></td>
                    <td>
                        <strong><?= e($r['nama_pasien']) ?></strong>
                        <div class="muted small">No. RM: <?= e($r['no_rm']) ?></div>
                    </td>
                    <td><?= e($r['nama_dokter'] ?? '-') ?></td>
                    <td><?= tgl($r['tanggal_resep']) ?></td>
                    <td><span class="badge badge-light"><?= (int) ($r['total_items'] ?? 1) ?> obat</span></td>
                    <td><strong><?= rupiah($r['total']) ?></strong></td>
                    <td>
                        <span class="status status-<?= strtolower(e($r['status'])) ?>">
                            <?= e($r['status']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a class="icon-btn icon-btn-primary" href="<?= route('resep/show/' . (int) $r['id_resep']) ?>" title="Detail Resep">
                            <?= icon('eye', '', 15) ?>
                        </a>
                        <?php if ($r['status'] === 'Menunggu' && has_role('admin', 'apoteker')): ?>
                            <form method="post" action="<?= route('resep/serahkan/' . (int) $r['id_resep']) ?>" class="inline"
                                  data-confirm="Serahkan obat untuk resep #<?= (int) $r['id_resep'] ?> (Pasien: <?= e($r['nama_pasien']) ?>) dan kurangi stok obat?">
                                <?= csrf_field() ?>
                                <button type="submit" class="icon-btn icon-btn-success" title="Serahkan Obat ke Pasien">
                                    <?= icon('check-circle', '', 15) ?>
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
