CREATE DATABASE IF NOT EXISTS lost_found CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lost_found;

DROP TABLE IF EXISTS laporan;
CREATE TABLE laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jenis ENUM('hilang', 'temuan') NOT NULL,
    nama_barang VARCHAR(80) NOT NULL,
    kategori VARCHAR(40) NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    tanggal DATE NOT NULL,
    deskripsi TEXT,
    kontak VARCHAR(80) NOT NULL,
    status ENUM('terbuka', 'dikembalikan') NOT NULL DEFAULT 'terbuka',
    nama_pengambil VARCHAR(60) NULL,
    kontak_pengambil VARCHAR(80) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO laporan (jenis, nama_barang, kategori, lokasi, tanggal, deskripsi, kontak) VALUES
('hilang', 'Dompet kulit cokelat', 'Dompet dan tas', 'Kantin gedung B', CURDATE() - INTERVAL 2 DAY, 'Isi KTM dan kartu ATM.', '0812-0000-0001'),
('temuan', 'Kunci motor Honda', 'Kunci', 'Parkiran perpustakaan', CURDATE() - INTERVAL 1 DAY, 'Gantungan kucing warna oranye.', '0812-0000-0002'),
('temuan', 'Earphone putih', 'Elektronik', 'Ruang 3.02', CURDATE() - INTERVAL 4 DAY, 'Case retak di pojok.', 'budi@example.com');
