# RENCANA ARSITEKTUR & ALUR KERJA APLIKASI KLINIK SEHAT

> Bahan ajar: Arsitektur MVC + PDO + CRUD + Alur Pendaftaran / Rekam Medis / Resep
> Platform: PHP (Native) + MySQL / MariaDB + PDO

---

## 1. RINGKASAN APLIKASI

**Klinik Sehat** adalah aplikasi manajemen klinik berbasis web yang memungkinkan:
- Pasien mendaftar online (tanpa login).
- Resepsionis memverifikasi pendaftaran dan memberikan arah ke dokter.
- Dokter mengisi rekam medis, riwayat, dan membuat resep.
- Apoteker menerima resep dan menyediakan obat.
- Admin mengelola pengguna, jadwal, obat, dan laporan.

Aplikasi menggunakan **Architecture MVC** (Model-View-Controller) dengan **PDO** (PHP Data Objects) untuk koneksi database.

---

## 2. ARBIT DESAIN: MVC + PDO

```
┌──────────────────────────────────────────────────────────────┐
│                         WEB BROWSER                          │
└──────────────────────────┬───────────────────────────────────┘
                           │ HTTP Request
┌──────────────────────────▼───────────────────────────────────┐
│                      Index.php (Front Controller)             │
│         - Parse URL → route → call Controller                │
│         - Load config, bootstrap, start session              │
└──────────────────────────┬───────────────────────────────────┘
                           │
┌──────────────────────────▼───────────────────────────────────┐
│                      CONTROLLER                               │
│         - Ambil data dari Request (POST/GET)                  │
│         - Validasi sederhana                                  │
│         - Bawa ke Model (PDO)                                 │
│         - Ambil hasil dari Model                              │
│         - Pilih View & kirim data (compact)                   │
└──────────────────────────┬───────────────────────────────────┘
                           │
┌──────────────────────────▼───────────────────────────────────┐
│                         MODEL (PDO)                           │
│         - Semua query ke database menggunakan PDO             │
│         - CRUD: create, read, update, delete                  │
│         - Prepared statements (untuk keamanan)                │
│         - Return array / false / row count                    │
└──────────────────────────┬───────────────────────────────────┘
                           │
┌──────────────────────────▼───────────────────────────────────┐
│                          VIEW (PHP Template)                   │
│         - HTML + PHP embedded                                 │
│         - Menampilkan data dari Controller                    │
│         - Form input (POST/GET)                               │
│         - Link / button → routeController#action              │
└──────────────────────────────────────────────────────────────┘
```

**Koneksi PDO:**
```php
// config/database.php
$host = '127.0.0.1';
$port = '8889';
$dbname = 'klinik_app';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
```

---

## 3. PENYALINGAN ROLES (Hak Akses)

| Role | Izin |
|---|---|
| `admin` | Semua fitur: pengguna, jadwal, obat, laporan, verifikasi |
| `dokter` | Rekam medis, jadwal, resep |
| `resepsionis` | Pendaftaran online, verifikasi, pasien |
| `apoteker` | Resep, obat, serah terus |

---

## 4. ALUR KERJA LENGKAP

```
Pasien
  │
  ▼
[1. Daftar Online] (pendaftaran/index.php)
  - Form: nama, tanggal lahir, jenis kelamin, alamat, tanggal kunjungan, poli
  - Submit → POST /pendaftaran/kirim
  - Validasi sederhana di Controller
  - Simpan ke tabel pendaftaran_online (PDO create)
  - Kirim notifikasi (email/telepon — opsional)
  │
  ▼
[2. Verifikasi Resepsionis] (pendaftaran/verifikasi.php)
  - Resepsionis lihat daftar tunggu
  - Klik "Terima" → status = 'Diterima'
  - Simpan di tabel pendaftaran_online (PDO update)
  - Akan muncul di dashboard resepsionis
  │
  ▼
[3. Penetapan ke Dokter] (dashboard — resepsionis)
  - Resepsionis pilih dokter & jadwal
  - Simpan ke tabel assignment / jadwal_pasien
  - Pasien menerima notifikasi
  │
  ▼
[4. Pemeriksaan oleh Dokter] (dokter/rekam_medis)
  - Dokter login
  - Lihat pasien yang diarahkan ke dia (dashboard)
  - Klik "Isi Rekam Medis"
  - Isi: Anamnesis, Pemeriksaan fisik, Penyakit yang diderita, Diagnosis
  - Simpan ke tabel rekam_medis (PDO create/update)
  │
  ▼
[5. Membuat Resep] (dokter/resep)
  - Dokter klik "Buat Resep"
  - Isi: obat, dosis, frekuensi, durasi
  - Simpan ke tabel resep (PDO create)
  - Kirim notifikasi ke apoteker
  │
  ▼
[6. Penyediaan oleh Apoteker] (apoteker/resep)
  - Apoteker login
  - Lihat daftar resep menunggu
  - Klik "Serah" → cek stok obat
  - Update stok (PDO update)
  - Simpan status = 'Sudah diserahkan'
  │
  ▼
[7. Pasien menerima obat] (pendaftaran/status)
  - Pasien lihat status
  - Mulai kunjungan, dapat obat
```

