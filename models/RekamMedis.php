<?php

class RekamMedis extends Model
{
        private $select = "SELECT rm.*, k.tanggal_kunjungan, k.poli, k.no_antrian, k.id_dokter,
                      d.nama AS nama_dokter, p.id_pasien, p.nama_pasien, p.no_rm,
                      p.jenis_kelamin, p.tanggal_lahir, p.no_hp, p.alamat
                       FROM rekam_medis rm
                       JOIN kunjungan k ON k.id_kunjungan = rm.id_kunjungan
                    LEFT JOIN users d ON d.id_user = k.id_dokter
                       JOIN pasien p ON p.id_pasien = k.id_pasien";

    public function countAll()
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM rekam_medis")->fetchColumn();
    }

    public function countByDoctor($doctorId)
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM rekam_medis rm
             JOIN kunjungan k ON k.id_kunjungan = rm.id_kunjungan
             WHERE k.id_dokter = ?"
        );
        $stmt->execute([(int) $doctorId]);
        return (int) $stmt->fetchColumn();
    }

    public function riwayatTerbaru($limit = 5, $doctorId = null)
    {
        $limit = (int) $limit;
        $sql = $this->select;
        $params = [];
        if ($doctorId !== null) {
            $sql .= ' WHERE k.id_dokter = ?';
            $params[] = (int) $doctorId;
        }
        $sql .= " ORDER BY rm.id_rekam_medis DESC LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function all($search = '', $doctorId = null)
    {
        $conditions = [];
        $params = [];
        if ($search !== '') {
            $like = '%' . $search . '%';
            $conditions[] = '(p.nama_pasien LIKE ? OR p.no_rm LIKE ? OR rm.diagnosa LIKE ?)';
            array_push($params, $like, $like, $like);
        }
        if ($doctorId !== null) {
            $conditions[] = 'k.id_dokter = ?';
            $params[] = (int) $doctorId;
        }

        $sql = $this->select;
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY rm.id_rekam_medis DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id, $doctorId = null)
    {
        $sql = $this->select . ' WHERE rm.id_rekam_medis = ?';
        $params = [(int) $id];
        if ($doctorId !== null) {
            $sql .= ' AND k.id_dokter = ?';
            $params[] = (int) $doctorId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function byPasien($id_pasien, $doctorId = null)
    {
        $sql = $this->select . ' WHERE p.id_pasien = ?';
        $params = [(int) $id_pasien];
        if ($doctorId !== null) {
            $sql .= ' AND k.id_dokter = ?';
            $params[] = (int) $doctorId;
        }
        $sql .= ' ORDER BY k.tanggal_kunjungan DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function kunjunganTersedia($doctorId = null)
    {
        $sql = "SELECT k.*, p.nama_pasien, p.no_rm
             FROM kunjungan k
             JOIN pasien p ON p.id_pasien = k.id_pasien
             LEFT JOIN rekam_medis rm ON rm.id_kunjungan = k.id_kunjungan
             WHERE rm.id_rekam_medis IS NULL AND k.status <> 'Batal'";
        $params = [];
        if ($doctorId !== null) {
            $sql .= ' AND k.id_dokter = ?';
            $params[] = (int) $doctorId;
        }
        $sql .= ' ORDER BY k.tanggal_kunjungan DESC, k.poli, k.no_antrian';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function visitAssignedToDoctor($visitId, $doctorId)
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM kunjungan WHERE id_kunjungan = ? AND id_dokter = ?'
        );
        $stmt->execute([(int) $visitId, (int) $doctorId]);
        return (int) $stmt->fetchColumn() === 1;
    }

    public function create($data)
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO rekam_medis (id_kunjungan, keluhan, diagnosa, tindakan, catatan)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['id_kunjungan'],
                $data['keluhan'],
                $data['diagnosa'],
                $data['tindakan'],
                $data['catatan']
            ]);
            $id = $this->db->lastInsertId();

            $stmt = $this->db->prepare(
                "UPDATE kunjungan SET status = 'Selesai' WHERE id_kunjungan = ?"
            );
            $stmt->execute([$data['id_kunjungan']]);

            $this->db->commit();
            return $id;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE rekam_medis
             SET keluhan = ?, diagnosa = ?, tindakan = ?, catatan = ?
             WHERE id_rekam_medis = ?"
        );
        return $stmt->execute([
            $data['keluhan'],
            $data['diagnosa'],
            $data['tindakan'],
            $data['catatan'],
            $id
        ]);
    }

    public function delete($id)
    {
        $rm = $this->find($id);
        if (!$rm) {
            return false;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("DELETE FROM rekam_medis WHERE id_rekam_medis = ?");
            $stmt->execute([$id]);

            $stmt = $this->db->prepare(
                "UPDATE kunjungan SET status = 'Diperiksa' WHERE id_kunjungan = ?"
            );
            $stmt->execute([$rm['id_kunjungan']]);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
