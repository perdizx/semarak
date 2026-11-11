// ==========================================
// FUNGSI UMUM (GLOBAL)
// ==========================================

document.addEventListener('DOMContentLoaded', () => {
    const responsiveTables = document.querySelectorAll('.responsive-data-table tbody');
    const enhanceResponsiveRows = (body) => {
        body.querySelectorAll('tr').forEach((row) => {
            if (row.querySelector('.responsive-row-toggle')) return;
            const summaryCell = [...row.cells].find((cell) =>
                ['Kegiatan', 'Uraian kegiatan', 'Angkatan'].includes(cell.dataset.label)
            );
            if (!summaryCell) return;

            row.classList.add('responsive-row-collapsible');
            const value = document.createElement('span');
            value.className = 'responsive-row-summary-value';
            while (summaryCell.firstChild) value.append(summaryCell.firstChild);
            summaryCell.classList.add('responsive-row-summary');
            summaryCell.append(value);

            const toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'responsive-row-toggle';
            toggle.setAttribute('aria-expanded', 'false');
            toggle.innerHTML = '<span>Detail</span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i>';
            toggle.addEventListener('click', () => {
                const expanded = row.classList.toggle('responsive-row-expanded');
                toggle.setAttribute('aria-expanded', String(expanded));
                toggle.querySelector('span').textContent = expanded ? 'Tutup' : 'Detail';
                toggle.querySelector('i').classList.toggle('rotate-180', expanded);
            });
            summaryCell.append(toggle);
        });
    };

    responsiveTables.forEach((body) => {
        enhanceResponsiveRows(body);
        new MutationObserver(() => enhanceResponsiveRows(body)).observe(body, { childList: true });
    });

    const dateElement = document.getElementById('currentDate');
    const timeElement = document.getElementById('currentTime');
    if (dateElement && timeElement) {
        const timezone = 'Asia/Jakarta';
        const dateFormatter = new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            weekday: 'long',
            timeZone: timezone
        });
        const timeFormatter = new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hourCycle: 'h23',
            timeZone: timezone
        });

        const updateClock = () => {
            const now = new Date();
            dateElement.textContent = dateFormatter.format(now);
            dateElement.dateTime = new Intl.DateTimeFormat('en-CA', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                timeZone: timezone
            }).format(now);
            timeElement.textContent = timeFormatter.format(now);
        };

        updateClock();
        window.setInterval(updateClock, 1000);
    }
});


// ==========================================
// FUNGSI UNTUK HALAMAN ABSENSI
// ==========================================

// Fungsi untuk Buka/Tutup (Accordion) baris kegiatan di halaman Absensi
function toggleAccordion(index) {
    const content = document.getElementById(`content-${index}`);
    const icon = document.getElementById(`icon-${index}`);

    if (content.classList.contains('hidden')) {
        // Tampilkan konten
        content.classList.remove('hidden');
        // Putar panah ke atas (180 derajat)
        icon.classList.add('rotate-180');
    } else {
        // Sembunyikan konten
        content.classList.add('hidden');
        // Kembalikan panah menghadap ke bawah
        icon.classList.remove('rotate-180');
    }
}


// ==========================================
// FUNGSI UNTUK HALAMAN PENGATURAN (JADWAL)
// ==========================================

// Menampilkan/Menyembunyikan panel opsi berdasarkan dropdown aturan jadwal
function toggleAturanJadwal(selectElement, index) {
    const value = selectElement.value;
    const opsiHari = document.getElementById(`opsi-hari-${index}`);
    const opsiRentang = document.getElementById(`opsi-rentang-${index}`);

    // Sembunyikan keduanya terlebih dahulu (Reset)
    opsiHari.classList.add('hidden');
    opsiRentang.classList.add('hidden');

    // Tampilkan panel sesuai pilihan yang dipilih
    if (value === 'hari_tertentu') {
        opsiHari.classList.remove('hidden');
    } else if (value === 'multi_rentang') {
        opsiRentang.classList.remove('hidden');
    }
}

// Menambahkan baris input rentang tanggal baru
function tambahRentang(index) {
    const container = document.getElementById(`container-rentang-${index}`);
    
    // HTML untuk 1 baris rentang baru (Mulai - Selesai) beserta tombol hapus
    const html = `
        <div class="flex items-center gap-2 text-xs mt-2">
            <input type="date" class="border border-slate-300 text-slate-600 rounded px-2 py-1.5 w-full outline-none focus:border-blue-500">
            <span class="text-slate-400"><i class="fa-solid fa-minus"></i></span>
            <input type="date" class="border border-slate-300 text-slate-600 rounded px-2 py-1.5 w-full outline-none focus:border-blue-500">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 px-1 text-base transition" title="Hapus Rentang">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    `;
    
    // Masukkan baris baru tersebut ke dalam container di posisi paling bawah
    container.insertAdjacentHTML('beforeend', html);
}