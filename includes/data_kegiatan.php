<?php
require_once __DIR__ . '/backend.php';

semarak_init_schema();
$daftar_kegiatan = semarak_get_kegiatan();
$daftar_anak = array_map(static function ($anak) {
    return $anak['nama'] ?? '';
}, semarak_get_anak());
