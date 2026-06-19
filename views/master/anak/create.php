<?php
$pageTitle = 'Dashboard Ibu';
$pageSubtitle = 'Ringkasan data anak, status gizi, dan riwayat bantuan.';

ob_start();
?>

<!-- Main Content -->
<main class="flex-1 p-6 flex flex-col items-center pt-2">

    <!-- Page Content -->
    <?php
    /** @var array $ibus */
    ?>
    <div class="w-full max-w-lg mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Registrasi Anak</h1>
            <p class="text-sm text-gray-500 mb-6">Tambahkan data balita untuk mulai memantau tumbuh kembang.</p>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-5 rounded-2xl border border-green-100 bg-green-50 text-green-700 px-4 py-3 text-sm font-medium"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 text-red-700 px-4 py-3 text-sm font-medium"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="/master/anak/store" method="POST" class="space-y-4">
                <!-- Ibu Kandung / Wali -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ibu Kandung / Wali</label>
                    <?php if (isset($ibu_login) && $ibu_login): ?>
                        <!--
                                    Jika Ibu yang login: tampilkan nama ibu sendiri (readonly)
                                    id_ibu dikirim via hidden input, tidak bisa dimanipulasi
                                -->
                        <input
                            type="text"
                            value="<?= htmlspecialchars($ibu_login['nama_ibu']); ?>"
                            readonly
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500 text-sm cursor-not-allowed">
                        <input type="hidden" name="id_ibu" value="<?= $ibu_login['id_ibu']; ?>">
                    <?php else: ?>
                        <!--
                                    Jika Admin/Kader: tampilkan dropdown semua Ibu
                                -->
                        <select name="id_ibu" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm transition">
                            <option value="" disabled selected>-- Pilih Ibu --</option>
                            <?php foreach ($ibus as $ibu): ?>
                                <option value="<?= $ibu['id_ibu']; ?>"><?= htmlspecialchars($ibu['nama_ibu']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- NIK Anak -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">NIK Anak</label>
                    <input type="text" name="NIK_anak" inputmode="numeric" pattern="[0-9]{16}" minlength="16" maxlength="16" required placeholder="Masukkan 16 digit NIK" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm transition">
                </div>

                <!-- Nama Anak -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap Anak</label>
                    <input type="text" name="nama_anak" required placeholder="Nama lengkap balita" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm transition">
                </div>

                <!-- Grid input Tanggal Lahir & Jenis Kelamin -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm transition">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Simpan Button -->
                <button type="submit" class="w-full rounded-2xl bg-teal-600 hover:bg-teal-700 px-5 py-3 font-bold text-white transition duration-200 mt-4">
                    Simpan Data Anak
                </button>

                <a href="/master/anak" class="block text-center text-sm font-semibold text-gray-500 hover:text-teal-700 mt-2">
                    Kembali
                </a>
            </form>
        </div>
    </div>
</main>
</div>

<script src="/js/app.js"></script>
</body>

</html>
<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
