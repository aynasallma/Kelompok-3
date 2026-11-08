<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Pasien.php';
require_once __DIR__ . '/../models/Obat.php';
require_once __DIR__ . '/../models/Resep.php';
require_once __DIR__ . '/../models/RekamMedis.php';
require_once __DIR__ . '/../models/PendaftaranOnline.php';
require_once __DIR__ . '/../models/JadwalDokter.php';

class DashboardController extends Controller
{
    public function __construct()
    {
        require_login();
    }

    public function index()
    {
        $user = current_user();
        $role = $user['role'];
        $userId = (int) $user['id_user'];

        $userModel = new User();
        $pasienModel = new Pasien();
        $obatModel = new Obat();
        $resepModel = new Resep();
        $rekamMedisModel = new RekamMedis();
        $pendaftaranModel = new PendaftaranOnline();
        $jadwalModel = new JadwalDokter();

        $data = [
            'title' => 'Dashboard ' . ucfirst($role),
            'role'  => $role,
            'user'  => $user
        ];

        switch ($role) {
            case 'admin':
                $data['totalPasien']       = $pasienModel->countAll();
                $data['totalDokter']       = $userModel->countByRole('dokter');
                $data['totalApoteker']     = $userModel->countByRole('apoteker');
                $data['totalObat']         = $obatModel->countAll();
                $data['totalKunjungan']    = $pasienModel->countTotalKunjungan();
                $data['kunjunganHariIni']  = $pasienModel->countKunjunganHariIni();
                $data['audit']             = $userModel->auditTerbaru(8);
                $data['statistikMingguan'] = $pasienModel->statistikKunjunganMingguan();
                $data['stokMenipis']       = $obatModel->stokMenipis();
                $data['pendaftaranPending']= $pendaftaranModel->countPending();
                $data['resepMenunggu']     = $resepModel->countMenunggu();
                break;

            case 'dokter':
                $data['kunjunganHariIni']     = $pasienModel->countKunjunganHariIni($userId);
                $data['pasienSelesai']        = $pasienModel->countKunjunganStatusHariIni('Selesai', $userId);
                $data['pasienMenunggu']       = $pasienModel->countKunjunganStatusHariIni('Menunggu', $userId) +
                                                $pasienModel->countKunjunganStatusHariIni('Diperiksa', $userId);
                $data['totalResep']           = $resepModel->countByDoctor($userId);
                $data['daftarKunjungan']      = $pasienModel->kunjunganHariIni($userId);
                $data['riwayatPemeriksaan']   = $rekamMedisModel->riwayatTerbaru(6, $userId);
                $data['jadwalHariIni']        = $jadwalModel->jadwalHariIni($userId);
                break;

            case 'apoteker':
                $data['totalResep']           = $resepModel->countAll();
                $data['resepMenunggu']        = $resepModel->countMenunggu();
                $data['resepDiserahkan']      = $resepModel->countDiserahkan();
                $data['totalObat']            = $obatModel->countAll();
                $data['stokMenipis']          = $obatModel->stokMenipis();
                $data['penyerahanTerbaru']    = $resepModel->resepTerbaruDiserahkan(6);
                $data['antreanResep']         = $resepModel->all(null, 'Menunggu');
                break;

            case 'resepsionis':
            default:
                $data['kunjunganHariIni']     = $pasienModel->countKunjunganHariIni();
                $data['kunjunganMenunggu']    = $pasienModel->countKunjunganStatusHariIni('Menunggu');
                $data['kunjunganSelesai']     = $pasienModel->countKunjunganStatusHariIni('Selesai');
                $data['pendaftaranMenunggu']  = $pendaftaranModel->countPending();
                $data['totalPasien']          = $pasienModel->countAll();
                $data['jadwalDokterHariIni']  = $jadwalModel->jadwalHariIni();
                $data['daftarKunjungan']      = $pasienModel->kunjunganHariIni();
                $data['pendingPendaftaran']   = array_slice($pendaftaranModel->pending(), 0, 5);
                break;
        }

        $this->view('dashboard/index', $data);
    }
}
