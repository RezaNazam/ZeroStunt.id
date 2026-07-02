<?php
// ============================================================
// ROUTES
// Daftar semua URL dan controller yang menanganinya
// Format: 'url' => [NamaController::class, 'namaMethod']
// ============================================================

return [
    // --Public Routes--
    // --- Landing Page ---
    '/landing' => [LandingController::class, 'index'],

    // --- Auth ---
    '/auth/login' => [AuthController::class, 'login'],
    '/auth/register' => [AuthController::class, 'register'],
    '/auth/logout' => [AuthController::class, 'logout'],

    // --- Dashboard ---
    '/dashboard' => [DashboardController::class, 'index'],

    // --- Profile ---
    '/profile' => [ProfileController::class, 'index', [ROLE_ADMIN, ROLE_KADER, ROLE_PETANI, ROLE_IBU]],
    '/profile/edit' => [ProfileController::class, 'edit', [ROLE_ADMIN, ROLE_KADER, ROLE_PETANI, ROLE_IBU]],
    '/profile/update' => [ProfileController::class, 'update', [ROLE_ADMIN, ROLE_KADER, ROLE_PETANI, ROLE_IBU]],
    '/profile/delete' => [ProfileController::class, 'delete', [ROLE_ADMIN, ROLE_KADER, ROLE_PETANI, ROLE_IBU]],

    // --- MASTER DATA ---
    // --- MASTER DATA ROUTE---

    // --- Master: User ---
    '/master/users' => [MasterController::class, 'indexUsers', [ROLE_ADMIN]],
    '/master/users/create' => [MasterController::class, 'createUsers', [ROLE_ADMIN]],
    '/master/users/store' => [MasterController::class, 'storeUsers', [ROLE_ADMIN]],
    '/master/users/edit' => [MasterController::class, 'editUsers', [ROLE_ADMIN]],
    '/master/users/update' => [MasterController::class, 'updateUsers', [ROLE_ADMIN]],
    '/master/users/delete' => [MasterController::class, 'deleteUsers', [ROLE_ADMIN]],

    // -- Master: Ibu ---
    '/master/ibu/create' => [MasterController::class, 'createIbu', [ROLE_IBU]],
    '/master/ibu/store' => [MasterController::class, 'storeIbu', [ROLE_IBU]],
    '/master/ibu/riwayat-periksa' => [MasterController::class, 'riwayatPeriksa', [ROLE_IBU]],
    '/master/ibu/histori-bantuan' => [MasterController::class, 'historiBantuan', [ROLE_IBU]],
    '/master/ibu/ibuAnak' => [MasterController::class, 'callIbuDanAnak', [ROLE_ADMIN]],

    // --- Master: Anak ---
    '/master/anak' => [MasterController::class, 'indexAnak', [ROLE_IBU]],
    '/master/anak/create' => [MasterController::class, 'createAnak', [ROLE_IBU]],
    '/master/anak/store' => [MasterController::class, 'storeAnak', [ROLE_IBU]],
    '/master/anak/edit' => [MasterController::class, 'editAnak', [ROLE_IBU]],
    '/master/anak/update' => [MasterController::class, 'updateAnak', [ROLE_IBU]],
    '/master/anak/delete' => [MasterController::class, 'deleteAnak', [ROLE_IBU]],

    // --- Master: Petani ---
    '/master/petani/create' => [MasterController::class, 'createPetani', [ROLE_PETANI]],
    '/master/petani/store' => [MasterController::class, 'storePetani', [ROLE_PETANI]],
    '/master/petani/riwayat-ekonomi' => [MasterController::class, 'riwayatEkonomi', [ROLE_PETANI]],
    '/master/petani/profil-lahan' => [MasterController::class, 'profilLahan', [ROLE_PETANI]],
    '/master/petani/lahan/create' => [MasterController::class, 'createLahanPetani', [ROLE_PETANI]],
    '/master/petani/lahan/store' => [MasterController::class, 'storeLahanPetani', [ROLE_PETANI]],

    // --- Master: Gudang ---
    '/master/gudang' => [MasterController::class, 'indexGudang', [ROLE_ADMIN]],
    '/master/gudang/create' => [MasterController::class, 'createGudang', [ROLE_ADMIN]],
    '/master/gudang/store' => [MasterController::class, 'storeGudang', [ROLE_ADMIN]],
    '/master/gudang/edit' => [MasterController::class, 'editGudang', [ROLE_ADMIN]],
    '/master/gudang/update' => [MasterController::class, 'updateGudang', [ROLE_ADMIN]],
    '/master/gudang/delete' => [MasterController::class, 'deleteGudang', [ROLE_ADMIN]],

    // --- Master: Komoditas ---
    '/master/komoditas' => [MasterController::class, 'indexKomoditas', [ROLE_ADMIN]],
    '/master/komoditas/create' => [MasterController::class, 'createKomoditas', [ROLE_ADMIN]],
    '/master/komoditas/store' => [MasterController::class, 'storeKomoditas', [ROLE_ADMIN]],
    '/master/komoditas/edit' => [MasterController::class, 'editKomoditas', [ROLE_ADMIN]],
    '/master/komoditas/update' => [MasterController::class, 'updateKomoditas', [ROLE_ADMIN]],
    '/master/komoditas/delete' => [MasterController::class, 'deleteKomoditas', [ROLE_ADMIN]],
    '/master/komoditas/restore' => [MasterController::class, 'restoreKomoditas', [ROLE_ADMIN]],

    // --- Master: Satuan ---
    '/master/satuan' => [MasterController::class, 'indexSatuan', [ROLE_ADMIN]],
    '/master/satuan/create' => [MasterController::class, 'createSatuan', [ROLE_ADMIN]],
    '/master/satuan/store' => [MasterController::class, 'storeSatuan', [ROLE_ADMIN]],
    '/master/satuan/edit' => [MasterController::class, 'editSatuan', [ROLE_ADMIN]],
    '/master/satuan/update' => [MasterController::class, 'updateSatuan', [ROLE_ADMIN]],
    '/master/satuan/delete' => [MasterController::class, 'deleteSatuan', [ROLE_ADMIN]],

    // --- Master: Standar Pertumbuhan ---
    '/master/standar-pertumbuhan' => [MasterController::class, 'callStandarPertumbuhan', [ROLE_ADMIN, ROLE_KADER]],

    // --- Master: Stok Posyandu ---
    '/master/kader/stok' => [MasterController::class, 'stokPosyandu', [ROLE_ADMIN, ROLE_KADER]],

    // --- Master: Paket Gizi ---
    '/master/paket-gizi' => [MasterController::class, 'paketGizi', [ROLE_ADMIN]],
    '/master/paket-gizi/edit' => [MasterController::class, 'editPaketGizi', [ROLE_ADMIN]],
    '/master/paket-gizi/update' => [MasterController::class, 'updatePaketGizi', [ROLE_ADMIN]],

    // -- route mater diatas --

    // --- TRANSAKSI DATA ROUTE

    // --- Transaksi Pengadaan ---
    // --- Transaksi Pengadaan (Admin -> akses buat pengadaan, Petani -> terima yang disanggupi) ---
    '/transaksi/pengadaan' => [TransaksiController::class, 'pengadaan', [ROLE_PETANI, ROLE_ADMIN]],
    '/transaksi/pengadaan/ambil' => [TransaksiController::class, 'ambilPengadaan', [ROLE_PETANI]],
    '/transaksi/pengadaan/buat' => [TransaksiController::class, 'buatPengadaan', [ROLE_ADMIN]],
    '/transaksi/pengadaan/simpan' => [TransaksiController::class, 'simpanPengadaan', [ROLE_ADMIN]],
    '/transaksi/pengadaan/detail' => [TransaksiController::class, 'detailPengadaan', [ROLE_PETANI, ROLE_ADMIN]],
    '/transaksi/pengadaan/lunasi' => [TransaksiController::class, 'lunasiPengadaan', [ROLE_ADMIN]],

    // --- Transaksi Penyerahan ---
    '/transaksi/penyerahan' => [TransaksiController::class, 'penyerahan', [ROLE_KADER]],
    '/transaksi/penyerahan/create' => [TransaksiController::class, 'createPenyerahan', [ROLE_KADER]],
    '/transaksi/penyerahan/store' => [TransaksiController::class, 'storePenyerahan', [ROLE_KADER]],
    '/transaksi/penyerahan/serahkan' => [TransaksiController::class, 'serahkanPenyerahan', [ROLE_KADER]],
    '/transaksi/penyerahan/detail' => [TransaksiController::class, 'detailPenyerahan', [ROLE_KADER, ROLE_IBU]],

    // --- Transaksi Distribusi ---
    '/transaksi/distribusi' => [TransaksiController::class, 'distribusi', [ROLE_ADMIN, ROLE_KADER]],
    '/transaksi/distribusi/create' => [TransaksiController::class, 'createDistribusi', [ROLE_ADMIN]],
    '/transaksi/distribusi/store' => [TransaksiController::class, 'storeDistribusi', [ROLE_ADMIN]],
    '/transaksi/distribusi/terima' => [TransaksiController::class, 'terimaDistribusi', [ROLE_KADER]],
    '/transaksi/distribusi/batal' => [TransaksiController::class, 'batalDistribusi', [ROLE_ADMIN]],
    '/transaksi/pemeriksaan' => [TransaksiController::class, 'pemeriksaan', [ROLE_ADMIN, ROLE_KADER, ROLE_IBU]],
    '/transaksi/pemeriksaan/create' => [TransaksiController::class, 'createPemeriksaan', [ROLE_ADMIN, ROLE_KADER]],
    '/transaksi/pemeriksaan/store' => [TransaksiController::class, 'storePemeriksaan', [ROLE_ADMIN, ROLE_KADER]],
    '/transaksi/pemeriksaan/delete' => [TransaksiController::class, 'deletePemeriksaan', [ROLE_ADMIN]],
    '/transaksi/pemeriksaan/kalkulasi' => [TransaksiController::class, 'kalkulasiGizi', [ROLE_ADMIN, ROLE_KADER]],


    // --- Laporan ---
    '/laporan' => [LaporanController::class, 'index', [ROLE_ADMIN, ROLE_KADER, ROLE_IBU, ROLE_PETANI]],
    '/laporan/pdf' => [LaporanController::class, 'downloadPdf', [ROLE_ADMIN, ROLE_KADER, ROLE_IBU, ROLE_PETANI]],

    // --- (tambahkan route lain di sini saat development) ---

];
