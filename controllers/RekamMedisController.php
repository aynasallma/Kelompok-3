<?php

require_once __DIR__ . '/../models/RekamMedis.php';
require_once __DIR__ . '/../models/Resep.php';

class RekamMedisController extends Controller
{
    private $rekamMedis;

    public function __construct()
    {
        require_role('admin', 'dokter');
        $this->rekamMedis = new RekamMedis();
    }

    public function index()
    {
        $search = trim($_GET['q'] ?? '');

        $this->view('rekam_medis/index', [
            'title'  => 'Rekam Medis',
            'items'  => $this->rekamMedis->all($search, $this->doctorId()),
            'search' => $search
        ]);
    }

    public function show($id = 0)
    {
        $item = $this->rekamMedis->find((int) $id, $this->doctorId());
        if (!$item) {
            flash('error', 'Rekam medis tidak ditemukan.');
            $this->redirect('/index.php?url=rekam_medis');
        }

        $resep = new Resep();

        $this->view('rekam_medis/show', [
            'title' => 'Detail Rekam Medis',
            'item'  => $item,
            'resep' => $resep->byRekamMedis($item['id_rekam_medis'])
        ]);
    }

    public function pdf($id = 0)
    {
        $item = $this->rekamMedis->find((int) $id, $this->doctorId());
        if (!$item) {
            http_response_code(404);
            die('Rekam medis tidak ditemukan.');
        }

        $resep = new Resep();
        $prescriptions = [];
        foreach ($resep->byRekamMedis($item['id_rekam_medis']) as $row) {
            $row['details'] = $resep->detail($row['id_resep']);
            $prescriptions[] = $row;
        }
        $filename = preg_replace('/[^A-Za-z0-9_-]/', '', $item['no_rm']);
        $this->downloadPdf('rekam_medis/pdf', [
            'item' => $item,
            'prescriptions' => $prescriptions
        ], 'rekam-medis-' . $filename . '.pdf');
    }

    public function create($id_kunjungan = 0)
    {
        $this->view('rekam_medis/form', [
            'title'     => 'Tambah Rekam Medis',
            'item'      => null,
            'kunjungan' => $this->rekamMedis->kunjunganTersedia($this->doctorId()),
            'terpilih'  => (int) $id_kunjungan,
            'action'    => route('rekam_medis/store')
        ]);
    }

    public function store()
    {
        $this->mustPost();

        $data = $this->input();
        $data['id_kunjungan'] = (int) ($_POST['id_kunjungan'] ?? 0);

        if (
            has_role('dokter') &&
            !$this->rekamMedis->visitAssignedToDoctor($data['id_kunjungan'], current_user()['id_user'])
        ) {
            http_response_code(403);
            die('Kunjungan ini tidak ditugaskan kepada akun Anda.');
        }
        if ($data['id_kunjungan'] <= 0 || $data['keluhan'] === '' || $data['diagnosa'] === '') {
            flash('error', 'Kunjungan, keluhan, dan diagnosa wajib diisi.');
            $this->redirect('/index.php?url=rekam_medis/create');
        }

        try {
            $id = $this->rekamMedis->create($data);
        } catch (PDOException $e) {
            flash('error', 'Rekam medis untuk kunjungan ini sudah ada atau kunjungan tidak valid.');
            $this->redirect('/index.php?url=rekam_medis/create');
        }

        audit('Menambah rekam medis #' . $id);
        flash('success', 'Rekam medis berhasil disimpan.');
        $this->redirect('/index.php?url=rekam_medis/show/' . $id);
    }

    public function edit($id = 0)
    {
        $item = $this->rekamMedis->find((int) $id, $this->doctorId());
        if (!$item) {
            flash('error', 'Rekam medis tidak ditemukan.');
            $this->redirect('/index.php?url=rekam_medis');
        }

        $this->view('rekam_medis/form', [
            'title'     => 'Edit Rekam Medis',
            'item'      => $item,
            'kunjungan' => [],
            'terpilih'  => (int) $item['id_kunjungan'],
            'action'    => route('rekam_medis/update/' . $item['id_rekam_medis'])
        ]);
    }

    public function update($id = 0)
    {
        $this->mustPost();

        $id = (int) $id;
        if (!$this->rekamMedis->find($id, $this->doctorId())) {
            http_response_code(403);
            die('Rekam medis ini tidak dapat diakses dari akun Anda.');
        }
        $data = $this->input();

        if ($data['keluhan'] === '' || $data['diagnosa'] === '') {
            flash('error', 'Keluhan dan diagnosa wajib diisi.');
            $this->redirect('/index.php?url=rekam_medis/edit/' . $id);
        }

        $this->rekamMedis->update($id, $data);
        audit('Mengubah rekam medis #' . $id);
        flash('success', 'Rekam medis berhasil diperbarui.');
        $this->redirect('/index.php?url=rekam_medis/show/' . $id);
    }

    public function delete($id = 0)
    {
        require_role('admin');
        $this->mustPost();

        $id = (int) $id;
        if ($this->rekamMedis->delete($id)) {
            audit('Menghapus rekam medis #' . $id);
            flash('success', 'Rekam medis berhasil dihapus.');
        }
        $this->redirect('/index.php?url=rekam_medis');
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
            'keluhan'  => trim($_POST['keluhan'] ?? ''),
            'diagnosa' => trim($_POST['diagnosa'] ?? ''),
            'tindakan' => trim($_POST['tindakan'] ?? ''),
            'catatan'  => trim($_POST['catatan'] ?? '')
        ];
    }

    private function doctorId()
    {
        return has_role('dokter') ? (int) current_user()['id_user'] : null;
    }
}
