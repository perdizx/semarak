<?php include 'includes/header.php'; ?>

<div class="semarak-card p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-blue-500"></i> Data Anak
            </h2>
            <p class="text-sm text-slate-500 mt-1">Kelola data santri / anak asuh</p>
        </div>
        <button type="button" id="btnTambah" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium">
            <i class="fa-solid fa-plus mr-2"></i> Input Anak
        </button>
    </div>

    <div id="modalAnak" class="child-modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalAnakTitle">
        <div class="child-modal-backdrop" data-close-modal></div>
        <div class="child-modal-content">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h3 id="modalAnakTitle" class="text-lg font-bold text-slate-800">Input Anak</h3>
                <button type="button" class="child-modal-close" aria-label="Tutup form" data-close-modal>
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
    <form id="formAnak" class="bg-slate-50 border border-slate-200 rounded-lg p-4">
        <input type="hidden" id="anakId" name="id">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">ID Anak</label>
                <input type="text" id="idAnak" name="id_manual" class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white focus:border-blue-500 outline-none" placeholder="Kosongkan untuk auto-generate">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
                <input type="text" id="nama" name="nama" class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white focus:border-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin</label>
                <select id="jenisKelamin" name="jenis_kelamin" class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white focus:border-blue-500 outline-none" required>
                    <option value="">Pilih</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Asal Daerah</label>
                <input type="text" id="asalDaerah" name="asal_daerah" class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white focus:border-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Agama</label>
                <input type="text" id="agama" name="agama" class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white focus:border-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Umur</label>
                <input type="number" id="umur" name="umur" min="1" class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white focus:border-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Angkatan</label>
                <input type="text" id="angkatan" name="angkatan" class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white focus:border-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white focus:border-blue-500 outline-none" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Non-Aktif">Non-Aktif</option>
                    <option value="Lulus">Lulus</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-5">
            <button type="button" id="btnBatal" class="border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 px-4 py-2 rounded text-sm font-medium">
                Batal
            </button>
            <button type="submit" id="btnSubmit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded text-sm font-medium">
                <i class="fa-solid fa-save mr-2"></i> Simpan
            </button>
        </div>
    </form>
        </div>
    </div>

    <div class="border border-slate-200 rounded-lg overflow-hidden">
        <div class="bg-slate-50 px-4 py-3 border-b border-slate-200">
            <h3 class="font-bold text-slate-700 text-sm"><i class="fa-solid fa-list mr-2"></i> Daftar Anak</h3>
        </div>
        <div class="child-table-wrapper overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white border-b border-slate-200 text-slate-600">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Jenis Kelamin</th>
                        <th class="px-4 py-3">Asal Daerah</th>
                        <th class="px-4 py-3">Agama</th>
                        <th class="px-4 py-3">Umur</th>
                        <th class="px-4 py-3">Angkatan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyAnak" class="bg-white"></tbody>
            </table>
        </div>
        <div id="mobileCards" class="child-mobile-list" aria-live="polite"></div>
    </div>
</div>

<script>
const form = document.getElementById('formAnak');
const tbody = document.getElementById('tbodyAnak');
const mobileCards = document.getElementById('mobileCards');
const submitButton = document.getElementById('btnSubmit');
const btnBatal = document.getElementById('btnBatal');
const btnTambah = document.getElementById('btnTambah');
const idField = document.getElementById('anakId');
const idManualField = document.getElementById('idAnak');
const modal = document.getElementById('modalAnak');
const modalTitle = document.getElementById('modalAnakTitle');

function apiFetch(url, options = {}) {
    const config = {
        headers: { 'Accept': 'application/json' },
        ...options,
    };

    if (!config.headers['Content-Type'] && !(options.body instanceof FormData)) {
        config.headers['Content-Type'] = 'application/json';
    }

    return fetch(url, config).then(async (response) => {
        const text = await response.text();
        let data = null;
        try {
            data = text ? JSON.parse(text) : null;
        } catch (error) {
            data = { success: false, message: text || 'Terjadi kesalahan.' };
        }

        if (!response.ok && !data.success) {
            throw new Error(data.message || 'Request gagal');
        }

        return data;
    });
}

