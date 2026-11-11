<?php

require_once __DIR__ . '/db.php';

function semarak_init_schema(): void
{
    $pdo = semarak_pdo();
    $pdo->exec("UPDATE anak a INNER JOIN masa_pembinaan m ON m.angkatan = a.angkatan
        SET a.status = 'Lulus'
        WHERE a.status = 'Aktif' AND m.tanggal_selesai IS NOT NULL AND m.tanggal_selesai <= CURDATE()");
}

function semarak_generate_anak_id(PDO $pdo): string
{
    $stmt = $pdo->query("SELECT id FROM anak WHERE id LIKE 'AN-%' ORDER BY CAST(SUBSTRING(id, 4) AS UNSIGNED) DESC LIMIT 1");
    $lastId = $stmt->fetchColumn();
    $nextNumber = 1;

    if ($lastId) {
        preg_match('/(\d+)$/', (string) $lastId, $matches);
        if (!empty($matches[1])) {
            $nextNumber = (int) $matches[1] + 1;
        }
    }

    return 'AN-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
}

function semarak_normalize_anak_payload(array $payload): array
{
    $nama = trim((string) ($payload['nama'] ?? ''));
    if ($nama === '') {
        throw new InvalidArgumentException('Nama anak wajib diisi.');
    }

    $jenisKelamin = trim((string) ($payload['jenis_kelamin'] ?? ''));
    if ($jenisKelamin === '') {
        throw new InvalidArgumentException('Jenis kelamin wajib diisi.');
    }

    $asalDaerah = trim((string) ($payload['asal_daerah'] ?? ''));
    if ($asalDaerah === '') {
        throw new InvalidArgumentException('Asal daerah wajib diisi.');
    }

    $agama = trim((string) ($payload['agama'] ?? ''));
    if ($agama === '') {
        throw new InvalidArgumentException('Agama wajib diisi.');
    }

    $umur = (int) ($payload['umur'] ?? 0);
    if ($umur <= 0) {
        throw new InvalidArgumentException('Umur wajib lebih dari 0.');
    }

    $angkatan = trim((string) ($payload['angkatan'] ?? ''));
    if ($angkatan === '') {
        throw new InvalidArgumentException('Angkatan wajib diisi.');
    }

    $status = trim((string) ($payload['status'] ?? 'Aktif'));
    if ($status === '') {
        $status = 'Aktif';
    }

    return [
        'nama' => $nama,
        'jenis_kelamin' => $jenisKelamin,
        'asal_daerah' => $asalDaerah,
        'agama' => $agama,
        'umur' => $umur,
        'angkatan' => $angkatan,
        'status' => $status,
    ];
}

function semarak_get_anak(): array
{
    $stmt = semarak_pdo()->query('SELECT * FROM anak ORDER BY nama ASC');
    return $stmt->fetchAll();
}

function semarak_get_kegiatan(): array
{
    $stmt = semarak_pdo()->query('SELECT * FROM kegiatan ORDER BY id ASC');
    return array_map(static function ($item) {
        return $item['nama'];
    }, $stmt->fetchAll());
}

function semarak_get_kegiatan_rows(): array
{
    $stmt = semarak_pdo()->query('SELECT id, nama FROM kegiatan ORDER BY id ASC');
    return $stmt->fetchAll();
}

function semarak_get_evaluasi(): array
{
    $stmt = semarak_pdo()->query('SELECT * FROM evaluasi ORDER BY tanggal DESC, id ASC');
    return $stmt->fetchAll();
}

function semarak_get_evaluasi_by_tanggal(string $tanggal): array
{
    $stmt = semarak_pdo()->prepare('SELECT anak_id, kegiatan_id, tanggal, nilai FROM evaluasi WHERE tanggal = ?');
    $stmt->execute([$tanggal]);
    return $stmt->fetchAll();
}

function semarak_nilai_label(string $nilai): string
{
    $labels = [
        'sesuai' => 'Sesuai',
        'arahan' => 'Arahan',
        'telat' => 'Telat',
        'tidak' => 'Tidak',
    ];

    return $labels[strtolower($nilai)] ?? ucfirst($nilai);
}

function semarak_hitung_status(array $evaluasi): array
{
    $status = [
        'sesuai' => 0,
        'arahan' => 0,
        'telat' => 0,
        'tidak' => 0,
        'libur' => 0,
    ];

    foreach ($evaluasi as $item) {
        $nilai = strtolower((string) ($item['nilai'] ?? ''));
        if (isset($status[$nilai])) {
            $status[$nilai]++;
        }
    }

    return $status;
}

function semarak_get_dashboard_summary(): array
{
    $pdo = semarak_pdo();
    $anakCount = (int) $pdo->query('SELECT COUNT(*) AS total FROM anak')->fetch()['total'];
    $kegiatanCount = (int) $pdo->query('SELECT COUNT(*) AS total FROM kegiatan')->fetch()['total'];
    $startDate = (string) ($_GET['mulai'] ?? '');
    $endDate = (string) ($_GET['sampai'] ?? '');
    $angkatan = trim((string) ($_GET['angkatan'] ?? ''));
    $statusAnak = trim((string) ($_GET['status_anak'] ?? ''));

    $sql = 'SELECT e.* FROM evaluasi e INNER JOIN anak a ON a.id = e.anak_id WHERE 1=1';
    $params = [];
    if ($startDate !== '') {
        $sql .= ' AND e.tanggal >= ?';
        $params[] = $startDate;
    }
    if ($endDate !== '') {
        $sql .= ' AND e.tanggal <= ?';
        $params[] = $endDate;
    }
    if ($angkatan !== '') {
        $sql .= ' AND a.angkatan = ?';
        $params[] = $angkatan;
    }
    if ($statusAnak !== '') {
        $sql .= ' AND a.status = ?';
        $params[] = $statusAnak;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $evaluasi = $stmt->fetchAll();
    $status = semarak_hitung_status($evaluasi);
    $total = max(1, count($evaluasi) - $status['libur']);

    $trendSql = 'SELECT DATE(e.tanggal) AS tanggal, SUM(e.nilai <> "libur") AS total, SUM(e.nilai = "sesuai") AS sesuai
        FROM evaluasi e INNER JOIN anak a ON a.id = e.anak_id WHERE 1=1';
    $trendParams = [];
    if ($startDate !== '') {
        $trendSql .= ' AND e.tanggal >= ?';
        $trendParams[] = $startDate;
    }
    if ($endDate !== '') {
        $trendSql .= ' AND e.tanggal <= ?';
        $trendParams[] = $endDate;
    }
    if ($angkatan !== '') {
        $trendSql .= ' AND a.angkatan = ?';
        $trendParams[] = $angkatan;
    }
    if ($statusAnak !== '') {
        $trendSql .= ' AND a.status = ?';
        $trendParams[] = $statusAnak;
    }
    $trendSql .= ' GROUP BY DATE(e.tanggal) ORDER BY DATE(e.tanggal)';
    $trendStmt = $pdo->prepare($trendSql);
    $trendStmt->execute($trendParams);

    return [
        'total_anak' => $angkatan !== '' || $statusAnak !== ''
            ? semarak_count_filtered_anak($angkatan, $statusAnak)
            : $anakCount,
        'total_kegiatan' => $kegiatanCount,
        'status' => $status,
        'presentase' => [
            'sesuai' => round(($status['sesuai'] / $total) * 100, 1),
            'arahan' => round(($status['arahan'] / $total) * 100, 1),
            'telat' => round(($status['telat'] / $total) * 100, 1),
            'tidak' => round(($status['tidak'] / $total) * 100, 1),
        ],
        'total_penilaian' => count($evaluasi),
        'tren' => $trendStmt->fetchAll(),
    ];
}

function semarak_count_filtered_anak(string $angkatan, string $status): int
{
    $sql = 'SELECT COUNT(*) FROM anak WHERE 1=1';
    $params = [];
    if ($angkatan !== '') {
        $sql .= ' AND angkatan = ?';
        $params[] = $angkatan;
    }
    if ($status !== '') {
        $sql .= ' AND status = ?';
        $params[] = $status;
    }
    $stmt = semarak_pdo()->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function semarak_get_summary_by_anak(string $anakId, string $startDate = '', string $endDate = ''): array
{
    $pdo = semarak_pdo();

    $anakStmt = $pdo->prepare('SELECT * FROM anak WHERE id = ? LIMIT 1');
    $anakStmt->execute([$anakId]);
    $anak = $anakStmt->fetch();

    if (!$anak) {
        return [
            'anak' => null,
            'status' => ['sesuai' => 0, 'arahan' => 0, 'telat' => 0, 'tidak' => 0, 'libur' => 0],
            'skor_total' => 0,
            'detail' => [],
        ];
    }

    $detail = [];
    $kegiatanRows = $pdo->query('SELECT id, nama FROM kegiatan ORDER BY id ASC')->fetchAll();
    foreach ($kegiatanRows as $row) {
        $detail[] = [
            'no' => (int) $row['id'],
            'kegiatan' => $row['nama'],
            'sesuai' => 0,
            'arahan' => 0,
            'telat' => 0,
            'tidak' => 0,
            'libur' => 0,
            'skor' => 0,
        ];
    }

    $status = ['sesuai' => 0, 'arahan' => 0, 'telat' => 0, 'tidak' => 0, 'libur' => 0];
    $evalSql = 'SELECT * FROM evaluasi WHERE anak_id = ?';
    $evalParams = [$anakId];
    if ($startDate !== '') {
        $evalSql .= ' AND tanggal >= ?';
        $evalParams[] = $startDate;
    }
    if ($endDate !== '') {
        $evalSql .= ' AND tanggal <= ?';
        $evalParams[] = $endDate;
    }
    $evalSql .= ' ORDER BY kegiatan_id ASC';
    $evalStmt = $pdo->prepare($evalSql);
    $evalStmt->execute($evalParams);

    foreach ($evalStmt->fetchAll() as $item) {
        $nilai = strtolower((string) ($item['nilai'] ?? ''));
        if (isset($status[$nilai])) {
            $status[$nilai]++;
        }

        $kegiatanIndex = (int) $item['kegiatan_id'] - 1;
        if (isset($detail[$kegiatanIndex])) {
            $detail[$kegiatanIndex][$nilai] = ($detail[$kegiatanIndex][$nilai] ?? 0) + 1;
        }
    }

    foreach ($detail as &$row) {
        $total = max(1, $row['sesuai'] + $row['arahan'] + $row['telat'] + $row['tidak']);
        $row['skor'] = round(($row['sesuai'] / $total) * 100, 1);
    }
    unset($row);

    $totalNilai = max(1, array_sum($status) - $status['libur']);
    $skorTotal = round(($status['sesuai'] / $totalNilai) * 100, 1);

    return [
        'anak' => $anak,
        'status' => $status,
        'skor_total' => $skorTotal,
        'detail' => $detail,
    ];
}

function semarak_get_report_data(string $anakId, string $startDate = '', string $endDate = ''): array
{
    $summary = semarak_get_summary_by_anak($anakId, $startDate, $endDate);
    $pdo = semarak_pdo();
    $stmt = $pdo->prepare('SELECT * FROM anak WHERE id = ? LIMIT 1');
    $stmt->execute([$anakId]);
    $anak = $stmt->fetch();

    if ($startDate === '') {
        $startDate = $endDate !== '' ? $endDate : date('Y-m-d');
    }
    if ($endDate === '') {
        $endDate = $startDate;
    }

    $notes = $pdo->prepare('SELECT catatan, rekomendasi FROM catatan_rapor WHERE anak_id = ? AND periode_mulai = ? AND periode_selesai = ? LIMIT 1');
    $notes->execute([$anakId, $startDate, $endDate]);
    $savedNotes = $notes->fetch() ?: ['catatan' => '', 'rekomendasi' => ''];

    return [
        'anak' => $anak,
        'ringkasan' => $summary,
        'tanggal_catat' => $endDate,
        'periode_mulai' => $startDate,
        'periode_selesai' => $endDate,
        'catatan' => $savedNotes['catatan'],
        'rekomendasi' => $savedNotes['rekomendasi'],
        'standar_evaluasi' => ['Berehan', 'Motekar', 'Calakan', 'Berseka', 'Waringkas'],
    ];
}

function semarak_get_settings(): array
{
    $pdo = semarak_pdo();
    $schedules = $pdo->query('SELECT kegiatan_id, aturan, hari_json, rentang_json FROM jadwal_kegiatan')->fetchAll();
    $scheduleByActivity = [];
    foreach ($schedules as $schedule) {
        $scheduleByActivity[(int) $schedule['kegiatan_id']] = [
            'aturan' => $schedule['aturan'],
            'hari' => json_decode($schedule['hari_json'] ?: '[]', true) ?: [],
            'rentang' => json_decode($schedule['rentang_json'] ?: '[]', true) ?: [],
        ];
    }

    $cohorts = $pdo->query('SELECT a.angkatan, COUNT(a.id) AS total_anak, m.tanggal_selesai
        FROM anak a LEFT JOIN masa_pembinaan m ON m.angkatan = a.angkatan
        GROUP BY a.angkatan, m.tanggal_selesai ORDER BY a.angkatan')->fetchAll();

    return ['jadwal' => $scheduleByActivity, 'angkatan' => $cohorts];
}

function semarak_save_settings(array $payload): array
{
    $pdo = semarak_pdo();
    $schedules = $payload['jadwal'] ?? [];
    if (!is_array($schedules)) {
        throw new InvalidArgumentException('Format jadwal tidak valid.');
    }

    $validRules = ['setiap_hari', 'hari_kerja', 'hari_tertentu', 'multi_rentang'];
    $pdo->beginTransaction();
    try {
        $exists = $pdo->prepare('SELECT id FROM kegiatan WHERE id = ?');
        $save = $pdo->prepare('INSERT INTO jadwal_kegiatan (kegiatan_id, aturan, hari_json, rentang_json)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE aturan = VALUES(aturan), hari_json = VALUES(hari_json), rentang_json = VALUES(rentang_json)');
        foreach ($schedules as $activityId => $schedule) {
            $activityId = (int) $activityId;
            $rule = (string) ($schedule['aturan'] ?? '');
            if ($activityId <= 0 || !in_array($rule, $validRules, true) || !is_array($schedule)) {
                throw new InvalidArgumentException('Aturan jadwal tidak valid.');
            }
            $exists->execute([$activityId]);
            if (!$exists->fetch()) {
                throw new InvalidArgumentException('Kegiatan jadwal tidak ditemukan.');
            }

            $days = $schedule['hari'] ?? [];
            $ranges = $schedule['rentang'] ?? [];
            if (!is_array($days) || !is_array($ranges)) {
                throw new InvalidArgumentException('Hari atau rentang jadwal tidak valid.');
            }
            foreach ($days as $day) {
                if (!in_array((string) $day, ['1', '2', '3', '4', '5', '6', '7'], true)) {
                    throw new InvalidArgumentException('Hari jadwal tidak valid.');
                }
            }
            foreach ($ranges as $range) {
                $fromValue = (string) ($range['mulai'] ?? '');
                $toValue = (string) ($range['selesai'] ?? '');
                $from = DateTime::createFromFormat('Y-m-d', $fromValue);
                $to = DateTime::createFromFormat('Y-m-d', $toValue);
                if (!$from || !$to || $from->format('Y-m-d') !== $fromValue || $to->format('Y-m-d') !== $toValue || $from > $to) {
                    throw new InvalidArgumentException('Rentang tanggal jadwal tidak valid.');
                }
            }
            $save->execute([
                $activityId,
                $rule,
                json_encode(array_values($days)),
                json_encode(array_values($ranges)),
            ]);
        }

        $cohorts = $payload['angkatan'] ?? [];
        if (!is_array($cohorts)) {
            throw new InvalidArgumentException('Format masa pembinaan tidak valid.');
        }
        $saveCohort = $pdo->prepare('INSERT INTO masa_pembinaan (angkatan, tanggal_selesai) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE tanggal_selesai = VALUES(tanggal_selesai)');
        foreach ($cohorts as $cohort => $endDate) {
            $cohort = trim((string) $cohort);
            if ($cohort === '') {
                throw new InvalidArgumentException('Nama angkatan tidak valid.');
            }
            if ($endDate !== '') {
                $parsed = DateTime::createFromFormat('Y-m-d', (string) $endDate);
                if (!$parsed || $parsed->format('Y-m-d') !== $endDate) {
                    throw new InvalidArgumentException('Tanggal selesai pembinaan tidak valid.');
                }
            }
            $saveCohort->execute([$cohort, $endDate !== '' ? $endDate : null]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    return ['success' => true, 'message' => 'Pengaturan berhasil disimpan.'];
}

function semarak_save_report_notes(array $payload): array
{
    $anakId = trim((string) ($payload['anak_id'] ?? ''));
    $startDate = (string) ($payload['periode_mulai'] ?? '');
    $endDate = (string) ($payload['periode_selesai'] ?? '');
    $catatan = trim((string) ($payload['catatan'] ?? ''));
    $rekomendasi = trim((string) ($payload['rekomendasi'] ?? ''));
    foreach ([$startDate, $endDate] as $date) {
        $parsed = DateTime::createFromFormat('Y-m-d', $date);
        if (!$parsed || $parsed->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException('Periode rapor tidak valid.');
        }
    }
    if ($startDate > $endDate) {
        throw new InvalidArgumentException('Tanggal mulai tidak boleh melewati tanggal selesai.');
    }

    $stmt = semarak_pdo()->prepare('INSERT INTO catatan_rapor (anak_id, periode_mulai, periode_selesai, catatan, rekomendasi)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE catatan = VALUES(catatan), rekomendasi = VALUES(rekomendasi)');
    $stmt->execute([$anakId, $startDate, $endDate, $catatan, $rekomendasi]);
    return ['success' => true, 'message' => 'Catatan dan rekomendasi rapor berhasil disimpan.'];
}

function semarak_store_anak(array $payload): array
{
    $pdo = semarak_pdo();
    $data = semarak_normalize_anak_payload($payload);
    $id = trim((string) ($payload['id'] ?? ''));

    if ($id === '') {
        $id = semarak_generate_anak_id($pdo);
    } else {
        if (!preg_match('/^AN-\d{3,}$/i', $id)) {
            throw new InvalidArgumentException('Format ID anak tidak valid. Contoh: AN-001');
        }
    }

    $check = $pdo->prepare('SELECT id FROM anak WHERE id = ? LIMIT 1');
    $check->execute([$id]);
    if ($check->fetch()) {
        throw new InvalidArgumentException('ID anak sudah ada. Gunakan ID lain atau biarkan sistem membuat otomatis.');
    }

    $stmt = $pdo->prepare('INSERT INTO anak (id, nama, jenis_kelamin, asal_daerah, agama, umur, angkatan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $id,
        $data['nama'],
        $data['jenis_kelamin'],
        $data['asal_daerah'],
        $data['agama'],
        $data['umur'],
        $data['angkatan'],
        $data['status'],
    ]);

    return [
        'success' => true,
        'message' => 'Data anak berhasil ditambahkan.',
        'data' => ['id' => $id] + $data,
    ];
}

function semarak_update_anak(string $id, array $payload): array
{
    $pdo = semarak_pdo();
    $existing = $pdo->prepare('SELECT id FROM anak WHERE id = ? LIMIT 1');
    $existing->execute([$id]);
    if (!$existing->fetch()) {
        throw new InvalidArgumentException('Data anak tidak ditemukan.');
    }

    $data = semarak_normalize_anak_payload($payload);
    $stmt = $pdo->prepare('UPDATE anak SET nama = ?, jenis_kelamin = ?, asal_daerah = ?, agama = ?, umur = ?, angkatan = ?, status = ? WHERE id = ?');
    $stmt->execute([
        $data['nama'],
        $data['jenis_kelamin'],
        $data['asal_daerah'],
        $data['agama'],
        $data['umur'],
        $data['angkatan'],
        $data['status'],
        $id,
    ]);

    return [
        'success' => true,
        'message' => 'Data anak berhasil diperbarui.',
        'data' => ['id' => $id] + $data,
    ];
}

function semarak_delete_anak(string $id): array
{
    $pdo = semarak_pdo();
    $check = $pdo->prepare('SELECT id FROM anak WHERE id = ? LIMIT 1');
    $check->execute([$id]);
    if (!$check->fetch()) {
        throw new InvalidArgumentException('Data anak tidak ditemukan.');
    }

    $pdo->prepare('DELETE FROM evaluasi WHERE anak_id = ?')->execute([$id]);
    $pdo->prepare('DELETE FROM anak WHERE id = ?')->execute([$id]);

    return [
        'success' => true,
        'message' => 'Data anak berhasil dihapus.',
        'id' => $id,
    ];
}

function semarak_save_evaluasi(array $payload): array
{
    $pdo = semarak_pdo();
    $anakId = (string) ($payload['anak_id'] ?? '');
    $kegiatanId = (int) ($payload['kegiatan_id'] ?? 0);
    $nilai = strtolower((string) ($payload['nilai'] ?? ''));
    $tanggal = (string) ($payload['tanggal'] ?? date('Y-m-d'));

    if ($anakId === '' || $kegiatanId <= 0 || !in_array($nilai, ['sesuai', 'arahan', 'telat', 'tidak', 'libur'], true)) {
        throw new InvalidArgumentException('Data evaluasi tidak valid.');
    }

    $check = $pdo->prepare('SELECT id FROM anak WHERE id = ? LIMIT 1');
    $check->execute([$anakId]);
    if (!$check->fetch()) {
        throw new InvalidArgumentException('Anak tidak ditemukan.');
    }

    $kegiatanCheck = $pdo->prepare('SELECT id FROM kegiatan WHERE id = ? LIMIT 1');
    $kegiatanCheck->execute([$kegiatanId]);
    if (!$kegiatanCheck->fetch()) {
        throw new InvalidArgumentException('Kegiatan tidak ditemukan.');
    }

    $upsert = $pdo->prepare('INSERT INTO evaluasi (anak_id, kegiatan_id, tanggal, nilai)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)');
    $upsert->execute([$anakId, $kegiatanId, $tanggal, $nilai]);

    return [
        'success' => true,
        'message' => 'Evaluasi berhasil disimpan',
        'payload' => [
            'anak_id' => $anakId,
            'kegiatan_id' => $kegiatanId,
            'tanggal' => $tanggal,
            'nilai' => $nilai,
        ],
    ];
}

function semarak_save_evaluasi_batch(array $items): array
{
    if (!$items) {
        throw new InvalidArgumentException('Pilih nilai untuk setidaknya satu anak sebelum menyimpan.');
    }

    $pdo = semarak_pdo();
    $pdo->beginTransaction();

    try {
        foreach ($items as $item) {
            semarak_save_evaluasi($item);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    return [
        'success' => true,
        'message' => 'Penilaian berhasil disimpan.',
        'saved' => count($items),
    ];
}
