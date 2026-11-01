<?php

require_once __DIR__ . '/../models/HariLibur.php';

class HariLiburController extends Controller
{
    private $hariLibur;

    public function __construct()
    {
        require_role('admin');
        $this->hariLibur = new HariLibur();
    }

    public function index()
    {
        $this->view('hari_libur/index', [
            'title' => 'Kalender Hari Libur',
            'items' => $this->hariLibur->all()
        ]);
    }

    public function store()
    {
        $this->mustPost();
        $date = $_POST['tanggal'] ?? '';
        $name = $_POST['nama'] ?? '';
        if (!is_string($date) || !$this->validDate($date) || !is_string($name) || trim($name) === '' || strlen($name) > 120) {
            flash('error', 'Isi tanggal dan nama hari libur dengan benar.');
            $this->redirect('/index.php?url=hari_libur');
        }

        try {
            $this->hariLibur->add($date, trim($name), current_user()['id_user']);
            audit('Menambahkan hari libur: ' . $date);
            flash('success', 'Hari libur berhasil ditambahkan. Pendaftaran dan jadwal publik akan ditutup pada tanggal tersebut.');
        } catch (PDOException $e) {
            flash('error', 'Tanggal tersebut sudah tercatat sebagai hari libur.');
        }
        $this->redirect('/index.php?url=hari_libur');
    }

    public function delete($date = '')
    {
        $this->mustPost();
        if (!$this->validDate($date)) {
            flash('error', 'Tanggal hari libur tidak valid.');
        } elseif ($this->hariLibur->delete($date)) {
            audit('Menghapus hari libur: ' . $date);
            flash('success', 'Hari libur berhasil dihapus.');
        }
        $this->redirect('/index.php?url=hari_libur');
    }

    private function validDate($value)
    {
        $date = is_string($value) ? DateTime::createFromFormat('!Y-m-d', $value) : false;
        return $date && $date->format('Y-m-d') === $value;
    }

    private function mustPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Metode tidak diizinkan.');
        }
    }
}