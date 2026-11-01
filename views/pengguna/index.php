<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="toolbar">
    <p class="muted">Akun yang melewati masa aktif otomatis masuk ke tabel pengguna nonaktif.</p>
    <a class="btn btn-primary" href="<?= route('pengguna/create') ?>">+ Tambah Pengguna</a>
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
                        <td>
                            <div class="user-table-actions">
                                <a class="btn btn-small" href="<?= route('pengguna/edit/' . (int) $item['id_user']) ?>">Edit</a>
                                <form method="post" action="<?= route('pengguna/nonaktifkan/' . (int) $item['id_user']) ?>" class="inline"
                                      data-confirm="Nonaktifkan pengguna ini?">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-small">Nonaktifkan</button>
                                </form>
                                <button class="btn btn-small btn-danger" type="button" disabled title="Nonaktifkan pengguna terlebih dahulu">Hapus</button>
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
                        <td>
                            <div class="user-table-actions">
                                <a class="btn btn-small" href="<?= route('pengguna/edit/' . (int) $item['id_user']) ?>">Edit</a>
                                <?php if ((int) $item['is_active'] === 0): ?>
                                    <form method="post" action="<?= route('pengguna/aktifkan/' . (int) $item['id_user']) ?>" class="inline"
                                          data-confirm="Aktifkan kembali pengguna ini?">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-small btn-primary" <?= !$dateEligible ? 'disabled title="Perbarui masa aktif terlebih dahulu"' : '' ?>>Aktifkan</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="<?= route('pengguna/nonaktifkan/' . (int) $item['id_user']) ?>" class="inline"
                                          data-confirm="Nonaktifkan pengguna ini agar dapat dihapus?">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-small">Nonaktifkan</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ((int) $item['is_active'] === 0): ?>
                                    <form method="post" action="<?= route('pengguna/delete/' . (int) $item['id_user']) ?>" class="inline"
                                          data-confirm="Hapus pengguna ini secara permanen?">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-small btn-danger">Hapus</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-small btn-danger" type="button" disabled title="Nonaktifkan pengguna terlebih dahulu">Hapus</button>
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