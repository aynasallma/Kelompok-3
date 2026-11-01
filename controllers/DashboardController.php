<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Pasien.php';
require_once __DIR__ . '/../models/Obat.php';
require_once __DIR__ . '/../models/Resep.php';
require_once __DIR__ . '/../models/PendaftaranOnline.php';

class DashboardController extends Controller
{
    public function __construct()
    {
        require_login();
    }

    public function index()
    {
        $role = current_user()['role'];
        $canViewPatients = in_array($role, ['admin', 'resepsionis', 'dokter'], true);
        $canViewPharmacy = in_array($role, ['admin', 'dokter', 'apoteker'], true);
        $pasien = new Pasien();
        $obat = $canViewPharmacy ? new Obat() : null;
        $resep = $canViewPharmacy ? new Resep() : null;
        $user   = new User();
        $pendaftaran = new PendaftaranOnline();

        $doctorId = has_role('dokter') ? (int) current_user()['id_user'] : null;
        $kunjungan = $canViewPatients ? $pasien->kunjunganHariIni($doctorId) : [];

        $this->view('dashboard/index', [
            'title'          => 'Dashboard',
            'canViewPatients' => $canViewPatients,
            'canViewPharmacy' => $canViewPharmacy,
            'totalPasien'    => $canViewPatients ? $pasien->countAll() : 0,
            'kunjungan'      => $kunjungan,
            'resepMenunggu'  => $canViewPharmacy ? $resep->countMenunggu() : 0,
            'stokMenipis'    => $canViewPharmacy ? $obat->stokMenipis() : [],
            'pendaftaranMenunggu' => in_array(current_user()['role'], ['admin', 'resepsionis'], true)
                ? $pendaftaran->countPending()
                : 0,
            'assignedPatients' => has_role('dokter')
                ? $pendaftaran->assignedToDoctor(current_user()['id_user'])
                : [],
            'audit'          => has_role('admin') ? $user->auditTerbaru(8) : []
        ]);
    }
}
