<?php

class Resep extends Model
{
    private $select = "SELECT r.*, rm.diagnosa, rm.id_kunjungan,
                              p.id_pasien, p.nama_pasien, p.no_rm
                       FROM resep r
                       JOIN rekam_medis rm ON rm.id_rekam_medis = r.id_rekam_medis
                       JOIN kunjungan k ON k.id_kunjungan = rm.id_kunjungan
                       JOIN pasien p ON p.id_pasien = k.id_pasien";

    public function all($doctorId = null)
    {
        $sql = $this->select;
        $params = [];
        if ($doctorId !== null) {
            $sql .= ' WHERE k.id_dokter = ?';
            $params[] = (int) $doctorId;
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
            "SELECT d.*, o.kode_obat, o.nama_obat, o.satuan
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
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO resep (id_rekam_medis, tanggal_resep) VALUES (?, ?)"
            );
            $stmt->execute([$id_rekam_medis, $tanggal]);
            $id_resep = $this->db->lastInsertId();

            $cariObat = $this->db->prepare("SELECT harga FROM obat WHERE id_obat = ?");
            $insert = $this->db->prepare(
                "INSERT INTO detail_resep
                    (id_resep, id_obat, dosis, aturan_pakai, jumlah, harga, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $total = 0;
            foreach ($items as $item) {
                $cariObat->execute([$item['id_obat']]);
                $harga = $cariObat->fetchColumn();
                if ($harga === false) {
                    throw new Exception('Obat tidak ditemukan.');
                }

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

            if ($status !== 'Menunggu') {
                throw new Exception('Resep ini sudah diproses.');
            }

            $details = $this->detail($id_resep);

            $cekStok = $this->db->prepare(
                "SELECT stok FROM obat WHERE id_obat = ? FOR UPDATE"
            );
            $kurangi = $this->db->prepare(
                "UPDATE obat SET stok = stok - ? WHERE id_obat = ?"
            );
            $catat = $this->db->prepare(
                "INSERT INTO transaksi_stok (id_obat, jenis, jumlah, keterangan)
                 VALUES (?, 'Keluar', ?, ?)"
            );

            foreach ($details as $d) {
                $cekStok->execute([$d['id_obat']]);
                $stok = (int) $cekStok->fetchColumn();

                if ($stok < $d['jumlah']) {
                    throw new Exception(
                        'Stok ' . $d['nama_obat'] . ' tidak cukup (tersedia ' .
                        $stok . ', dibutuhkan ' . $d['jumlah'] . ').'
                    );
                }

                $kurangi->execute([$d['jumlah'], $d['id_obat']]);
                $catat->execute([
                    $d['id_obat'],
                    $d['jumlah'],
                    'Resep #' . $id_resep
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
