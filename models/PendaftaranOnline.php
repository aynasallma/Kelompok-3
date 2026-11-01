<?php

require_once __DIR__ . '/Pasien.php';

class PendaftaranOnline extends Model
{
    public function countRecentFromIp($ipHash)
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM pendaftaran_online
             WHERE ip_hash = ? AND diajukan_pada >= DATE_SUB(NOW(), INTERVAL 1 HOUR)"
        );
        $stmt->execute([$ipHash]);
        return (int) $stmt->fetchColumn();
    }

    public function create($data, $tokenHash, $ipHash, $reference)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO pendaftaran_online
             (token_hash, nomor_pendaftaran, ip_hash, nama_pasien, jenis_kelamin, tanggal_lahir, alamat, no_hp, poli, tanggal_kunjungan)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $tokenHash,
            $reference,
            $ipHash,
            $data['nama_pasien'],
            $data['jenis_kelamin'],
            $data['tanggal_lahir'],
            $data['alamat'],
            $data['no_hp'],
            $data['poli'],
            $data['tanggal_kunjungan']
        ]);
    }

    public function findPublicStatus($tokenHash, $reference = '')
    {
        $stmt = $this->db->prepare(
                "SELECT p.status, p.poli, p.tanggal_kunjungan, p.diajukan_pada, p.direview_pada,
                    (SELECT r.status FROM resep r
                     JOIN rekam_medis rm ON rm.id_rekam_medis = r.id_rekam_medis
                     JOIN kunjungan k ON k.id_kunjungan = rm.id_kunjungan
                     WHERE k.id_pendaftaran = p.id_pendaftaran
                     ORDER BY r.id_resep DESC LIMIT 1) AS status_resep
                 FROM pendaftaran_online p
                 WHERE p.token_hash = ? OR p.nomor_pendaftaran = ? LIMIT 1"
        );
        $stmt->execute([$tokenHash, $reference]);
        return $stmt->fetch();
    }

    public function pending()
    {
        return $this->db->query(
            "SELECT * FROM pendaftaran_online WHERE status = 'Menunggu'
             ORDER BY diajukan_pada ASC"
        )->fetchAll();
    }

    public function recentlyReviewed($limit = 50)
    {
        $limit = max(1, min(100, (int) $limit));
        return $this->db->query(
            "SELECT p.*, u.nama AS nama_reviewer, dokter.nama AS nama_dokter, pasien.no_rm
             FROM pendaftaran_online p
             LEFT JOIN users u ON u.id_user = p.direview_oleh
             LEFT JOIN users dokter ON dokter.id_user = p.id_dokter
             LEFT JOIN pasien ON pasien.id_pasien = p.id_pasien
             WHERE p.status <> 'Menunggu'
             ORDER BY p.direview_pada DESC LIMIT $limit"
        )->fetchAll();
    }

    public function countPending()
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM pendaftaran_online WHERE status = 'Menunggu'"
        )->fetchColumn();
    }

    public function resolve($id, $decision, $reviewerId, $note = '', $doctorId = 0)
    {
        if (!in_array($decision, ['Disetujui', 'Ditolak'], true)) {
            throw new InvalidArgumentException('Keputusan tidak valid.');
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM pendaftaran_online WHERE id_pendaftaran = ? FOR UPDATE"
            );
            $stmt->execute([(int) $id]);
            $request = $stmt->fetch();
            if (!$request || $request['status'] !== 'Menunggu') {
                $this->db->rollBack();
                return false;
            }

            $patientId = null;
            $visitId = null;
            if ($decision === 'Disetujui') {
                require_once __DIR__ . '/JadwalDokter.php';
                if (!(new JadwalDokter())->doctorCanHandle($doctorId, $request['poli'], $request['tanggal_kunjungan'])) {
                    throw new InvalidArgumentException('Pilih dokter aktif yang memiliki jadwal pada poli dan tanggal tersebut.');
                }
            }
            if ($decision === 'Disetujui') {
                $patient = new Pasien();
                $patientId = $patient->create([
                    'nama_pasien' => $request['nama_pasien'],
                    'jenis_kelamin' => $request['jenis_kelamin'],
                    'tanggal_lahir' => $request['tanggal_lahir'],
                    'alamat' => $request['alamat'],
                    'no_hp' => $request['no_hp']
                ]);

                $visitDate = $request['tanggal_kunjungan'];
                $queue = $this->db->prepare(
                    "SELECT COALESCE(MAX(no_antrian), 0) + 1
                     FROM kunjungan WHERE tanggal_kunjungan = ? AND poli = ? FOR UPDATE"
                );
                $queue->execute([$visitDate, $request['poli']]);
                $queueNumber = (int) $queue->fetchColumn();

                $visit = $this->db->prepare(
                    "INSERT INTO kunjungan
                     (id_pasien, id_dokter, id_pendaftaran, tanggal_kunjungan, no_antrian, poli, status)
                     VALUES (?, ?, ?, ?, ?, ?, 'Menunggu')"
                );
                $visit->execute([
                    $patientId, (int) $doctorId, (int) $id,
                    $visitDate, $queueNumber, $request['poli']
                ]);
                $visitId = (int) $this->db->lastInsertId();
            }

            $update = $this->db->prepare(
                "UPDATE pendaftaran_online
                  SET status = ?, catatan_staf = ?, id_pasien = ?, id_dokter = ?, direview_oleh = ?, direview_pada = NOW()
                 WHERE id_pendaftaran = ? AND status = 'Menunggu'"
            );
              $update->execute([
                 $decision, $note !== '' ? $note : null, $patientId,
                 $decision === 'Disetujui' ? (int) $doctorId : null,
                 (int) $reviewerId, (int) $id
              ]);
            if ($update->rowCount() !== 1) {
                throw new RuntimeException('Permohonan sudah diproses oleh petugas lain.');
            }

            $this->db->commit();
            return [
                'id_pasien' => $patientId,
                'nama_pasien' => $request['nama_pasien'],
                'id_dokter' => $decision === 'Disetujui' ? (int) $doctorId : null,
                'id_kunjungan' => $visitId
            ];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function assignedToDoctor($doctorId)
    {
        $stmt = $this->db->prepare(
                "SELECT p.id_pendaftaran, p.nomor_pendaftaran, p.poli, p.diajukan_pada,
                    pasien.id_pasien, pasien.nama_pasien, pasien.no_rm,
                    k.id_kunjungan, rm.id_rekam_medis
             FROM pendaftaran_online p
             JOIN pasien ON pasien.id_pasien = p.id_pasien
                 JOIN kunjungan k ON k.id_pendaftaran = p.id_pendaftaran
                 LEFT JOIN rekam_medis rm ON rm.id_kunjungan = k.id_kunjungan
             WHERE p.id_dokter = ? AND p.status = 'Disetujui' AND k.status <> 'Selesai'
                 ORDER BY k.status = 'Selesai', p.direview_pada DESC"
        );
        $stmt->execute([(int) $doctorId]);
        return $stmt->fetchAll();
    }
}