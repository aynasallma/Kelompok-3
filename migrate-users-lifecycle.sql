USE klinik_app;

ALTER TABLE users
    ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1,
    ADD COLUMN aktif_mulai DATE NULL,
    ADD COLUMN aktif_sampai DATE NULL;

UPDATE users
SET aktif_mulai = CURDATE(), aktif_sampai = '2099-12-31'
WHERE aktif_mulai IS NULL OR aktif_sampai IS NULL;

ALTER TABLE users
    MODIFY COLUMN aktif_mulai DATE NOT NULL,
    MODIFY COLUMN aktif_sampai DATE NOT NULL;