---

## 5. REQUIRE MENMENT (APA YANG DIBUTUHKAN)

### 5.1 Sistem
- PHP 8.0+ (Native, tanpa framework)
- MariaDB / MySQL 8.0+
- PDO (bawaan PHP)
- Apache / Nginx / MAMP / XAMPP

### 5.2 Tabel Database (Dasar)

```
users                    # id_user, nama, username, password, role, is_active
pasien                   # id_pasien, no_rm, nama, jenis_kelamin, tanggal_lahir, alamat, no_hp
pendaftaran_online       # id_pendaftaran, id_pasien, nomor_pendaftaran, 
                         #   nama_pasien, poli, tanggal_kunjungan, status, 
                         #   diajukan_pada
jadwal_dokter            # id_jadwal, id_dokter, hari, jam_mulai, jam_selesai, 
                         #   poli, lokasi, is_active
resep                    # id_resep, id_pasien, id_dokter, no_rm, tanggal_resep,
                         #   obat, dosis, frekuensi, durasi, total, status
rekam_medis              # id_rekam_medis, id_pasien, id_dokter, no_rm, 
                         #   tgl_pemeriksaan, anamnesis, pemeriksaan_fisik,
                         #   penyakit_derita, diagnosis, berat_badan, tekanan_darah
obat                     # id_obat, kode_obat, nama_obat, stok, stok_minimum
apoteker                 # id_apoteker, nama, username, password, is_active
laporan                  # id_laporan, id_pasien, tgl_laporan, ringkasan
pendaftaran_detail       # id_detail, id_pendaftaran, id_dokter, 
                         #   jadwal_id, status_penetapan
```

### 5.3 Koneksi PDO (Database)

```php
// models/Database.php
class Database {
    private static $instance = null;
    public static function getConnection() {
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $port = '8889';
            $dbname = 'klinik_app';
            $username = 'root';
            $password = 'root';
            self::$instance = new PDO(
                "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
        return self::$instance;
    }
}
```

---

## 6. CRUD — APA YANG DIGUNAKAN

### 6.1 Pendaftaran Online (Rohan)

| Operasi | Model Method | Controller Action | View |
|---|---|---|---|
| **C**reate | `PendaftaranOnline::create()` | `PendaftaranController::kirim()` | `pendaftaran/index.php` (form) |
| **R**ead | `PendaftaranOnline::pending()` | `PendaftaranController::verifikasi()` | `pendaftaran/verifikasi.php` |
| **U**pdate | `PendaftaranOnline::updateStatus()` | `PendaftaranController::verifikasi()` | `pendaftaran/status.php` |
| **D**elete | `PendaftaranOnline::delete()` | `PendaftaranController::hapus()` | — |

### 6.2 Rekam Medis (Alvin)

| Operasi | Model Method | Controller Action | View |
|---|---|---|---|
| **C**reate | `RekamMedis::create()` | `RekamMedisController::create()` | `rekam_medis/form.php` |
| **R**ead | `RekamMedis::getById()` | `RekamMedisController::show()` | `rekam_medis/show.php` |
| **U**pdate | `RekamMedis::update()` | `RekamMedisController::update()` | `rekam_medis/form.php` |
| **D**elete | `RekamMedis::delete()` | `RekamMedisController::hapus()` | — |

### 6.3 Resep (Salma)

