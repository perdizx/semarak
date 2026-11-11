<?php
require_once __DIR__ . '/includes/backend.php';
semarak_init_schema();
$children = semarak_get_anak();
$cohorts = array_values(array_unique(array_column($children, 'angkatan')));
include __DIR__ . '/includes/header.php';
?>

<section class="dashboard-hero p-6 md:p-8 mb-6">
    <div class="dashboard-hero-content relative z-10">
        <div class="max-w-2xl">
            <h2 class="text-1xl md:text-2xl font-bold leading-tight">Pantau perkembangan anak</h2>
            <p class="text-sm md:text-base text-white/85 mt-2">Catat perkembangan karakter anak setiap hari.</p>
        </div>
        <div class="dashboard-datetime" aria-label="Tanggal dan waktu saat ini">
            <div class="dashboard-current-date">
                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                <time id="currentDate" datetime="<?= date('Y-m-d') ?>"><?= date('d/m/Y') ?></time>
            </div>
            <time id="currentTime" class="dashboard-current-time" aria-label="Waktu saat ini">--:--:--</time>
        </div>
    </div>
</section>

<section class="mb-7" aria-labelledby="portalHeading">
    <div class="flex items-end justify-between gap-3 mb-3">
        <div>
            <h3 id="portalHeading" class="font-bold text-slate-800">Menu utama</h3>
            <p class="text-xs text-slate-500 mt-1">Pilih layanan yang ingin Anda buka.</p>
        </div>
    </div>
    <div class="portal-list">
        <a href="anak.php" class="portal-card">
            <span class="portal-card-icon portal-icon-blue"><i class="fa-solid fa-user-group"></i></span>
            <span class="portal-card-copy">
                <span class="portal-card-title">Data Anak</span>
            </span>
            <i class="fa-solid fa-arrow-right portal-card-arrow"></i>
        </a>
        <a href="summary.php" class="portal-card">
            <span class="portal-card-icon portal-icon-green"><i class="fa-solid fa-chart-column"></i></span>
            <span class="portal-card-copy">
                <span class="portal-card-title">Summary Individu</span>
            </span>
            <i class="fa-solid fa-arrow-right portal-card-arrow"></i>
        </a>
        <a href="report.php" class="portal-card">
            <span class="portal-card-icon portal-icon-orange"><i class="fa-solid fa-file-lines"></i></span>
            <span class="portal-card-copy">
                <span class="portal-card-title">Rapor</span>
            </span>
            <i class="fa-solid fa-arrow-right portal-card-arrow"></i>
        </a>
        <a href="pengaturan.php" class="portal-card">
            <span class="portal-card-icon portal-icon-slate"><i class="fa-solid fa-gear"></i></span>
            <span class="portal-card-copy">
                <span class="portal-card-title">Pengaturan</span>
            </span>
            <i class="fa-solid fa-arrow-right portal-card-arrow"></i>
        </a>
    </div>
</section>

<div id="dashboardMessage" class="hidden mb-4 rounded-xl p-3 text-sm" role="status" aria-live="polite"></div>

<section class="mb-6">
    <div class="flex items-end justify-between gap-3 mb-3">
        <div>
            <h3 class="font-bold text-slate-800">Hasil evaluasi</h3>
            <p class="text-xs text-slate-500 mt-1">Persentase dari penilaian yang masuk sesuai filter.</p>
        </div>
        <span class="hidden sm:inline-flex items-center gap-2 text-xs text-slate-500">
            <span class="w-2 h-2 rounded-full bg-indigo-500"></span> Data diperbarui otomatis
        </span>
    </div>
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 md:gap-4">
        <article class="dashboard-stat p-4 md:p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs md:text-sm font-semibold text-slate-600">Sesuai</p>
                <span class="stat-icon bg-green-50 text-green-600"><i class="fa-solid fa-check"></i></span>
            </div>
            <p id="statSesuai" class="text-2xl md:text-3xl font-bold text-slate-800">0.0%</p>
            <p class="text-xs text-slate-400 mt-1">Konsisten mengikuti kegiatan</p>
        </article>
        <article class="dashboard-stat p-4 md:p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs md:text-sm font-semibold text-slate-600">Dengan arahan</p>
                <span class="stat-icon bg-amber-50 text-amber-600"><i class="fa-solid fa-hand-point-up"></i></span>
            </div>
            <p id="statArahan" class="text-2xl md:text-3xl font-bold text-slate-800">0.0%</p>
            <p class="text-xs text-slate-400 mt-1">Memerlukan pengingat</p>
        </article>
        <article class="dashboard-stat p-4 md:p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs md:text-sm font-semibold text-slate-600">Terlambat</p>
                <span class="stat-icon bg-orange-50 text-orange-600"><i class="fa-solid fa-clock"></i></span>
            </div>
            <p id="statTelat" class="text-2xl md:text-3xl font-bold text-slate-800">0.0%</p>
            <p class="text-xs text-slate-400 mt-1">Perlu pendampingan waktu</p>
        </article>
        <article class="dashboard-stat p-4 md:p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs md:text-sm font-semibold text-slate-600">Tidak dilakukan</p>
                <span class="stat-icon bg-rose-50 text-rose-600"><i class="fa-solid fa-triangle-exclamation"></i></span>
            </div>
            <p id="statTidak" class="text-2xl md:text-3xl font-bold text-slate-800">0.0%</p>
            <p class="text-xs text-slate-400 mt-1">Perlu tindak lanjut</p>
        </article>
    </div>
