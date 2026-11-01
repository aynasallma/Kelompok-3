<?php

require_once __DIR__ . '/../models/Obat.php';

class ObatController extends Controller
{
    private $obat;

    public function __construct()
    {
        require_role('admin', 'apoteker', 'dokter');
        $this->obat = new Obat();
    }

    public function index()
    {
        $search = trim($_GET['q'] ?? '');

        $this->view('obat/index', [
            'title'  => 'Data Obat',
            'items'  => $this->obat->all($search),
            'search' => $search
        ]);
    }

    public function create()
    {
        require_role('admin', 'apoteker');

        $this->view('obat/form', [
            'title'  => 'Tambah Obat',
            'item'   => null,
            'action' => route('obat/store')
        ]);
    }

    public function store()
    {
        require_role('admin', 'apoteker');
        $this->mustPost();

        $data = $this->input();
        if ($error = $this->validate($data)) {
            flash('error', $error);
            $this->redirect('/index.php?url=obat/create');
        }

        try {
            $this->obat->create($data);
        } catch (PDOException $e) {
            flash('error', 'Kode obat sudah digunakan.');
            $this->redirect('/index.php?url=obat/create');
        }

        audit('Menambah obat: ' . $data['nama_obat']);
        flash('success', 'Obat berhasil ditambahkan.');
        $this->redirect('/index.php?url=obat');
    }

    public function edit($id = 0)
    {
        require_role('admin', 'apoteker');

        $item = $this->obat->find((int) $id);
        if (!$item) {
            flash('error', 'Obat tidak ditemukan.');
            $this->redirect('/index.php?url=obat');
        }

        $this->view('obat/form', [
            'title'  => 'Edit Obat',
            'item'   => $item,
            'action' => route('obat/update/' . $item['id_obat'])
        ]);
    }

    public function update($id = 0)
    {
        require_role('admin', 'apoteker');
        $this->mustPost();

        $id = (int) $id;
        $data = $this->input();
        if ($error = $this->validate($data)) {
            flash('error', $error);
            $this->redirect('/index.php?url=obat/edit/' . $id);
        }

        try {
            $this->obat->update($id, $data);
        } catch (PDOException $e) {
            flash('error', 'Kode obat sudah digunakan.');
            $this->redirect('/index.php?url=obat/edit/' . $id);
        }

        audit('Mengubah obat: ' . $data['nama_obat']);
        flash('success', 'Data obat berhasil diperbarui.');
        $this->redirect('/index.php?url=obat');
    }

    public function delete($id = 0)
    {
        require_role('admin');
        $this->mustPost();

        $item = $this->obat->find((int) $id);
        if ($item) {
            try {
                $this->obat->delete($item['id_obat']);
                audit('Menghapus obat: ' . $item['nama_obat']);
                flash('success', 'Obat berhasil dihapus.');
            } catch (PDOException $e) {
                flash('error', 'Obat tidak dapat dihapus karena sudah dipakai pada resep.');
            }
        }
        $this->redirect('/index.php?url=obat');
    }

    public function stok($id = 0)
    {
        require_role('admin', 'apoteker');

        $item = $this->obat->find((int) $id);
        if (!$item) {
            flash('error', 'Obat tidak ditemukan.');
            $this->redirect('/index.php?url=obat');
        }

        $this->view('obat/stok', [
            'title'   => 'Stok Obat',
            'item'    => $item,
            'riwayat' => $this->obat->riwayat($item['id_obat'])
        ]);
    }

    public function tambahStok($id = 0)
    {
        require_role('admin', 'apoteker');
        $this->mustPost();

        $item = $this->obat->find((int) $id);
        $jumlah = (int) ($_POST['jumlah'] ?? 0);
        $ket = trim($_POST['keterangan'] ?? '');

        if (!$item || $jumlah <= 0) {
            flash('error', 'Jumlah stok masuk harus lebih dari 0.');
            $this->redirect('/index.php?url=obat/stok/' . (int) $id);
        }

        $this->obat->tambahStok($item['id_obat'], $jumlah, $ket !== '' ? $ket : 'Stok masuk');
        audit('Stok masuk ' . $jumlah . ' ' . $item['satuan'] . ' untuk ' . $item['nama_obat']);
        flash('success', 'Stok berhasil ditambahkan.');
        $this->redirect('/index.php?url=obat/stok/' . $item['id_obat']);
    }

    private function mustPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Metode tidak diizinkan.');
        }
    }

    private function input()
    {
        return [
            'kode_obat'    => trim($_POST['kode_obat'] ?? ''),
            'nama_obat'    => trim($_POST['nama_obat'] ?? ''),
            'kategori'     => trim($_POST['kategori'] ?? ''),
            'satuan'       => trim($_POST['satuan'] ?? ''),
            'harga'        => (float) ($_POST['harga'] ?? 0),
            'stok'         => (int) ($_POST['stok'] ?? 0),
            'stok_minimum' => (int) ($_POST['stok_minimum'] ?? 10)
        ];
    }

    private function validate($data)
    {
        if ($data['kode_obat'] === '' || $data['nama_obat'] === '' || $data['satuan'] === '') {
            return 'Kode obat, nama obat, dan satuan wajib diisi.';
        }
        if ($data['harga'] < 0 || $data['stok'] < 0 || $data['stok_minimum'] < 0) {
            return 'Harga dan stok tidak boleh negatif.';
        }
        return null;
    }
}
