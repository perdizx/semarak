<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEMARAK - Sistem Monitoring & Raport Karakter Anak</title>
    <link rel="icon" type="image/jpg" href="assets/logo/semarak.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="app-body bg-slate-50 text-slate-800 font-sans">
    <header class="app-header sticky top-0 z-50">
        <div class="app-header-inner container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 min-w-0">
                <div>
                    <h1 class="text-xl font-bold leading-tight flex items-center gap-2">
                        SEMARAK <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full font-normal">UPTD PPSGBR</span>
                    </h1>
                    <p class="text-xs text-blue-100">Sistem Monitoring & Raport Karakter Anak</p>
                </div>
            </a>
            <div class="flex items-center gap-2 md:gap-3">
                <a href="absensi.php" aria-label="Input evaluasi" title="Input evaluasi" class="app-header-action px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i><span>Input Evaluasi</span>
                </a>
                <?php if (basename($_SERVER['PHP_SELF']) === 'index.php'): ?>
                    <button id="dashboardFilterToggle" type="button" class="app-header-filter px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2" aria-expanded="false" aria-controls="dashboardFilterPanel">
                        <i class="fa-solid fa-sliders"></i><span>Filter</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php if (basename($_SERVER['PHP_SELF']) === 'index.php'): ?>
            <div class="dashboard-filter-anchor">
                <section id="dashboardFilterPanel" class="dashboard-filter-popover hidden" aria-label="Filter ringkasan perkembangan">
                    <div class="dashboard-filter-heading">
                        <span class="font-semibold text-slate-800">Filter ringkasan</span>
                        <button id="resetDashboardFilters" type="button" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            <i class="fa-solid fa-rotate-left mr-1"></i>Reset
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="text-xs font-semibold text-slate-600">
                            Angkatan
                            <select id="filterAngkatan" class="mt-1 block w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-normal bg-white focus:border-indigo-400 outline-none">
                                <option value="">Semua Angkatan</option>
                                <?php foreach ($cohorts as $cohort): ?>
                                    <option value="<?= htmlspecialchars($cohort, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($cohort, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="text-xs font-semibold text-slate-600">
                            Status anak
                            <select id="filterStatus" class="mt-1 block w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-normal bg-white focus:border-indigo-400 outline-none">
                                <option value="Aktif">Hanya Siswa Aktif</option>
                                <option value="">Semua Status</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                                <option value="Lulus">Lulus</option>
                            </select>
                        </label>
                        <label class="text-xs font-semibold text-slate-600">
                            Dari tanggal
                            <input id="filterMulai" type="date" class="mt-1 block w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-normal bg-white focus:border-indigo-400 outline-none">
                        </label>
                        <label class="text-xs font-semibold text-slate-600">
                            Sampai tanggal
                            <input id="filterSampai" type="date" class="mt-1 block w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-normal bg-white focus:border-indigo-400 outline-none">
                        </label>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    </header>
    <main class="app-main container mx-auto px-4 py-6 max-w-7xl">