</section>

<section class="grid grid-cols-1 xl:grid-cols-[0.8fr_1.7fr] gap-4">
    <div class="grid grid-cols-3 xl:grid-cols-1 gap-3">
        <article class="dashboard-panel p-4">
            <p class="text-xs text-slate-500">Anak sesuai filter</p>
            <p id="totalAnak" class="text-2xl font-bold text-slate-800 mt-1">0</p>
        </article>
        <article class="dashboard-panel p-4">
            <p class="text-xs text-slate-500">Kegiatan terpantau</p>
            <p id="totalKegiatan" class="text-2xl font-bold text-slate-800 mt-1">0</p>
        </article>
        <article class="dashboard-panel p-4">
            <p class="text-xs text-slate-500">Penilaian tersimpan</p>
            <p id="totalPenilaian" class="text-2xl font-bold text-slate-800 mt-1">0</p>
        </article>
    </div>
    <div class="dashboard-panel p-4 md:p-5">
        <div class="mb-4">
            <h3 class="font-bold text-slate-800"><i class="fa-solid fa-chart-area mr-2 text-indigo-500"></i>Tren penilaian harian</h3>
            <p class="text-xs text-slate-500 mt-1">Jumlah evaluasi dan proporsi nilai sesuai per hari.</p>
        </div>
        <div id="chartEmpty" class="text-center text-slate-400 py-10">Belum ada data untuk filter ini.</div>
        <div id="trendChart" class="space-y-3"></div>
    </div>
</section>

<script>
(() => {
    const inputs = ['filterAngkatan', 'filterStatus', 'filterMulai', 'filterSampai'].map((id) => document.getElementById(id));
    const message = document.getElementById('dashboardMessage');
    const filterToggle = document.getElementById('dashboardFilterToggle');
    const filterPanel = document.getElementById('dashboardFilterPanel');
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);

    async function loadDashboard() {
        const query = new URLSearchParams({ action: 'dashboard' });
        const [cohort, status, from, to] = inputs.map((input) => input.value);
        if (cohort) query.set('angkatan', cohort);
        if (status) query.set('status_anak', status);
        if (from) query.set('mulai', from);
        if (to) query.set('sampai', to);
        try {
            const response = await fetch(`api.php?${query}`);
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Dashboard tidak dapat dimuat.');
            document.getElementById('statSesuai').textContent = `${Number(data.presentase.sesuai).toFixed(1)}%`;
            document.getElementById('statArahan').textContent = `${Number(data.presentase.arahan).toFixed(1)}%`;
            document.getElementById('statTelat').textContent = `${Number(data.presentase.telat).toFixed(1)}%`;
            document.getElementById('statTidak').textContent = `${Number(data.presentase.tidak).toFixed(1)}%`;
            document.getElementById('totalAnak').textContent = data.total_anak;
            document.getElementById('totalKegiatan').textContent = data.total_kegiatan;
            document.getElementById('totalPenilaian').textContent = data.total_penilaian;

            const trend = data.tren || [];
            document.getElementById('chartEmpty').classList.toggle('hidden', trend.length > 0);
            const max = Math.max(1, ...trend.map((item) => Number(item.total)));
            document.getElementById('trendChart').innerHTML = trend.map((item) => {
                const total = Number(item.total);
                const percent = total ? (Number(item.sesuai) / total) * 100 : 0;
                return `
                    <div class="grid grid-cols-[90px_1fr_110px] gap-3 items-center text-xs">
                        <span class="text-slate-500">${escapeHtml(item.tanggal)}</span>
                        <div class="h-5 rounded bg-slate-100 overflow-hidden">
                            <div class="h-full bg-green-500" style="width:${Math.max(3, (total / max) * 100)}%" title="${total} penilaian; ${percent.toFixed(1)}% sesuai"></div>
                        </div>
                        <span class="text-right text-slate-600">${total} nilai · ${percent.toFixed(0)}% sesuai</span>
                    </div>
                `;
            }).join('');
            message.className = 'hidden mb-4 rounded-lg p-3 text-sm';
        } catch (error) {
            message.textContent = error.message;
            message.className = 'mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 p-3 text-sm';
        }
    }

    inputs.forEach((input) => input.addEventListener('change', loadDashboard));
    filterToggle.addEventListener('click', () => {
        const isOpen = filterToggle.getAttribute('aria-expanded') === 'true';
        filterToggle.setAttribute('aria-expanded', String(!isOpen));
        filterPanel.classList.toggle('hidden', isOpen);
    });
    document.addEventListener('click', (event) => {
        if (!filterPanel.contains(event.target) && !filterToggle.contains(event.target)) {
            filterPanel.classList.add('hidden');
            filterToggle.setAttribute('aria-expanded', 'false');
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            filterPanel.classList.add('hidden');
            filterToggle.setAttribute('aria-expanded', 'false');
            filterToggle.focus();
        }
    });
    document.getElementById('resetDashboardFilters').addEventListener('click', () => {
        inputs[0].value = '';
        inputs[1].value = 'Aktif';
        inputs[2].value = '';
        inputs[3].value = '';
        loadDashboard();
    });
    loadDashboard();
})();
</script>

<?php include 'includes/footer.php'; ?>
