CREATE DATABASE IF NOT EXISTS semarak CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE semarak;

CREATE TABLE IF NOT EXISTS anak (
    id VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    jenis_kelamin VARCHAR(20) NOT NULL,
    asal_daerah VARCHAR(100) NOT NULL,
    agama VARCHAR(50) NOT NULL,
    umur INT NOT NULL,
    angkatan VARCHAR(50) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kegiatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS evaluasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anak_id VARCHAR(20) NOT NULL,
    kegiatan_id INT NOT NULL,
    tanggal DATE NOT NULL,
    nilai ENUM('sesuai','arahan','telat','tidak','libur') NOT NULL,
    FOREIGN KEY (anak_id) REFERENCES anak(id) ON DELETE CASCADE,
    FOREIGN KEY (kegiatan_id) REFERENCES kegiatan(id) ON DELETE CASCADE,
    UNIQUE KEY unique_eval (anak_id, kegiatan_id, tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS jadwal_kegiatan (
    kegiatan_id INT PRIMARY KEY,
    aturan ENUM('setiap_hari','hari_kerja','hari_tertentu','multi_rentang') NOT NULL DEFAULT 'setiap_hari',
    hari_json TEXT NULL,
    rentang_json TEXT NULL,
    FOREIGN KEY (kegiatan_id) REFERENCES kegiatan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS masa_pembinaan (
    angkatan VARCHAR(50) PRIMARY KEY,
    tanggal_selesai DATE NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS catatan_rapor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anak_id VARCHAR(20) NOT NULL,
    periode_mulai DATE NOT NULL,
    periode_selesai DATE NOT NULL,
    catatan TEXT NOT NULL DEFAULT '',
    rekomendasi TEXT NOT NULL DEFAULT '',
    UNIQUE KEY unique_rapor (anak_id, periode_mulai, periode_selesai),
    FOREIGN KEY (anak_id) REFERENCES anak(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO anak (id, nama, jenis_kelamin, asal_daerah, agama, umur, angkatan, status)
VALUES
('AN-001', 'Endang Sri Ayu', 'Perempuan', 'Bandung', 'Islam', 16, 'Angkatan II', 'Aktif'),
('AN-002', 'Doddy', 'Laki-laki', 'Bogor', 'Islam', 15, 'Angkatan II', 'Aktif'),
('AN-003', 'Rohmah Hidayah', 'Perempuan', 'Garut', 'Islam', 17, 'Angkatan II', 'Aktif'),
('AN-004', 'Isti Komah Insani', 'Perempuan', 'Cianjur', 'Islam', 16, 'Angkatan II', 'Aktif'),
('AN-005', 'Cece', 'Laki-laki', 'Sukabumi', 'Islam', 15, 'Angkatan II', 'Aktif')
ON DUPLICATE KEY UPDATE nama=VALUES(nama), jenis_kelamin=VALUES(jenis_kelamin), asal_daerah=VALUES(asal_daerah), agama=VALUES(agama), umur=VALUES(umur), angkatan=VALUES(angkatan), status=VALUES(status);

INSERT INTO kegiatan (nama)
VALUES
('Aku Bangun Pagi Sendiri Jam 04.30'),
('Aku Shalat Shubuh Jam 04.30 di Masjid'),
('Aku Mengikuti Kegiatan Olahraga Jam 05.30 s.d. 06.15'),
('Aku Mengikuti Kegiatan Mandi Pagi Jam 06.15 s.d. 06.45'),
('Aku Mengikuti Kegiatan Makan Pagi Jam 06.45 s.d. 07.15'),
('Aku Membersihkan Tempat Tidur, Menyapu dan Mengepel Asrama Jam 07.15 s.d. 08.00'),
('Aku Mengikuti Kegiatan Bimbingan Keterampilan Jam 08.00 s.d. 11.00'),
('Aku Mengikuti Kegiatan Bimbingan Kedisiplinan Jam 08.00 s.d. 11.00'),
('Aku Mengikuti Kegiatan Bimbingan Batik Ecoprint Jam 08.00 s.d. 12.00'),
('Aku Mengikuti Kegiatan Bimbingan Sosial I Jam 11.00 s.d. 12.00'),
('Aku Shalat Dzuhur Jam 12.00 di Masjid'),
('Aku Mengikuti Kegiatan Makan Siang Jam 12.30 s.d. 13.00'),
('Aku Mengikuti Kegiatan Bimbingan Sosial II Jam 13.00 s.d. 15.00'),
('Aku Mengikuti Kegiatan Bimbingan Farming Jam 13.00 s.d. 15.00'),
('Aku Shalat Ashar Jam 15.00 di Masjid'),
('Aku Mandi Sore Jam 15.30 s.d. 16.00'),
('Aku Mencuci Pakaian Kotor dan Menyetrika Pakaian Jam 16.00 s.d. 17.30'),
('Aku Shalat Maghrib Jam 17.45 di Masjid'),
('Aku Mengikuti Kegiatan Makan Malam Jam 18.00 s.d. 18.30'),
('Aku Shalat Isya\' Jam 19.00 di Masjid'),
('Aku Mengikuti Kegiatan Pengajian Jam 19.15 s.d. 20.00'),
('Aku Tidur Tepat Waktu di Kamar Tempat Tidurku Jam 21.00')
ON DUPLICATE KEY UPDATE nama=VALUES(nama);

INSERT INTO evaluasi (anak_id, kegiatan_id, tanggal, nilai)
VALUES
('AN-001', 1, '2026-09-24', 'sesuai'),
('AN-001', 2, '2026-09-24', 'sesuai'),
('AN-001', 3, '2026-09-24', 'arahan'),
('AN-001', 4, '2026-09-24', 'sesuai'),
('AN-001', 5, '2026-09-24', 'sesuai'),
('AN-001', 6, '2026-09-24', 'telat'),
('AN-001', 7, '2026-09-24', 'sesuai'),
('AN-001', 8, '2026-09-24', 'sesuai'),
('AN-002', 1, '2026-09-24', 'arahan'),
('AN-002', 2, '2026-09-24', 'sesuai'),
('AN-002', 3, '2026-09-24', 'sesuai'),
('AN-002', 4, '2026-09-24', 'arahan'),
('AN-002', 5, '2026-09-24', 'sesuai'),
('AN-003', 1, '2026-09-24', 'sesuai'),
('AN-003', 2, '2026-09-24', 'sesuai'),
('AN-003', 3, '2026-09-24', 'sesuai'),
('AN-003', 4, '2026-09-24', 'arahan'),
('AN-004', 1, '2026-09-24', 'sesuai'),
('AN-004', 2, '2026-09-24', 'arahan'),
('AN-004', 3, '2026-09-24', 'sesuai'),
('AN-005', 1, '2026-09-24', 'tidak'),
('AN-005', 2, '2026-09-24', 'arahan'),
('AN-005', 3, '2026-09-24', 'sesuai')
ON DUPLICATE KEY UPDATE nilai=VALUES(nilai);
