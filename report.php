<?php
require_once __DIR__ . '/includes/backend.php';
semarak_init_schema();
$anakList = semarak_get_anak();
include __DIR__ . '/includes/header.php';
?>

<div class="semarak-card p-6">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6 print:hidden">
        <h2 class="text-xl font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-pdf text-blue-500"></i> Cetak Dokumen Raport
        </h2>
        <div class="flex flex-wrap gap-2">
            <select id="reportAnak" class="border border-slate-300 rounded px-3 py-1.5 text-sm bg-white">
                <?php foreach ($anakList as $anak): ?>
                    <option value="<?= htmlspecialchars($anak['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($anak['nama'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <select id="reportPeriode" class="border border-slate-300 rounded px-3 py-1.5 text-sm bg-white">
                <option value="harian">Raport Harian</option>
                <option value="mingguan">Raport Mingguan</option>
                <option value="bulanan">Raport Bulanan</option>
                <option value="custom">Rentang Tanggal</option>
            </select>
            <input id="reportMulai" type="date" aria-label="Tanggal mulai rapor" class="hidden border border-slate-300 rounded px-3 py-1.5 text-sm bg-white">
            <input id="reportSampai" type="date" aria-label="Tanggal akhir rapor" class="hidden border border-slate-300 rounded px-3 py-1.5 text-sm bg-white">
            <button type="button" id="btnSimpanCatatan" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-1.5 rounded text-sm font-medium">
                <i class="fa-solid fa-save mr-1"></i> Simpan Catatan
            </button>
            <button type="button" id="btnCetak" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak
            </button>
        </div>
    </div>

    <div id="reportMessage" class="hidden mb-4 rounded-lg p-3 text-sm print:hidden" role="status" aria-live="polite"></div>
    <article id="reportDocument" class="border border-slate-200 rounded-lg p-6 md:p-10 max-w-4xl mx-auto shadow-sm bg-white">
        <header class="text-center mb-8 border-b-2 border-black pb-4">
            <h3 class="font-bold text-lg">PEMERINTAH PROVINSI JAWA BARAT</h3>
            <h2 class="font-bold text-xl">DINAS SOSIAL</h2>
            <h4 class="font-bold">UPTD PUSAT PELAYANAN SOSIAL GRIYA BINA REMAJA</h4>
            <p class="text-xs">Jalan Maribaya No. 22 Lembang, Bandung Barat, Jawa Barat 40391</p>
            <p class="text-xs">Telepon. (022) 2786120</p>
            <p class="text-xs">Website: www.dinsos.ppsgbr.jabarprov.go.id Pos-el uptdppsgbr@gmail.com</p>
        </header>

        <div class="flex justify-between items-center bg-blue-50 border border-blue-200 p-3 rounded mb-6">
            <span class="font-bold text-sm tracking-wide">EVALUASI KARAKTER ANAK ASUH</span>
            <span class="bg-blue-600 text-white text-xs px-3 py-1 rounded font-bold">RAPORT PILAR KARAKTER</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6">
            <div id="reportIdentity"></div>
            <div id="reportPeriod"></div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-center mb-6" id="reportScores"></div>
        <div class="border border-slate-200 rounded-lg overflow-hidden mb-6">
            <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                <h4 class="text-sm font-bold text-slate-700">REKAPITULASI PER KEGIATAN</h4>
            </div>
            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-data-table w-full text-xs text-left">
                    <thead class="bg-white border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2">No</th><th class="px-3 py-2">Kegiatan</th>
                            <th class="px-3 py-2 text-center">Sesuai</th><th class="px-3 py-2 text-center">Arahan</th>
                            <th class="px-3 py-2 text-center">Telat</th><th class="px-3 py-2 text-center">Tidak</th>
                        </tr>
                    </thead>
                    <tbody id="reportRows"></tbody>
                </table>
            </div>
        </div>

        <div class="border border-slate-200 rounded-lg overflow-hidden">
            <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                <h4 class="text-sm font-bold text-slate-700"><i class="fa-solid fa-comments mr-2"></i> CATATAN & REKOMENDASI</h4>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="catatanPerilaku" class="text-sm font-medium mb-2 block">Catatan Perilaku:</label>
                    <textarea id="catatanPerilaku" class="border border-slate-200 rounded w-full min-h-32 p-3 text-sm print:border-0 print:p-0 print:resize-none"></textarea>
                </div>
                <div>
                    <label for="rekomendasi" class="text-sm font-medium mb-2 block">Rekomendasi:</label>
                    <textarea id="rekomendasi" class="border border-slate-200 rounded w-full min-h-32 p-3 text-sm print:border-0 print:p-0 print:resize-none"></textarea>
                </div>
            </div>
        </div>
    </article>
</div>

