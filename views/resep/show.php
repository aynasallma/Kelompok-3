<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="panel">
    <dl class="detail">
        <dt>Resep</dt><dd>#<?= (int) $item['id_resep'] ?></dd>
        <dt>Tanggal</dt><dd><?= tgl($item['tanggal_resep']) ?></dd>
        <dt>Pasien</dt><dd><?= e($item['no_rm'] . ' - ' . $item['nama_pasien']) ?></dd>
        <dt>Diagnosa</dt><dd><?= e($item['diagnosa']) ?></dd>
        <dt>Status</dt>
        <dd><span class="status status-<?= strtolower(e($item['status'])) ?>"><?= e($item['status']) ?></span></dd>
    </dl>
</div>

<div class="panel">
    <h2>Daftar Obat</h2>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>Obat</th><th>Dosis</th><th>Aturan Pakai</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
        <?php foreach ($detail as $d): ?>
            <tr>
                <td><?= e($d['nama_obat']) ?></td>
                <td><?= e($d['dosis']) ?></td>
                <td><?= e($d['aturan_pakai']) ?></td>
                <td><?= (int) $d['jumlah'] ?> <?= e($d['satuan']) ?></td>
                <td><?= rupiah($d['harga']) ?></td>
                <td><?= rupiah($d['subtotal']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><th colspan="5" class="right">Total</th><th><?= rupiah($item['total']) ?></th></tr>
        </tfoot>
    </table>
    </div>

    <div class="form-actions">
        <a class="btn" href="<?= route('resep') ?>">&laquo; Kembali</a>
        <?php if ($item['status'] === 'Menunggu'): ?>
            <?php if (has_role('admin', 'apoteker')): ?>
                <form method="post" action="<?= route('resep/serahkan/' . (int) $item['id_resep']) ?>" class="inline"
                      data-confirm="Serahkan obat dan kurangi stok?">
                    <?= csrf_field() ?>
                    <button class="btn btn-primary">Serahkan Obat</button>
                </form>
            <?php endif; ?>
            <form method="post" action="<?= route('resep/batal/' . (int) $item['id_resep']) ?>" class="inline"
                  data-confirm="Batalkan resep ini?">
                <?= csrf_field() ?>
                <button class="btn btn-danger">Batalkan</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
