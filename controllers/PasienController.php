<?php

require_once __DIR__ . '/../models/Pasien.php';
require_once __DIR__ . '/../models/RekamMedis.php';
require_once __DIR__ . '/../models/JadwalDokter.php';

class PasienController extends Controller
{
    private $pasien;
    private $jadwal;
    private $daftarPoli = ['Poli Umum', 'Poli Gigi', 'Poli Anak', 'Poli Kandungan'];

    public function __construct()
    {
        require_role('admin', 'dokter', 'resepsionis');
        $this->pasien = new Pasien();
        $this->jadwal = new JadwalDokter();
    }

    public function index()
    {
        $search = trim($_GET['q'] ?? '');
        $dateFrom = $this->normalizeDateFilter($_GET['from'] ?? '');
        $dateTo = $this->normalizeDateFilter($_GET['to'] ?? '');

        $this->view('pasien/index', [
            'title'  => 'Data Pasien',
            'items'  => $this->pasien->all($search, $dateFrom, $dateTo, $this->doctorId()),
            'search' => $search,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }

    private function normalizeDateFilter($value)
    {
        if (!is_string($value) || $value === '') {
            return '';
        }

        $date = DateTime::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : '';
    }

    public function show($id = 0)
    {
        $doctorId = $this->doctorId();
        $pasien = $this->pasien->find((int) $id, $doctorId);
        if (!$pasien) {
            flash('error', 'Pasien tidak ditemukan.');
            $this->redirect('/index.php?url=pasien');
        }

        $rekamMedis = new RekamMedis();
        $bookingDate = $this->jadwal->nextAvailableDate();
        $poliOptions = $this->jadwal->poliOptionsForDate($bookingDate);
        $doctorsByPoli = [];
        foreach ($poliOptions as $poli) {
            $doctorsByPoli[$poli] = $this->jadwal->doctorsForPoliOnDate($poli, $bookingDate);
        }

        $this->view('pasien/show', [
            'title'      => 'Detail Pasien',
            'pasien'     => $pasien,
            'kunjungan'  => $this->pasien->kunjunganByPasien($pasien['id_pasien'], $doctorId),
            'rekamMedis' => has_role('admin', 'dokter')
                ? $rekamMedis->byPasien($pasien['id_pasien'], $doctorId)
                : [],
            'bookingDate' => $bookingDate,
            'poliOptions' => $poliOptions,
            'doctorsByPoli' => $doctorsByPoli,
            'visitClosedMessage' => !$poliOptions ? 'Tidak ada dokter dengan jadwal aktif pada tanggal ini.' : ''
        ]);
    }

    public function pilihan()
    {
        require_role('admin', 'resepsionis');
        $requestedDate = $_GET['tanggal'] ?? '';
        $date = $this->validBookingDate($requestedDate) ? $requestedDate : null;
        $poliOptions = $date ? $this->jadwal->poliOptionsForDate($date) : [];
        $doctorsByPoli = [];
        foreach ($poliOptions as $poli) {
            $doctorsByPoli[$poli] = $this->jadwal->doctorsForPoliOnDate($poli, $date);
        }
        $doctors = [];
        if (is_string($_GET['poli'] ?? null) && $_GET['poli'] !== '') {
            $doctors = $this->jadwal->doctorsForPoliOnDate($_GET['poli'], $date ?: '');
        }
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode([
            'options' => $poliOptions,
            'doctorsByPoli' => $doctorsByPoli,
            'doctors' => $doctors,
            'closedMessage' => !$poliOptions ? 'Tidak ada dokter dengan jadwal aktif pada tanggal ini.' : ''
        ]);
        exit;
    }

    public function create()
    {
        require_role('admin', 'resepsionis');

        $this->view('pasien/form', [
            'title'  => 'Tambah Pasien',
            'item'   => null,
            'action' => route('pasien/store')
        ]);
    }

    public function store()
    {
        require_role('admin', 'resepsionis');
        $this->mustPost();

        $data = $this->input();
        if ($error = $this->validate($data)) {
            set_old($_POST);
            flash('error', $error);
            $this->redirect('/index.php?url=pasien/create');
        }

        $id = $this->pasien->create($data);
        audit('Menambah pasien: ' . $data['nama_pasien']);
        flash('success', 'Pasien berhasil ditambahkan.');
        $this->redirect('/index.php?url=pasien/show/' . $id);
    }

    public function edit($id = 0)
    {
        require_role('admin', 'resepsionis');

        $item = $this->pasien->find((int) $id);
        if (!$item) {
            flash('error', 'Pasien tidak ditemukan.');
            $this->redirect('/index.php?url=pasien');
        }

        $this->view('pasien/form', [
            'title'  => 'Edit Data Pasien',
            'item'   => $item,
            'action' => route('pasien/update/' . $item['id_pasien'])
        ]);
    }

    public function update($id = 0)
    {
        require_role('admin', 'resepsionis');
        $this->mustPost();

        $id = (int) $id;
        $data = $this->input();
        if ($error = $this->validate($data)) {
            set_old($_POST);
            flash('error', $error);
            $this->redirect('/index.php?url=pasien/edit/' . $id);
        }

        $this->pasien->update($id, $data);
        audit('Mengubah data pasien: ' . $data['nama_pasien']);
        flash('success', 'Data pasien berhasil diperbarui.');
        $this->redirect('/index.php?url=pasien/show/' . $id);
    }

    public function delete($id = 0)
    {
        require_role('admin');
        $this->mustPost();

        $item = $this->pasien->find((int) $id);
        if ($item) {
            $this->pasien->delete($item['id_pasien']);
            audit('Menghapus pasien: ' . $item['nama_pasien']);
            flash('success', 'Pasien beserta riwayatnya berhasil dihapus.');
        }
        $this->redirect('/index.php?url=pasien');
    }

    /* ----- Kunjungan ----- */

    public function kunjungan($id = 0)
    {
        require_role('admin', 'resepsionis');
        $this->mustPost();

        $pasien = $this->pasien->find((int) $id);
        if (!$pasien) {
            $this->redirect('/index.php?url=pasien');
        }

        $poli = $_POST['poli'] ?? '';
        $tanggal = $_POST['tanggal_kunjungan'] ?? '';
        $doctorId = $_POST['id_dokter'] ?? '';

        if (
            !is_string($poli) ||
            !is_string($tanggal) ||
            !is_string($doctorId) ||
            !$this->validBookingDate($tanggal) ||
            !$this->jadwal->doctorCanHandle((int) $doctorId, $poli, $tanggal)
        ) {
            flash('error', 'Tanggal, poli, atau dokter tidak tersedia. Pilih jadwal aktif.');
            $this->redirect('/index.php?url=pasien/show/' . $pasien['id_pasien']);
        }

        $hasil = $this->pasien->createKunjungan($pasien['id_pasien'], $tanggal, $poli, (int) $doctorId);
        audit('Mendaftarkan kunjungan ' . $pasien['nama_pasien'] . ' (' . $poli . ')');
        flash('success', 'Kunjungan terdaftar. Nomor antrian: ' . $hasil['no_antrian']);
        $this->redirect('/index.php?url=pasien/show/' . $pasien['id_pasien']);
    }

    public function status($id_kunjungan = 0)
    {
        require_role('admin', 'dokter', 'resepsionis');
        $this->mustPost();

        if (
            has_role('dokter') &&
            !$this->pasien->visitAssignedToDoctor($id_kunjungan, current_user()['id_user'])
        ) {
            http_response_code(403);
            die('Kunjungan ini tidak ditugaskan kepada akun Anda.');
        }

        $status = $_POST['status'] ?? '';
        $allowed = ['Menunggu', 'Diperiksa', 'Selesai', 'Batal'];

        if (in_array($status, $allowed, true)) {
            $this->pasien->updateStatusKunjungan((int) $id_kunjungan, $status);
            audit('Mengubah status kunjungan #' . (int) $id_kunjungan . ' menjadi ' . $status);
            flash('success', 'Status kunjungan diperbarui.');
        }

        $back = $_POST['back'] ?? 'dashboard';
        if (!preg_match('/^[a-z_\/0-9]+$/', $back)) {
            $back = 'dashboard';
        }
        $this->redirect('/index.php?url=' . $back);
    }

    /* ----- Helper ----- */

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
            'nama_pasien'   => trim($_POST['nama_pasien'] ?? ''),
            'jenis_kelamin' => $_POST['jenis_kelamin'] ?? '',
            'tanggal_lahir' => $_POST['tanggal_lahir'] ?? '',
            'alamat'        => trim($_POST['alamat'] ?? ''),
            'no_hp'         => trim($_POST['no_hp'] ?? '')
        ];
    }

    private function validate($data)
    {
        if ($data['nama_pasien'] === '') {
            return 'Nama pasien wajib diisi.';
        }
        if (!in_array($data['jenis_kelamin'], ['L', 'P'], true)) {
            return 'Pilih jenis kelamin yang valid.';
        }
        if (!strtotime($data['tanggal_lahir'])) {
            return 'Tanggal lahir tidak valid.';
        }
        if ($data['tanggal_lahir'] > date('Y-m-d')) {
            return 'Tanggal lahir tidak boleh di masa depan.';
        }
        if ($data['no_hp'] === '') {
            return 'Nomor HP / kontak pasien wajib diisi.';
        }
        if (!preg_match('/^[0-9+ -]{8,20}$/', $data['no_hp'])) {
            return 'Format nomor HP tidak valid (gunakan minimal 8 angka/karakter telepon).';
        }
        return null;
    }

    private function doctorId()
    {
        return has_role('dokter') ? (int) current_user()['id_user'] : null;
    }

    private function validBookingDate($value)
    {
        if (!is_string($value) || $value < date('Y-m-d')) {
            return false;
        }
        $date = DateTime::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }
}