<script>
(() => {
    const child = document.getElementById('reportAnak');
    const period = document.getElementById('reportPeriode');
    const start = document.getElementById('reportMulai');
    const end = document.getElementById('reportSampai');
    const message = document.getElementById('reportMessage');
    let reportData = null;
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    function setPeriodDates() {
        const today = new Date();
        const todayValue = formatDate(today);
        end.value = todayValue;
        if (period.value === 'mingguan') {
            const startDate = new Date(today);
            startDate.setDate(today.getDate() - 6);
            start.value = formatDate(startDate);
        } else if (period.value === 'bulanan') {
            start.value = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
        } else if (period.value === 'harian') {
            start.value = todayValue;
        }
        const showDates = period.value === 'custom';
        start.classList.toggle('hidden', !showDates);
        end.classList.toggle('hidden', !showDates);
    }

    async function loadReport() {
        if (!child.value) return;
        if (period.value !== 'custom') setPeriodDates();
        if (!start.value || !end.value || start.value > end.value) {
            message.textContent = 'Rentang tanggal rapor tidak valid.';
            message.className = 'mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 p-3 text-sm print:hidden';
            return;
        }
        const query = new URLSearchParams({
            action: 'report', id: child.value, mulai: start.value, sampai: end.value
        });
        try {
            const response = await fetch(`api.php?${query}`);
            reportData = await response.json();
            if (!response.ok || !reportData.anak) throw new Error(reportData.message || 'Rapor tidak dapat dimuat.');
            const data = reportData;
            const anak = data.anak;
            document.getElementById('reportIdentity').innerHTML = `
                <p class="mb-1"><span class="font-medium inline-block w-40">ID & Nama Anak:</span> ${escapeHtml(anak.id)} · ${escapeHtml(anak.nama)}</p>
                <p class="mb-1"><span class="font-medium inline-block w-40">Umur & Agama:</span> ${escapeHtml(anak.umur)} tahun · ${escapeHtml(anak.agama)}</p>
                <p class="mb-1"><span class="font-medium inline-block w-40">Asal Daerah:</span> ${escapeHtml(anak.asal_daerah)}</p>
                <p class="mb-1"><span class="font-medium inline-block w-40">Jenis Kelamin:</span> ${escapeHtml(anak.jenis_kelamin)}</p>
                <p class="mb-1"><span class="font-medium inline-block w-40">Angkatan:</span> ${escapeHtml(anak.angkatan)}</p>
            `;
            document.getElementById('reportPeriod').innerHTML = `
                <p class="mb-1"><span class="font-medium inline-block w-32">Tanggal Catat:</span> ${escapeHtml(data.tanggal_catat)}</p>
                <p class="mb-1"><span class="font-medium inline-block w-32">Status:</span> ${escapeHtml(anak.status)}</p>
                <p class="mb-1"><span class="font-medium inline-block w-32">Periode:</span> ${escapeHtml(data.periode_mulai)} s.d. ${escapeHtml(data.periode_selesai)}</p>
                <p class="mb-1"><span class="font-medium inline-block w-32">Standar:</span> ${data.standar_evaluasi.map(escapeHtml).join(', ')}</p>
            `;
            const names = { sesuai: 'Sesuai', arahan: 'Arahan', telat: 'Telat', tidak: 'Tidak' };
            const colors = {
                sesuai: 'border-green-200 bg-green-50 text-green-700',
                arahan: 'border-yellow-200 bg-yellow-50 text-yellow-700',
                telat: 'border-orange-200 bg-orange-50 text-orange-700',
                tidak: 'border-red-200 bg-red-50 text-red-700'
            };
            const total = ['sesuai', 'arahan', 'telat', 'tidak'].reduce((sum, key) => sum + Number(data.ringkasan.status[key] || 0), 0);
            document.getElementById('reportScores').innerHTML = Object.keys(names).map((key) => `
                <div class="border rounded-lg py-3 ${colors[key]}">
                    <p class="text-xs font-bold">${names[key]}</p>
                    <p class="text-xl font-bold">${Number(data.ringkasan.status[key])}</p>
                    <p class="text-xs">${total ? ((Number(data.ringkasan.status[key]) / total) * 100).toFixed(1) : '0.0'}%</p>
                </div>
            `).join('') + `
                <div class="border border-blue-200 bg-blue-50 text-blue-700 rounded-lg py-3">
                    <p class="text-xs font-bold">SKOR</p>
                    <p class="text-xl font-bold">${Number(data.ringkasan.skor_total).toFixed(1)}%</p>
                    <p class="text-xs">Total</p>
                </div>
            `;
            document.getElementById('reportRows').innerHTML = data.ringkasan.detail.map((row) => `
                <tr class="border-b border-slate-100">
                    <td data-label="No" class="px-3 py-2">${Number(row.no)}</td><td data-label="Kegiatan" class="px-3 py-2">${escapeHtml(row.kegiatan)}</td>
                    <td data-label="Sesuai" class="px-3 py-2 text-center">${Number(row.sesuai)}</td><td data-label="Arahan" class="px-3 py-2 text-center">${Number(row.arahan)}</td>
                    <td data-label="Telat" class="px-3 py-2 text-center">${Number(row.telat)}</td><td data-label="Tidak" class="px-3 py-2 text-center">${Number(row.tidak)}</td>
                </tr>
            `).join('');
            document.getElementById('catatanPerilaku').value = data.catatan || '';
            document.getElementById('rekomendasi').value = data.rekomendasi || '';
            message.className = 'hidden mb-4 rounded-lg p-3 text-sm print:hidden';
        } catch (error) {
            message.textContent = error.message;
            message.className = 'mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 p-3 text-sm print:hidden';
        }
    }

    period.addEventListener('change', loadReport);
    child.addEventListener('change', loadReport);
    start.addEventListener('change', () => { if (period.value === 'custom') loadReport(); });
    end.addEventListener('change', () => { if (period.value === 'custom') loadReport(); });
    document.getElementById('btnCetak').addEventListener('click', () => window.print());
    document.getElementById('btnSimpanCatatan').addEventListener('click', async () => {
        if (!reportData?.anak) return;
        try {
            const response = await fetch('api.php?action=report_notes_save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    anak_id: child.value,
                    periode_mulai: start.value,
                    periode_selesai: end.value,
                    catatan: document.getElementById('catatanPerilaku').value,
                    rekomendasi: document.getElementById('rekomendasi').value
                })
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Catatan gagal disimpan.');
            message.textContent = result.message;
            message.className = 'mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 p-3 text-sm print:hidden';
        } catch (error) {
            message.textContent = error.message;
            message.className = 'mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 p-3 text-sm print:hidden';
        }
    });
    loadReport();
})();
</script>

<?php include 'includes/footer.php'; ?>
