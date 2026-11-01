<?php

class Pasien extends Model
{
    public function all($search = '', $dateFrom = '', $dateTo = '', $doctorId = null)
    {
        $conditions = [];
        $params = [];

        if ($search !== '') {
            $like = '%' . $search . '%';
            $conditions[] = '(nama_pasien LIKE ? OR no_rm LIKE ?)';
            $params[] = $like;
            $params[] = $like;
        }

        if ($dateFrom !== '') {
            $conditions[] = 'created_at >= ?';
            $params[] = $dateFrom . ' 00:00:00';
        }

        if ($dateTo !== '') {
            $conditions[] = 'created_at < DATE_ADD(?, INTERVAL 1 DAY)';
            $params[] = $dateTo;
        }
        if ($doctorId !== null) {
            $conditions[] = 'EXISTS (SELECT 1 FROM kunjungan kj WHERE kj.id_pasien = pasien.id_pasien AND kj.id_dokter = ?)';
            $params[] = (int) $doctorId;
        }

        $sql = 'SELECT * FROM pasien';
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY id_pasien DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id, $doctorId = null)
    {
        $sql = 'SELECT * FROM pasien WHERE id_pasien = ?';
        $params = [(int) $id];
        if ($doctorId !== null) {
            $sql .= ' AND EXISTS (SELECT 1 FROM kunjungan kj WHERE kj.id_pasien = pasien.id_pasien AND kj.id_dokter = ?)';
            $params[] = (int) $doctorId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function countAll()
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM pasien")->fetchColumn();
    }

    public function generateNoRM()
    {
        $prefix = 'RM-' . date('Y') . '-';
        $stmt = $this->db->prepare(
            "SELECT no_rm FROM pasien WHERE no_rm LIKE ? ORDER BY no_rm DESC LIMIT 1"
        );
        $stmt->execute([$prefix . '%']);
        $last = $stmt->fetchColumn();

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO pasien (no_rm, nama_pasien, jenis_kelamin, tanggal_lahir, alamat, no_hp)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $this->generateNoRM(),
            $data['nama_pasien'],
            $data['jenis_kelamin'],
            $data['tanggal_lahir'],
            $data['alamat'],
            $data['no_hp']
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE pasien
             SET nama_pasien = ?, jenis_kelamin = ?, tanggal_lahir = ?, alamat = ?, no_hp = ?
             WHERE id_pasien = ?"
        );
        return $stmt->execute([
            $data['nama_pasien'],
            $data['jenis_kelamin'],
            $data['tanggal_lahir'],
            $data['alamat'],
            $data['no_hp'],
            $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM pasien WHERE id_pasien = ?");
        return $stmt->execute([$id]);
    }

    /* ----- Kunjungan ----- */

    public function kunjunganByPasien($id_pasien, $doctorId = null)
    {
        $sql = "SELECT k.*, rm.id_rekam_medis
             FROM kunjungan k
             LEFT JOIN rekam_medis rm ON rm.id_kunjungan = k.id_kunjungan
             WHERE k.id_pasien = ?";
        $params = [(int) $id_pasien];
        if ($doctorId !== null) {
            $sql .= ' AND k.id_dokter = ?';
            $params[] = (int) $doctorId;
        }
        $sql .= ' ORDER BY k.tanggal_kunjungan DESC, k.id_kunjungan DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function kunjunganHariIni($doctorId = null)
    {
        $sql = "SELECT k.*, p.nama_pasien, p.no_rm, rm.id_rekam_medis
             FROM kunjungan k
             JOIN pasien p ON p.id_pasien = k.id_pasien
             LEFT JOIN rekam_medis rm ON rm.id_kunjungan = k.id_kunjungan
             WHERE k.tanggal_kunjungan = CURDATE()";
        $params = [];
        if ($doctorId !== null) {
            $sql .= ' AND k.id_dokter = ?';
            $params[] = (int) $doctorId;
        }
        $sql .= ' ORDER BY k.poli, k.no_antrian';
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

    public function createKunjungan($id_pasien, $tanggal, $poli, $id_dokter)
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "SELECT COALESCE(MAX(no_antrian), 0) + 1
                 FROM kunjungan
                 WHERE tanggal_kunjungan = ? AND poli = ?
                 FOR UPDATE"
            );
            $stmt->execute([$tanggal, $poli]);
            $noAntrian = (int) $stmt->fetchColumn();

            $stmt = $this->db->prepare(
                 "INSERT INTO kunjungan (id_pasien, id_dokter, tanggal_kunjungan, no_antrian, poli)
                  VALUES (?, ?, ?, ?, ?)"
            );
              $stmt->execute([$id_pasien, (int) $id_dokter, $tanggal, $noAntrian, $poli]);
            $id = $this->db->lastInsertId();

            $this->db->commit();
            return ['id' => $id, 'no_antrian' => $noAntrian];
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateStatusKunjungan($id_kunjungan, $status)
    {
        $stmt = $this->db->prepare(
            "UPDATE kunjungan SET status = ? WHERE id_kunjungan = ?"
        );
        return $stmt->execute([$status, $id_kunjungan]);
    }
}