| Operasi | Model Method | Controller Action | View |
|---|---|---|---|
| **C**reate | `Resep::create()` | `ResepController::create()` | `resep/form.php` |
| **R**ead | `Resep::getAllPending()` | `ResepController::index()` | `resep/index.php` |
| **U**pdate | `Resep::updateStatus()` | `ResepController::serah()` | `resep/show.php` |
| **D**elete | `Resep::delete()` | `ResepController::hapus()` | — |

---

## 7. VIEW → CONTROLLER → MODEL (DETAIL)

### 7.1 Pendaftaran Online (Rohan)

#### Controller — `PendafataranController.php`
```php
class PendaftaranController extends Controller {
    public function index() {
        // 1. Ambil dropdown poli dari Model
        $poliOptions = (new PendaftaranOnline())->poliList();
        // 2. Kirim ke View
        $this->view('pendaftaran/index', [
            'title' => 'Daftar Pasien Baru',
            'poliOptions' => $poliOptions,
        ]);
    }

    public function kirim() {
        // 1. Cek method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pendaftaran');
            exit;
        }
        // 2. Ambil data dari Request
        $data = [
            'nama_pasien' => $_POST['nama_pasien'] ?? '',
            'tanggal_lahir' => $_POST['tanggal_lahir'] ?? '',
            'jenis_kelamin' => $_POST['jenis_kelamin'] ?? '',
            'alamat' => $_POST['alamat'] ?? '',
            'no_hp' => $_POST['no_hp'] ?? '',
            'tanggal_kunjungan' => $_POST['tanggal_kunjungan'] ?? '',
            'poli' => $_POST['poli'] ?? '',
        ];
        // 3. Validasi sederhana
        $errors = [];
        if (empty($data['nama_pasien'])) $errors[] = 'Nama harus diisi';
        if (empty($data['tanggal_lahir'])) $errors[] = 'Tanggal lahir harus diisi';
        if (empty($data['no_hp'])) $errors[] = 'No HP harus diisi';
        if (empty($data['tanggal_kunjungan'])) $errors[] = 'Tanggal kunjungan harus diisi';
        if (empty($data['poli'])) $errors[] = 'Poli harus dipilih';
        if (!empty($errors)) {
            flash('error', implode(' | ', $errors));
            header('Location: /pendaftaran');
            exit;
        }
        // 4. Simpan ke database (via Model)
        $pendaftaran = new PendaftaranOnline();
        $result = $pendaftaran->create($data);
        if ($result) {
            flash('success', 'Pendaftaran berhasil dikirim');
        } else {
            flash('error', 'Gagal mengirim pendaftaran');
        }
        header('Location: /pendaftaran');
        exit;
    }

    public function verifikasi() {
        $pendaftaran = new PendaftaranOnline();
        $pending = $pendaftaran->pending();
        $this->view('pendaftaran/verifikasi', [
            'title' => 'Verifikasi Pendaftaran',
            'pending' => $pending,
        ]);
    }

    public function terima($id) {
        $pendaftaran = new PendaftaranOnline();
        $pendaftaran->updateStatus($id, 'Diterima');
        flash('success', 'Pendaftaran diterima');
        header('Location: /pendaftaran/verifikasi');
        exit;
    }
}
```

#### Model — `models/PendaftaranOnline.php`
```php
class PendaftaranOnline {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // CREATE
    public function create($data) {
        $sql = "INSERT INTO pendaftaran_online 
                (nama_pasien, jenis_kelamin, tanggal_lahir, alamat, no_hp, 
                 tanggal_kunjungan, poli, diajukan_pada) 
                VALUES (:nama_pasien, :jenis_kelamin, :tanggal_lahir, :alamat, 
                        :no_hp, :tanggal_kunjungan, :poli, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nama_pasien' => $data['nama_pasien'],
            ':jenis_kelamin' => $data['jenis_kelamin'],
            ':tanggal_lahir' => $data['tanggal_lahir'],
            ':alamat' => $data['alamat'],
            ':no_hp' => $data['no_hp'],
            ':tanggal_kunjungan' => $data['tanggal_kunjungan'],
            ':poli' => $data['poli'],
        ]);
    }

    // READ (pending)
    public function pending() {
        $sql = "SELECT * FROM pendaftaran_online 
                WHERE status = 'Menunggu' 
                ORDER BY diajukan_pada DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // UPDATE status
    public function updateStatus($id, $status) {
        $sql = "UPDATE pendaftaran_online 
                SET status = :status, 
                    diproses_pada = NOW() 
                WHERE id_pendaftaran = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id,
        ]);
    }

    // poli list
    public function poliList() {
        $sql = "SELECT DISTINCT poli FROM pendaftaran_online 
                WHERE poli != ''";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, PDO::FETCH_COLUMN);
    }
}
```

