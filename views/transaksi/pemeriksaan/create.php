<?php
$data = $data ?? [];

$pageTitle = 'Input Pemeriksaan Baru';
$pageSubtitle = 'Form pencatatan hasil pemeriksaan antropometri balita.';

$anakList = $data['anak'] ?? [];

ob_start();
?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900">
                Input Pemeriksaan Baru
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Catat hasil timbangan, tinggi badan, dan status gizi anak secara akurat.
            </p>
        </div>

        <a href="/transaksi/pemeriksaan"
            class="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-700 transition hover:bg-gray-50">
            Kembali
        </a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="/transaksi/pemeriksaan/store" method="POST" class="space-y-6">
        <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="mb-5 text-lg font-extrabold text-gray-900 border-b border-gray-50 pb-3">
                Informasi Hasil Pemeriksaan
            </h3>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Dropdown Nama Anak -->
                <div class="md:col-span-2">
                    <label for="id_anak" class="mb-2 block text-sm font-bold text-gray-700">
                        Nama Anak <span class="text-red-500">*</span>
                    </label>
                    <select id="id_anak" name="id_anak" required
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:bg-white bg-white">
                        <option value="">-- Pilih Anak --</option>
                        <?php if (empty($anakList)): ?>
                            <option value="" disabled>Tidak ada data anak untuk posyandu Anda</option>
                        <?php else: ?>
                            <?php foreach ($anakList as $a): ?>
                                <option value="<?= htmlspecialchars($a['id_anak']); ?>">
                                    <?= htmlspecialchars($a['nama_anak']); ?> (Ibu: <?= htmlspecialchars($a['nama_ibu']); ?>) <?= isset($a['nama_posyandu']) ? ' - Posyandu: ' . htmlspecialchars($a['nama_posyandu']) : '' ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Tanggal Pemeriksaan -->
                <div>
                    <label for="tanggal_pemeriksaan" class="mb-2 block text-sm font-bold text-gray-700">
                        Tanggal Pemeriksaan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_pemeriksaan" name="tanggal_pemeriksaan" value="<?= date('Y-m-d'); ?>" required
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:bg-white">
                </div>

                <!-- Usia Anak (Otomatis) -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-gray-700">
                        Usia Anak (Otomatis)
                    </label>
                    <input type="text" id="usia_tampil" readonly
                        class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-600 outline-none select-none"
                        value="-">
                </div>

                <!-- Status Gizi (Otomatis) -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-gray-700">
                        Status Gizi & Skala Prioritas (Otomatis)
                    </label>
                    <div id="status_gizi_tampil" class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-500 font-bold select-none">
                        -
                    </div>
                </div>

                <!-- Berat Badan -->
                <div>
                    <label for="berat_badan" class="mb-2 block text-sm font-bold text-gray-700">
                        Berat Badan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-2xl border border-gray-200 bg-gray-50 focus-within:border-teal-500 focus-within:bg-white transition-all overflow-hidden flex items-center">
                        <input type="number" id="berat_badan" name="berat_badan" min="0" step="0.01" required placeholder="0.00"
                            class="w-full bg-transparent px-4 py-3 text-sm outline-none">
                        <span class="px-4 py-3 text-sm font-bold text-gray-400 bg-gray-100 border-l border-gray-200 select-none">
                            Kg
                        </span>
                    </div>
                </div>

                <!-- Tinggi Badan -->
                <div>
                    <label for="tinggi_badan" class="mb-2 block text-sm font-bold text-gray-700">
                        Tinggi Badan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-2xl border border-gray-200 bg-gray-50 focus-within:border-teal-500 focus-within:bg-white transition-all overflow-hidden flex items-center">
                        <input type="number" id="tinggi_badan" name="tinggi_badan" min="0" step="0.1" required placeholder="0.0"
                            class="w-full bg-transparent px-4 py-3 text-sm outline-none">
                        <span class="px-4 py-3 text-sm font-bold text-gray-400 bg-gray-100 border-l border-gray-200 select-none">
                            Cm
                        </span>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <label for="catatan" class="mb-2 block text-sm font-bold text-gray-700">
                        Catatan / Rekomendasi
                    </label>
                    <textarea id="catatan" name="catatan" rows="3" placeholder="Opsional..."
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:bg-white"></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="/transaksi/pemeriksaan"
                class="rounded-2xl border border-gray-200 bg-white px-6 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition">
                Batal
            </a>

            <button type="submit"
                class="rounded-2xl bg-teal-600 px-6 py-3 text-sm font-bold text-white hover:bg-teal-700 transition">
                Simpan Hasil Pemeriksaan
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const standarPertumbuhan = <?= $data['standar_json'] ?? '{}' ?>;
        const anakData = <?= $data['anak_data'] ?? '[]' ?>;

        const selectAnak = document.getElementById('id_anak');
        const inputTanggal = document.getElementById('tanggal_pemeriksaan');
        const inputBB = document.getElementById('berat_badan');
        const inputTB = document.getElementById('tinggi_badan');
        
        const usiaTampil = document.getElementById('usia_tampil');
        const statusGiziTampil = document.getElementById('status_gizi_tampil');

        function hitungStatusGizi() {
            const idAnak = selectAnak.value;
            const tanggalExam = inputTanggal.value;
            const bb = parseFloat(inputBB.value);
            const tb = parseFloat(inputTB.value);

            if (!idAnak || !tanggalExam) {
                usiaTampil.value = '-';
                statusGiziTampil.innerHTML = '-';
                statusGiziTampil.className = "w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-500 font-bold select-none";
                return;
            }

            const child = anakData.find(a => a.id_anak == idAnak);
            if (!child) {
                usiaTampil.value = '-';
                statusGiziTampil.innerHTML = '-';
                statusGiziTampil.className = "w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-500 font-bold select-none";
                return;
            }

            // Calculate age in months
            const birthDate = new Date(child.tgl_lahir);
            const examDate = new Date(tanggalExam);
            let months = (examDate.getFullYear() - birthDate.getFullYear()) * 12 + (examDate.getMonth() - birthDate.getMonth());
            if (examDate.getDate() < birthDate.getDate()) {
                months--;
            }
            months = Math.max(0, months);
            usiaTampil.value = months + ' Bulan';

            if (isNaN(bb) || isNaN(tb) || bb <= 0 || tb <= 0) {
                statusGiziTampil.innerHTML = 'Masukkan berat dan tinggi badan...';
                statusGiziTampil.className = "w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-400 font-bold select-none";
                return;
            }

            // Fetch standards
            const clampAge = Math.max(0, Math.min(60, months));
            const keyBBU = `${child.jenis_kelamin}_BB/U_${clampAge}`;
            const keyTBU = `${child.jenis_kelamin}_TB/U_${clampAge}`;
            const stdBBU = standarPertumbuhan[keyBBU];
            const stdTBU = standarPertumbuhan[keyTBU];

            let statusGizi = 'Prioritas 3';
            let priority = 3;

            if (stdBBU && stdTBU) {
                const bbBelowMinus3 = bb < stdBBU.sd_minus_3;
                const bbBelowMinus2 = bb < stdBBU.sd_minus_2;
                const tbBelowMinus3 = tb < stdTBU.sd_minus_3;
                const tbBelowMinus2 = tb < stdTBU.sd_minus_2;

                if (tbBelowMinus3 || bbBelowMinus3) {
                    statusGizi = 'Prioritas 1';
                    priority = 1;
                } else if (bbBelowMinus2 || tbBelowMinus2) {
                    statusGizi = 'Prioritas 2';
                    priority = 2;
                } else {
                    statusGizi = 'Prioritas 3';
                    priority = 3;
                }
            } else if (stdBBU) {
                if (bb < stdBBU.sd_minus_3) {
                    statusGizi = 'Prioritas 1';
                    priority = 1;
                } else if (bb < stdBBU.sd_minus_2) {
                    statusGizi = 'Prioritas 2';
                    priority = 2;
                } else {
                    statusGizi = 'Prioritas 3';
                    priority = 3;
                }
            } else if (stdTBU) {
                if (tb < stdTBU.sd_minus_3) {
                    statusGizi = 'Prioritas 1';
                    priority = 1;
                } else if (tb < stdTBU.sd_minus_2) {
                    statusGizi = 'Prioritas 2';
                    priority = 2;
                } else {
                    statusGizi = 'Prioritas 3';
                    priority = 3;
                }
            }

            // Update badge color
            let badgeClass = "bg-green-100 text-green-800 border-green-200";
            if (priority === 1) {
                badgeClass = "bg-red-100 text-red-800 border-red-200";
            } else if (priority === 2) {
                badgeClass = "bg-amber-100 text-amber-800 border-amber-200";
            }

            statusGiziTampil.className = `w-full rounded-2xl border px-4 py-3 text-sm font-bold select-none ${badgeClass}`;
            statusGiziTampil.innerHTML = `${statusGizi}`;
        }

        selectAnak.addEventListener('change', hitungStatusGizi);
        inputTanggal.addEventListener('change', hitungStatusGizi);
        inputBB.addEventListener('input', hitungStatusGizi);
        inputTB.addEventListener('input', hitungStatusGizi);
    });
</script>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>
