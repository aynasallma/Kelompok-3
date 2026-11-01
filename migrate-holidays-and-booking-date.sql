USE klinik_app;

CREATE TABLE IF NOT EXISTS hari_libur (
    tanggal DATE PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    dibuat_oleh INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dibuat_oleh) REFERENCES users(id_user) ON DELETE SET NULL
);

ALTER TABLE pendaftaran_online
    ADD COLUMN tanggal_kunjungan DATE NULL;

UPDATE pendaftaran_online
SET tanggal_kunjungan = DATE(diajukan_pada)
WHERE tanggal_kunjungan IS NULL;

ALTER TABLE pendaftaran_online
    MODIFY COLUMN tanggal_kunjungan DATE NOT NULL;