<?php

require_once __DIR__ . '/../models/Laporan.php';

class LaporanController extends Controller
{
    private $laporan;

    public function __construct()
    {
        require_role('admin');
        $this->laporan = new Laporan();
    }

    public function index()
    {
        [$from, $to] = $this->dateRange();

        $this->view('laporan/index', [
            'title' => 'Laporan Klinik',
            'from' => $from,
            'to' => $to,
            'report' => $this->laporan->ringkasan($from, $to)
        ]);
    }

    public function pdf()
    {
        [$from, $to] = $this->dateRange();
        $this->downloadPdf('laporan/pdf', [
            'from' => $from,
            'to' => $to,
            'report' => $this->laporan->ringkasan($from, $to)
        ], 'laporan-klinik-' . $from . '-sampai-' . $to . '.pdf');
    }

    private function dateRange()
    {
        $from = $this->validDate($_GET['from'] ?? '') ?: date('Y-m-01');
        $to = $this->validDate($_GET['to'] ?? '') ?: date('Y-m-d');
        return $from > $to ? [$to, $from] : [$from, $to];
    }

    private function validDate($value)
    {
        if (!is_string($value) || $value === '') {
            return null;
        }
        $date = DateTime::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : null;
    }
}