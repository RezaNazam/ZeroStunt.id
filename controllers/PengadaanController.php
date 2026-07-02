<?php

class PengadaanController {
    
    // 1. Fungsi untuk menampilkan form
    public function create() {
        // Sesuaikan cara panggil / include file view di proyek lu
        // Contoh native:
        include 'views/master/transaksi/pengadaan/create.php';
    }

    // 2. Fungsi untuk memproses data dari form (ketika disubmit)
    public function store() {
        // Ambil data POST
        $namaBarang   = $_POST['nama_barang'] ?? '';
        $kategori     = $_POST['kategori'] ?? '';
        $jumlah       = intval($_POST['jumlah'] ?? 0);
        $hargaSatuan  = floatval($_POST['harga_satuan'] ?? 0);
        $totalHarga   = $jumlah * $hargaSatuan;
        $tanggal      = $_POST['tanggal_dibutuhkan'] ?? '';
        $deskripsi    = $_POST['deskripsi'] ?? '';
        
        $namaFileBaru = null;

        // Logika file upload dokumen pendukung
        if (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['dokumen']['tmp_name'];
            $fileName = $_FILES['dokumen']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            // Generate nama unik
            $namaFileBaru = time() . '_' . md5($fileName) . '.' . $fileExtension;
            
            // Tentukan folder tujuan upload
            $uploadFileDir = './uploads/pengadaan/';
            if(!is_dir($uploadFileDir)){
                mkdir($uploadFileDir, 0755, true);
            }
            
            move_uploaded_file($fileTmpPath, $uploadFileDir . $namaFileBaru);
        }

        // [TULIS QUERY DATABASE DI SINI]
        // Contoh menggunakan PDO/Sqli proyek lu:
        // $db->query("INSERT INTO pengadaan (...) VALUES (...)");

        // Setelah sukses, arahkan kembali (redirect) ke halaman list pengadaan
        header("Location: /pengadaan?status=success");
        exit();
    }
}