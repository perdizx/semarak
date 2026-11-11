<?php
require_once __DIR__ . '/includes/backend.php';
semarak_init_schema();
$anakList = semarak_get_anak();
$kegiatanList = semarak_get_kegiatan_rows();
$tanggalHariIni = date('Y-m-d');
include __DIR__ . '/includes/header.php';
?>

<div class="semarak-card p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h2 class="text-xl font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-list-check text-blue-500"></i> Input Evaluasi Karakter
        </h2>
        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
            Tanggal penilaian
            <input id="tanggalEvaluasi" type="date" value="<?= htmlspecialchars($tanggalHariIni, ENT_QUOTES, 'UTF-8') ?>"
                class="border border-slate-300 rounded px-3 py-1.5 bg-white outline-none focus:border-blue-500">
        </label>
    </div>

    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
        <p class="text-sm text-blue-800">
            Pilih satu nilai untuk setiap anak pada masing-masing kegiatan, lalu tekan
            <strong>Simpan Penilaian</strong>. Nilai yang sudah tersimpan akan dimuat kembali saat tanggal dipilih.
        </p>
    </div>

    <?php if ($anakList && $kegiatanList): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex flex-col md:flex-row justify-between gap-3 md:items-center mb-3">
                <p class="text-sm font-medium text-yellow-800"><i class="fa-solid fa-briefcase-medical mr-2"></i>Tandai anak sakit / izin untuk seluruh kegiatan pada tanggal ini</p>
                <button type="button" id="simpanLibur" class="bg-yellow-600 hover:bg-yellow-700 text-white rounded px-4 py-2 text-sm font-medium">
                    Simpan Sakit / Izin
                </button>
            </div>
            <div class="flex flex-wrap gap-3">
                <?php foreach ($anakList as $anak): ?>
                    <label class="flex items-center gap-2 text-sm bg-white px-3 py-1.5 border border-yellow-200 rounded cursor-pointer">
                        <input type="checkbox" data-leave-child="<?= htmlspecialchars($anak['id'], ENT_QUOTES, 'UTF-8') ?>" class="rounded text-yellow-600">
                        <?= htmlspecialchars($anak['nama'], ENT_QUOTES, 'UTF-8') ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$anakList): ?>
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
            Belum ada data anak. Tambahkan data terlebih dahulu melalui menu Data Anak.
        </div>
    <?php elseif (!$kegiatanList): ?>
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
            Belum ada kegiatan yang dapat dinilai.
        </div>
    <?php else: ?>
        <div id="pesanEvaluasi" class="hidden mb-4 rounded-lg p-3 text-sm" role="status" aria-live="polite"></div>
        <div class="space-y-3">
            <?php foreach ($kegiatanList as $index => $kegiatan): ?>
                <section class="border border-slate-200 rounded-lg overflow-hidden bg-white" data-activity="<?= (int) $kegiatan['id'] ?>">
                    <button type="button" onclick="toggleAccordion(<?= $index ?>)"
                        class="w-full text-left bg-slate-100 hover:bg-slate-200 px-4 py-3 border-b border-slate-200 flex justify-between items-center transition">
                        <span class="font-bold text-slate-700">
                            <span class="bg-blue-600 text-white rounded-full w-6 h-6 inline-flex items-center justify-center text-xs mr-2"><?= $index + 1 ?></span>
                            <?= htmlspecialchars($kegiatan['nama'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <span class="flex items-center gap-4">
                            <span id="status-<?= (int) $kegiatan['id'] ?>" class="text-xs bg-slate-200 text-slate-600 px-2 py-0.5 rounded">Belum disimpan</span>
                            <i id="icon-<?= $index ?>" class="fa-solid fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                        </span>
                    </button>

                    <div id="content-<?= $index ?>" class="hidden">
                        <div class="px-4 py-2 bg-slate-50 flex flex-wrap items-center gap-2 border-b border-slate-200 text-sm">
                            <span class="font-medium text-slate-600 mr-2">Set semua anak:</span>
                            <button type="button" data-bulk="sesuai" class="text-green-700 bg-green-100 hover:bg-green-200 border border-green-200 px-3 py-1.5 rounded">Sesuai</button>
                            <button type="button" data-bulk="arahan" class="text-yellow-700 bg-yellow-100 hover:bg-yellow-200 border border-yellow-200 px-3 py-1.5 rounded">Arahan</button>
                            <button type="button" data-bulk="telat" class="text-orange-700 bg-orange-100 hover:bg-orange-200 border border-orange-200 px-3 py-1.5 rounded">Telat</button>
                            <button type="button" data-bulk="tidak" class="text-red-700 bg-red-100 hover:bg-red-200 border border-red-200 px-3 py-1.5 rounded">Tidak</button>
                        </div>

                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-slate-50/50">
                            <?php foreach ($anakList as $anak): ?>
                                <div class="border border-slate-200 bg-white rounded-md p-3 shadow-sm"
                                    data-child="<?= htmlspecialchars($anak['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <p class="font-medium text-sm mb-3 truncate text-slate-700">
                                        <?= htmlspecialchars($anak['nama'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <span data-value-label class="hidden text-xs text-slate-500 block mb-2"></span>
                                    <div class="grid grid-cols-2 gap-2 text-xs font-medium">
                                        <button type="button" data-value="sesuai" class="eval-button py-1.5 rounded border border-slate-200 text-slate-600 hover:bg-green-100">Sesuai</button>
                                        <button type="button" data-value="arahan" class="eval-button py-1.5 rounded border border-slate-200 text-slate-600 hover:bg-yellow-100">Arahan</button>
                                        <button type="button" data-value="telat" class="eval-button py-1.5 rounded border border-slate-200 text-slate-600 hover:bg-orange-100">Telat</button>
                                        <button type="button" data-value="tidak" class="eval-button py-1.5 rounded border border-slate-200 text-slate-600 hover:bg-red-100">Tidak</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="bg-slate-50 px-4 py-3 text-right border-t border-slate-200">
                            <button type="button" data-save class="text-sm bg-teal-600 hover:bg-teal-700 text-white font-medium rounded px-6 py-2 transition shadow-sm">
                                <i class="fa-solid fa-check mr-1"></i> Simpan Penilaian
                            </button>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
(() => {
    const dateInput = document.getElementById('tanggalEvaluasi');
    const message = document.getElementById('pesanEvaluasi');
    const labels = { sesuai: 'Sesuai', arahan: 'Arahan', telat: 'Telat', tidak: 'Tidak' };
    const colors = {
        sesuai: ['bg-green-600', 'text-white', 'border-green-600'],
        arahan: ['bg-yellow-500', 'text-white', 'border-yellow-500'],
        telat: ['bg-orange-500', 'text-white', 'border-orange-500'],
        tidak: ['bg-red-600', 'text-white', 'border-red-600'],
        libur: ['bg-slate-500', 'text-white', 'border-slate-500']
    };

    function showMessage(text, success) {
        if (!message) return;
        message.textContent = text;
        message.className = `mb-4 rounded-lg border p-3 text-sm ${success
            ? 'border-green-200 bg-green-50 text-green-800'
            : 'border-red-200 bg-red-50 text-red-800'}`;
    }

    function setValue(card, value) {
        card.dataset.value = value;
        const label = card.querySelector('[data-value-label]');
        if (label) {
            label.textContent = value === 'libur' ? 'Sakit / Izin' : '';
            label.classList.toggle('hidden', value !== 'libur');
        }
        card.querySelectorAll('.eval-button').forEach((button) => {
            button.classList.remove(...Object.values(colors).flat());
            if (button.dataset.value === value) {
                button.classList.add(...colors[value]);
            }
        });
        if (value === 'libur') {
            card.querySelectorAll('.eval-button').forEach((button) => {
                button.classList.remove('bg-green-600', 'bg-yellow-500', 'bg-orange-500', 'bg-red-600', 'bg-slate-500', 'text-white', 'border-green-600', 'border-yellow-500', 'border-orange-500', 'border-red-600', 'border-slate-500');
            });
            card.dataset.value = 'libur';
            card.classList.add('bg-slate-100');
        } else {
            card.classList.remove('bg-slate-100');
        }
    }

    function isActivityScheduled(activityId, dateValue, schedules) {
        const schedule = schedules[activityId] || { aturan: 'setiap_hari', hari: [], rentang: [] };
        const date = new Date(`${dateValue}T12:00:00`);
        const weekday = String(date.getDay() === 0 ? 7 : date.getDay());
        if (schedule.aturan === 'hari_kerja') return ['1', '2', '3', '4', '5'].includes(weekday);
        if (schedule.aturan === 'hari_tertentu') return (schedule.hari || []).map(String).includes(weekday);
        if (schedule.aturan === 'multi_rentang') {
            return (schedule.rentang || []).some((range) => range.mulai && range.selesai && dateValue >= range.mulai && dateValue <= range.selesai);
        }
        return true;
    }

    async function loadEvaluations() {
        try {
            const [evalResponse, settingsResponse] = await Promise.all([
                fetch(`api.php?action=evaluasi&tanggal=${encodeURIComponent(dateInput.value)}`),
                fetch('api.php?action=settings')
            ]);
            const rows = await evalResponse.json();
            const settings = await settingsResponse.json();
            if (!evalResponse.ok || !Array.isArray(rows)) throw new Error(rows.message || 'Gagal memuat penilaian.');
            if (!settingsResponse.ok) throw new Error(settings.message || 'Gagal memuat jadwal.');

            document.querySelectorAll('[data-activity]').forEach((activity) => {
                activity.querySelectorAll('[data-child]').forEach((card) => {
                    card.dataset.value = '';
                    setValue(card, '');
                });
                const scheduled = isActivityScheduled(activity.dataset.activity, dateInput.value, settings.jadwal || {});
                activity.dataset.scheduled = scheduled ? '1' : '0';
                activity.querySelectorAll('.eval-button, [data-bulk], [data-save]').forEach((button) => { button.disabled = !scheduled; });
                document.getElementById(`status-${activity.dataset.activity}`).textContent = scheduled ? 'Belum disimpan' : 'Tidak dijadwalkan';
            });

            rows.forEach((row) => {
                const activity = document.querySelector(`[data-activity="${row.kegiatan_id}"]`);
                const card = activity?.querySelector(`[data-child="${CSS.escape(row.anak_id)}"]`);
                if (card) setValue(card, row.nilai);
            });

            document.querySelectorAll('[data-activity]').forEach((activity) => {
                const cards = [...activity.querySelectorAll('[data-child]')];
                const allSaved = cards.length > 0 && cards.every((card) => card.dataset.value);
                document.getElementById(`status-${activity.dataset.activity}`).textContent = allSaved
                    ? 'Tersimpan'
                    : activity.dataset.scheduled === '1' ? 'Belum disimpan' : 'Tidak dijadwalkan';
            });

            if (message) message.className = 'hidden mb-4 rounded-lg p-3 text-sm';
        } catch (error) {
            showMessage(error.message, false);
        }
    }

    document.querySelectorAll('[data-activity]').forEach((activity) => {
        activity.addEventListener('click', async (event) => {
            const bulkButton = event.target.closest('[data-bulk]');
            if (bulkButton) {
                activity.querySelectorAll('[data-child]').forEach((card) => setValue(card, bulkButton.dataset.bulk));
                document.getElementById(`status-${activity.dataset.activity}`).textContent = 'Belum disimpan';
                return;
            }

            const evalButton = event.target.closest('.eval-button');
            if (evalButton) {
                const card = evalButton.closest('[data-child]');
                setValue(card, evalButton.dataset.value);
                document.getElementById(`status-${activity.dataset.activity}`).textContent = 'Belum disimpan';
                return;
            }

            const saveButton = event.target.closest('[data-save]');
            if (!saveButton) return;

            const items = [...activity.querySelectorAll('[data-child]')]
                .filter((card) => card.dataset.value)
                .map((card) => ({
                    anak_id: card.dataset.child,
                    kegiatan_id: Number(activity.dataset.activity),
                    tanggal: dateInput.value,
                    nilai: card.dataset.value
                }));

            if (!items.length) {
                showMessage('Pilih nilai untuk setidaknya satu anak sebelum menyimpan.', false);
                return;
            }

            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...';
            try {
                const response = await fetch('api.php?action=save_evaluasi_batch', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ items })
                });
                const result = await response.json();
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Gagal menyimpan penilaian.');
                }

                document.getElementById(`status-${activity.dataset.activity}`).textContent = 'Tersimpan';
                showMessage(result.message, true);
            } catch (error) {
                showMessage(error.message, false);
            } finally {
                saveButton.disabled = false;
                saveButton.innerHTML = '<i class="fa-solid fa-check mr-1"></i> Simpan Penilaian';
            }
        });
    });

    document.getElementById('simpanLibur')?.addEventListener('click', async () => {
        const selected = [...document.querySelectorAll('[data-leave-child]:checked')];
        if (!selected.length) {
            showMessage('Pilih anak yang sakit atau izin terlebih dahulu.', false);
            return;
        }
        const items = [];
        selected.forEach((checkbox) => {
            document.querySelectorAll('[data-activity]').forEach((activity) => {
                items.push({
                    anak_id: checkbox.dataset.leaveChild,
                    kegiatan_id: Number(activity.dataset.activity),
                    tanggal: dateInput.value,
                    nilai: 'libur'
                });
            });
        });
        const button = document.getElementById('simpanLibur');
        button.disabled = true;
        try {
            const response = await fetch('api.php?action=save_evaluasi_batch', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ items })
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Status sakit/izin gagal disimpan.');
            await loadEvaluations();
            showMessage(`Sakit/izin disimpan untuk ${selected.length} anak pada seluruh kegiatan tanggal ini.`, true);
        } catch (error) {
            showMessage(error.message, false);
        } finally {
            button.disabled = false;
        }
    });

    dateInput?.addEventListener('change', () => {
        document.querySelectorAll('[data-leave-child]').forEach((checkbox) => { checkbox.checked = false; });
        loadEvaluations();
    });
    if (dateInput) loadEvaluations();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
