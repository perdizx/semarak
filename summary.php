<?php
require_once __DIR__ . '/includes/backend.php';
semarak_init_schema();
$anakList = semarak_get_anak();
include __DIR__ . '/includes/header.php';
?>

<div class="semarak-card p-6">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <h2 class="text-xl font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-user-check text-blue-500"></i> Summary & Analisis Individu
        </h2>
        <div class="flex flex-wrap gap-2">
            <select id="pilihAnak" class="border border-slate-300 rounded px-3 py-1.5 text-sm bg-white">
                <?php foreach ($anakList as $anak): ?>
                    <option value="<?= htmlspecialchars($anak['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($anak['nama'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($anak['status'], ENT_QUOTES, 'UTF-8') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <input id="mulai" type="date" aria-label="Tanggal mulai" class="border border-slate-300 rounded px-3 py-1.5 text-sm bg-white">
            <input id="sampai" type="date" aria-label="Tanggal selesai" class="border border-slate-300 rounded px-3 py-1.5 text-sm bg-white">
            <button id="refreshSummary" type="button" class="bg-slate-100 border border-slate-300 px-3 py-1.5 rounded hover:bg-slate-200" aria-label="Muat ulang">
                <i class="fa-solid fa-rotate"></i>
            </button>
        </div>
    </div>

    <div id="summaryMessage" class="hidden mb-4 rounded-lg p-3 text-sm" role="status" aria-live="polite"></div>
    <div id="anakProfile" class="border border-slate-200 rounded-lg p-6 flex flex-col md:flex-row items-start md:items-center gap-6 mb-6"></div>
    <div id="summaryCards" class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6 text-center"></div>

    <div class="border border-slate-200 rounded-lg overflow-hidden">
        <div class="bg-slate-50 px-4 py-3 border-b border-slate-200">
            <h4 id="detailHeading" class="text-sm font-bold text-slate-700">
                <i class="fa-solid fa-list-ul mr-2"></i> REKAPITULASI RINCI PER KEGIATAN
            </h4>
        </div>
        <div class="responsive-table-wrap overflow-x-auto">
            <table class="responsive-data-table w-full text-sm text-left">
                <thead class="text-xs text-slate-500 bg-white border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Kegiatan</th>
                        <th class="px-4 py-3 text-center">Sesuai</th>
                        <th class="px-4 py-3 text-center">Arahan</th>
                        <th class="px-4 py-3 text-center">Telat</th>
                        <th class="px-4 py-3 text-center">Tidak</th>
                        <th class="px-4 py-3 text-center">Libur</th>
                        <th class="px-4 py-3 text-center">Skor</th>
                    </tr>
                </thead>
                <tbody id="detailRows"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