function resetForm() {
    form.reset();
    idField.value = '';
    idManualField.value = '';
    submitButton.innerHTML = '<i class="fa-solid fa-save mr-2"></i> Simpan';
    modalTitle.textContent = 'Input Anak';
    closeModal();
}

function openModal() {
    modal.classList.remove('hidden');
    document.body.classList.add('modal-open');
    document.getElementById('nama').focus();
}

function closeModal() {
    modal.classList.add('hidden');
    document.body.classList.remove('modal-open');
    btnTambah.focus();
}

function renderTable(rows) {
    if (!Array.isArray(rows) || rows.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                    Belum ada data anak.
                </td>
            </tr>
        `;
        mobileCards.innerHTML = '<p class="px-4 py-8 text-center text-slate-500">Belum ada data anak.</p>';
        return;
    }

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);
    const statusClass = (status) => status === 'Aktif'
        ? 'bg-green-100 text-green-700'
        : status === 'Lulus' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700';

    tbody.innerHTML = rows.map((item) => `
        <tr class="border-b border-slate-100 hover:bg-slate-50">
            <td class="px-4 py-3 font-medium text-slate-700">${escapeHtml(item.id || '-')}</td>
            <td class="px-4 py-3">${escapeHtml(item.nama || '-')}</td>
            <td class="px-4 py-3">${escapeHtml(item.jenis_kelamin || '-')}</td>
            <td class="px-4 py-3">${escapeHtml(item.asal_daerah || '-')}</td>
            <td class="px-4 py-3">${escapeHtml(item.agama || '-')}</td>
            <td class="px-4 py-3">${escapeHtml(item.umur || '-')}</td>
            <td class="px-4 py-3">${escapeHtml(item.angkatan || '-')}</td>
            <td class="px-4 py-3">
                <span class="px-2 py-1 rounded text-xs font-medium ${statusClass(item.status)}">
                    ${escapeHtml(item.status || 'Aktif')}
                </span>
            </td>
            <td class="px-4 py-3 text-center">
                <div class="flex justify-center gap-2">
                    <button type="button" data-action="edit" data-id="${escapeHtml(item.id)}" aria-label="Edit ${escapeHtml(item.nama)}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button type="button" data-action="delete" data-id="${escapeHtml(item.id)}" aria-label="Hapus ${escapeHtml(item.nama)}" class="text-red-600 hover:text-red-800 text-xs font-medium">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');

    mobileCards.innerHTML = rows.map((item) => `
        <article class="child-mobile-card">
            <div class="child-mobile-card-heading">
                <div class="min-w-0">
                    <h4 class="child-mobile-name">${escapeHtml(item.nama || '-')}</h4>
                    <p class="child-mobile-id">${escapeHtml(item.id || '-')}</p>
                </div>
                <div class="child-mobile-heading-actions">
                    <span class="px-2 py-1 rounded text-xs font-medium ${statusClass(item.status)}">${escapeHtml(item.status || 'Aktif')}</span>
                    <button type="button" class="child-mobile-toggle" aria-expanded="false" aria-label="Tampilkan detail ${escapeHtml(item.nama || '')}">
                        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <dl class="child-mobile-details">
                <div><dt>Jenis kelamin</dt><dd>${escapeHtml(item.jenis_kelamin || '-')}</dd></div>
                <div><dt>Asal daerah</dt><dd>${escapeHtml(item.asal_daerah || '-')}</dd></div>
                <div><dt>Agama</dt><dd>${escapeHtml(item.agama || '-')}</dd></div>
                <div><dt>Umur</dt><dd>${escapeHtml(item.umur || '-')} tahun</dd></div>
                <div><dt>Angkatan</dt><dd>${escapeHtml(item.angkatan || '-')}</dd></div>
            </dl>
            <div class="child-mobile-actions">
                <button type="button" data-action="edit" data-id="${escapeHtml(item.id)}" class="child-mobile-edit">
                    <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Edit
                </button>
                <button type="button" data-action="delete" data-id="${escapeHtml(item.id)}" class="child-mobile-delete">
                    <i class="fa-solid fa-trash" aria-hidden="true"></i> Hapus
                </button>
            </div>
        </article>
    `).join('');
}

async function loadAnak() {
    try {
        const data = await apiFetch('api.php?action=anak');
        renderTable(data);
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="9" class="px-4 py-8 text-center text-red-500">${error.message}</td></tr>`;
        mobileCards.textContent = error.message;
    }
}

async function handleSubmit(event) {
    event.preventDefault();

    const payload = {
        id: idField.value || idManualField.value || '',
        nama: document.getElementById('nama').value,
        jenis_kelamin: document.getElementById('jenisKelamin').value,
        asal_daerah: document.getElementById('asalDaerah').value,
        agama: document.getElementById('agama').value,
        umur: Number(document.getElementById('umur').value),
        angkatan: document.getElementById('angkatan').value,
        status: document.getElementById('status').value
    };

    const action = idField.value ? 'anak_update' : 'anak_store';

    try {
        const result = await apiFetch(`api.php?action=${action}`, {
            method: 'POST',
            body: JSON.stringify(payload),
        });

        alert(result.message || 'Berhasil');
        resetForm();
        await loadAnak();
    } catch (error) {
        alert(error.message || 'Gagal menyimpan data anak');
    }
}

async function handleRowButton(event) {
    const button = event.target.closest('button');
    if (!button) return;

    const { action, id } = button.dataset;
    if (!id) return;

    if (action === 'edit') {
        try {
            const data = await apiFetch(`api.php?action=anak_detail&id=${encodeURIComponent(id)}`);
            if (!data || data.success === false) {
                throw new Error(data.message || 'Data tidak ditemukan');
            }

            idField.value = data.id;
            idManualField.value = data.id;
            document.getElementById('nama').value = data.nama || '';
            document.getElementById('jenisKelamin').value = data.jenis_kelamin || '';
            document.getElementById('asalDaerah').value = data.asal_daerah || '';
            document.getElementById('agama').value = data.agama || '';
            document.getElementById('umur').value = data.umur || 0;
            document.getElementById('angkatan').value = data.angkatan || '';
            document.getElementById('status').value = data.status || 'Aktif';
            submitButton.innerHTML = '<i class="fa-solid fa-floppy-disk mr-2"></i> Update';
            modalTitle.textContent = 'Edit Data Anak';
            openModal();
        } catch (error) {
            alert(error.message || 'Data tidak dapat dibuka');
        }
        return;
    }

    if (action === 'delete') {
        const confirmed = confirm(`Apakah Anda yakin ingin menghapus data anak ${id}?`);
        if (!confirmed) return;

        try {
            const result = await apiFetch(`api.php?action=anak_delete&id=${encodeURIComponent(id)}`, {
                method: 'POST'
            });
            alert(result.message || 'Data berhasil dihapus');
            await loadAnak();
        } catch (error) {
            alert(error.message || 'Gagal menghapus data');
        }
    }
}

form.addEventListener('submit', handleSubmit);
btnBatal.addEventListener('click', resetForm);
btnTambah.addEventListener('click', () => {
    form.reset();
    idField.value = '';
    idManualField.value = '';
    submitButton.innerHTML = '<i class="fa-solid fa-save mr-2"></i> Simpan';
    modalTitle.textContent = 'Input Anak';
    openModal();
});
modal.addEventListener('click', (event) => {
    if (event.target.closest('[data-close-modal]')) resetForm();
});
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !modal.classList.contains('hidden')) resetForm();
});
tbody.addEventListener('click', handleRowButton);
mobileCards.addEventListener('click', handleRowButton);
mobileCards.addEventListener('click', (event) => {
    const toggle = event.target.closest('.child-mobile-toggle');
    if (!toggle) return;
    const card = toggle.closest('.child-mobile-card');
    const expanded = card.classList.toggle('child-mobile-expanded');
    toggle.setAttribute('aria-expanded', String(expanded));
    toggle.setAttribute('aria-label', `${expanded ? 'Sembunyikan' : 'Tampilkan'} detail ${card.querySelector('.child-mobile-name').textContent}`);
    toggle.querySelector('i').classList.toggle('rotate-180', expanded);
});

loadAnak();
</script>

<?php include 'includes/footer.php'; ?>