#### View — `pendaftaran/index.php`
```php
<form method="post" action="/pendaftaran/kirim">
    <?= csrf_field() ?>
    <label>Nama Lengkap</label>
    <input type="text" name="nama_pasien" required>
    <label>Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir" required>
    <label>Jenis Kelamin</label>
    <select name="jenis_kelamin" required>
        <option value="">Pilih</option>
        <option value="L">Laki-laki</option>
        <option value="P">Perempuan</option>
    </select>
    <label>Alamat</label>
    <textarea name="alamat"></textarea>
    <label>No HP</label>
    <input type="tel" name="no_hp" required>
    <label>Tanggal Kunjungan</label>
    <input type="date" name="tanggal_kunjungan" required>
    <label>Poli Tujuan</label>
    <select name="poli" required>
        <option value="">Pilih poli</option>
        <?php foreach ($poliOptions as $poli): ?>
            <option value="<?= e($poli) ?>"><?= e($poli) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Kirim Pendaftaran</button>
</form>
```

---

### 7.2 Rekam Medis (Alvin)

#### Controller — `RekamMedisController.php`
```php
class RekamMedisController extends Controller {
    public function create($id_pasien) {
        $pasien = (new Pasien())->getById($id_pasien);
        $this->view('rekam_medis/form', [
            'title' => 'Isi Rekam Medis',
            'pasien' => $pasien,
        ]);
    }

    public function store() {
        // 1. Ambil data POST
        $data = [
            'id_pasien' => $_POST['id_pasien'] ?? 0,
            'no_rm' => $_POST['no_rm'] ?? '',
            'tgl_pemeriksaan' => $_POST['tgl_pemeriksaan'] ?? date('Y-m-d'),
            'anamnesis' => $_POST['anamnesis'] ?? '',
            'pemeriksaan_fisik' => $_POST['pemeriksaan_fisik'] ?? '',
            'penyakit_derita' => $_POST['penyakit_derita'] ?? '',
            'diagnosis' => $_POST['diagnosis'] ?? '',
            'berat_badan' => $_POST['berat_badan'] ?? 0,
            'tinggi_badan' => $_POST['tinggi_badan'] ?? 0,
            'tekanan_darah' => $_POST['tekanan_darah'] ?? '',
        ];
        // 2. Validasi
        $errors = [];
        if (empty($data['id_pasien'])) $errors[] = 'Pasien wajib diisi';
        if (empty($data['no_rm'])) $errors[] = 'No RM wajib diisi';
        if (empty($data['diagnosis'])) $errors[] = 'Diagnosis wajib diisi';
        if (!empty($errors)) {
            flash('error', implode(' | ', $errors));
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        // 3. Simpan
        $rekam = new RekamMedis();
        $result = $rekam->create($data);
        if ($result) {
            flash('success', 'Rekam medis disimpan');
        } else {
            flash('error', 'Gagal menyimpan rekam medis');
        }
        header('Location: /dokter/rekam_medis');
        exit;
    }
}
```

