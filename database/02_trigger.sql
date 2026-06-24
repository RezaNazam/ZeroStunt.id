
-- cek stok sebelum terima
DROP TRIGGER IF EXISTS trg_t_distribusi_before_terima;

DELIMITER $$

CREATE TRIGGER trg_t_distribusi_before_terima
BEFORE
UPDATE ON t_distribusi
FOR EACH ROW
BEGIN
    IF OLD.status_distribusi = 'Dikirim'
        AND NEW.status_distribusi = 'Diterima' THEN

    IF EXISTS (
            SELECT 1
    FROM t_distribusi_detail dd
        LEFT JOIN t_stok s
        ON s.id_gudang = OLD.id_gudang_asal
            AND s.id_komoditas = dd.id_komoditas
    WHERE dd.id_distribusi = OLD.id_distribusi
        AND COALESCE(s.jumlah_stok, 0) < dd.jumlah
        ) THEN
            SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT
    = 'Stok gudang asal tidak mencukupi untuk menerima distribusi.';
END
IF;

    END
IF;
END$$

DELIMITER ;

-- update stok setelah terima
DROP TRIGGER IF EXISTS trg_t_distribusi_after_terima;

DELIMITER $$

CREATE TRIGGER trg_t_distribusi_after_terima
AFTER
UPDATE ON t_distribusi
FOR EACH ROW
BEGIN
    IF OLD.status_distribusi = 'Dikirim'
        AND NEW.status_distribusi = 'Diterima' THEN

    UPDATE t_stok s
        JOIN t_distribusi_detail dd
    ON dd.id_komoditas = s.id_komoditas
    SET s
    .jumlah_stok = s.jumlah_stok - dd.jumlah
        WHERE dd.id_distribusi = NEW.id_distribusi
          AND s.id_gudang = NEW.id_gudang_asal;

    INSERT INTO t_stok
        (
        id_gudang,
        id_komoditas,
        jumlah_stok
        )
    SELECT
        NEW.id_gudang_tujuan,
        dd.id_komoditas,
        dd.jumlah
    FROM t_distribusi_detail dd
    WHERE dd.id_distribusi = NEW.id_distribusi
    ON DUPLICATE KEY
    UPDATE
            jumlah_stok = jumlah_stok + VALUES
    (jumlah_stok);

END
IF;
END$$

DELIMITER ;


-- trigger cek stok sebelum diserahkan
DROP TRIGGER IF EXISTS trg_t_penyerahan_before_diserahkan;

DELIMITER $$

CREATE TRIGGER trg_t_penyerahan_before_diserahkan
BEFORE
UPDATE ON t_penyerahan
FOR EACH ROW
BEGIN
    IF OLD.status_penyerahan = 'Diproses'
        AND NEW.status_penyerahan = 'Diserahkan' THEN

    IF EXISTS (
            SELECT 1
    FROM t_penyerahan_detail pd
        LEFT JOIN t_stok s
        ON s.id_gudang = OLD.id_gudang
            AND s.id_komoditas = pd.id_komoditas
    WHERE pd.id_penyerahan = OLD.id_penyerahan
        AND COALESCE(s.jumlah_stok, 0) < pd.jumlah
        ) THEN
            SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT
    = 'Stok posyandu tidak mencukupi untuk penyerahan bantuan.';
END
IF;

    END
IF;
END$$

DELIMITER ;


-- trigger cek stok setelah diserahkan
DROP TRIGGER IF EXISTS trg_t_penyerahan_after_diserahkan;

DELIMITER $$

CREATE TRIGGER trg_t_penyerahan_after_diserahkan
AFTER UPDATE ON t_penyerahan
FOR EACH ROW
BEGIN
    IF OLD.status_penyerahan = 'Diproses'
       AND NEW.status_penyerahan = 'Diserahkan' THEN

        UPDATE t_stok s
        JOIN t_penyerahan_detail pd
            ON pd.id_komoditas = s.id_komoditas
        SET s.jumlah_stok = s.jumlah_stok - pd.jumlah
        WHERE pd.id_penyerahan = NEW.id_penyerahan
          AND s.id_gudang = NEW.id_gudang;

    END IF;
END$$

DELIMITER ;