<?php

require_once __DIR__ . '/../models/JadwalDokter.php';

class JadwalController extends Controller
{
    private $jadwal;

    public function __construct()
    {
        $this->jadwal = new JadwalDokter();
    }

    public function index()
    {
        if (is_logged_in()) {
            require_login();
        }
        $isAdmin = has_role('admin');
        $isDoctor = has_role('dokter');
        $doctorId = $isDoctor ? (int) current_user()['id_user'] : null;
        $isPublic = !is_logged_in();
        $date = $isPublic
            ? $this->validDate($_GET['tanggal'] ?? '') ?: $this->jadwal->nextAvailableDate()
            : null;
        $holidayName = $isPublic ? $this->jadwal->isHoliday($date) : false;
        $items = $isPublic
            ? ($holidayName ? [] : $this->jadwal->forDate($date))
            : $this->jadwal->all($doctorId, $isAdmin || $doctorId !== null);
        $this->view('jadwal/index', [
            'title' => 'Jadwal Dokter',
            'items' => $items,
            'isAdmin' => $isAdmin,
            'isDoctor' => $isDoctor,
            'isPublic' => $isPublic,
            'date' => $date,
            'closedMessage' => $holidayName
                ? 'Klinik tutup pada hari libur: ' . $holidayName . '.'
                : ($isPublic && !$items ? 'Tidak ada dokter dengan jadwal praktik pada tanggal ini.' : '')
        ]);
    }

    private function validDate($value)
    {
        if (!is_string($value) || $value === '') {
            return null;
        }
        $date = DateTime::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value && $value >= date('Y-m-d') ? $value : null;
    }

    public function create()
    {
        require_role('admin');
        $this->view('jadwal/form', [
            'title' => 'Tambah Jadwal Dokter',
            'item' => null,
            'doctors' => $this->jadwal->activeDoctors(),
            'action' => route('jadwal/store')
        ]);
    }

    public function store()
    {
        require_role('admin');
        $this->mustPost();
        $data = $this->input();
        if ($error = $this->validate($data)) {
            flash('error', $error);
            $this->redirect('/index.php?url=jadwal/create');
        }

        $this->jadwal->save($data);
        audit('Menambahkan jadwal dokter: ' . $data['hari'] . ' ' . $data['jam_mulai']);
        flash('success', 'Jadwal dokter berhasil ditambahkan.');
        $this->redirect('/index.php?url=jadwal');
    }

    public function edit($id = 0)
    {
        require_role('admin');
        $item = $this->jadwal->find((int) $id);
        if (!$item) {
            flash('error', 'Jadwal tidak ditemukan.');
            $this->redirect('/index.php?url=jadwal');
        }
        $this->view('jadwal/form', [
            'title' => 'Edit Jadwal Dokter',
            'item' => $item,
            'doctors' => $this->jadwal->activeDoctors(),
            'action' => route('jadwal/update/' . (int) $item['id_jadwal'])
        ]);
    }

    public function update($id = 0)
    {
        require_role('admin');
        $this->mustPost();
        $id = (int) $id;
        if (!$this->jadwal->find($id)) {
            flash('error', 'Jadwal tidak ditemukan.');
            $this->redirect('/index.php?url=jadwal');
        }
        $data = $this->input();
        if ($error = $this->validate($data, $id)) {
            flash('error', $error);
            $this->redirect('/index.php?url=jadwal/edit/' . $id);
        }

        $this->jadwal->save($data, $id);
        audit('Mengubah jadwal dokter: ' . $data['hari'] . ' ' . $data['jam_mulai']);
        flash('success', 'Jadwal dokter berhasil diperbarui.');
        $this->redirect('/index.php?url=jadwal');
    }

    public function delete($id = 0)
    {
        require_role('admin');
        $this->mustPost();
        $item = $this->jadwal->find((int) $id);
        if ($item && $this->jadwal->delete($id)) {
            audit('Menghapus jadwal dokter: ' . $item['nama_dokter'] . ' ' . $item['hari']);
            flash('success', 'Jadwal dokter berhasil dihapus.');
        }
        $this->redirect('/index.php?url=jadwal');
    }

    private function input()
    {
        return [
            'id_user' => (int) ($_POST['id_user'] ?? 0),
            'hari' => trim($_POST['hari'] ?? ''),
            'jam_mulai' => trim($_POST['jam_mulai'] ?? ''),
            'jam_selesai' => trim($_POST['jam_selesai'] ?? ''),
            'poli' => trim($_POST['poli'] ?? ''),
            'lokasi' => trim($_POST['lokasi'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
    }

    private function validate($data, $excludeId = 0)
    {
        if (!$this->jadwal->isDoctor($data['id_user'])) {
            return 'Pilih akun dokter yang valid.';
        }
        if (!$this->jadwal->validDay($data['hari'])) {
            return 'Pilih hari jadwal yang valid.';
        }
        if (!$this->validTime($data['jam_mulai']) || !$this->validTime($data['jam_selesai']) || $data['jam_mulai'] >= $data['jam_selesai']) {
            return 'Jam selesai harus setelah jam mulai.';
        }
        if ($data['poli'] === '' || $data['lokasi'] === '') {
            return 'Poli dan lokasi wajib diisi.';
        }
        if ($data['is_active'] && $this->jadwal->hasConflict(
            $data['id_user'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'], $excludeId
        )) {
            return 'Jadwal dokter bertabrakan dengan jadwal aktif lainnya.';
        }
        return null;
    }

    private function validTime($value)
    {
        return is_string($value) && preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value);
    }

    private function mustPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Metode tidak diizinkan.');
        }
    }
}