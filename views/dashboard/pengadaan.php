<?php
require_once '../models/Pengadaan.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pengadaanModel = new Pengadaan();

/*
    HANDLE BUTTON ACC (UPDATE DATABASE)
*/
if (isset($_POST['ambil'])) {

    $idPengadaan = (int) $_POST['id_pengadaan'];
    $idPetani = $_SESSION['user_id']; // FIX INI

    $pengadaanModel->ambil($idPengadaan, $idPetani);

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

/*
    AMBIL DATA TERBARU DARI DATABASE
    (INI YANG BIKIN STATUS TIDAK KEMBALI LAGI)
*/
$pengadaan = $pengadaanModel->getAll();

$pageTitle = 'Pengadaan Saya';
$pageSubtitle = 'Pilih pengadaan komoditas yang tersedia dari posyandu.';

$limit = 5;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalData = $pengadaanModel->countAll();
$totalPage = ceil($totalData / $limit);

$pengadaan = $pengadaanModel->getAll($limit, $offset);

ob_start();
?>

<section class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-gray-100">
        <h2 class="text-xl font-extrabold text-gray-900">
            Daftar Pengadaan
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Pilih pengadaan yang masih tersedia untuk diambil.
        </p>
    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Komoditas</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Jumlah</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Posyandu</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                <?php foreach ($pengadaan as $index => $row): ?>

                    <?php
                    $badgeClass = $row['status'] === 'Tersedia'
                        ? 'bg-green-50 text-green-700'
                        : 'bg-red-50 text-red-700';
                    ?>

                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-6 py-4 font-semibold text-gray-900">
                            <?= htmlspecialchars($row['nama_komoditas']); ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <?= $row['jumlah']; ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <?= $row['posyandu']; ?>
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass; ?>">
                                <?= $row['status']; ?>
                            </span>
                        </td>

                        <td class="px-6 py-4">

                            <?php if ($row['status'] === 'Tersedia'): ?>
                                
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="id_pengadaan" value="<?= $row['id_pengadaan'] ?>">

                                    <button type="submit" name="ambil"
                                        class="px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50">
                                        ACC
                                    </button>
                                </form>

                            <?php else: ?>

                                <button
                                    disabled
                                    class="px-4 py-2 rounded-xl bg-gray-200 text-gray-500 font-semibold cursor-not-allowed">
                                    Diambil
                                </button>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>
        <div class="flex justify-center gap-2 mt-6">

            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                <a href="?page=<?= $i ?>"
                class="px-3 py-1 rounded-lg border <?= ($i == $page) ? 'bg-green-500 text-white' : 'bg-white' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

        </div>

    </div>

</section>
</div>  

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>