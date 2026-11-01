<?php

require_once __DIR__ . '/../models/User.php';

class PenggunaController extends Controller
{
    private $user;

    public function __construct()
    {
        require_role('admin');
        $this->user = new User();
    }

    public function index()
    {
        $this->view('pengguna/index', [
            'title' => 'Manajemen Pengguna',
            'activeUsers' => $this->user->allByStatus(true),
            'inactiveUsers' => $this->user->allByStatus(false)
        ]);
    }

    public function create()
    {
        $this->view('pengguna/form', [
            'title' => 'Tambah Pengguna',
            'item' => null,
            'action' => route('pengguna/store'),
            'activeFrom' => date('Y-m-d'),
            'activeUntil' => date('Y-m-d', strtotime('+1 year'))
        ]);
    }

    public function store()
    {
        $this->mustPost();
        $data = $this->input();
        if ($error = $this->validate($data, true)) {
            flash('error', $error);
            $this->redirect('/index.php?url=pengguna/create');
        }

        try {
            $this->user->create(
                $data['nama'], $data['username'], $data['password'], $data['role'],
                $data['aktif_mulai'], $data['aktif_sampai']
            );
        } catch (PDOException $e) {
            flash('error', 'Username sudah digunakan.');
            $this->redirect('/index.php?url=pengguna/create');
        }

        audit('Menambah pengguna: ' . $data['username']);
        flash('success', 'Pengguna berhasil ditambahkan.');
        $this->redirect('/index.php?url=pengguna');
    }

    public function edit($id = 0)
    {
        $item = $this->user->findById((int) $id);
        if (!$item) {
            flash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('/index.php?url=pengguna');
        }

        $this->view('pengguna/form', [
            'title' => 'Edit Pengguna',
            'item' => $item,
            'action' => route('pengguna/update/' . (int) $item['id_user']),
            'activeFrom' => $item['aktif_mulai'],
            'activeUntil' => $item['aktif_sampai']
        ]);
    }

    public function update($id = 0)
    {
        $this->mustPost();
        $id = (int) $id;
        $item = $this->user->findById($id);
        if (!$item) {
            flash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('/index.php?url=pengguna');
        }

        $data = $this->input();
        if ($error = $this->validate($data, false)) {
            flash('error', $error);
            $this->redirect('/index.php?url=pengguna/edit/' . $id);
        }

        $remainsActiveAdmin = $item['role'] === 'admin'
            && (int) $item['is_active'] === 1
            && $item['aktif_mulai'] <= date('Y-m-d')
            && $item['aktif_sampai'] >= date('Y-m-d');
        $staysActiveAdmin = $data['role'] === 'admin'
            && $data['aktif_mulai'] <= date('Y-m-d')
            && $data['aktif_sampai'] >= date('Y-m-d');
        if (
            $remainsActiveAdmin &&
            !$staysActiveAdmin &&
            $this->user->countCurrentActiveAdmins() <= 1
        ) {
            flash('error', 'Akun ini adalah admin aktif terakhir dan tidak dapat diubah menjadi nonaktif.');
            $this->redirect('/index.php?url=pengguna/edit/' . $id);
        }

        try {
            $this->user->update(
                $id, $data['nama'], $data['username'], $data['role'],
                $data['aktif_mulai'], $data['aktif_sampai'], $data['password']
            );
        } catch (PDOException $e) {
            flash('error', 'Username sudah digunakan.');
            $this->redirect('/index.php?url=pengguna/edit/' . $id);
        }

        audit('Mengubah pengguna: ' . $data['username']);
        flash('success', 'Data pengguna berhasil diperbarui.');
        $this->redirect('/index.php?url=pengguna');
    }

    public function nonaktifkan($id = 0)
    {
        $this->mustPost();
        $id = (int) $id;
        $item = $this->user->findById($id);
        if (!$item) {
            flash('error', 'Pengguna tidak ditemukan.');
        } elseif (
            $item['role'] === 'admin' &&
            $this->user->isCurrentlyActive($item) &&
            $this->user->countCurrentActiveAdmins() <= 1
        ) {
            flash('error', 'Admin aktif terakhir tidak dapat dinonaktifkan.');
        } elseif ($this->user->setActive($id, false)) {
            audit('Menonaktifkan pengguna: ' . $item['username']);
            flash('success', 'Pengguna berhasil dinonaktifkan.');
        }
        $this->redirect('/index.php?url=pengguna');
    }

    public function aktifkan($id = 0)
    {
        $this->mustPost();
        $id = (int) $id;
        $item = $this->user->findById($id);
        if (!$item) {
            flash('error', 'Pengguna tidak ditemukan.');
        } elseif (!$this->user->setActive($id, true)) {
            flash('error', 'Pengguna hanya dapat diaktifkan dalam rentang masa aktifnya.');
        } else {
            audit('Mengaktifkan pengguna: ' . $item['username']);
            flash('success', 'Pengguna berhasil diaktifkan.');
        }
        $this->redirect('/index.php?url=pengguna');
    }

    public function delete($id = 0)
    {
        $this->mustPost();
        $id = (int) $id;
        $item = $this->user->findById($id);
        if (!$item) {
            flash('error', 'Pengguna tidak ditemukan.');
        } elseif ((int) $item['is_active'] !== 0) {
            flash('error', 'Nonaktifkan pengguna terlebih dahulu sebelum menghapus.');
        } elseif ($this->user->deleteIfInactive($id)) {
            audit('Menghapus pengguna: ' . $item['username']);
            flash('success', 'Pengguna berhasil dihapus.');
        }
        $this->redirect('/index.php?url=pengguna');
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
        $fields = ['nama', 'username', 'password', 'role', 'aktif_mulai', 'aktif_sampai'];
        foreach ($fields as $field) {
            if (isset($_POST[$field]) && !is_string($_POST[$field])) {
                return null;
            }
        }

        return [
            'nama' => trim($_POST['nama'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? '',
            'aktif_mulai' => $_POST['aktif_mulai'] ?? '',
            'aktif_sampai' => $_POST['aktif_sampai'] ?? ''
        ];
    }

    private function validate($data, $requirePassword)
    {
        if (!is_array($data)) {
            return 'Data pengguna tidak valid.';
        }
        if ($data['nama'] === '' || $data['username'] === '' || !in_array($data['role'], ['admin', 'resepsionis', 'dokter', 'apoteker'], true)) {
            return 'Nama, username, dan satu role yang valid wajib diisi.';
        }
        if (strlen($data['username']) > 50) {
            return 'Username maksimal 50 karakter.';
        }
        if (($requirePassword || $data['password'] !== '') && strlen($data['password']) < 12) {
            return 'Password minimal 12 karakter.';
        }
        if (!$this->validDate($data['aktif_mulai']) || !$this->validDate($data['aktif_sampai'])) {
            return 'Tanggal masa aktif tidak valid.';
        }
        if ($data['aktif_mulai'] > $data['aktif_sampai']) {
            return 'Tanggal akhir masa aktif harus sama atau setelah tanggal mulai.';
        }
        return null;
    }

    private function validDate($value)
    {
        if (!is_string($value)) {
            return false;
        }
        $date = DateTime::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }
}