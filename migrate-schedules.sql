USE klinik_app;

CREATE TABLE IF NOT EXISTS jadwal_dokter (
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