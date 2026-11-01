<?php

class JadwalDokter extends Model
{
    private $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function all($doctorId = null, $includeInactive = false)
    {
        $conditions = [];
        $params = [];
        if (!$includeInactive) {
            $conditions[] = 'j.is_active = 1';
            $conditions[] = 'u.is_active = 1';
            $conditions[] = 'u.aktif_mulai <= ? AND u.aktif_sampai >= ?';
            $today = date('Y-m-d');
            $params[] = $today;
            $params[] = $today;
        }
        if ($doctorId !== null) {
            $conditions[] = 'j.id_user = ?';
            $params[] = (int) $doctorId;
        }

        $sql = "SELECT j.*, u.nama AS nama_dokter, u.is_active AS status_akun,
                   u.aktif_mulai AS akun_aktif_mulai, u.aktif_sampai AS akun_aktif_sampai
                FROM jadwal_dokter j
                JOIN users u ON u.id_user = j.id_user";
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= " ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), j.jam_mulai, u.nama";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare(
            "SELECT j.*, u.nama AS nama_dokter
             FROM jadwal_dokter j JOIN users u ON u.id_user = j.id_user
             WHERE j.id_jadwal = ?"
        );
        $stmt->execute([(int) $id]);
        return $stmt->fetch();
    }

    public function activeDoctors()
    {
        $stmt = $this->db->prepare(
            "SELECT id_user, nama FROM users
             WHERE role = 'dokter' AND is_active = 1
             AND aktif_mulai <= ? AND aktif_sampai >= ?
             ORDER BY nama"
        );
        $today = date('Y-m-d');
        $stmt->execute([$today, $today]);
        return $stmt->fetchAll();
    }

    public function poliOptions()
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare(
            "SELECT DISTINCT j.poli FROM jadwal_dokter j
             JOIN users u ON u.id_user = j.id_user
             WHERE j.is_active = 1 AND u.is_active = 1
             AND u.aktif_mulai <= ? AND u.aktif_sampai >= ?
             ORDER BY j.poli"
        );
        $stmt->execute([$today, $today]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function weekdayForDate($date)
    {
        $days = [1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $timestamp = strtotime($date);
        return $timestamp ? $days[(int) date('N', $timestamp)] : null;
    }

    public function isHoliday($date)
    {
        $stmt = $this->db->prepare('SELECT nama FROM hari_libur WHERE tanggal = ? LIMIT 1');
        $stmt->execute([$date]);
        return $stmt->fetchColumn() ?: false;
    }

    public function poliOptionsForDate($date)
    {
        $day = $this->weekdayForDate($date);
        if (!$day || $date < date('Y-m-d') || $this->isHoliday($date)) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT DISTINCT j.poli FROM jadwal_dokter j
             JOIN users u ON u.id_user = j.id_user
               WHERE j.hari = ? AND j.is_active = 1 AND u.role = 'dokter' AND u.is_active = 1
             AND u.aktif_mulai <= ? AND u.aktif_sampai >= ?
               AND (? > CURDATE() OR j.jam_selesai > CURTIME())
             ORDER BY j.poli"
        );
           $stmt->execute([$day, $date, $date, $date]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function nextAvailableDate($daysAhead = 31)
    {
        $daysAhead = max(1, min(62, (int) $daysAhead));
        for ($offset = 0; $offset < $daysAhead; $offset++) {
            $date = date('Y-m-d', strtotime('+' . $offset . ' days'));
            if ($this->poliOptionsForDate($date)) {
                return $date;
            }
        }
        return date('Y-m-d');
    }

    public function doctorsForPoli($poli)
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare(
            "SELECT DISTINCT u.id_user, u.nama FROM jadwal_dokter j
             JOIN users u ON u.id_user = j.id_user
             WHERE j.poli = ? AND j.is_active = 1 AND u.role = 'dokter' AND u.is_active = 1
             AND u.aktif_mulai <= ? AND u.aktif_sampai >= ?
             ORDER BY u.nama"
        );
        $stmt->execute([$poli, $today, $today]);
        return $stmt->fetchAll();
    }

    public function doctorsForPoliOnDate($poli, $date)
    {
        $day = $this->weekdayForDate($date);
        if (!$day || $date < date('Y-m-d') || $this->isHoliday($date)) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT DISTINCT u.id_user, u.nama FROM jadwal_dokter j
             JOIN users u ON u.id_user = j.id_user
             WHERE j.hari = ? AND j.poli = ? AND j.is_active = 1 AND u.role = 'dokter' AND u.is_active = 1
             AND u.aktif_mulai <= ? AND u.aktif_sampai >= ?
             AND (? > CURDATE() OR j.jam_selesai > CURTIME())
             ORDER BY u.nama"
        );
        $stmt->execute([$day, $poli, $date, $date, $date]);
        return $stmt->fetchAll();
    }

    public function forDate($date)
    {
        $day = $this->weekdayForDate($date);
        if (!$day || $date < date('Y-m-d') || $this->isHoliday($date)) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT j.*, u.nama AS nama_dokter, u.is_active AS status_akun,
                    u.aktif_mulai AS akun_aktif_mulai, u.aktif_sampai AS akun_aktif_sampai
             FROM jadwal_dokter j JOIN users u ON u.id_user = j.id_user
             WHERE j.hari = ? AND j.is_active = 1 AND u.role = 'dokter' AND u.is_active = 1
             AND u.aktif_mulai <= ? AND u.aktif_sampai >= ?
             AND (? > CURDATE() OR j.jam_selesai > CURTIME())
             ORDER BY j.jam_mulai, u.nama"
        );
        $stmt->execute([$day, $date, $date, $date]);
        return $stmt->fetchAll();
    }

    public function doctorCanHandle($doctorId, $poli, $date = null)
    {
        $doctors = $date === null
            ? $this->doctorsForPoli($poli)
            : $this->doctorsForPoliOnDate($poli, $date);
        return in_array((int) $doctorId, array_map('intval', array_column($doctors, 'id_user')), true);
    }

    public function isDoctor($id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE id_user = ? AND role = 'dokter'");
        $stmt->execute([(int) $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function hasConflict($idUser, $hari, $start, $end, $excludeId = 0)
    {
        $sql = "SELECT COUNT(*) FROM jadwal_dokter
                WHERE id_user = ? AND hari = ? AND is_active = 1
                AND jam_mulai < ? AND jam_selesai > ?";
        $params = [(int) $idUser, $hari, $end, $start];
        if ($excludeId) {
            $sql .= ' AND id_jadwal <> ?';
            $params[] = (int) $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function save($data, $id = 0)
    {
        if ($id) {
            $stmt = $this->db->prepare(
                "UPDATE jadwal_dokter
                 SET id_user = ?, hari = ?, jam_mulai = ?, jam_selesai = ?, poli = ?, lokasi = ?, is_active = ?
                 WHERE id_jadwal = ?"
            );
            return $stmt->execute([
                $data['id_user'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'],
                $data['poli'], $data['lokasi'], $data['is_active'], $id
            ]);
        }

        $stmt = $this->db->prepare(
            "INSERT INTO jadwal_dokter (id_user, hari, jam_mulai, jam_selesai, poli, lokasi, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['id_user'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'],
            $data['poli'], $data['lokasi'], $data['is_active']
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM jadwal_dokter WHERE id_jadwal = ?");
        return $stmt->execute([(int) $id]);
    }

    public function validDay($day)
    {
        return in_array($day, $this->hari, true);
    }
}