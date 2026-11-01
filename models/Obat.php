<?php

class Obat extends Model
{
    public function all($search = '')
    {
        if ($search !== '') {
            $stmt = $this->db->prepare(
                "SELECT * FROM obat
                 WHERE nama_obat LIKE ? OR kode_obat LIKE ? OR kategori LIKE ?
                 ORDER BY nama_obat"
            );
            $like = '%' . $search . '%';
            $stmt->execute([$like, $like, $like]);
            return $stmt->fetchAll();
        }

        return $this->db->query("SELECT * FROM obat ORDER BY nama_obat")->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM obat WHERE id_obat = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function stokMenipis()
    {
        return $this->db->query(
            "SELECT * FROM obat WHERE stok <= stok_minimum ORDER BY stok ASC"
        )->fetchAll();
    }

    public function create($data)
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO obat (kode_obat, nama_obat, kategori, satuan, harga, stok, stok_minimum)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['kode_obat'],
                $data['nama_obat'],
                $data['kategori'],
                $data['satuan'],
                $data['harga'],
                $data['stok'],
                $data['stok_minimum']
            ]);
            $id = $this->db->lastInsertId();

            if ($data['stok'] > 0) {
                $this->catatTransaksi($id, 'Masuk', $data['stok'], 'Stok awal');
            }

            $this->db->commit();
            return $id;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        $lama = $this->find($id);
        if (!$lama) {
            return false;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "UPDATE obat
                 SET kode_obat = ?, nama_obat = ?, kategori = ?, satuan = ?,
                     harga = ?, stok = ?, stok_minimum = ?
                 WHERE id_obat = ?"
            );
            $stmt->execute([
                $data['kode_obat'],
                $data['nama_obat'],
                $data['kategori'],
                $data['satuan'],
                $data['harga'],
                $data['stok'],
                $data['stok_minimum'],
                $id
            ]);

            $selisih = (int) $data['stok'] - (int) $lama['stok'];
            if ($selisih !== 0) {
                $this->catatTransaksi($id, 'Penyesuaian', $selisih, 'Penyesuaian stok manual');
            }

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("DELETE FROM transaksi_stok WHERE id_obat = ?");
            $stmt->execute([$id]);

            $stmt = $this->db->prepare("DELETE FROM obat WHERE id_obat = ?");
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function tambahStok($id, $jumlah, $keterangan)
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("UPDATE obat SET stok = stok + ? WHERE id_obat = ?");
            $stmt->execute([$jumlah, $id]);

            $this->catatTransaksi($id, 'Masuk', $jumlah, $keterangan);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function riwayat($id, $limit = 20)
    {
        $limit = (int) $limit;
        $stmt = $this->db->prepare(
            "SELECT * FROM transaksi_stok
             WHERE id_obat = ?
             ORDER BY id_transaksi DESC
             LIMIT $limit"
        );
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    private function catatTransaksi($id_obat, $jenis, $jumlah, $keterangan)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO transaksi_stok (id_obat, jenis, jumlah, keterangan)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$id_obat, $jenis, $jumlah, $keterangan]);
    }
}
