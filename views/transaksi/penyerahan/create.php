<?php
$ibus = $ibus ?? [];
$anaks = $anaks ?? [];
$gudangs = $gudangs ?? [];
$pakets = $pakets ?? [];

ob_start();
?>

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">
            Tambah Penyerahan Bantuan
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Input data penyerahan bantuan gizi ke ibu penerima.
        </p>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="/transaksi/penyerahan/store" method="POST"
        class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-5">

        <div class="grid gap-5 md:grid-cols-2">
            <!-- IBU -->
            <div>
                <label class="block text-sm font-bold mb-2">Ibu Penerima</label>
                <select name="id_ibu" id="id_ibu" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-3">
                    <option value="" disabled selected>Pilih Ibu</option>
                    <?php foreach ($ibus as $ibu): ?>
                        <option value="<?= htmlspecialchars($ibu['id_ibu']); ?>">
                            <?= htmlspecialchars($ibu['nama_ibu']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- ANAK -->
            <div>
                <label class="block text-sm font-bold mb-2">Penerima Bantuan (Anak / Ibu Hamil)</label>
                <select name="id_anak" id="id_anak" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-3">
                    <option value="" disabled selected>Pilih Ibu terlebih dahulu</option>
                </select>
            </div>

            <!-- GUDANG -->
            <?php $gudangAktif = $gudangs[0] ?? null; ?>

            <input type="hidden" name="id_gudang" value="<?= (int) ($idGudangDefault ?? 0); ?>">

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Posyandu / Gudang
                </label>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                    <div class="font-bold text-gray-900">
                        <?= htmlspecialchars($gudangAktif['nama_gudang'] ?? 'Gudang tidak ditemukan'); ?>
                    </div>

                </div>
                <div class="mt-1 text-xs text-gray-500">
                    Otomatis berdasarkan akun kader yang sedang login.
                </div>
            </div>

            <!-- TANGGAL -->
            <div>
                <label class="block text-sm font-bold mb-2">Tanggal Penyerahan</label>
                <input type="date" name="tanggal_penyerahan" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-3">
            </div>
        </div>


        <!-- DETAIL BANTUAN -->
        <div class="rounded-2xl border border-teal-100 bg-teal-50 p-4">
            <div class="mb-3">
                <h3 class="text-sm font-extrabold text-teal-900">
                    Paket Bantuan Otomatis
                </h3>
                <p class="text-sm text-teal-700">
                    Paket ditentukan otomatis berdasarkan skala prioritas anak.
                </p>
            </div>

            <div id="anakInfo" class="mb-3 rounded-xl bg-white px-4 py-3 text-sm text-gray-600">
                Pilih anak untuk melihat status gizi dan prioritas.
            </div>

            <div id="paketPreview" class="space-y-2 text-sm text-gray-600">
                Isi paket akan muncul setelah anak dipilih.
            </div>
        </div>

        <!-- CATATAN -->
        <div>
            <label class="block text-sm font-bold mb-2">Catatan</label>
            <textarea name="catatan" rows="3"
                class="w-full rounded-xl border border-gray-200 px-4 py-3"></textarea>
        </div>

        <button type="submit"
            class="bg-teal-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-teal-700">
            Simpan Penyerahan
        </button>

    </form>
</div>

<script>
    const ibuData = <?= json_encode($ibus, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    const anakData = <?= json_encode($anaks, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    const paketData = <?= json_encode($pakets, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    const ibuSelect = document.getElementById('id_ibu');
    const anakSelect = document.getElementById('id_anak');
    const anakInfo = document.getElementById('anakInfo');
    const paketPreview = document.getElementById('paketPreview');

    function getPaketByPrioritas(prioritas) {
        return paketData.find(function(paket) {
            return paket.kode_prioritas === 'PRIORITAS_' + prioritas;
        });
    }

    function renderPaketHTML(paket, prioritas) {
        if (!paket || !paket.details || paket.details.length === 0) {
            paketPreview.innerHTML = `
                <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-red-700">
                    Paket Prioritas ${prioritas} belum aktif atau belum memiliki detail komoditas.
                </div>
            `;
            return;
        }

        paketPreview.innerHTML = `
            <div class="mb-2 font-bold text-teal-900">
                ${paket.nama_paket}
            </div>
        ` + paket.details.map(function(detail) {
            return `
                <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-100 bg-white px-4 py-3">
                    <span class="font-bold text-gray-800">${detail.nama_komoditas}</span>
                    <span class="font-extrabold text-teal-700">
                        ${Number(detail.jumlah).toLocaleString('id-ID')} ${detail.satuan}
                    </span>
                </div>
            `;
        }).join('');
    }

    function renderAnakOptions() {
        const idIbu = ibuSelect.value;

        anakSelect.innerHTML = '<option value="" disabled selected>Pilih Penerima Bantuan</option>';
        anakInfo.innerHTML = 'Pilih penerima untuk melihat status gizi dan prioritas.';
        paketPreview.innerHTML = 'Isi paket akan muncul setelah penerima dipilih.';

        // 1. Cari data Ibu yang dipilih
        const ibu = ibuData.find(function(item) {
            return String(item.id_ibu) === String(idIbu);
        });

        // 2. Filter data Anak berdasarkan Ibu
        const filteredAnak = anakData.filter(function(anak) {
            return String(anak.id_ibu) === String(idIbu);
        });

        let hasOptions = false;

        // 3. Cek apakah Ibu sedang hamil
        if (ibu && (String(ibu.is_pregnant) === '1' || ibu.is_pregnant === 1)) {
            const option = document.createElement('option');
            option.value = 'ibu_hamil'; // Value penanda khusus untuk backend
            option.textContent = '[Bantuan Ibu Hamil] - Prioritas 1';
            anakSelect.appendChild(option);
            hasOptions = true;
        }

        // 4. Tambahkan list anak jika ada
        filteredAnak.forEach(function(anak) {
            const option = document.createElement('option');
            option.value = anak.id_anak;
            option.textContent = anak.nama_anak + ' - Prioritas ' + anak.skala_prioritas;
            anakSelect.appendChild(option);
            hasOptions = true;
        });

        // 5. Jika tidak hamil DAN tidak punya anak
        if (!hasOptions) {
            anakSelect.innerHTML = '<option value="" disabled selected>Ibu ini tidak hamil dan belum memiliki data anak</option>';
        }
    }

    function renderPaketOtomatis() {
        const selectedValue = anakSelect.value;

        // Jika yang dipilih adalah opsi Ibu Hamil
        if (selectedValue === 'ibu_hamil') {
            const prioritas = 1;
            const paket = getPaketByPrioritas(prioritas);

            anakInfo.innerHTML = `
                <div class="flex flex-col gap-1">
                    <p class="font-bold text-gray-900">Penerima: Ibu Hamil</p>
                    <p>Status: <span class="font-bold text-amber-600">Sedang Hamil</span></p>
                    <p>Skala prioritas: <span class="font-bold text-teal-700">Prioritas ${prioritas}</span></p>
                </div>
            `;

            renderPaketHTML(paket, prioritas);
            return;
        }

        // Jika yang dipilih adalah Anak (normal flow)
        const anak = anakData.find(function(item) {
            return String(item.id_anak) === String(selectedValue);
        });

        if (!anak) {
            anakInfo.innerHTML = 'Pilih penerima untuk melihat status gizi dan prioritas.';
            paketPreview.innerHTML = 'Isi paket akan muncul setelah penerima dipilih.';
            return;
        }

        const prioritas = anak.skala_prioritas;
        const paket = getPaketByPrioritas(prioritas);

        anakInfo.innerHTML = `
            <div class="flex flex-col gap-1">
                <p class="font-bold text-gray-900">${anak.nama_anak}</p>
                <p>Status gizi: <span class="font-bold">${anak.st_gizi_skrg || '-'}</span></p>
                <p>Skala prioritas: <span class="font-bold text-teal-700">Prioritas ${prioritas}</span></p>
            </div>
        `;

        renderPaketHTML(paket, prioritas);
    }

    ibuSelect.addEventListener('change', renderAnakOptions);
    anakSelect.addEventListener('change', renderPaketOtomatis);
</script>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>