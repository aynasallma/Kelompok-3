<?php

class HariLibur extends Model
{
    public function all()
    {
        return $this->db->query(
            "SELECT h.*, u.nama AS nama_petugas
             FROM hari_libur h LEFT JOIN users u ON u.id_user = h.dibuat_oleh
             ORDER BY h.tanggal DESC"
        )->fetchAll();
    }

    public function add($date, $name, $userId)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO hari_libur (tanggal, nama, dibuat_oleh) VALUES (?, ?, ?)'
        );
        return $stmt->execute([$date, $name, (int) $userId]);
    }

    public function delete($date)
    {
        $stmt = $this->db->prepare('DELETE FROM hari_libur WHERE tanggal = ?');
        return $stmt->execute([$date]) && $stmt->rowCount() > 0;
    }
}