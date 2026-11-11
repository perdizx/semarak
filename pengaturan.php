<?php
require_once __DIR__ . '/includes/backend.php';
semarak_init_schema();
$kegiatan = semarak_get_kegiatan_rows();
$anak = semarak_get_anak();
$angkatan = array_values(array_unique(array_column($anak, 'angkatan')));
include __DIR__ . '/includes/header.php';
?>

<div class="semarak-card p-6 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-orange-500"></i> Pengaturan Jadwal Kegiatan
            </h2>
            <p class="text-sm text-slate-500 mt-1">Atur hari atau rentang tanggal aktif untuk setiap kegiatan.</p>
        </div>
        <button id="simpanJadwal" type="button" class="bg-orange-500 hover:bg-orange-600 text-white font-medium px-6 py-2 rounded shadow-sm">
            <i class="fa-solid fa-save mr-2"></i>Simpan Jadwal
        </button>
    </div>

    <div id="pengaturanMessage" class="hidden mb-4 rounded-lg p-3 text-sm" role="status" aria-live="polite"></div>
    <div class="responsive-table-wrap border border-slate-200 rounded-lg">
        <table class="responsive-data-table w-full text-sm text-left">
            <thead class="text-xs text-slate-600 bg-slate-50 border-b border-slate-200">
                <tr><th class="px-4 py-3 w-12 text-center">No</th><th class="px-4 py-3">Uraian Kegiatan</th><th class="px-4 py-3 min-w-80">Aturan Jadwal Aktif</th></tr>
            </thead>
            <tbody>
                <?php foreach ($kegiatan as $index => $item): ?>
                    <tr class="border-b border-slate-100 hover:bg-slate-50/50" data-schedule="<?= (int) $item['id'] ?>">
                        <td data-label="No" class="px-4 py-3 text-center text-slate-500 align-top"><?= $index + 1 ?></td>
                        <td data-label="Uraian kegiatan" class="px-4 py-3 font-medium text-slate-700 align-top"><?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td data-label="Aturan jadwal aktif" class="px-4 py-3">
                            <select data-rule class="w-full border border-slate-300 rounded px-2 py-1.5 text-sm bg-white">
                                <option value="setiap_hari">Setiap Hari</option>
                                <option value="hari_kerja">Hari Kerja (Senin-Jumat)</option>
                                <option value="multi_rentang">Beberapa Rentang (Multi)</option>
                                <option value="hari_tertentu">Hari Tertentu</option>
                            </select>
                            <div data-days class="hidden mt-3 p-3 bg-white border border-slate-200 rounded">
                                <div class="flex flex-wrap gap-3 text-xs text-slate-600">
                                    <?php foreach (['1' => 'Senin', '2' => 'Selasa', '3' => 'Rabu', '4' => 'Kamis', '5' => 'Jumat', '6' => 'Sabtu', '7' => 'Minggu'] as $day => $name): ?>
                                        <label class="flex items-center gap-1">
                                            <input type="checkbox" data-day="<?= $day ?>" class="rounded"> <?= $name ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div data-ranges class="hidden mt-3 p-3 bg-white border border-slate-200 rounded">
                                <div data-range-list class="space-y-2"></div>
                                <button type="button" data-add-range class="mt-3 text-xs bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded w-full">+ Tambah Rentang</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="semarak-card p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-graduation-cap text-purple-500"></i> Pengaturan Masa Pembinaan
            </h2>
            <p class="text-sm text-slate-500 mt-1">Tanggal selesai tersimpan per angkatan.</p>
        </div>
        <button id="simpanPembinaan" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded shadow-sm">
            Simpan & Terapkan Aturan
        </button>
    </div>
    <div class="responsive-table-wrap border border-slate-200 rounded-lg">
        <table class="responsive-data-table w-full text-sm text-left">
            <thead class="text-xs text-slate-600 bg-slate-50 border-b border-slate-200">
                <tr><th class="px-4 py-3">Angkatan</th><th class="px-4 py-3">Total Anak</th><th class="px-4 py-3">Tanggal Selesai Pembinaan</th></tr>
            </thead>
            <tbody id="cohortRows">
                <?php foreach ($angkatan as $cohort): ?>
                    <tr class="border-b border-slate-100" data-cohort="<?= htmlspecialchars($cohort, ENT_QUOTES, 'UTF-8') ?>">
                        <td data-label="Angkatan" class="px-4 py-3 font-medium"><?= htmlspecialchars($cohort, ENT_QUOTES, 'UTF-8') ?></td>
                        <td data-label="Total anak" class="px-4 py-3"><?= count(array_filter($anak, static function ($item) use ($cohort) { return $item['angkatan'] === $cohort; })) ?> anak</td>
                        <td data-label="Tanggal selesai pembinaan" class="px-4 py-3"><input data-end-date type="date" class="border border-slate-300 rounded px-2 py-1.5 text-sm"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(() => {
    const message = document.getElementById('pengaturanMessage');
    const weekdays = ['1', '2', '3', '4', '5'];

    function showMessage(text, success) {
        message.textContent = text;
        message.className = `mb-4 rounded-lg border p-3 text-sm ${success
            ? 'border-green-200 bg-green-50 text-green-800'
            : 'border-red-200 bg-red-50 text-red-800'}`;
    }

    function addRange(container, range = {}) {
        const row = document.createElement('div');
        row.className = 'schedule-range-row flex items-center gap-2 text-xs';
        const from = document.createElement('input');
        from.type = 'date';
        from.dataset.from = '';
        from.setAttribute('aria-label', 'Tanggal mulai rentang aktif');
        from.value = range.mulai || '';
        from.className = 'border border-slate-300 rounded px-2 py-1.5 w-full';
        const to = document.createElement('input');
        to.type = 'date';
        to.dataset.to = '';
        to.setAttribute('aria-label', 'Tanggal selesai rentang aktif');
        to.value = range.selesai || '';
        to.className = 'border border-slate-300 rounded px-2 py-1.5 w-full';
        const remove = document.createElement('button');
        remove.type = 'button';
        remove.textContent = 'Hapus';
        remove.className = 'text-red-600 px-2';
        remove.addEventListener('click', () => row.remove());
        row.append(from, document.createTextNode(' s.d. '), to, remove);
        container.appendChild(row);
    }

    function showRuleOptions(row) {
        const rule = row.querySelector('[data-rule]').value;
        row.querySelector('[data-days]').classList.toggle('hidden', rule !== 'hari_tertentu');
        row.querySelector('[data-ranges]').classList.toggle('hidden', rule !== 'multi_rentang');
    }

    async function loadSettings() {
        try {
            const response = await fetch('api.php?action=settings');
            const settings = await response.json();
            if (!response.ok) throw new Error(settings.message || 'Pengaturan gagal dimuat.');
            document.querySelectorAll('[data-schedule]').forEach((row) => {
                const id = row.dataset.schedule;
                const setting = settings.jadwal?.[id] || { aturan: 'setiap_hari', hari: weekdays, rentang: [] };
                row.querySelector('[data-rule]').value = setting.aturan;
                row.querySelectorAll('[data-day]').forEach((input) => {
                    input.checked = (setting.hari || []).map(String).includes(input.dataset.day);
                });
                const list = row.querySelector('[data-range-list]');
                list.innerHTML = '';
                (setting.rentang || []).forEach((range) => addRange(list, range));
                if (!list.children.length) addRange(list);
                showRuleOptions(row);
            });
            document.querySelectorAll('[data-cohort]').forEach((row) => {
                const saved = settings.angkatan?.find((item) => item.angkatan === row.dataset.cohort);
                row.querySelector('[data-end-date]').value = saved?.tanggal_selesai || '';
            });
        } catch (error) {
            showMessage(error.message, false);
        }
    }

    document.querySelectorAll('[data-schedule]').forEach((row) => {
        row.querySelector('[data-rule]').addEventListener('change', () => showRuleOptions(row));
        row.querySelector('[data-add-range]').addEventListener('click', () => addRange(row.querySelector('[data-range-list]')));
    });

    document.getElementById('simpanJadwal').addEventListener('click', async () => {
        const schedules = {};
        document.querySelectorAll('[data-schedule]').forEach((row) => {
            schedules[row.dataset.schedule] = {
                aturan: row.querySelector('[data-rule]').value,
                hari: [...row.querySelectorAll('[data-day]:checked')].map((input) => input.dataset.day),
                rentang: [...row.querySelector('[data-range-list]').children].map((range) => ({
                    mulai: range.querySelector('[data-from]').value,
                    selesai: range.querySelector('[data-to]').value
                })).filter((range) => range.mulai && range.selesai)
            };
        });
        await saveSettings({ jadwal: schedules }, 'Pengaturan jadwal berhasil disimpan.');
    });

    document.getElementById('simpanPembinaan').addEventListener('click', async () => {
        const cohorts = {};
        document.querySelectorAll('[data-cohort]').forEach((row) => {
            cohorts[row.dataset.cohort] = row.querySelector('[data-end-date]').value;
        });
        await saveSettings({ angkatan: cohorts }, 'Masa pembinaan berhasil disimpan.');
    });

    async function saveSettings(payload, successMessage) {
        try {
            const response = await fetch('api.php?action=settings_save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Pengaturan gagal disimpan.');
            showMessage(successMessage, true);
        } catch (error) {
            showMessage(error.message, false);
        }
    }

    loadSettings();
})();
</script>

<?php include 'includes/footer.php'; ?>
