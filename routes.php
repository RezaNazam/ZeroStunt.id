<?php
// ============================================================
// ROUTES
// Daftar semua URL dan controller yang menanganinya
// Format: 'url' => [NamaController::class, 'namaMethod']
// ============================================================

return [

    // --- Auth ---
    '/auth/login' => [AuthController::class, 'login'],
    '/auth/register' => [AuthController::class, 'register'],
    '/auth/logout' => [AuthController::class, 'logout'],

    // --- Dashboard ---
    '/dashboard' => [DashboardController::class, 'index'],

    // --- MASTER DATA ---

    // -- Master: Ibu ---
    '/master/ibu/create' => [MasterController::class, 'createIbu'],
    '/master/ibu/store' => [MasterController::class, 'storeIbu'],

    // --- Master: Petani ---
    '/master/petani/create' => [MasterController::class, 'createPetani'],
    '/master/petani/store' => [MasterController::class, 'storePetani'],
    '/pengadaan' => [DashboardController::class, 'pengadaan'],

    // --- Master: Gudang ---
    '/master/gudang' => [MasterController::class, 'indexGudang'],
    '/master/gudang/create' => [MasterController::class, 'createGudang'],
    '/master/gudang/store' => [MasterController::class, 'storeGudang'],
    '/master/gudang/edit' => [MasterController::class, 'editGudang'],
    '/master/gudang/update' => [MasterController::class, 'updateGudang'],
    '/master/gudang/delete' => [MasterController::class, 'deleteGudang'],

    // --- Master: Komoditas ---
    '/master/komoditas' => [MasterController::class, 'indexKomoditas'],
    '/master/komoditas/create' => [MasterController::class, 'createKomoditas'],
    '/master/komoditas/store' => [MasterController::class, 'storeKomoditas'],
    '/master/komoditas/edit' => [MasterController::class, 'editKomoditas'],
    '/master/komoditas/update' => [MasterController::class, 'updateKomoditas'],
    '/master/komoditas/delete' => [MasterController::class, 'deleteKomoditas'],
    
    // --- Master: Satuan ---
    '/master/satuan' => [MasterController::class, 'indexSatuan'],
    '/master/satuan/create' => [MasterController::class, 'createSatuan'],
    '/master/satuan/store' => [MasterController::class, 'storeSatuan'],
    '/master/satuan/edit' => [MasterController::class, 'editSatuan'],
    '/master/satuan/update' => [MasterController::class, 'updateSatuan'],
    '/master/satuan/delete' => [MasterController::class, 'deleteSatuan'],

    // --- Additional ----
    '/riwayat-ekonomi' => [MasterController::class, 'riwayatEkonomi'],
    '/profil-lahan' => [MasterController::class, 'profilLahan'],

    // --- (tambahkan route lain di sini saat development) ---
    '/landing' => [LandingController::class, 'index']

];