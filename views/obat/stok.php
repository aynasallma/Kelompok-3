<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="panel">
    <h2><?= e($item['nama_obat']) ?> (<?= e($item['kode_obat']) ?>)</h2>
    <p>Stok saat ini: <strong><?= (int) $item['stok'] ?> <?= e($item['satuan']) ?></strong>
       &middot; Minimum: <?= (int) $item['stok_minimum'] ?></p>

    <form method="post" action="<?= route('obat/tambahStok/' . (int) $item['id_obat']) ?>" class="row-form staff-inline-grid">
        <?= csrf_field() ?>
        <div>
            <label>Jumlah Stok Masuk <span class="required">*</span></label>
            <input type="number" name="jumlah" min="1" placeholder="Minimal 1" required>
        </div>
        <div>
            <label>Keterangan Tambahan</label>
            <input type="text" name="keterangan" placeholder="Contoh: Pengadaan dari PBF / Supplier">
        </div>
        <div>
            <button class="btn btn-primary">Tambah Stok</button>
        </div>
    </form>
    <a class="btn" href="<?= route('obat') ?>">&laquo; Kembali</a>
</div>

<div class="panel">
    <h2>Riwayat Transaksi Stok</h2>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Waktu</th><th>Jenis</th><th>Jumlah</th><th>Keterangan</th></tr></thead>
        <tbody>
        <?php if (!$riwayat): ?>
            <tr><td colspan="4" class="empty">Belum ada transaksi.</td></tr>
        <?php endif; ?>
        <?php foreach ($riwayat as $t): ?>
            <tr>
                <td><?= e($t['tanggal_transaksi']) ?></td>
                <td><?= e($t['jenis']) ?></td>
                <td><?= (int) $t['jumlah'] ?></td>
                <td><?= e($t['keterangan']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