#### Model — `models/RekamMedis.php`
```php
class RekamMedis {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // CREATE
    public function create($data) {
        $sql = "INSERT INTO rekam_medis 
                (id_pasien, no_rm, tgl_pemeriksaan, anamnesis, 
                 pemeriksaan_fisik, penyakit_derita, diagnosis,
                 berat_badan, tinggi_badan, tekanan_darah) 
                VALUES 
                (:id_pasien, :no_rm, :tgl_pemeriksaan, :anamnesis, 
                 :pemeriksaan_fisik, :penyakit_derita, :diagnosis,
                 :berat_badan, :tinggi_badan, :tekanan_darah)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_pasien' => $data['id_pasien'],
            ':no_rm' => $data['no_rm'],
            ':tgl_pemeriksaan' => $data['tgl_pemeriksaan'],
            ':anamnesis' => $data['anamnesis'],
            ':pemeriksaan_fisik' => $data['pemeriksaan_fisik'],
            ':penyakit_derita' => $data['penyakit_derita'],
            ':diagnosis' => $data['diagnosis'],
            ':berat_badan' => $data['berat_badan'],
            ':tinggi_badan' => $data['tinggi_badan'],
            ':tekanan_darah' => $data['tekanan_darah'],
        ]);
    }

    // READ (get by ID)
    public function getById($id) {
        $sql = "SELECT * FROM rekam_medis WHERE id_rekam_medis = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // READ (list for dashboard)
    public function forDoctor($id_dokter) {
        $sql = "SELECT rm.*, p.nama AS nama_pasien 
                FROM rekam_medis rm 
                JOIN pasien p ON rm.id_pasien = p.id_pasien 
                WHERE rm.id_dokter = :id_dokter 
                ORDER BY rm.tgl_pemeriksaan DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_dokter' => $id_dokter]);
        return $stmt->fetchAll();
    }

    // UPDATE
    public function update($id, $data) {
        $sql = "UPDATE rekam_medis 
                SET anamnesis = :anamnesis, 
                    pemeriksaan_fisik = :pemeriksaan_fisik,
                    diagnosis = :diagnosis,
                    tgl_pemeriksaan = :tgl_pemeriksaan
                WHERE id_rekam_medis = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':anamnesis' => $data['anamnesis'],
            ':pemeriksaan_fisik' => $data['pemeriksaan_fisik'],
            ':diagnosis' => $data['diagnosis'],
            ':tgl_pemeriksaan' => $data['tgl_pemeriksaan'],
            ':id' => $id,
        ]);
    }

    // DELETE
    public function delete($id) {
        $sql = "DELETE FROM rekam_medis WHERE id_rekam_medis = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
```

#### View — `rekam_medis/form.php`
```php
<h1>Isi Rekam Medis</h1>
<form method="post" action="/dokter/rekam_medis/store">
    <input type="hidden" name="id_pasien" value="<?= e($pasien['id_pasien']) ?>">
    <input type="hidden" name="no_rm" value="<?= e($pasien['no_rm']) ?>">
    
    <label>Anamnesis</label>
    <textarea name="anamnesis" rows="4"></textarea>
    
    <label>Pemeriksaan Fisik</label>
    <textarea name="pemeriksaan_fisik" rows="4"></textarea>
    
    <label>Penyakit yang diderita</label>
    <textarea name="penyakit_derita"></textarea>
    
    <label>Diagnosis</label>
    <input type="text" name="diagnosis" required>
    
    <label>Berat Badan (kg)</label>
    <input type="number" name="berat_badan" step="0.1">
    
    <label>Tinggi Badan (cm)</label>
    <input type="number" name="tinggi_badan" step="0.1">
    
    <label>Tekanan Darah</label>
    <input type="text" name="tekanan_darah" placeholder="contoh: 120/80">
    
    <button type="submit">Simpan Rekam Medis</button>
</form>
```

---

### 7.3 Resep (Salma)

#### Controller — `ResepController.php`
```php
class ResepController extends Controller {
    public function index() {
        $resep = new Resep();
        $pending = $resep->getAllPending();
        $this->view('resep/index', [
            'title' => 'Resep Menunggu',
            'pending' => $pending,
        ]);
    }

    public function create() {
        $obatList = (new Obat())->all();
        $this->view('resep/form', [
            'title' => 'Buat Resep',
            'obatList' => $obatList,
        ]);
    }

    public function store() {
        $data = [
            'id_pasien' => $_POST['id_pasien'] ?? 0,
            'id_dokter' => $_POST['id_dokter'] ?? 0,
            'tanggal_resep' => $_POST['tanggal_resep'] ?? date('Y-m-d'),
            'obat' => $_POST['obat'] ?? '',
            'dosis' => $_POST['dosis'] ?? '',
            'frekuensi' => $_POST['frekuensi'] ?? '',
            'durasi' => $_POST['durasi'] ?? '',
            'total' => $_POST['total'] ?? 0,
        ];
        $errors = [];
        if (empty($data['id_pasien'])) $errors[] = 'Pasien wajib diisi';
        if (empty($data['id_dokter'])) $errors[] = 'Dokter wajib diisi';
        if (empty($data['obat'])) $errors[] = 'Obat wajib diisi';
        if (empty($data['dosis'])) $errors[] = 'Dosis wajib diisi';
        if (!empty($errors)) {
            flash('error', implode(' | ', $errors));
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        $resep = new Resep();
        $result = $resep->create($data);
        if ($result) {
            flash('success', 'Resep dibuat');
            // Kirim notifikasi ke apoteker
            // (opsional: email/telepon)
        } else {
            flash('error', 'Gagal membuat resep');
        }
        header('Location: /apoteker/resep');
        exit;
    }

    public function serah($id) {
        $resep = new Resep();
        $resep->updateStatus($id, 'Diserahkan');
        flash('success', 'Resep diserahkan ke apoteker');
        header('Location: /apoteker/resep');
        exit;
    }
}
```

