<?php

require_once __DIR__ . '/../models/PendaftaranOnline.php';
require_once __DIR__ . '/../models/Pasien.php';
require_once __DIR__ . '/../models/JadwalDokter.php';

class PendaftaranController extends Controller
{
    private $pendaftaran;
    private $jadwal;

    public function __construct()
    {
        $this->pendaftaran = new PendaftaranOnline();
        $this->jadwal = new JadwalDokter();
    }

    public function index()
    {
        $bookingDate = $this->validBookingDate($_GET['tanggal'] ?? '') ?: $this->jadwal->nextAvailableDate();
        $poliOptions = $this->jadwal->poliOptionsForDate($bookingDate);
        $holiday = $this->jadwal->isHoliday($bookingDate);
        $this->view('pendaftaran/index', [
            'title' => 'Pendaftaran Pasien Online | Klinik Sehat',
            'activeNav' => 'pendaftaran',
            'bookingDate' => $bookingDate,
            'poliOptions' => $poliOptions,
            'closedMessage' => $holiday
                ? 'Pendaftaran tutup karena ' . $holiday . '.'
                : (!$poliOptions ? 'Tidak ada dokter dengan jadwal aktif pada tanggal ini.' : '')
        ]);
    }

    public function pilihan()
    {
        $date = $this->validBookingDate($_GET['tanggal'] ?? '');
        if (!$date) {
            http_response_code(400);
            $options = [];
            $closedMessage = 'Tanggal kunjungan tidak valid.';
        } else {
            $holiday = $this->jadwal->isHoliday($date);
            $options = $this->jadwal->poliOptionsForDate($date);
            $closedMessage = $holiday
                ? 'Pendaftaran tutup karena ' . $holiday . '.'
                : (!$options ? 'Tidak ada dokter dengan jadwal aktif pada tanggal ini.' : '');
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode(['options' => $options, 'closedMessage' => $closedMessage]);
        exit;
    }

    public function kirim()
    {
        $this->mustPost();
        $honeypot = $_POST['website'] ?? '';
        if (!is_string($honeypot) || $honeypot !== '') {
            $this->redirect('/index.php?url=pendaftaran');
        }

        $ipHash = hash_hmac(
            'sha256',
            (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
            DB_PASS . DB_NAME
        );
        if ($this->pendaftaran->countRecentFromIp($ipHash) >= 5) {
            flash('error', 'Batas pendaftaran sementara tercapai. Silakan coba kembali nanti.');
            $this->redirect('/index.php?url=pendaftaran');
        }

        $data = $this->input();
        if ($error = $this->validate($data)) {
            set_old($_POST);
            flash('error', $error);
            $this->redirect('/index.php?url=pendaftaran');
        }

        try {
            $reference = 'REG-' . strtoupper(bin2hex(random_bytes(16)));
            $this->pendaftaran->create($data, hash('sha256', $reference), $ipHash, $reference);
            $_SESSION['registration_confirmation'] = $reference;
            audit('Menerima pendaftaran pasien online');
            $this->redirect('/index.php?url=pendaftaran/status');
        } catch (Throwable $e) {
            error_log('Online patient registration failed: ' . $e->getMessage());
            flash('error', 'Pendaftaran belum dapat disimpan. Silakan coba kembali.');
            $this->redirect('/index.php?url=pendaftaran');
        }
    }

    public function status()
    {
        header('Cache-Control: no-store, private');
        $confirmation = $_SESSION['registration_confirmation'] ?? null;
        $lookup = $_SESSION['registration_lookup_status'] ?? null;
        $this->view('pendaftaran/status', [
            'title' => 'Status Pendaftaran | Klinik Sehat',
            'activeNav' => 'status',
            'confirmation' => is_string($confirmation) ? $confirmation : null,
            'lookupStatus' => is_array($lookup) ? ($lookup['status'] ?? null) : null,
            'prescriptionStatus' => is_array($lookup) ? ($lookup['status_resep'] ?? null) : null,
            'appointmentPoli' => is_array($lookup) ? ($lookup['poli'] ?? null) : null,
            'appointmentDate' => is_array($lookup) ? ($lookup['tanggal_kunjungan'] ?? null) : null
        ]);
        unset($_SESSION['registration_confirmation'], $_SESSION['registration_lookup_status']);
    }

    public function lacak()
    {
        $this->mustPost();
        $token = $_POST['reference'] ?? '';
        if (!is_string($token)) {
            flash('error', 'Kode pendaftaran tidak ditemukan. Periksa kembali kode Anda.');
            $this->redirect('/index.php?url=pendaftaran/status');
        }
        $validReference = preg_match('/^REG-[A-F0-9]{32}$/', $token)
            || preg_match('/^[a-f0-9]{64}$/', $token);
        if (!$validReference) {
            flash('error', 'Kode pendaftaran tidak ditemukan. Periksa kembali kode Anda.');
            $this->redirect('/index.php?url=pendaftaran/status');
        }

        $request = $this->pendaftaran->findPublicStatus(hash('sha256', $token), $token);
        if (!$request) {
            flash('error', 'Kode pendaftaran tidak ditemukan. Periksa kembali kode Anda.');
            $this->redirect('/index.php?url=pendaftaran/status');
        }

        $_SESSION['registration_lookup_status'] = [
            'status' => $request['status'],
            'status_resep' => $request['status_resep'],
            'poli' => $request['poli'],
            'tanggal_kunjungan' => $request['tanggal_kunjungan']
        ];
        $this->redirect('/index.php?url=pendaftaran/status');
    }

    public function verifikasi()
    {
        require_role('admin', 'resepsionis');
        $pending = $this->pendaftaran->pending();
        $doctorsByRequest = [];
        foreach ($pending as $item) {
            $doctorsByRequest[$item['id_pendaftaran']] = $this->jadwal->doctorsForPoliOnDate(
                $item['poli'],
                $item['tanggal_kunjungan']
            );
        }
        $this->view('pendaftaran/verifikasi', [
            'title' => 'Verifikasi Pendaftaran Online',
            'pending' => $pending,
            'doctorsByRequest' => $doctorsByRequest,
            'reviewed' => $this->pendaftaran->recentlyReviewed()
        ]);
    }

    public function setujui($id = 0)
    {
        $this->resolve((int) $id, 'Disetujui');
    }

    public function tolak($id = 0)
    {
        $this->resolve((int) $id, 'Ditolak');
    }

    private function resolve($id, $decision)
    {
        require_role('admin', 'resepsionis');
        $this->mustPost();
        $note = $_POST['catatan_staf'] ?? '';
        $doctorId = 0;
        if ($decision === 'Disetujui') {
            $rawDoctorId = $_POST['id_dokter'] ?? '';
            if (!is_string($rawDoctorId) || !ctype_digit($rawDoctorId)) {
                flash('error', 'Pilih dokter yang akan menangani pasien.');
                $this->redirect('/index.php?url=pendaftaran/verifikasi');
            }
            $doctorId = (int) $rawDoctorId;
        }
        if (!is_string($note) || strlen($note) > 500) {
            flash('error', 'Catatan maksimal 500 karakter.');
            $this->redirect('/index.php?url=pendaftaran/verifikasi');
        }

        try {
            $result = $this->pendaftaran->resolve(
                $id,
                $decision,
                current_user()['id_user'],
                trim($note),
                $doctorId
            );
            if (!$result) {
                flash('error', 'Permohonan tidak ditemukan atau sudah diproses.');
            } elseif ($decision === 'Disetujui') {
                $patient = (new Pasien())->find($result['id_pasien']);
                audit('Menyetujui pendaftaran pasien: ' . $result['nama_pasien']);
                flash('success', 'Pendaftaran disetujui. No. RM pasien: ' . $patient['no_rm']);
            } else {
                audit('Menolak pendaftaran pasien: ' . $result['nama_pasien']);
                flash('success', 'Pendaftaran ditolak dan status pemohon diperbarui.');
            }
        } catch (Throwable $e) {
            error_log('Online registration review failed: ' . $e->getMessage());
            flash('error', 'Permohonan gagal diproses. Muat ulang antrean dan coba kembali.');
        }
        $this->redirect('/index.php?url=pendaftaran/verifikasi');
    }

    private function input()
    {
        $fields = ['nama_pasien', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'no_hp', 'poli', 'tanggal_kunjungan'];
        $data = [];
        foreach ($fields as $field) {
            $value = $_POST[$field] ?? '';
            if (!is_string($value)) {
                return null;
            }
            $data[$field] = trim($value);
        }
        return $data;
    }

    private function validate($data)
    {
        if (!$data) {
            return 'Data pendaftaran tidak valid.';
        }
        if (strlen($data['nama_pasien']) < 2 || strlen($data['nama_pasien']) > 100) {
            return 'Nama harus terdiri dari 2 sampai 100 karakter.';
        }
        if (!in_array($data['jenis_kelamin'], ['L', 'P'], true)) {
            return 'Pilih jenis kelamin yang valid.';
        }
        $date = DateTime::createFromFormat('!Y-m-d', $data['tanggal_lahir']);
        if (!$date || $date->format('Y-m-d') !== $data['tanggal_lahir'] || $data['tanggal_lahir'] > date('Y-m-d')) {
            return 'Tanggal lahir tidak valid.';
        }
        if (strlen($data['alamat']) > 1000) {
            return 'Alamat maksimal 1.000 karakter.';
        }
        $bookingDate = $this->validBookingDate($data['tanggal_kunjungan']);
        if (!$bookingDate || $bookingDate < date('Y-m-d')) {
            return 'Pilih tanggal kunjungan yang valid mulai hari ini.';
        }
        if (!in_array($data['poli'], $this->jadwal->poliOptionsForDate($bookingDate), true)) {
            return 'Poli tidak tersedia pada tanggal yang dipilih.';
        }
        if (!preg_match('/^[0-9+() .-]{8,20}$/', $data['no_hp'])) {
            return 'Nomor telepon harus berisi 8 sampai 20 karakter yang valid.';
        }
        if (($_POST['persetujuan'] ?? '') !== '1') {
            return 'Persetujuan penggunaan data diperlukan untuk mengirim pendaftaran.';
        }
        return null;
    }

    private function mustPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Metode tidak diizinkan.');
        }
    }

    private function validBookingDate($value)
    {
        if (!is_string($value) || $value === '') {
            return null;
        }
        $date = DateTime::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : null;
    }
}