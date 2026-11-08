<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- Top Patient & Visit Header Banner -->
<div class="panel medical-record-header" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <span class="badge badge-primary" style="font-size: 14px; font-weight: 700; padding: 4px 10px;"><?= e($item['no_rm']) ?></span>
                <h2 style="margin: 0; font-size: 22px;"><?= e($item['nama_pasien']) ?></h2>
                <span class="badge badge-light"><?= ($item['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan' ?></span>
            </div>
            <div class="muted small" style="display: flex; flex-wrap: wrap; gap: 16px; font-size: 13px; align-items: center;">
                <span style="display: inline-flex; align-items: center; gap: 5px;"><?= icon('calendar', '', 14) ?> Kunjungan: <strong><?= tgl($item['tanggal_kunjungan']) ?></strong></span>
                <span style="display: inline-flex; align-items: center; gap: 5px;"><?= icon('building-2', '', 14) ?> Poli: <strong><?= e($item['poli']) ?></strong> (Antrian #<?= (int) $item['no_antrian'] ?>)</span>
                <span style="display: inline-flex; align-items: center; gap: 5px;"><?= icon('stethoscope', '', 14) ?> Dokter: <strong><?= e($item['nama_dokter'] ?? '-') ?></strong></span>
                <?php if (!empty($item['tanggal_lahir'])): ?>
                    <span style="display: inline-flex; align-items: center; gap: 5px;"><?= icon('cake', '', 14) ?> Tgl Lahir: <strong><?= tgl($item['tanggal_lahir']) ?></strong></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="header-action-group" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <a class="btn" href="<?= route('rekam_medis') ?>">&laquo; Kembali</a>
            <a class="btn" href="<?= route('rekam_medis/edit/' . (int) $item['id_rekam_medis']) ?>"><?= icon('edit-3', '', 15) ?> Edit Catatan</a>
            <a class="btn btn-secondary" href="<?= route('rekam_medis/pdf/' . (int) $item['id_rekam_medis']) ?>"><?= icon('file-down', '', 15) ?> Unduh PDF</a>
            <a class="btn btn-primary" href="<?= route('resep/create/' . (int) $item['id_rekam_medis']) ?>"><?= icon('plus', '', 15) ?> Buat Resep Obat</a>
        </div>
    </div>
</div>

<!-- 2-Column Responsive Grid on Desktop, 1-Column on Mobile -->
<div class="rm-grid-container">
    <!-- LEFT COLUMN: Anamnesis, Diagnosis, Tindakan, Catatan -->
    <div class="rm-col-primary">
        <!-- Section Keluhan & Anamnesis -->
        <div class="panel rm-card">
            <div class="rm-card-header">
                <span class="rm-icon-pill icon-complaint"><?= icon('stethoscope', '', 18) ?></span>
                <h3 class="rm-card-title">Keluhan Pasien (Anamnesis)</h3>
            </div>
            <div class="rm-card-body">
                <p class="rm-text-content"><?= nl2br(e($item['keluhan'])) ?></p>
            </div>
        </div>

        <!-- Section Diagnosa & Tindakan -->
        <div class="panel rm-card">
            <div class="rm-card-header">
                <span class="rm-icon-pill icon-diagnosis"><?= icon('activity', '', 18) ?></span>
                <h3 class="rm-card-title">Diagnosis &amp; Pemeriksaan Fisik</h3>
            </div>
            <div class="rm-card-body">
                <div class="rm-diagnosis-badge">
                    <span class="rm-label-sm">DIAGNOSIS UTAMA:</span>
                    <strong class="rm-diagnosis-text"><?= nl2br(e($item['diagnosa'])) ?></strong>
                </div>

                <?php if (!empty($item['tindakan'])): ?>
                    <div class="rm-sub-section" style="margin-top: 16px;">
                        <span class="rm-label-sm">TINDAKAN MEDIS / PROSEDUR:</span>
                        <div class="rm-action-box">
                            <?= nl2br(e($item['tindakan'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section Catatan Medis -->
        <?php if (!empty($item['catatan'])): ?>
            <div class="panel rm-card">
                <div class="rm-card-header">
                    <span class="rm-icon-pill icon-note"><?= icon('file-text', '', 18) ?></span>
                    <h3 class="rm-card-title">Catatan Medis &amp; Instruksi Dokter</h3>
                </div>
                <div class="rm-card-body">
                    <p class="rm-text-content note-box"><?= nl2br(e($item['catatan'])) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT COLUMN: Resep Terkait & Timeline Riwayat Medis -->
    <div class="rm-col-secondary">
        <!-- Section Resep Obat Terkait -->
        <div class="panel rm-card">
            <div class="rm-card-header" style="justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="rm-icon-pill icon-rx"><?= icon('pill', '', 18) ?></span>
                    <h3 class="rm-card-title">Resep Obat Kunjungan Ini</h3>
                </div>
                <?php if (!$resep): ?>
                    <a class="btn btn-small btn-primary" href="<?= route('resep/create/' . (int) $item['id_rekam_medis']) ?>"><?= icon('plus', '', 13) ?> Buat Resep</a>
                <?php endif; ?>
            </div>
            <div class="rm-card-body" style="padding: 0;">
                <?php if (!$resep): ?>
                    <div style="padding: 20px; text-align: center; color: var(--muted);">
                        <p style="margin: 0 0 10px;">Belum ada resep obat untuk rekam medis ini.</p>
                        <a class="btn btn-small btn-primary" href="<?= route('resep/create/' . (int) $item['id_rekam_medis']) ?>"><?= icon('plus', '', 13) ?> Racik Resep Obat</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($resep as $r): ?>
                        <div class="resep-card-item" style="padding: 16px; border-bottom: 1px solid var(--border);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <div>
                                    <strong>Resep #<?= (int) $r['id_resep'] ?></strong>
                                    <span class="muted small">&bull; <?= tgl($r['tanggal_resep']) ?></span>
                                </div>
                                <span class="status status-<?= strtolower(e($r['status'])) ?>"><?= e($r['status']) ?></span>
                            </div>

                            <ul class="resep-item-list" style="margin: 8px 0; padding-left: 18px; font-size: 13px;">
                                <?php foreach ($r['items'] ?? [] as $it): ?>
                                    <li style="margin-bottom: 4px;">
                                        <strong><?= e($it['nama_obat']) ?></strong> &mdash; <?= (int) $it['jumlah'] ?> <?= e($it['satuan']) ?>
                                        <div class="muted small"><?= e($it['dosis']) ?> &bull; <?= e($it['aturan_pakai']) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; font-size: 13px;">
                                <span>Total: <strong><?= rupiah($r['total']) ?></strong></span>
                                <a class="btn btn-small" href="<?= route('resep/show/' . (int) $r['id_resep']) ?>">Detail Resep &rarr;</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section Timeline Riwayat Pasien -->
        <div class="panel rm-card">
            <div class="rm-card-header">
                <span class="rm-icon-pill icon-history"><?= icon('history', '', 18) ?></span>
                <h3 class="rm-card-title">Riwayat Rekam Medis Pasien</h3>
            </div>
            <div class="rm-card-body" style="padding: 16px;">
                <?php
                // Filter riwayat kunjungan lain pasien ini
                $riwayatLain = array_filter($riwayatPasien ?? [], function ($row) use ($item) {
                    return (int) $row['id_rekam_medis'] !== (int) $item['id_rekam_medis'];
                });
                ?>
                <?php if (empty($riwayatLain)): ?>
                    <p class="muted small" style="margin: 0; text-align: center; padding: 12px 0;">
                        Ini adalah kunjungan pertama / belum ada rekam medis sebelumnya untuk pasien ini.
                    </p>
                <?php else: ?>
                    <div class="rm-timeline">
                        <?php foreach (array_slice($riwayatLain, 0, 5) as $hist): ?>
                            <div class="timeline-node">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <div class="timeline-date"><?= tgl($hist['tanggal_kunjungan']) ?> &bull; <span class="badge badge-light"><?= e($hist['poli']) ?></span></div>
                                    <div class="timeline-diagnosis"><strong><?= e($hist['diagnosa']) ?></strong></div>
                                    <div class="timeline-meta muted small">Dokter: <?= e($hist['nama_dokter'] ?? '-') ?></div>
                                    <a class="timeline-link small" href="<?= route('rekam_medis/show/' . (int) $hist['id_rekam_medis']) ?>">Lihat Rekam Medis &rarr;</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
