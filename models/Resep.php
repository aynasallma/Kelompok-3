<?php

class Resep extends Model
{
    private $select = "SELECT r.*, rm.diagnosa, rm.id_kunjungan,
                              p.id_pasien, p.nama_pasien, p.no_rm, p.jenis_kelamin, p.tanggal_lahir,
                              u.nama AS nama_dokter,
                              COALESCE(item_counts.total_items, 0) AS total_items
                       FROM resep r
                       JOIN rekam_medis rm ON rm.id_rekam_medis = r.id_rekam_medis
                       JOIN kunjungan k ON k.id_kunjungan = rm.id_kunjungan
                       JOIN pasien p ON p.id_pasien = k.id_pasien
                       LEFT JOIN users u ON u.id_user = k.id_dokter
                       LEFT JOIN (
                           SELECT id_resep, COUNT(*) AS total_items
                           FROM detail_resep
                           GROUP BY id_resep
                       ) item_counts ON item_counts.id_resep = r.id_resep";

    public function all($doctorId = null, $status = null)
    {
        $sql = $this->select;
        $clauses = [];
        $params = [];

        if ($doctorId !== null) {
            $clauses[] = 'k.id_dokter = ?';
            $params[] = (int) $doctorId;
        }
        if ($status !== null && $status !== '') {
            $clauses[] = 'r.status = ?';
            $params[] = $status;
        }

        if ($clauses) {
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }
        $sql .= ' ORDER BY r.id_resep DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id, $doctorId = null)
    {
        $sql = $this->select . ' WHERE r.id_resep = ?';
        $params = [(int) $id];
        if ($doctorId !== null) {
            $sql .= ' AND k.id_dokter = ?';
            $params[] = (int) $doctorId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function byRekamMedis($id_rekam_medis)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM resep WHERE id_rekam_medis = ? ORDER BY id_resep DESC"
        );
        $stmt->execute([$id_rekam_medis]);
        return $stmt->fetchAll();
    }

    public function detail($id_resep)
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, o.kode_obat, o.nama_obat, o.satuan, o.stok AS stok_sekarang
             FROM detail_resep d
             JOIN obat o ON o.id_obat = d.id_obat
             WHERE d.id_resep = ?
             ORDER BY d.id_detail_resep"
        );
        $stmt->execute([$id_resep]);
        return $stmt->fetchAll();
    }

    public function countMenunggu()
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM resep WHERE status = 'Menunggu'"
        )->fetchColumn();
    }

    public function countDiserahkan()
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM resep WHERE status = 'Diserahkan'"
        )->fetchColumn();
    }

    public function countAll()
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM resep")->fetchColumn();
    }

    public function countByDoctor($doctorId)
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM resep r
             JOIN rekam_medis rm ON rm.id_rekam_medis = r.id_rekam_medis
             JOIN kunjungan k ON k.id_kunjungan = rm.id_kunjungan
             WHERE k.id_dokter = ?"
        );
        $stmt->execute([(int) $doctorId]);
        return (int) $stmt->fetchColumn();
    }

    public function resepTerbaruDiserahkan($limit = 5)
    {
        $limit = (int) $limit;
        $sql = $this->select . " WHERE r.status = 'Diserahkan' ORDER BY r.id_resep DESC LIMIT {$limit}";
        return $this->db->query($sql)->fetchAll();
    }

    public function rekamMedisList($doctorId = null)
    {
        $sql = "SELECT rm.id_rekam_medis, rm.diagnosa, k.tanggal_kunjungan,
                    p.nama_pasien, p.no_rm
             FROM rekam_medis rm
             JOIN kunjungan k ON k.id_kunjungan = rm.id_kunjungan
             JOIN pasien p ON p.id_pasien = k.id_pasien";
        $params = [];
        if ($doctorId !== null) {
            $sql .= ' WHERE k.id_dokter = ?';
            $params[] = (int) $doctorId;
        }
        $sql .= ' ORDER BY rm.id_rekam_medis DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function recordAssignedToDoctor($recordId, $doctorId)
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM rekam_medis rm
             JOIN kunjungan k ON k.id_kunjungan = rm.id_kunjungan
             WHERE rm.id_rekam_medis = ? AND k.id_dokter = ?"
        );
        $stmt->execute([(int) $recordId, (int) $doctorId]);
        return (int) $stmt->fetchColumn() === 1;
    }

    /**
     * $items: array of ['id_obat', 'dosis', 'aturan_pakai', 'jumlah']
     */
    public function create($id_rekam_medis, $tanggal, $items)
    {
        // 1. Akumulasikan kebutuhan obat per id_obat
        $kebutuhan = [];
        foreach ($items as $item) {
            $idObat = (int) ($item['id_obat'] ?? 0);
            $jumlah = (int) ($item['jumlah'] ?? 0);
            if ($idObat <= 0 || $jumlah <= 0) {
                throw new InvalidArgumentException("Setiap baris obat harus memiliki obat yang valid dan jumlah lebih dari 0.");
            }
            $kebutuhan[$idObat] = ($kebutuhan[$idObat] ?? 0) + $jumlah;
        }

        if (empty($kebutuhan)) {
            throw new InvalidArgumentException("Resep harus memiliki minimal satu obat yang valid.");
        }

        $this->db->beginTransaction();
        try {
            // 2. Kunci dan periksa stok untuk setiap obat
            $cekStok = $this->db->prepare(
                "SELECT id_obat, nama_obat, stok FROM obat WHERE id_obat = ? FOR UPDATE"
            );

            foreach ($kebutuhan as $idObat => $totalDiminta) {
                $cekStok->execute([$idObat]);
                $obatData = $cekStok->fetch(PDO::FETCH_ASSOC);

                if (!$obatData) {
                    throw new Exception("Obat dengan ID {$idObat} tidak ditemukan di database.");
                }

                $stokTersedia = (int) $obatData['stok'];
                if ($stokTersedia < $totalDiminta) {
                    throw new Exception(
                        "Stok obat '{$obatData['nama_obat']}' tidak mencukupi. Tersedia: {$stokTersedia}, total yang diresepkan: {$totalDiminta}."
                    );
                }
            }

            // 3. Simpan header resep
            $stmt = $this->db->prepare(
                "INSERT INTO resep (id_rekam_medis, tanggal_resep, status, total) VALUES (?, ?, 'Menunggu', 0)"
            );
            $stmt->execute([$id_rekam_medis, $tanggal]);
            $id_resep = $this->db->lastInsertId();

            // 4. Simpan detail resep
            $cariObat = $this->db->prepare("SELECT harga FROM obat WHERE id_obat = ?");
            $insert = $this->db->prepare(
                "INSERT INTO detail_resep
                    (id_resep, id_obat, dosis, aturan_pakai, jumlah, harga, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $total = 0;
            foreach ($items as $item) {
                $cariObat->execute([$item['id_obat']]);
                $harga = (float) $cariObat->fetchColumn();

                $subtotal = $harga * $item['jumlah'];
                $total += $subtotal;

                $insert->execute([
                    $id_resep,
                    $item['id_obat'],
                    $item['dosis'],
                    $item['aturan_pakai'],
                    $item['jumlah'],
                    $harga,
                    $subtotal
                ]);
            }

            $stmt = $this->db->prepare("UPDATE resep SET total = ? WHERE id_resep = ?");
            $stmt->execute([$total, $id_resep]);

            $this->db->commit();
            return $id_resep;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Serahkan obat ke pasien: kurangi stok dan catat transaksi keluar.
     * Melempar Exception bila stok tidak cukup.
     */
    public function serahkan($id_resep)
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "SELECT status FROM resep WHERE id_resep = ? FOR UPDATE"
            );
            $stmt->execute([$id_resep]);
            $status = $stmt->fetchColumn();

            if ($status === false) {
                throw new Exception('Resep tidak ditemukan.');
            }

            if ($status !== 'Menunggu') {
                throw new Exception('Resep ini sudah diproses (status: ' . $status . ').');
            }

            $details = $this->detail($id_resep);
            if (empty($details)) {
                throw new Exception('Detail resep kosong.');
            }

            // Akumulasi kebutuhan obat di detail_resep jika ada obat yang sama di beberapa baris
            $kebutuhan = [];
            foreach ($details as $d) {
                $idObat = (int) $d['id_obat'];
                $kebutuhan[$idObat] = ($kebutuhan[$idObat] ?? 0) + (int) $d['jumlah'];
            }

            $cekStok = $this->db->prepare(
                "SELECT id_obat, nama_obat, stok FROM obat WHERE id_obat = ? FOR UPDATE"
            );
            $kurangi = $this->db->prepare(
                "UPDATE obat SET stok = stok - ? WHERE id_obat = ?"
            );
            $catat = $this->db->prepare(
                "INSERT INTO transaksi_stok (id_obat, jenis, jumlah, keterangan)
                 VALUES (?, 'Keluar', ?, ?)"
            );

            // Validasi stok terkunci sebelum pengurangan
            foreach ($kebutuhan as $idObat => $totalDiminta) {
                $cekStok->execute([$idObat]);
                $obatData = $cekStok->fetch(PDO::FETCH_ASSOC);

                if (!$obatData) {
                    throw new Exception("Data obat tidak ditemukan di database.");
                }

                $stok = (int) $obatData['stok'];
                if ($stok < $totalDiminta) {
                    throw new Exception(
                        "Stok obat '{$obatData['nama_obat']}' tidak mencukupi saat penyerahan (tersedia: {$stok}, dibutuhkan: {$totalDiminta})."
                    );
                }

                $kurangi->execute([$totalDiminta, $idObat]);
                $catat->execute([
                    $idObat,
                    $totalDiminta,
                    'Penyerahan Resep #' . $id_resep
                ]);
            }

            $stmt = $this->db->prepare(
                "UPDATE resep SET status = 'Diserahkan' WHERE id_resep = ?"
            );
            $stmt->execute([$id_resep]);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function batal($id_resep, $doctorId = null)
    {
        $sql = "UPDATE resep r
                JOIN rekam_medis rm ON rm.id_rekam_medis = r.id_rekam_medis
                JOIN kunjungan k ON k.id_kunjungan = rm.id_kunjungan
                SET r.status = 'Batal'
                WHERE r.id_resep = ? AND r.status = 'Menunggu'";
        $params = [(int) $id_resep];
        if ($doctorId !== null) {
            $sql .= ' AND k.id_dokter = ?';
            $params[] = (int) $doctorId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }
}