#### Model — `models/Resep.php`
```php
class Resep {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // CREATE
    public function create($data) {
        $sql = "INSERT INTO resep 
                (id_pasien, id_dokter, no_rm, tanggal_resep, 
                 obat, dosis, frekuensi, durasi, total, status) 
                VALUES 
                (:id_pasien, :id_dokter, :no_rm, :tanggal_resep, 
                 :obat, :dosis, :frekuensi, :durasi, :total, 'Menunggu')";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_pasien' => $data['id_pasien'],
            ':id_dokter' => $data['id_dokter'],
            ':no_rm' => $data['no_rm'],
            ':tanggal_resep' => $data['tanggal_resep'],
            ':obat' => $data['obat'],
            ':dosis' => $data['dosis'],
            ':frekuensi' => $data['frekuensi'],
            ':durasi' => $data['durasi'],
            ':total' => $data['total'],
        ]);
    }

    // READ (all pending)
    public function getAllPending() {
        $sql = "SELECT r.*, p.nama AS nama_pasien, d.nama AS dokter 
                FROM resep r 
                JOIN pasien p ON r.id_pasien = p.id_pasien 
                JOIN users d ON r.id_dokter = d.id_user 
                WHERE r.status = 'Menunggu' 
                ORDER BY r.tanggal_resep DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // READ (get by ID)
    public function getById($id) {
        $sql = "SELECT * FROM resep WHERE id_resep = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // UPDATE status (Serah)
    public function updateStatus($id, $status) {
        $sql = "UPDATE resep 
                SET status = :status, 
                    dibawa_oleh_apoteker = NOW() 
                WHERE id_resep = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id,
        ]);
    }

    // UPDATE stok obat
    public function updateStok($kode_obat, $qty) {
        $sql = "UPDATE obat 
                SET stok = stok - :qty 
                WHERE kode_obat = :kode_obat";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':qty' => $qty,
            ':kode_obat' => $kode_obat,
        ]);
    }

    // DELETE
    public function delete($id) {
        $sql = "DELETE FROM resep WHERE id_resep = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
```

#### View — `resep/form.php`
```php
<h1>Buat Resep</h1>
<form method="post" action="/apoteker/resep/store">
    <input type="hidden" name="id_pasien" value="<?= e($pasien['id_pasien']) ?>">
    <input type="hidden" name="no_rm" value="<?= e($pasien['no_rm']) ?>">
    
    <label>Obat</label>
    <select name="obat" required>
        <option value="">Pilih obat</option>
        <?php foreach ($obatList as $obat): ?>
            <option value="<?= e($obat['nama_obat']) ?>">
                <?= e($obat['nama_obat']) ?> (Stok: <?= e($obat['stok']) ?>)
            </option>
        <?php endforeach; ?>
    </select>
    
    <label>Dosis</label>
    <input type="text" name="dosis" required placeholder="contoh: 500mg">
    
    <label>Frekuensi</label>
    <input type="text" name="frekuensi" required placeholder="contoh: 2x sehari">
    
    <label>Durasi</label>
    <input type="text" name="durasi" required placeholder="contoh: 7 hari">
    
    <label>Total Biaya</label>
    <input type="number" name="total" step="0.01">
    
    <button type="submit">Buat Resep</button>
</form>
```

---

## 8. RINGKASAN TABEL & FUNGSI (PENJELASAN)

| Tabel | Fungsi Utama | CRUD |
|---|---|---|
| `users` | Manajemen akun & role | C, R, U, D |
| `pasien` | Data pasien | C, R, U, D |
| `pendaftaran_online` | Pendaftaran dari web (tanpa login) | C, R, U, D |
| `pendaftaran_detail` | Alokasi ke dokter & jadwal | C, R, U, D |
| `jadwal_dokter` | Jadwal praktik | C, R, U, D |
| `rekam_medis` | Catatan pemeriksaan dokter | C, R, U, D |
| `resep` | Resep obat dari dokter | C, R, U, D |
| `obat` | Stok obat | C, R, U, D |
| `apoteker` | Akun apoteker | C, R, U, D |
| `laporan` | Data laporan / audit | C, R, U, D |

