<?php

require_once __DIR__ . '/../models/JadwalDokter.php';

class BerandaController extends Controller
{
    public function index()
    {
        $jadwal = new JadwalDokter();
        $bookingDate = $jadwal->nextAvailableDate();
        $this->view('beranda/index', [
            'title' => 'Klinik Sehat | Pelayanan Kesehatan Terpercaya',
            'activeNav' => 'beranda',
            'bookingDate' => $bookingDate,
            'jadwal' => array_slice($jadwal->forDate($bookingDate), 0, 3)
        ]);
    }
}