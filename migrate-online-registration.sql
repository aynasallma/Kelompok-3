USE klinik_app;

ALTER TABLE users
    MODIFY COLUMN role ENUM('admin', 'resepsionis', 'dokter', 'apoteker') NOT NULL DEFAULT 'admin';

CREATE TABLE IF NOT EXISTS pendaftaran_online (
    id_pendaftaran INT AUTO_INCREMENT PRIMARY KEY,
    token_hash CHAR(64) NOT NULL UNIQUE,
    ip_hash CHAR(64) NOT NULL,
    nama_pasien VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    tanggal_lahir DATE NOT NULL,
    alamat TEXT,
    no_hp VARCHAR(20) NOT NULL,
    status ENUM('Menunggu', 'Disetujui', 'Ditolak') NOT NULL DEFAULT 'Menunggu',
    catatan_staf VARCHAR(500),
    id_pasien INT NULL,
    direview_oleh INT NULL,
    diajukan_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    direview_pada TIMESTAMP NULL,
    FOREIGN KEY (id_pasien) REFERENCES pasien(id_pasien) ON DELETE SET NULL,
    FOREIGN KEY (direview_oleh) REFERENCES users(id_user) ON DELETE SET NULL,
    INDEX idx_pendaftaran_status (status, diajukan_pada),
    INDEX idx_pendaftaran_ip (ip_hash, diajukan_pada)
);