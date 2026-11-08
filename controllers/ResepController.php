<?php

require_once __DIR__ . '/../models/Resep.php';
require_once __DIR__ . '/../models/Obat.php';

class ResepController extends Controller
{
    private $resep;

    public function __construct()
    {
        require_role('admin', 'dokter', 'apoteker');
        $this->resep = new Resep();
    }

    public function index()
    {
        $status = trim($_GET['status'] ?? '');
        $statusFilter = in_array($status, ['Menunggu', 'Diserahkan', 'Batal'], true) ? $status : null;

        $this->view('resep/index', [
            'title'        => 'Daftar Resep',
            'items'        => $this->resep->all($this->doctorId(), $statusFilter),
            'statusFilter' => $statusFilter,
            'countMenunggu' => $this->resep->countMenunggu()
        ]);
    }

    public function show($id = 0)
    {
        $item = $this->resep->find((int) $id, $this->doctorId());
        if (!$item) {
            flash('error', 'Resep tidak ditemukan.');
            $this->redirect('/index.php?url=resep');
        }

        $this->view('resep/show', [
            'title'  => 'Detail Resep #' . (int) $item['id_resep'],
            'item'   => $item,
            'detail' => $this->resep->detail($item['id_resep'])
        ]);
    }

    public function create($id_rekam_medis = 0)
    {
        require_role('admin', 'dokter');

        $obat = new Obat();

        $this->view('resep/form', [
            'title'      => 'Buat Resep',
            'rekamMedis' => $this->resep->rekamMedisList($this->doctorId()),
            'obat'       => $obat->all(),
            'terpilih'   => (int) $id_rekam_medis
        ]);
    }

    public function store()
    {
        require_role('admin', 'dokter');
        $this->mustPost();

        $id_rm = (int) ($_POST['id_rekam_medis'] ?? 0);
        if (
            has_role('dokter') &&
            !$this->resep->recordAssignedToDoctor($id_rm, current_user()['id_user'])
        ) {
            http_response_code(403);
            die('Rekam medis ini tidak ditugaskan kepada akun Anda.');
        }
        $tanggal = $_POST['tanggal_resep'] ?? date('Y-m-d');

        $items = [];
        $listObat = $_POST['id_obat'] ?? [];
        $adaKosong = false;

        foreach ($listObat as $i => $id_obat) {
            $id_obat = (int) $id_obat;
            $jumlah = (int) ($_POST['jumlah'][$i] ?? 0);
            $dosis = trim($_POST['dosis'][$i] ?? '');
            $aturan = trim($_POST['aturan_pakai'][$i] ?? '');

            if ($id_obat <= 0) {
                continue;
            }

            if ($dosis === '' || $aturan === '' || $jumlah <= 0) {
                $adaKosong = true;
            }

            $items[] = [
                'id_obat'      => $id_obat,
                'dosis'        => $dosis,
                'aturan_pakai' => $aturan,
                'jumlah'       => $jumlah
            ];
        }

        if ($id_rm <= 0) {
            set_old($_POST);
            flash('error', 'Silakan pilih rekam medis terlebih dahulu.');
            $this->redirect('/index.php?url=resep/create/' . $id_rm);
        }

        if (!strtotime($tanggal)) {
            set_old($_POST);
            flash('error', 'Format tanggal resep tidak valid.');
            $this->redirect('/index.php?url=resep/create/' . $id_rm);
        }

        if (count($items) === 0) {
            set_old($_POST);
            flash('error', 'Masukkan minimal satu obat ke dalam resep.');
            $this->redirect('/index.php?url=resep/create/' . $id_rm);
        }

        if ($adaKosong) {
            set_old($_POST);
            flash('error', 'Setiap obat harus dilengkapi dengan dosis, aturan pakai, dan jumlah minimal 1.');
            $this->redirect('/index.php?url=resep/create/' . $id_rm);
        }

        try {
            $id = $this->resep->create($id_rm, $tanggal, $items);
        } catch (Throwable $e) {
            set_old($_POST);
            flash('error', $e->getMessage());
            $this->redirect('/index.php?url=resep/create/' . $id_rm);
        }

        audit('Membuat resep #' . $id);
        flash('success', 'Resep #' . $id . ' berhasil dibuat dan menunggu penyerahan oleh apoteker.');
        $this->redirect('/index.php?url=resep/show/' . $id);
    }

    public function serahkan($id = 0)
    {
        require_role('admin', 'apoteker');
        $this->mustPost();

        $id = (int) $id;
        try {
            $this->resep->serahkan($id);
            audit('Menyerahkan obat resep #' . $id);
            flash('success', 'Obat untuk resep #' . $id . ' berhasil diserahkan kepada pasien dan stok obat telah dikurangi.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }

        // Jika diserahkan dari tabel daftar resep, kembalikan ke daftar resep
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer && strpos($referer, 'url=resep') !== false && strpos($referer, 'url=resep/show') === false && strpos($referer, 'url=resep/create') === false) {
            $this->redirect('/index.php?url=resep');
        }

        $this->redirect('/index.php?url=resep/show/' . $id);
    }

    public function batal($id = 0)
    {
        require_role('admin', 'apoteker', 'dokter');
        $this->mustPost();

        $id = (int) $id;
        if ($this->resep->batal($id, has_role('dokter') ? current_user()['id_user'] : null)) {
            audit('Membatalkan resep #' . $id);
            flash('success', 'Resep dibatalkan.');
        } else {
            flash('error', 'Resep tidak dapat dibatalkan.');
        }
        $this->redirect('/index.php?url=resep/show/' . $id);
    }

    private function mustPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Metode tidak diizinkan.');
        }
    }

    private function doctorId()
    {
        return has_role('dokter') ? (int) current_user()['id_user'] : null;
    }
}
