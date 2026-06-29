<?php
$pageTitle = 'Buat Lowongan Pengadaan Pangan';
$pageSubtitle = 'Publikasikan kebutuhan komoditas pangan baru untuk disanggupi oleh petani mitra.';

$daftarGudang = $data['daftar_gudang'] ?? [];
$daftarKomoditas = $data['daftar_komoditas'] ?? [];

ob_start();
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="/transaksi/pengadaan"
            class="inline-flex items-center gap-2 rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Riwayat
        </a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl bg-red-50 border border-red-100 p-4 text-sm text-red-600 flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>
                <?= $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </span>
        </div>
    <?php endif; ?>

    <form method="POST" action="/transaksi/pengadaan/simpan" class="space-y-6">

        <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-gray-900 border-b border-gray-50 pb-3">Informasi Utama (Header)</h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Gudang Tujuan
                        Pengiriman <span class="text-red-500">*</span></label>
                    <select name="id_gudang" required
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-teal-500 focus:bg-white focus:outline-none">
                        <option value="">-- Pilih Gudang Pusat --</option>
                        <?php foreach ($daftarGudang as $gudang): ?>
                            <option value="<?= $gudang['id_gudang'] ?>">
                                <?= htmlspecialchars($gudang['nama_gudang']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tanggal
                        Publikasi <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_pengadaan" required value="<?= date('Y-m-d') ?>"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-teal-500 focus:bg-white focus:outline-none">
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Keterangan
                        Tambahan (Opsional)</label>
                    <textarea name="keterangan" rows="3"
                        placeholder="Contoh: Kebutuhan mendesak untuk balita gizi buruk wilayah Cikarang Selatan..."
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-teal-500 focus:bg-white focus:outline-none"></textarea>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-50 pb-3">
                <h3 class="text-base font-bold text-gray-900">Rincian Komoditas Pangan (Detail)</h3>
                <button type="button" id="btn-tambah-baris"
                    class="inline-flex items-center gap-2 rounded-xl bg-teal-50 px-4 py-2 text-xs font-bold text-teal-700 transition hover:bg-teal-100">
                    <i class="fa-solid fa-plus"></i> Tambah Baris
                </button>
            </div>

            <div id="container-komoditas" class="space-y-3">

                <div
                    class="baris-komoditas grid grid-cols-1 gap-3 sm:grid-cols-12 items-end border border-gray-100 p-4 rounded-2xl bg-gray-50/50">
                    <div class="sm:col-span-5">
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Pilih Komoditas <span
                                class="text-red-500">*</span></label>
                        <select name="id_komoditas[]" required
                            class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-teal-500 focus:outline-none">
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($daftarKomoditas as $komoditas): ?>

                                <option value="<?= $komoditas['id_komoditas'] ?>">
                                    <?= htmlspecialchars($komoditas['nama_komoditas']) ?> (
                                    <?= htmlspecialchars($komoditas['nama_satuan'] ?? '-'); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Diminta <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="jumlah[]" step="0.01" required min="0.01" placeholder="Contoh: 100"
                            class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-teal-500 focus:outline-none">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Harga Satuan (Rp) <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="harga_satuan[]" required min="0" placeholder="Contoh: 15000"
                            class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-teal-500 focus:outline-none">
                    </div>
                    <div class="sm:col-span-1 text-right">
                        <button type="button"
                            class="btn-hapus-baris w-full sm:w-auto h-[42px] rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition flex items-center justify-center"
                            title="Hapus baris">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>

                </div>

            </div>

            <div class="pt-4 border-t border-gray-50 text-right">
                <button type="submit"
                    class="w-full sm:w-auto rounded-2xl bg-teal-600 px-8 py-3 text-sm font-bold text-white transition hover:bg-teal-700 shadow-md shadow-teal-100">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Publikasikan Pengadaan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wadahContainer = document.getElementById('container-komoditas');
        const tombolTambah = document.getElementById('btn-tambah-baris');

        // Mengkloning baris pertama sebagai master cetakan (blueprint) untuk baris baru
        const cetakanBarisBaru = wadahContainer.querySelector('.baris-komoditas').cloneNode(true);

        // Fungsi membersihkan value pada cetakan agar tidak menduplikat isi inputan sebelumnya
        cetakanBarisBaru.querySelectorAll('input').forEach(input => input.value = '');
        cetakanBarisBaru.querySelector('select').value = '';

        // Aksi klik tombol Tambah Baris
        tombolTambah.addEventListener('click', function () {
            const barisGandaan = cetakanBarisBaru.cloneNode(true);
            wadahContainer.appendChild(barisGandaan);
            perbaruiKondisiTombolHapus();
        });

        // Aksi klik tombol Hapus Baris menggunakan teknik Event Delegation
        wadahContainer.addEventListener('click', function (event) {
            if (event.target.closest('.btn-hapus-baris')) {
                const daftarSeluruhBaris = wadahContainer.querySelectorAll('.baris-komoditas');
                // Cegah penghapusan jika baris hanya tersisa satu di form
                if (daftarSeluruhBaris.length > 1) {
                    event.target.closest('.baris-komoditas').remove();
                    perbaruiKondisiTombolHapus();
                }
            }
        });

        // Fungsi menyembunyikan tombol hapus jika baris komoditas tinggal sisa 1
        function perbaruiKondisiTombolHapus() {
            const daftarSeluruhBaris = wadahContainer.querySelectorAll('.baris-komoditas');
            daftarSeluruhBaris.forEach(baris => {
                const tombolHapus = baris.querySelector('.btn-hapus-baris');
                if (daftarSeluruhBaris.length === 1) {
                    tombolHapus.style.display = 'none';
                } else {
                    tombolHapus.style.display = 'flex';
                }
            });
        }

        // Jalankan pemeriksaan kondisi tombol pertama kali saat halaman dimuat
        perbaruiKondisiTombolHapus();
    });
</script>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>