<?php
if ($isPublic) {
    $activeNav = 'jadwal';
    require __DIR__ . '/../layouts/public_header.php';
} else {
    require __DIR__ . '/../layouts/header.php';
}
?>

<?php if ($isPublic): ?>
<main class="public-schedule-page">
    <section class="schedule-page-heading section-shell">
        <div>
            <p class="eyebrow">Kunjungan yang terencana</p>
            <h2>Jadwal Praktik Dokter</h2>
            <p class="muted">Pilih waktu kunjungan yang sesuai dengan jadwal praktik dokter.</p>
        </div>
        <form method="get" action="<?= BASE_URL ?>/index.php" class="schedule-date-filter">
            <input type="hidden" name="url" value="jadwal">
            <div><label for="schedule-date">Tanggal kunjungan</label><input id="schedule-date" type="date" name="tanggal" value="<?= e($date) ?>" min="<?= date('Y-m-d') ?>" required></div>
            <button class="btn btn-primary">Lihat jadwal</button>
        </form>
    </section>
    <div class="section-shell schedule-table-shell">
<?php else: ?>
    <div class="page-header">
        <div class="page-header-text">
            <h2><?= $isDoctor ? 'Jadwal Praktik Saya' : 'Jadwal Praktik Dokter' ?></h2>
            <p class="muted">Pilih waktu kunjungan dan kelola jadwal dokter yang aktif di setiap poli klinik.</p>
        </div>
        <?php if ($isAdmin): ?>
            <div class="page-header-actions">
                <a class="btn btn-primary" href="<?= route('jadwal/create') ?>">
                    <?= icon('plus', '', 16) ?> Tambah Jadwal
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="panel">
<?php endif; ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Hari</th><th>Jam Praktik</th><th>Dokter</th><th>Poli</th><th>Lokasi</th>
                        <?php if ($isAdmin || $isDoctor): ?><th>Status</th><?php endif; ?>
                        <?php if ($isAdmin): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$items): ?>
                        <tr><td colspan="<?= $isAdmin ? '7' : ($isDoctor ? '6' : '5') ?>" class="empty">Belum ada jadwal praktik yang tersedia.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($items as $item): ?>
                        <?php
                        $visibleToPublic = (int) $item['is_active'] === 1
                            && (int) $item['status_akun'] === 1
                            && $item['akun_aktif_mulai'] <= date('Y-m-d')
                            && $item['akun_aktif_sampai'] >= date('Y-m-d');
                        ?>
                        <tr>
                            <td><strong><?= e($item['hari']) ?></strong></td>
                            <td><?= e(substr($item['jam_mulai'], 0, 5)) ?>–<?= e(substr($item['jam_selesai'], 0, 5)) ?></td>
                            <td><?= e($item['nama_dokter']) ?></td>
                            <td><?= e($item['poli']) ?></td>
                            <td><?= e($item['lokasi']) ?></td>
                            <?php if ($isAdmin || $isDoctor): ?>
                                <td><span class="status <?= $visibleToPublic ? 'status-selesai' : 'status-batal' ?>"><?= $visibleToPublic ? 'Tayang' : 'Disembunyikan' ?></span></td>
                            <?php endif; ?>
                            <?php if ($isAdmin): ?>
                                <td class="actions">
                                    <a class="icon-btn icon-btn-edit" href="<?= route('jadwal/edit/' . (int) $item['id_jadwal']) ?>" title="Edit Jadwal">
                                        <?= icon('edit-3', '', 15) ?>
                                    </a>
                                    <form method="post" action="<?= route('jadwal/delete/' . (int) $item['id_jadwal']) ?>" class="inline" data-confirm="Hapus jadwal ini secara permanen?">
                                        <?= csrf_field() ?>
                                        <button class="icon-btn icon-btn-danger" type="submit" title="Hapus Jadwal">
                                            <?= icon('trash', '', 15) ?>
                                        </button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($isPublic && $closedMessage): ?><p class="schedule-disclaimer section-shell"><?= e($closedMessage) ?></p><?php endif; ?>
    <?php if ($isPublic): ?><p class="schedule-disclaimer section-shell">Jadwal dapat berubah. Silakan periksa kembali sebelum berkunjung.</p><?php endif; ?>
<?php if ($isPublic): ?></main><?php endif; ?>

<?php if ($isPublic): require __DIR__ . '/../layouts/public_footer.php'; else: require __DIR__ . '/../layouts/footer.php'; endif; ?>