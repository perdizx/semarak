<?php

require __DIR__ . '/includes/backend.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? 'dashboard';
$method = $_SERVER['REQUEST_METHOD'];

function semarak_validate_optional_date(string $date): void
{
    if ($date === '') {
        return;
    }
    $parsed = DateTime::createFromFormat('Y-m-d', $date);
    if (!$parsed || $parsed->format('Y-m-d') !== $date) {
        throw new InvalidArgumentException('Format tanggal tidak valid.');
    }
}

try {
    semarak_init_schema();
    semarak_validate_optional_date((string) ($_GET['mulai'] ?? ''));
    semarak_validate_optional_date((string) ($_GET['sampai'] ?? ''));
    if (!empty($_GET['mulai']) && !empty($_GET['sampai']) && $_GET['mulai'] > $_GET['sampai']) {
        throw new InvalidArgumentException('Tanggal mulai tidak boleh melewati tanggal selesai.');
    }

    if ($method === 'POST' && $action === 'save_evaluasi') {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload) || empty($payload)) {
            $payload = $_POST;
        }

        echo json_encode(semarak_save_evaluasi($payload));
        exit;
    }

    if ($method === 'POST' && $action === 'save_evaluasi_batch') {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload) || !isset($payload['items']) || !is_array($payload['items'])) {
            throw new InvalidArgumentException('Format data penilaian tidak valid.');
        }

        echo json_encode(semarak_save_evaluasi_batch($payload['items']));
        exit;
    }

    if ($method === 'POST' && $action === 'settings_save') {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            throw new InvalidArgumentException('Format pengaturan tidak valid.');
        }

        echo json_encode(semarak_save_settings($payload));
        exit;
    }

    if ($method === 'POST' && $action === 'report_notes_save') {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            throw new InvalidArgumentException('Format catatan rapor tidak valid.');
        }

        echo json_encode(semarak_save_report_notes($payload));
        exit;
    }

    if ($method === 'POST' && $action === 'anak_store') {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload) || empty($payload)) {
            $payload = $_POST;
        }

        echo json_encode(semarak_store_anak($payload));
        exit;
    }

    if ($method === 'POST' && $action === 'anak_update') {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload) || empty($payload)) {
            $payload = $_POST;
        }

        $id = trim((string) ($payload['id'] ?? ''));
        if ($id === '') {
            throw new InvalidArgumentException('ID anak wajib diisi saat update.');
        }

        echo json_encode(semarak_update_anak($id, $payload));
        exit;
    }

    if ($method === 'POST' && $action === 'anak_delete') {
        $id = trim((string) ($_GET['id'] ?? $_POST['id'] ?? ''));
        if ($id === '') {
            throw new InvalidArgumentException('ID anak wajib diisi.');
        }

        echo json_encode(semarak_delete_anak($id));
        exit;
    }

    if ($action === 'dashboard') {
        echo json_encode(semarak_get_dashboard_summary());
        exit;
    }

    if ($action === 'anak' || $action === 'anak_list') {
        echo json_encode(semarak_get_anak());
        exit;
    }

    if ($action === 'anak_detail') {
        $id = trim((string) ($_GET['id'] ?? ''));
        $list = semarak_get_anak();
        $data = null;
        foreach ($list as $item) {
            if (($item['id'] ?? '') === $id) {
                $data = $item;
                break;
            }
        }

        echo json_encode($data ?? ['success' => false, 'message' => 'Data anak tidak ditemukan.']);
        exit;
    }

    if ($action === 'kegiatan') {
        echo json_encode(semarak_get_kegiatan());
        exit;
    }

    if ($action === 'evaluasi') {
        $tanggal = (string) ($_GET['tanggal'] ?? date('Y-m-d'));
        $parsedDate = DateTime::createFromFormat('Y-m-d', $tanggal);
        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $tanggal) {
            throw new InvalidArgumentException('Format tanggal tidak valid.');
        }

        echo json_encode(semarak_get_evaluasi_by_tanggal($tanggal));
        exit;
    }

    if ($action === 'settings') {
        echo json_encode(semarak_get_settings());
        exit;
    }

    if ($action === 'summary') {
        $anakId = $_GET['id'] ?? semarak_get_anak()[0]['id'] ?? '';
        echo json_encode(semarak_get_summary_by_anak(
            (string) $anakId,
            (string) ($_GET['mulai'] ?? ''),
            (string) ($_GET['sampai'] ?? '')
        ));
        exit;
    }

    if ($action === 'report') {
        $anakId = $_GET['id'] ?? semarak_get_anak()[0]['id'] ?? '';
        echo json_encode(semarak_get_report_data(
            (string) $anakId,
            (string) ($_GET['mulai'] ?? ''),
            (string) ($_GET['sampai'] ?? '')
        ));
        exit;
    }

    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Endpoint tidak ditemukan']);
} catch (Throwable $e) {
    http_response_code($e instanceof InvalidArgumentException ? 400 : 500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
