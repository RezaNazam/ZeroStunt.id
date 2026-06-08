// ============================================================
// ZeroStunt.id — Dynamic Form (for Pengadaan & Distribusi)
// Used for: add/remove item rows, auto-calculate totals
// ============================================================

let rowCount = 0;

function addRow(containerId, komoditasList) {
  rowCount++;
  const container = document.getElementById(containerId);
  const row = document.createElement('div');
  row.className = 'form-row flex gap-4 items-end mb-3 p-3 bg-gray-50 rounded border';
  row.id = 'row-' + rowCount;

  // Build komoditas options
  let options = '<option value="">-- Pilih Komoditas --</option>';
  komoditasList.forEach(function (k) {
    options += `<option value="${k.id_komoditas}">${k.nama_komoditas} (${k.singkat})</option>`;
  });

  row.innerHTML = `
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Komoditas</label>
            <select name="komoditas[]" onchange="updateSubtotal(${rowCount})"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500" required>
                ${options}
            </select>
        </div>
        <div class="w-28">
            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
            <input type="number" name="jumlah[]" id="jumlah-${rowCount}" step="0.01" min="0.01"
                oninput="updateSubtotal(${rowCount})"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500" required>
        </div>
        <div class="w-36">
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
            <input type="number" name="harga[]" id="harga-${rowCount}" step="0.01" min="0"
                oninput="updateSubtotal(${rowCount})"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
        </div>
        <div class="w-36">
            <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
            <input type="text" id="subtotal-${rowCount}" readonly
                class="w-full border border-gray-200 rounded px-3 py-2 bg-gray-100 text-gray-600">
        </div>
        <div class="flex-shrink-0">
            <button type="button" onclick="removeRow(${rowCount})"
                class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 mt-5">✕</button>
        </div>
    `;
  container.appendChild(row);
}

function removeRow(rowId) {
  const row = document.getElementById('row-' + rowId);
  if (row) {
    row.remove();
    updateTotal();
  }
}

function updateSubtotal(rowId) {
  const jumlah = parseFloat(document.getElementById('jumlah-' + rowId)?.value || 0);
  const harga = parseFloat(document.getElementById('harga-' + rowId)?.value || 0);
  const subtotal = jumlah * harga;
  const subtotalEl = document.getElementById('subtotal-' + rowId);
  if (subtotalEl) {
    subtotalEl.value = subtotal.toLocaleString('id-ID');
  }
  updateTotal();
}

function updateTotal() {
  let total = 0;
  document.querySelectorAll('[id^="jumlah-"]').forEach(function (jumlahEl) {
    const rowId = jumlahEl.id.split('-')[1];
    const jumlah = parseFloat(jumlahEl.value || 0);
    const hargaEl = document.getElementById('harga-' + rowId);
    const harga = parseFloat(hargaEl?.value || 0);
    total += jumlah * harga;
  });

  const totalEl = document.getElementById('total-bayar');
  if (totalEl) {
    totalEl.value = total.toLocaleString('id-ID');
  }
  const totalHiddenEl = document.getElementById('total-bayar-hidden');
  if (totalHiddenEl) {
    totalHiddenEl.value = total;
  }
}

// Auto-fetch prioritas when Ibu is selected (for Penyerahan form)
function onIbuSelected(ibuId) {
  if (!ibuId) return;
  fetch(`/api/ibu/${ibuId}/prioritas`)
    .then((res) => res.json())
    .then((data) => {
      const prioritasEl = document.getElementById('skala-prioritas-display');
      const paketEl = document.getElementById('paket-display');
      if (prioritasEl && data.prioritas) {
        prioritasEl.textContent = 'Prioritas ' + data.prioritas;
      }
      if (paketEl && data.paket) {
        paketEl.textContent = data.paket;
      }
    })
    .catch((err) => console.error('Error fetching prioritas:', err));
}
