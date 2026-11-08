<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div class="page-header-text">
        <h2>Resep Medis #<?= (int) $item['id_resep'] ?></h2>
        <p class="muted">Diterbitkan pada tanggal: <strong><?= tgl($item['tanggal_resep']) ?></strong> &bull; Pasien: <strong><?= e($item['nama_pasien']) ?></strong></p>
    </div>
    <div class="page-header-actions">
        <a class="btn" href="<?= route('resep') ?>">&laquo; Kembali ke Daftar Resep</a>
    </div>
</div>

<div class="panel">
    <div class="toolbar" style="margin-bottom: 16px;">
        <div>
            <h3 style="margin: 0;">Rincian Resep &amp; Informasi Pasien</h3>
        </div>
        <div>
            <span class="status status-<?= strtolower(e($item['status'])) ?>" style="font-size: 14px; padding: 6px 14px;">
                <?= e($item['status']) ?>
            </span>
        </div>
    </div>

    <?php if ($item['status'] === 'Diserahkan'): ?>
        <div class="alert alert-success" style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div>
                <strong>Obat Telah Diserahkan</strong>
                <p style="margin: 0; font-size: 13px;">Resep ini telah selesai diproses oleh bagian farmasi/apoteker dan stok obat telah dikurangi.</p>
            </div>
        </div>
    <?php elseif ($item['status'] === 'Menunggu'): ?>
        <div class="alert alert-warning" style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <strong>Menunggu Penyerahan Obat</strong>
                <p style="margin: 0; font-size: 13px;">Resep telah dibuat oleh dokter dan menunggu penyerahan obat kepada pasien oleh apoteker.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="info-grid-2col">
        <div class="card detail-card">
            <h3 class="card-section-title">Informasi Pasien</h3>
            <dl class="detail-list">
                <dt>Nama Lengkap</dt>
                <dd><strong><a href="<?= route('pasien/show/' . (int) $item['id_pasien']) ?>"><?= e($item['nama_pasien']) ?></a></strong></dd>
                <dt>No. Rekam Medis</dt>
                <dd><code><?= e($item['no_rm']) ?></code></dd>
                <dt>Jenis Kelamin</dt>
                <dd><?= ($item['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan' ?></dd>
                <dt>Tanggal Lahir</dt>
                <dd><?= tgl($item['tanggal_lahir'] ?? '') ?></dd>
            </dl>
        </div>

        <div class="card detail-card">
            <h3 class="card-section-title">Informasi Medis &amp; Dokter</h3>
            <dl class="detail-list">
                <dt>Dokter Penulis Resep</dt>
                <dd><strong><?= e($item['nama_dokter'] ?? '-') ?></strong></dd>
                <dt>Diagnosa Medis</dt>
                <dd><?= nl2br(e($item['diagnosa'])) ?></dd>
                <dt>Rekam Medis</dt>
                <dd><a class="link-btn" href="<?= route('rekam_medis/show/' . (int) $item['id_rekam_medis']) ?>">Lihat Rekam Medis #<?= (int) $item['id_rekam_medis'] ?> &rarr;</a></dd>
            </dl>
        </div>
    </div>
</div>

<div class="panel">
    <h2>Daftar Item Obat</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Kode</th>
                    <th>Nama Obat</th>
                    <th>Dosis</th>
                    <th>Aturan Pakai</th>
                    <th>Jumlah</th>
                    <th>Stok Sekarang</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($detail as $idx => $d): ?>
                <tr>
                    <td><?= $idx + 1 ?></td>
                    <td><code><?= e($d['kode_obat']) ?></code></td>
                    <td><strong><?= e($d['nama_obat']) ?></strong></td>
                    <td><?= e($d['dosis']) ?></td>
                    <td><?= e($d['aturan_pakai']) ?></td>
                    <td><strong><?= (int) $d['jumlah'] ?> <?= e($d['satuan']) ?></strong></td>
                    <td>
                        <span class="badge <?= (int) $d['stok_sekarang'] < (int) $d['jumlah'] && $item['status'] === 'Menunggu' ? 'badge-danger' : 'badge-light' ?>">
                            <?= (int) $d['stok_sekarang'] ?> <?= e($d['satuan']) ?>
                        </span>
                    </td>
                    <td><?= rupiah($d['harga']) ?></td>
                    <td><strong><?= rupiah($d['subtotal']) ?></strong></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8" style="text-align: right; font-size: 13.5px; font-weight: 700;">Total Biaya Resep:</th>
                    <th style="font-size: 14.5px; font-weight: 700; color: var(--primary);"><?= rupiah($item['total']) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="form-actions" style="margin-top: 24px;">
        <a class="btn" href="<?= route('resep') ?>">&laquo; Kembali ke Daftar Resep</a>

        <?php if ($item['status'] === 'Menunggu'): ?>
            <?php if (has_role('admin', 'apoteker')): ?>
                <form method="post" action="<?= route('resep/serahkan/' . (int) $item['id_resep']) ?>" class="inline"
                      data-confirm="Konfirmasi penyerahan obat untuk Resep #<?= (int) $item['id_resep'] ?>? Stok obat akan otomatis dipotong dan status resep berubah menjadi Diserahkan.">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-success" style="display: inline-flex; align-items: center; gap: 8px;">
                        <?= icon('check-check', '', 18) ?>
                        <span>Serahkan Obat ke Pasien</span>
                    </button>
                </form>
            <?php endif; ?>

            <?php if (has_role('admin', 'dokter')): ?>
                <form method="post" action="<?= route('resep/batal/' . (int) $item['id_resep']) ?>" class="inline"
                      data-confirm="Batalkan resep medis ini? Resep yang dibatalkan tidak dapat diproses lagi.">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Batalkan Resep</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
