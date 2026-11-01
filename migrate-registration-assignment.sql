USE klinik_app;

ALTER TABLE pendaftaran_online
    ADD COLUMN nomor_pendaftaran VARCHAR(40) NULL,
    ADD COLUMN poli VARCHAR(100) NOT NULL DEFAULT 'Poli Umum',
    ADD COLUMN id_dokter INT NULL;

UPDATE pendaftaran_online
SET nomor_pendaftaran = CONCAT('REG-', UPPER(SUBSTRING(token_hash, 1, 32)))
WHERE nomor_pendaftaran IS NULL;

ALTER TABLE pendaftaran_online
    MODIFY COLUMN nomor_pendaftaran VARCHAR(40) NOT NULL,
    ADD UNIQUE KEY uq_pendaftaran_nomor (nomor_pendaftaran),
    ADD CONSTRAINT fk_pendaftaran_dokter FOREIGN KEY (id_dokter)
        REFERENCES users(id_user) ON DELETE SET NULL;

ALTER TABLE kunjungan
    ADD COLUMN id_dokter INT NULL,
    ADD COLUMN id_pendaftaran INT NULL UNIQUE,
    ADD CONSTRAINT fk_kunjungan_dokter FOREIGN KEY (id_dokter)
        REFERENCES users(id_user) ON DELETE SET NULL,
    ADD CONSTRAINT fk_kunjungan_pendaftaran FOREIGN KEY (id_pendaftaran)
        REFERENCES pendaftaran_online(id_pendaftaran) ON DELETE SET NULL;