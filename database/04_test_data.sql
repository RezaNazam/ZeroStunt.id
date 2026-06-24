INSERT INTO pengadaan
(
    id_petani,
    id_komoditas,
    jumlah,
    posyandu,
    status
)
VALUES
(NULL, 1, 100, 'Posyandu Mawar', 'Tersedia'),
(NULL, 2, 250, 'Posyandu Melati', 'Tersedia'),
(NULL, 1, 150, 'Posyandu Anggrek', 'Tersedia');

INSERT INTO komoditas_pangan
(
    nama_komoditas,
    kategori_gizi,
    id_satuan,
    deskripsi
)
VALUES
(
    'telur',
    'Protein Hewani',
    2,
    'telur ayam'
),
(
    'sayur',
    'Vitamin dan Mineral',
    2,
    'sayuran hijau'
);