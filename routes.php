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

    // --- (tambahkan route lain di sini saat development) ---
    '/landing' => [LandingController::class, 'index']

];