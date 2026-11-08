<?php
require __DIR__ . '/../layouts/header.php';

$oldIdObat = old('id_obat');
$oldDosis = old('dosis');
$oldAturan = old('aturan_pakai');
$oldJumlah = old('jumlah');
$hasOldRows = is_array($oldIdObat) && count($oldIdObat) > 0;
?>

<div class="panel">
    <div class="toolbar" style="margin-bottom: 16px;">
        <div>
            <h2>Buat Resep Medis</h2>
            <p class="muted">Pilih rekam medis pasien dan racik obat yang diperlukan sesuai ketersediaan stok farmasi.</p>
        </div>
    </div>

    <form method="post" action="<?= route('resep/store') ?>" class="staff-form prescription-form" id="form-resep">
        <?= csrf_field() ?>

        <div class="staff-form-grid">
            <div class="field-wide">
                <label for="id_rekam_medis">Rekam Medis Pasien <span class="required">*</span></label>
                <select id="id_rekam_medis" name="id_rekam_medis" required>
                    <option value="">-- Pilih Rekam Medis Pasien --</option>
                    <?php
                    $selectedRm = (int) old('id_rekam_medis', $terpilih);
                    foreach ($rekamMedis as $rm):
                    ?>
                        <option value="<?= (int) $rm['id_rekam_medis'] ?>" <?= $selectedRm === (int) $rm['id_rekam_medis'] ? 'selected' : '' ?>>
                            <?= e(tgl($rm['tanggal_kunjungan']) . ' | ' . $rm['no_rm'] . ' - ' . $rm['nama_pasien'] . ' | Diagnosa: ' . $rm['diagnosa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($rekamMedis)): ?>
                    <p class="muted small" style="color: var(--amber-700, #b45309);">Belum ada rekam medis yang menunggu pembuatan resep.</p>
                <?php endif; ?>
            </div>

            <div>
                <label for="tanggal_resep">Tanggal Resep <span class="required">*</span></label>
                <input id="tanggal_resep" type="date" name="tanggal_resep" value="<?= e(old('tanggal_resep', date('Y-m-d'))) ?>" required>
            </div>
        </div>

        <div class="prescription-items-section" style="margin-top: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="margin: 0;">Daftar Obat yang Diresepkan <span class="required">*</span></h3>
                <span class="muted small" style="display: inline-flex; align-items: center; gap: 4px;"><?= icon('info', '', 14) ?> Pencarian obat &amp; validasi stok langsung</span>
            </div>

            <div id="stock-warning-banner" class="alert alert-error" style="display: none; margin-bottom: 14px;">
                <strong>Peringatan Stok Obat!</strong>
                <p id="stock-warning-message" style="margin: 4px 0 0;"></p>
            </div>

            <div id="resep-items">
                <?php
                $initialRows = $hasOldRows ? count($oldIdObat) : 1;
                for ($rowIdx = 0; $rowIdx < $initialRows; $rowIdx++):
                    $curId = (int) ($hasOldRows ? ($oldIdObat[$rowIdx] ?? 0) : 0);
                    $curDosis = $hasOldRows ? ($oldDosis[$rowIdx] ?? '') : '';
                    $curAturan = $hasOldRows ? ($oldAturan[$rowIdx] ?? '') : '';
                    $curJumlah = $hasOldRows ? ($oldJumlah[$rowIdx] ?? '') : '';
                ?>
                    <div class="resep-row card" style="padding: 14px; margin-bottom: 12px; border: 1px solid var(--border); overflow: visible;">
                        <div class="resep-row-grid">
                            <div class="field-obat">
                                <label>Pilih Obat <span class="required">*</span></label>
                                <div class="searchable-select-wrap">
                                    <input type="text" class="obat-search-input" placeholder="Ketik nama atau kode obat..." autocomplete="off">
                                    <select name="id_obat[]" class="obat-select" required style="display: none;">
                                        <option value="" data-stok="0" data-harga="0" data-satuan="" data-nama="">-- Pilih Obat --</option>
                                        <?php foreach ($obat as $o): ?>
                                            <option value="<?= (int) $o['id_obat'] ?>"
                                                    data-harga="<?= e($o['harga']) ?>"
                                                    data-stok="<?= (int) $o['stok'] ?>"
                                                    data-satuan="<?= e($o['satuan']) ?>"
                                                    data-nama="<?= e($o['nama_obat']) ?>"
                                                    data-kode="<?= e($o['kode_obat']) ?>"
                                                    <?= $curId === (int) $o['id_obat'] ? 'selected' : '' ?>>
                                                <?= e($o['nama_obat'] . ' [' . $o['kode_obat'] . '] (Stok: ' . $o['stok'] . ' ' . $o['satuan'] . ' - ' . rupiah($o['harga']) . ')') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="searchable-dropdown-list" style="display: none;"></div>
                                </div>
                                <div class="obat-stock-indicator" style="margin-top: 4px; font-size: 12px;"></div>
                            </div>

                            <div class="field-dosis">
                                <label>Dosis <span class="required">*</span></label>
                                <input type="text" name="dosis[]" value="<?= e($curDosis) ?>" placeholder="Contoh: 3 x 1 tablet" required>
                            </div>

                            <div class="field-aturan">
                                <label>Aturan Pakai <span class="required">*</span></label>
                                <input type="text" name="aturan_pakai[]" value="<?= e($curAturan) ?>" placeholder="Contoh: Sesudah makan" required>
                            </div>

                            <div class="field-jumlah">
                                <label>Jumlah <span class="required">*</span></label>
                                <input type="number" name="jumlah[]" min="1" value="<?= e($curJumlah) ?>" placeholder="Jml" class="jumlah-input" required>
                                <div class="row-stock-alert" style="color: #b91c1c; font-size: 11px; margin-top: 2px;"></div>
                            </div>

                            <div class="field-hapus" style="align-self: flex-end;">
                                <button type="button" class="btn btn-danger remove-row" title="Hapus baris obat ini">&times; Hapus</button>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="prescription-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px; padding: 12px; background: #f8fafc; border-radius: 8px;">
                <button type="button" class="btn" id="add-row">+ Tambah Obat Lain</button>
                <div style="text-align: right;">
                    <span class="muted">Perkiraan Total Biaya Resep:</span>
                    <strong id="estimasi-total" style="font-size: 18px; color: var(--primary); margin-left: 8px;">Rp 0</strong>
                </div>
            </div>
        </div>

        <div class="form-actions" style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary" id="btn-submit-resep">Simpan Resep</button>
            <a class="btn" href="<?= route('resep') ?>">Batal</a>
        </div>
    </form>
</div>

<!-- Template data obat dalam JSON untuk JavaScript searchable selector -->
<script>
window.__DATA_OBAT__ = <?= json_encode(array_map(function ($o) {
    return [
        'id' => (int) $o['id_obat'],
        'kode' => $o['kode_obat'],
        'nama' => $o['nama_obat'],
        'kategori' => $o['kategori'] ?? '',
        'satuan' => $o['satuan'],
        'harga' => (float) $o['harga'],
        'stok' => (int) $o['stok']
    ];
}, $obat), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