---

## 9. STRUKTUR FILES APLIKASI

```
klinik-app/
├── index.php                  # Front Controller (route)
├── config/
│   └── database.php           # PDO koneksi
├── public/
│   ├── css/
│   │   ├── style.css          # Dashboard & staff styles
│   │   └── landing.css        # Public landing styles
│   └── js/
│       └── app.js             # Front-end JS
├── controllers/
│   ├── AuthController.php     # Login/Logout
│   ├── BerandaController.php  # Landing page
│   ├── PendaftaranController.php  # Pendaftaran online
│   ├── RekamMedisController.php   # Rekam medis (Alvin)
│   ├── ResepController.php      # Resep (Salma)
│   ├── DashboardController.php    # Dashboard
│   ├── JadwalController.php
│   ├── PenggunaController.php
│   └── LaporanController.php
├── models/
│   ├── Database.php           # PDO singleton
│   ├── User.php               # User CRUD + auth
│   ├── PendaftaranOnline.php  # Pendaftaran online
│   ├── Pasien.php             # Pasien CRUD
│   ├── RekamMedis.php         # Rekam medis (Alvin)
│   ├── Resep.php              # Resep (Salma)
│   ├── JadwalDokter.php
│   ├── Obat.php
│   └── Laporan.php
└── views/
    ├── auth/
    │   └── login.php
    ├── beranda/
    │   └── index.php
    ├── pendaftaran/
    │   ├── index.php          # Form pendaftaran (Rohan)
    │   ├── verifikasi.php
    │   └── status.php
    ├── rekam_medis/
    │   ├── form.php           # Isi rekam medis (Alvin)
    │   ├── show.php
    │   └── index.php
    ├── resep/
    │   ├── form.php           # Buat resep (Salma)
    │   ├── index.php
    │   └── show.php
    ├── dashboard/
    │   └── index.php
    ├── jadwal/
    │   ├── index.php
    │   └── form.php
    ├── pengguna/
    │   ├── index.php
    │   └── form.php
    ├── obat/
    │   ├── index.php
    │   └── form.php
    └── layouts/
        ├── header.php
        ├── footer.php
        ├── public_header.php
        └── public_footer.php
```

---

## 10. RINGKASAN TUGAS KELOMPOK

| Anggota | Bagian | Fokus |
|---|---|---|
| **Rohan** | Pendaftaran Online | Form pendaftaran, verifikasi, alokasi ke dokter |
| **Alvin** | Rekam Medis | Anamnesis, diagnosis, catatan medis |
| **Salma** | Resep & Apoteker | Resep, stok obat, serah terus |

### Pre-requisite (sebelum coding)
1. Desain database (MySQL) — tabel di atas
2. PDO connection (config/database.php)
3. MVC structure (controllers/, models/, views/)
4. CSRF protection & flash messages
5. Role-based access (admin/dokter/resepsionis/apoteker)

### Roadmap Implementasi
1. **Week 1**: Database design + PDO Connection + Auth (login/logout)
2. **Week 2**: Beranda + Pendaftaran Online (Rohan)
3. **Week 3**: Verifikasi + Dashboard (Rohan)
4. **Week 4**: Rekam Medis (Alvin)
5. **Week 5**: Resep + Opname Stok (Salma)
6. **Week 6**: Laporan + Testing + Deploy

---

## 11. POIN PENTING

1. **PDO** digunakan untuk semua query database (keamanan prepared statements)
2. **MVC** dipisah: Controller = logic, Model = database, View = tampilan
3. **CRUD** = Create/Read/Update/Delete — setiap fitur menggunakan pola ini
4. **Pendaftaran** = tanpa login, pasien hanya isi data → verifikasi → alokasi
5. **Rekam Medis** = dokter isi, data pasien + diagnosis
6. **Resep** = dokter buat, apoteker serah, stok update
7. **Role-based** = setiap role bisa lihat/handle fitur sesuai izin
8. **Flash messages** = alert sukses/error
9. **Route** = URL → controller → method → view

---

*Dibuat untuk bahan ajar: Arsitektur MVC + PDO + CRUD + Alur Klinik Sehat*
