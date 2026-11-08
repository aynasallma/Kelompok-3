<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Manajemen Pengguna</h2>
        <p class="muted">Kelola akun staf medis, dokter, apoteker, dan hak akses sistem.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-primary" href="<?= route('pengguna/create') ?>">
            <?= icon('plus', '', 16) ?> Tambah Pengguna
        </a>
    </div>
</div>

<div class="panel">
    <div class="toolbar">
        <h2>Pengguna Aktif</h2>
        <span class="small muted"><?= count($activeUsers) ?> pengguna</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Nama</th><th>Username</th><th>Role</th><th>Masa Aktif</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if (!$activeUsers): ?>
                    <tr><td colspan="5" class="empty">Belum ada pengguna aktif.</td></tr>
                <?php endif; ?>
                <?php foreach ($activeUsers as $item): ?>
                    <tr>
                        <td><?= e($item['nama']) ?></td>
                        <td><?= e($item['username']) ?></td>
                        <td><?= e(ucfirst($item['role'])) ?></td>
                        <td><?= tgl($item['aktif_mulai']) ?> - <?= tgl($item['aktif_sampai']) ?></td>
                        <td class="actions">
                            <div class="user-table-actions">
                                <a class="icon-btn icon-btn-edit" href="<?= route('pengguna/edit/' . (int) $item['id_user']) ?>" title="Edit Pengguna">
                                    <?= icon('edit-3', '', 15) ?>
                                </a>
                                <form method="post" action="<?= route('pengguna/nonaktifkan/' . (int) $item['id_user']) ?>" class="inline"
                                      data-confirm="Nonaktifkan pengguna ini?">
                                    <?= csrf_field() ?>
                                    <button class="icon-btn icon-btn-warning" type="submit" title="Nonaktifkan Pengguna">
                                        <?= icon('power', '', 15) ?>
                                    </button>
                                </form>
                                <button class="icon-btn icon-btn-danger" type="button" disabled title="Nonaktifkan pengguna terlebih dahulu">
                                    <?= icon('trash', '', 15) ?>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <div class="toolbar">
        <h2>Pengguna Nonaktif</h2>
        <span class="small muted"><?= count($inactiveUsers) ?> pengguna</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Nama</th><th>Username</th><th>Role</th><th>Masa Aktif</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if (!$inactiveUsers): ?>
                    <tr><td colspan="6" class="empty">Belum ada pengguna nonaktif.</td></tr>
                <?php endif; ?>
                <?php foreach ($inactiveUsers as $item): ?>
                    <?php
                    $today = date('Y-m-d');
                    $status = (int) $item['is_active'] === 0
                        ? 'Dinonaktifkan'
                        : ($item['aktif_mulai'] > $today ? 'Belum berlaku' : 'Kedaluwarsa');
                    $statusClass = (int) $item['is_active'] === 0 ? 'status-nonaktif' : 'status-expired';
                    $dateEligible = $item['aktif_mulai'] <= $today && $item['aktif_sampai'] >= $today;
                    ?>
                    <tr>
                        <td><?= e($item['nama']) ?></td>
                        <td><?= e($item['username']) ?></td>
                        <td><?= e(ucfirst($item['role'])) ?></td>
                        <td><?= tgl($item['aktif_mulai']) ?> - <?= tgl($item['aktif_sampai']) ?></td>
                        <td><span class="status <?= $statusClass ?>"><?= e($status) ?></span></td>
                        <td class="actions">
                            <div class="user-table-actions">
                                <a class="icon-btn icon-btn-edit" href="<?= route('pengguna/edit/' . (int) $item['id_user']) ?>" title="Edit Pengguna">
                                    <?= icon('edit-3', '', 15) ?>
                                </a>
                                <?php if ((int) $item['is_active'] === 0): ?>
                                    <form method="post" action="<?= route('pengguna/aktifkan/' . (int) $item['id_user']) ?>" class="inline"
                                          data-confirm="Aktifkan kembali pengguna ini?">
                                        <?= csrf_field() ?>
                                        <button class="icon-btn icon-btn-success" type="submit" <?= !$dateEligible ? 'disabled title="Perbarui masa aktif terlebih dahulu"' : 'title="Aktifkan Pengguna"' ?>>
                                            <?= icon('user-check', '', 15) ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="<?= route('pengguna/nonaktifkan/' . (int) $item['id_user']) ?>" class="inline"
                                          data-confirm="Nonaktifkan pengguna ini agar dapat dihapus?">
                                        <?= csrf_field() ?>
                                        <button class="icon-btn icon-btn-warning" type="submit" title="Nonaktifkan Pengguna">
                                            <?= icon('power', '', 15) ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if ((int) $item['is_active'] === 0): ?>
                                    <form method="post" action="<?= route('pengguna/delete/' . (int) $item['id_user']) ?>" class="inline"
                                          data-confirm="Hapus pengguna ini secara permanen?">
                                        <?= csrf_field() ?>
                                        <button class="icon-btn icon-btn-danger" type="submit" title="Hapus Pengguna Permanen">
                                            <?= icon('trash', '', 15) ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="icon-btn icon-btn-danger" type="button" disabled title="Nonaktifkan pengguna terlebih dahulu">
                                        <?= icon('trash', '', 15) ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>