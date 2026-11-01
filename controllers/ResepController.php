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
        $this->view('resep/index', [
            'title' => 'Resep',
            'items' => $this->resep->all($this->doctorId())
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
            'title'  => 'Detail Resep',
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
        foreach ($listObat as $i => $id_obat) {
            $id_obat = (int) $id_obat;
            $jumlah = (int) ($_POST['jumlah'][$i] ?? 0);

            if ($id_obat <= 0 || $jumlah <= 0) {
                continue;
            }

            $items[] = [
                'id_obat'      => $id_obat,
                'dosis'        => trim($_POST['dosis'][$i] ?? ''),
                'aturan_pakai' => trim($_POST['aturan_pakai'][$i] ?? ''),
                'jumlah'       => $jumlah
            ];
        }

        $lengkap = true;
        foreach ($items as $item) {
            if ($item['dosis'] === '' || $item['aturan_pakai'] === '') {
                $lengkap = false;
            }
        }

        if ($id_rm <= 0 || !strtotime($tanggal) || count($items) === 0 || !$lengkap) {
            flash('error', 'Pilih rekam medis, lalu isi minimal satu obat lengkap dengan dosis, aturan pakai, dan jumlah.');
            $this->redirect('/index.php?url=resep/create/' . $id_rm);
        }

        try {
            $id = $this->resep->create($id_rm, $tanggal, $items);
        } catch (Throwable $e) {
            flash('error', 'Resep gagal disimpan. Periksa kembali data yang dimasukkan.');
            $this->redirect('/index.php?url=resep/create/' . $id_rm);
        }

        audit('Membuat resep #' . $id);
        flash('success', 'Resep berhasil dibuat.');
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
            flash('success', 'Obat diserahkan dan stok berhasil dikurangi.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
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