(() => {
    const childSelect = document.getElementById('pilihAnak');
    const startInput = document.getElementById('mulai');
    const endInput = document.getElementById('sampai');
    const profile = document.getElementById('anakProfile');
    const cards = document.getElementById('summaryCards');
    const rows = document.getElementById('detailRows');
    const message = document.getElementById('summaryMessage');
    const labels = [
        ['sesuai', 'SESUAI', 'green'],
        ['arahan', 'ARAHAN', 'yellow'],
        ['telat', 'TERLAMBAT', 'orange'],
        ['tidak', 'TIDAK', 'red']
    ];
    const colorClasses = {
        green: { box: 'bg-green-50 border-green-200', title: 'text-green-700', value: 'text-green-600' },
        yellow: { box: 'bg-yellow-50 border-yellow-200', title: 'text-yellow-700', value: 'text-yellow-600' },
        orange: { box: 'bg-orange-50 border-orange-200', title: 'text-orange-700', value: 'text-orange-600' },
        red: { box: 'bg-red-50 border-red-200', title: 'text-red-700', value: 'text-red-600' }
    };
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);

    async function loadSummary() {
        if (!childSelect.value) {
            profile.innerHTML = '<p class="text-slate-500">Belum ada data anak untuk ditampilkan.</p>';
            cards.innerHTML = '';
            rows.innerHTML = '';
            return;
        }

        const query = new URLSearchParams({ action: 'summary', id: childSelect.value });
        if (startInput.value) query.set('mulai', startInput.value);
        if (endInput.value) query.set('sampai', endInput.value);

        try {
            const response = await fetch(`api.php?${query}`);
            const data = await response.json();
            if (!response.ok || !data.anak) throw new Error(data.message || 'Summary tidak dapat dimuat.');

            const anak = data.anak;
            const initials = anak.nama.split(/\s+/).slice(0, 2).map((part) => part.charAt(0)).join('').toUpperCase();
            profile.innerHTML = `
                <div class="w-16 h-16 shrink-0 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl font-bold">${escapeHtml(initials)}</div>
                <div>
                    <h3 class="font-bold text-lg">${escapeHtml(anak.nama)}</h3>
                    <p class="text-sm text-slate-500">${escapeHtml(anak.jenis_kelamin)} · ${escapeHtml(anak.status)}</p>
                </div>
                <div class="md:ml-auto md:text-right text-sm">
                    <p class="text-slate-500">Asal Daerah & Agama: ${escapeHtml(anak.asal_daerah)} · ${escapeHtml(anak.agama)}</p>
                    <p class="text-slate-500">ID, Umur & Angkatan: ${escapeHtml(anak.id)} · ${escapeHtml(anak.umur)} tahun · ${escapeHtml(anak.angkatan)}</p>
                </div>
            `;

            const total = labels.reduce((sum, [key]) => sum + Number(data.status[key] || 0), 0);
            cards.innerHTML = labels.map(([key, title, color]) => `
                <div class="${colorClasses[color].box} border rounded-lg py-4">
                    <p class="text-xs font-bold ${colorClasses[color].title} mb-1">${title}</p>
                    <p class="text-2xl font-bold ${colorClasses[color].value}">${Number(data.status[key] || 0)}</p>
                    <p class="text-xs ${colorClasses[color].value}">${total ? ((Number(data.status[key] || 0) / total) * 100).toFixed(1) : '0.0'}%</p>
                </div>
            `).join('') + `
                <div class="bg-blue-50 border border-blue-200 rounded-lg py-4">
                    <p class="text-xs font-bold text-blue-700 mb-1">SKOR TOTAL</p>
                    <p class="text-2xl font-bold text-blue-600">${Number(data.skor_total || 0).toFixed(1)}%</p>
                </div>
            `;
            rows.innerHTML = data.detail.map((row) => `
                <tr class="border-b border-slate-100">
                    <td data-label="No" class="px-4 py-3">${Number(row.no)}</td>
                    <td data-label="Kegiatan" class="px-4 py-3">${escapeHtml(row.kegiatan)}</td>
                    <td data-label="Sesuai" class="px-4 py-3 text-center">${Number(row.sesuai)}</td>
                    <td data-label="Arahan" class="px-4 py-3 text-center">${Number(row.arahan)}</td>
                    <td data-label="Telat" class="px-4 py-3 text-center">${Number(row.telat)}</td>
                    <td data-label="Tidak" class="px-4 py-3 text-center">${Number(row.tidak)}</td>
                    <td data-label="Libur" class="px-4 py-3 text-center">${Number(row.libur)}</td>
                    <td data-label="Skor" class="px-4 py-3 text-center font-semibold">${Number(row.skor).toFixed(1)}%</td>
                </tr>
            `).join('');
            message.className = 'hidden mb-4 rounded-lg p-3 text-sm';
        } catch (error) {
            message.textContent = error.message;
            message.className = 'mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 p-3 text-sm';
        }
    }

    childSelect.addEventListener('change', loadSummary);
    startInput.addEventListener('change', loadSummary);
    endInput.addEventListener('change', loadSummary);
    document.getElementById('refreshSummary').addEventListener('click', loadSummary);
    loadSummary();
})();
</script>

<?php include 'includes/footer.php'; ?>
