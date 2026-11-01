<?php

class Laporan extends Model
{
    public function ringkasan($from, $to)
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM pasien WHERE DATE(created_at) BETWEEN ? AND ?"
        );
        $stmt->execute([$from, $to]);
        $newPatients = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM kunjungan WHERE tanggal_kunjungan BETWEEN ? AND ?"
        );
        $stmt->execute([$from, $to]);
        $visits = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM rekam_medis rm
             JOIN kunjungan k ON k.id_kunjungan = rm.id_kunjungan
             WHERE k.tanggal_kunjungan BETWEEN ? AND ?"
        );
        $stmt->execute([$from, $to]);
        $medicalRecords = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT COUNT(*), COALESCE(SUM(total), 0)
             FROM resep WHERE tanggal_resep BETWEEN ? AND ?"
        );
        $stmt->execute([$from, $to]);
        $prescriptions = $stmt->fetch();

        $stmt = $this->db->prepare(
            "SELECT status, COUNT(*) AS jumlah FROM kunjungan
             WHERE tanggal_kunjungan BETWEEN ? AND ? GROUP BY status ORDER BY status"
        );
        $stmt->execute([$from, $to]);
        $visitsByStatus = $stmt->fetchAll();

        $stmt = $this->db->prepare(
            "SELECT poli, COUNT(*) AS jumlah FROM kunjungan
             WHERE tanggal_kunjungan BETWEEN ? AND ? GROUP BY poli ORDER BY jumlah DESC, poli"
        );
        $stmt->execute([$from, $to]);

        return [
            'newPatients' => $newPatients,
            'visits' => $visits,
            'medicalRecords' => $medicalRecords,
            'prescriptions' => (int) $prescriptions['COUNT(*)'],
            'prescriptionValue' => (float) $prescriptions['COALESCE(SUM(total), 0)'],
            'visitsByStatus' => $visitsByStatus,
            'visitsByPoli' => $stmt->fetchAll()
        ];
    }
}