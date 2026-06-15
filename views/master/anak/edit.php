<?php
$pageTitle = 'Dashboard Ibu';
$pageSubtitle = 'Ringkasan data anak, status gizi, dan riwayat bantuan.';

ob_start();
?>

        <!-- Main Content -->
        <main class="flex-1 p-6 flex flex-col items-center pt-2">
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4 w-full max-w-lg mx-auto"><?= $_SESSION['success'] ?></div>
                <script>
                    setTimeout(function() {
                        window.location.href = '/master/anak';
                    }, 2000); // Redirect to list page after 2 seconds
                </script>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 text-red-800 p-4 rounded mb-4 w-full max-w-lg mx-auto"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Page Content -->
            <?php
            /** @var array $anak */
            /** @var array $ibus */
            /** @var array|null $ibu_login */
            ?>
            <div class="w-full max-w-lg mx-auto">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Ubah Data Anak</h1>
                    <p class="text-sm text-gray-500 mb-6">Perbarui data tumbuh kembang balita Anda.</p>

                    <form action="/master/anak/update" method="POST" class="space-y-4">
                        <input type="hidden" name="id_anak" value="<?= $anak['id_anak']; ?>">

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
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500 text-sm cursor-not-allowed"
                                >
                                <input type="hidden" name="id_ibu" value="<?= $ibu_login['id_ibu']; ?>">
                            <?php else: ?>
                                <!--
                                    Jika Admin/Kader: tampilkan dropdown semua Ibu
                                -->
                                <select name="id_ibu" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm transition">
                                    <option value="" disabled>-- Pilih Ibu --</option>
                                    <?php foreach ($ibus as $ibu): ?>
                                        <option value="<?= $ibu['id_ibu']; ?>" <?= $ibu['id_ibu'] == $anak['id_ibu'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($ibu['nama_ibu']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <!-- NIK Anak -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">NIK Anak</label>
                            <input type="text" name="NIK_anak" value="<?= htmlspecialchars($anak['NIK_anak'] ?? ''); ?>" maxlength="16" required placeholder="Masukkan 16 digit NIK" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm transition">
                        </div>

                        <!-- Nama Anak -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap Anak</label>
                            <input type="text" name="nama_anak" value="<?= htmlspecialchars($anak['nama_anak']); ?>" required placeholder="Nama lengkap balita" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm transition">
                        </div>

                        <!-- Grid input Tanggal Lahir & Jenis Kelamin -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" value="<?= htmlspecialchars($anak['tgl_lahir']); ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                                <select name="jenis_kelamin" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm transition">
                                    <option value="L" <?= $anak['jenis_kelamin'] === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?= $anak['jenis_kelamin'] === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Simpan Button -->
                        <button type="submit" class="w-full rounded-2xl bg-teal-600 hover:bg-teal-700 px-5 py-3 font-bold text-white transition duration-200 mt-4">
                            Perbarui Data Anak
                        </button>
                        
                        <a href="/master/anak" class="block text-center text-sm font-semibold text-gray-500 hover:text-teal-700 mt-2">
                            Batal
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
