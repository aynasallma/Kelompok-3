CREATE DATABASE IF NOT EXISTS klinik_app
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE klinik_app;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'resepsionis', 'dokter', 'apoteker') NOT NULL DEFAULT 'admin',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    aktif_mulai DATE NOT NULL,
    aktif_sampai DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE jadwal_dokter (
    id_jadwal INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    poli VARCHAR(100) NOT NULL,
    lokasi VARCHAR(150) NOT NULL DEFAULT 'Klinik Utama',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    INDEX idx_jadwal_hari_aktif (hari, is_active)
);

CREATE TABLE hari_libur (
    tanggal DATE PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    dibuat_oleh INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dibuat_oleh) REFERENCES users(id_user) ON DELETE SET NULL
);

CREATE TABLE pasien (
    id_pasien INT AUTO_INCREMENT PRIMARY KEY,
    no_rm VARCHAR(20) NOT NULL UNIQUE,
    nama_pasien VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    tanggal_lahir DATE NOT NULL,
    alamat TEXT,
    no_hp VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pendaftaran_online (
    id_pendaftaran INT AUTO_INCREMENT PRIMARY KEY,
    token_hash CHAR(64) NOT NULL UNIQUE,
    nomor_pendaftaran VARCHAR(40) NOT NULL UNIQUE,
    ip_hash CHAR(64) NOT NULL,
    nama_pasien VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    tanggal_lahir DATE NOT NULL,
    alamat TEXT,
    no_hp VARCHAR(20) NOT NULL,
    poli VARCHAR(100) NOT NULL,
    tanggal_kunjungan DATE NOT NULL,
    status ENUM('Menunggu', 'Disetujui', 'Ditolak') NOT NULL DEFAULT 'Menunggu',
    catatan_staf VARCHAR(500),
    id_pasien INT NULL,
    id_dokter INT NULL,
    direview_oleh INT NULL,
    diajukan_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    direview_pada TIMESTAMP NULL,
    FOREIGN KEY (id_pasien) REFERENCES pasien(id_pasien) ON DELETE SET NULL,
    FOREIGN KEY (id_dokter) REFERENCES users(id_user) ON DELETE SET NULL,
    FOREIGN KEY (direview_oleh) REFERENCES users(id_user) ON DELETE SET NULL,
    INDEX idx_pendaftaran_status (status, diajukan_pada),
    INDEX idx_pendaftaran_ip (ip_hash, diajukan_pada)
);

CREATE TABLE kunjungan (
    id_kunjungan INT AUTO_INCREMENT PRIMARY KEY,
    id_pasien INT NOT NULL,
    id_dokter INT NULL,
    id_pendaftaran INT NULL UNIQUE,
    tanggal_kunjungan DATE NOT NULL,
    no_antrian INT NOT NULL,
    poli VARCHAR(100) NOT NULL,
    status ENUM('Menunggu', 'Diperiksa', 'Selesai', 'Batal')
        DEFAULT 'Menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pasien)
        REFERENCES pasien(id_pasien)
        ON DELETE CASCADE,
    FOREIGN KEY (id_dokter)
        REFERENCES users(id_user)
        ON DELETE SET NULL,
    FOREIGN KEY (id_pendaftaran)
        REFERENCES pendaftaran_online(id_pendaftaran)
        ON DELETE SET NULL,
    UNIQUE KEY unik_antrian (tanggal_kunjungan, poli, no_antrian)
);

CREATE TABLE rekam_medis (
    id_rekam_medis INT AUTO_INCREMENT PRIMARY KEY,
    id_kunjungan INT NOT NULL UNIQUE,
    keluhan TEXT NOT NULL,
    diagnosa TEXT NOT NULL,
    tindakan TEXT,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kunjungan)
        REFERENCES kunjungan(id_kunjungan)
        ON DELETE CASCADE
);

CREATE TABLE obat (
    id_obat INT AUTO_INCREMENT PRIMARY KEY,
    kode_obat VARCHAR(30) NOT NULL UNIQUE,
    nama_obat VARCHAR(100) NOT NULL,
    kategori VARCHAR(100),
    satuan VARCHAR(30) NOT NULL,
    harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    stok_minimum INT NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE resep (
    id_resep INT AUTO_INCREMENT PRIMARY KEY,
    id_rekam_medis INT NOT NULL,
    tanggal_resep DATE NOT NULL,
    status ENUM('Menunggu', 'Diserahkan', 'Batal')
        DEFAULT 'Menunggu',
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rekam_medis)
        REFERENCES rekam_medis(id_rekam_medis)
        ON DELETE CASCADE
);

CREATE TABLE detail_resep (
    id_detail_resep INT AUTO_INCREMENT PRIMARY KEY,
    id_resep INT NOT NULL,
    id_obat INT NOT NULL,
    dosis VARCHAR(100) NOT NULL,
    aturan_pakai VARCHAR(150) NOT NULL,
    jumlah INT NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_resep)
        REFERENCES resep(id_resep)
        ON DELETE CASCADE,
    FOREIGN KEY (id_obat)
        REFERENCES obat(id_obat)
);

CREATE TABLE transaksi_stok (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_obat INT NOT NULL,
    jenis ENUM('Masuk', 'Keluar', 'Penyesuaian') NOT NULL,
    jumlah INT NOT NULL,
    keterangan VARCHAR(255),
    tanggal_transaksi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_obat)
        REFERENCES obat(id_obat)
);

CREATE TABLE audit_trail (
    id_audit INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NULL,
    aktivitas VARCHAR(255) NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user)
        REFERENCES users(id_user)
        ON DELETE SET NULL
);
