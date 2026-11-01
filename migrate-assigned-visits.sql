USE klinik_app;

ALTER TABLE kunjungan
    ADD COLUMN id_dokter INT NULL,
    ADD COLUMN id_pendaftaran INT NULL UNIQUE,
    ADD CONSTRAINT fk_kunjungan_dokter FOREIGN KEY (id_dokter)
        REFERENCES users(id_user) ON DELETE SET NULL,
    ADD CONSTRAINT fk_kunjungan_pendaftaran FOREIGN KEY (id_pendaftaran)
        REFERENCES pendaftaran_online(id_pendaftaran) ON DELETE SET NULL;