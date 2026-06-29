-- =========================
-- Trigger pengadaan
-- =========================

DELIMITER $$

-- Hapus trigger lama jika ada agar tidak bentrok
DROP TRIGGER IF EXISTS after_update_lunas_pengadaan$$

CREATE TRIGGER after_update_lunas_pengadaan
AFTER UPDATE ON t_pengadaan
FOR EACH ROW
BEGIN
    -- 1. Cek syarat: Hanya jalan jika status_bayar berubah dari Pending ke Lunas
    IF OLD.status_bayar = 'Pending' AND NEW.status_bayar = 'Lunas' THEN
        
        -- 2. Ambil data dari detail, langsung tembak/pindahkan ke stok_log
        INSERT INTO stok_log (id_gudang, id_komoditas, qty_in, qty_out)
        SELECT NEW.id_gudang, pd.id_komoditas, pd.jumlah, 0.00
        FROM t_pengadaan_detail pd
        WHERE pd.id_pengadaan = NEW.id_pengadaan
        -- 3. Jika duplikat (kombinasi gudang & barang sudah ada), otomatis update qty_in
        ON DUPLICATE KEY UPDATE qty_in = qty_in + VALUES(qty_in);

    END IF;
END$$

DELIMITER ;

-- =========================
-- Trigger distribusi
-- =========================

-- cek stok sebelum terima
DROP TRIGGER IF EXISTS trg_t_distribusi_before_terima;

DELIMITER $$

CREATE TRIGGER trg_t_distribusi_before_terima
BEFORE UPDATE ON t_distribusi
FOR EACH ROW
BEGIN
    IF OLD.status_distribusi = 'Dikirim'
       AND NEW.status_distribusi = 'Diterima' THEN

        IF EXISTS (
            SELECT 1
            FROM t_distribusi_detail dd
            LEFT JOIN stok_log sl
                ON sl.id_gudang = OLD.id_gudang_asal
                AND sl.id_komoditas = dd.id_komoditas
            WHERE dd.id_distribusi = OLD.id_distribusi
              AND (COALESCE(sl.qty_in, 0) - COALESCE(sl.qty_out, 0)) < dd.jumlah
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stok gudang asal tidak mencukupi untuk menerima distribusi.';
        END IF;

    END IF;
END$$

DELIMITER ;

-- update stok setelah terima
DROP TRIGGER IF EXISTS trg_t_distribusi_after_terima;

DELIMITER $$

CREATE TRIGGER trg_t_distribusi_after_terima
AFTER UPDATE ON t_distribusi
FOR EACH ROW
BEGIN
    IF OLD.status_distribusi = 'Dikirim'
       AND NEW.status_distribusi = 'Diterima' THEN

        -- Gudang asal keluar barang
        INSERT INTO stok_log (
            id_gudang,
            id_komoditas,
            qty_in,
            qty_out
        )
        SELECT
            NEW.id_gudang_asal,
            dd.id_komoditas,
            0.00,
            dd.jumlah
        FROM t_distribusi_detail dd
        WHERE dd.id_distribusi = NEW.id_distribusi
        ON DUPLICATE KEY UPDATE
            qty_out = qty_out + VALUES(qty_out);

        -- Gudang tujuan masuk barang
        INSERT INTO stok_log (
            id_gudang,
            id_komoditas,
            qty_in,
            qty_out
        )
        SELECT
            NEW.id_gudang_tujuan,
            dd.id_komoditas,
            dd.jumlah,
            0.00
        FROM t_distribusi_detail dd
        WHERE dd.id_distribusi = NEW.id_distribusi
        ON DUPLICATE KEY UPDATE
            qty_in = qty_in + VALUES(qty_in);

    END IF;
END$$

DELIMITER ;

-- =========================
-- Trigger penyerahan
-- =========================

-- trigger cek stok sebelum diserahkan
DROP TRIGGER IF EXISTS trg_t_penyerahan_before_diserahkan;

DELIMITER $$

CREATE TRIGGER trg_t_penyerahan_before_diserahkan
BEFORE UPDATE ON t_penyerahan
FOR EACH ROW
BEGIN
    IF OLD.status_penyerahan = 'Diproses'
       AND NEW.status_penyerahan = 'Diserahkan' THEN

        IF EXISTS (
            SELECT 1
            FROM t_penyerahan_detail pd
            LEFT JOIN stok_log sl
                ON sl.id_gudang = OLD.id_gudang
                AND sl.id_komoditas = pd.id_komoditas
            WHERE pd.id_penyerahan = OLD.id_penyerahan
              AND (COALESCE(sl.qty_in, 0) - COALESCE(sl.qty_out, 0)) < pd.jumlah
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stok posyandu tidak mencukupi untuk penyerahan bantuan.';
        END IF;

    END IF;
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

        INSERT INTO stok_log (
            id_gudang,
            id_komoditas,
            qty_in,
            qty_out
        )
        SELECT
            NEW.id_gudang,
            pd.id_komoditas,
            0.00,
            pd.jumlah
        FROM t_penyerahan_detail pd
        WHERE pd.id_penyerahan = NEW.id_penyerahan
        ON DUPLICATE KEY UPDATE
            qty_out = qty_out + VALUES(qty_out);

    END IF;
END$$

DELIMITER ;