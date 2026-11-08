<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Kalender Hari Libur</h2>
        <p class="muted">Atur tanggal libur operasional klinik untuk menonaktifkan reservasi dan jadwal praktik dokter.</p>
    </div>
</div>

<div class="panel">
    <h2>Tambahkan tanggal libur</h2>
    <form method="post" action="<?= route('hari_libur/store') ?>" class="staff-form">
        <?= csrf_field() ?>
        <div class="staff-form-grid">
            <div><label for="tanggal-libur">Tanggal Libur <span class="required">*</span></label><input id="tanggal-libur" type="date" name="tanggal" min="<?= date('Y-m-d') ?>" required></div>
            <div><label for="nama-libur">Nama Hari Libur <span class="required">*</span></label><input id="nama-libur" type="text" name="nama" maxlength="120" placeholder="Contoh: Libur Nasional / Cuti Bersama" required></div>
        </div>
        <div class="form-actions"><button class="btn btn-primary">Simpan hari libur</button></div>
    </form>
</div>

<div class="panel">
    <h2>Kalender hari libur</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Nama</th><th>Ditambahkan oleh</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (!$items): ?><tr><td colspan="4" class="empty">Belum ada tanggal libur.</td></tr><?php endif; ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= tgl($item['tanggal']) ?></td>
                        <td><?= e($item['nama']) ?></td>
                        <td><?= e($item['nama_petugas'] ?? '-') ?></td>
                        <td class="actions">
                            <form method="post" action="<?= route('hari_libur/delete/' . $item['tanggal']) ?>" class="inline" data-confirm="Hapus hari libur ini?">
                                <?= csrf_field() ?>
                                <button class="icon-btn icon-btn-danger" type="submit" title="Hapus Hari Libur">
                                    <?= icon('trash', '', 15) ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>