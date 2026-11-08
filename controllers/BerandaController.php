<?php
require_once __DIR__ . '/../models/JadwalDokter.php';
require_once __DIR__ . '/../models/User.php';

class BerandaController extends Controller
{
    public function index()
    {
        $jadwal = new JadwalDokter();
        $bookingDate = $jadwal->nextAvailableDate();
        $doctorModel = new User();
        $doctors = $doctorModel->doctorsActive();

        // Attach each doctor's active jadwal (poli/hari/jam) for the landing page
        $doctorList = [];
        foreach ($doctors as $doc) {
            $activeJadwal = $jadwal->doctorsActiveFor($doc['id_user']);
            $doctorList[] = [
                'id_user'      => $doc['id_user'],
                'nama'         => $doc['nama'],
                'is_active'    => (int) $doc['is_active'] === 1,
                'poli'         => $activeJadwal ? $activeJadwal['poli'] : 'Berinunci',
                'hari'         => $activeJadwal ? $activeJadwal['hari'] : '-',
                'jam_mulai'    => $activeJadwal ? date('H.i', strtotime($activeJadwal['jam_mulai'])) : '-',
                'jam_selesai'  => $activeJadwal ? date('H.i', strtotime($activeJadwal['jam_selesai'])) : '-',
                'lokasi'       => $activeJadwal ? $activeJadwal['lokasi'] : '-',
            ];
        }
        $this->view('beranda/index', [
            'title'          => 'Klinik Sehat | Pelayanan Kesehatan Terpercaya',
            'activeNav'      => 'beranda',
            'bookingDate'    => $bookingDate,
            'jadwal'         => array_slice($jadwal->forDate($bookingDate), 0, 3),
            'doctorList'     => $doctorList,
        ]);
    }
}