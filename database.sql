-- ============================================================
-- Warung Kelontong POS — Skema & Data Awal
-- Import melalui phpMyAdmin setelah membuat database 'warung_kelontong'
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS kategori (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS produk (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  kategori_id INT UNSIGNED NULL,
  satuan VARCHAR(20) NOT NULL DEFAULT 'pcs',
  barcode VARCHAR(64) NULL DEFAULT NULL,
  harga_beli DECIMAL(12,2) NOT NULL DEFAULT 0,
  harga_jual DECIMAL(12,2) NOT NULL DEFAULT 0,
  stok INT NOT NULL DEFAULT 0,
  stok_minimal INT NOT NULL DEFAULT 5,
  is_new TINYINT(1) NOT NULL DEFAULT 0,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_nama (nama),
  KEY idx_kategori (kategori_id),
  UNIQUE KEY idx_barcode (barcode),
  CONSTRAINT fk_produk_kategori FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transaksi (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(24) NOT NULL UNIQUE,
  tanggal DATETIME NOT NULL,
  subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
  diskon DECIMAL(14,2) NOT NULL DEFAULT 0,
  total DECIMAL(14,2) NOT NULL DEFAULT 0,
  bayar DECIMAL(14,2) NOT NULL DEFAULT 0,
  kembalian DECIMAL(14,2) NOT NULL DEFAULT 0,
  metode VARCHAR(16) NOT NULL DEFAULT 'Tunai',
  laba DECIMAL(14,2) NOT NULL DEFAULT 0,
  kasir VARCHAR(60) NOT NULL DEFAULT 'Kasir',
  catatan VARCHAR(255) NULL,
  KEY idx_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transaksi_detail (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  transaksi_id INT UNSIGNED NOT NULL,
  produk_id INT UNSIGNED NULL,
  nama_produk VARCHAR(150) NOT NULL,
  satuan VARCHAR(20) NOT NULL DEFAULT 'pcs',
  harga_beli DECIMAL(12,2) NOT NULL DEFAULT 0,
  harga_jual DECIMAL(12,2) NOT NULL DEFAULT 0,
  qty INT NOT NULL DEFAULT 0,
  subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
  laba DECIMAL(12,2) NOT NULL DEFAULT 0,
  KEY idx_transaksi (transaksi_id),
  KEY idx_produk (produk_id),
  CONSTRAINT fk_detail_transaksi FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
  CONSTRAINT fk_detail_produk FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS import_log (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_file VARCHAR(200) NOT NULL,
  total_row INT NOT NULL DEFAULT 0,
  jumlah_baru INT NOT NULL DEFAULT 0,
  jumlah_update INT NOT NULL DEFAULT 0,
  jumlah_gagal INT NOT NULL DEFAULT 0,
  dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pengaturan (
  kunci VARCHAR(60) PRIMARY KEY,
  nilai TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;

-- Kategori
INSERT INTO kategori (nama) VALUES ('Sembako');
INSERT INTO kategori (nama) VALUES ('Minuman');
INSERT INTO kategori (nama) VALUES ('Snack');
INSERT INTO kategori (nama) VALUES ('Rokok');
INSERT INTO kategori (nama) VALUES ('Lainnya');

-- Produk
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Beras Pandan Wangi 5kg', 1, 'sak', NULL, 65000, 72000, 12);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Beras Ramos 5kg', 1, 'sak', NULL, 58000, 65000, 15);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Minyak Goreng 2x1L', 1, 'pouch', '8992753100123', 34000, 39000, 8);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Gula Pasir 1kg', 1, 'kg', NULL, 15500, 18000, 20);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Telur Ayam 1kg', 1, 'kg', NULL, 26000, 29500, 18);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Kecap Manis Bango 135ml', 1, 'botol', '8991002104105', 17500, 21000, 6);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Tepung Segitiga Biru 1kg', 1, 'kg', '8991002102345', 12000, 14500, 10);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Indomie Goreng', 1, 'pcs', '8991002101234', 2800, 3500, 72);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Aqua 600ml', 2, 'botol', '8992696403018', 2800, 3500, 48);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Teh Pucuk Harum 350ml', 2, 'botol', '8993175630012', 3000, 4000, 36);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Kopi Kapal Api Special', 2, 'sachet', '8991001110034', 1500, 2000, 60);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Susu Ultra Milk Coklat 250ml', 2, 'kotak', '8998009010123', 5500, 7000, 24);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Le Minerale 1500ml', 2, 'botol', '8992696412027', 5000, 6500, 20);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Chitato Sapi Panggang 68g', 3, 'pcs', '8991102000123', 8500, 11000, 15);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Oreo Vanilla 133g', 3, 'pcs', '8992696407016', 7500, 9500, 18);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('SilverQueen Chunky Bar', 3, 'pcs', '8993175203011', 14000, 18000, 9);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Beng-Beng', 3, 'pcs', '8993175200119', 1500, 2000, 50);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Tango Wafer Coklat', 3, 'pcs', '8993175200140', 8000, 10000, 14);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Sampoerna Mild 12', 4, 'bks', '8992821110123', 27000, 30500, 11);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Gudang Garam Surya 12', 4, 'bks', '8992821120123', 30000, 33500, 9);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Marlboro Red 20', 4, 'bks', '7622300861234', 32000, 36500, 7);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Djarum Super 12', 4, 'bks', '8992821140123', 28500, 31500, 10);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Sabun Lifebuoy 250ml', 5, 'botol', '8999999101019', 14000, 17500, 12);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Deterjen Rinso 800g', 5, 'pcs', '8999999520123', 18000, 22500, 10);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Pasta Gigi Pepsodent 190g', 5, 'pcs', '8999999010123', 13000, 16500, 11);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Baterai ABC AA (2 pcs)', 5, 'pack', '8999999300123', 7000, 9000, 20);
INSERT INTO produk (nama, kategori_id, satuan, barcode, harga_beli, harga_jual, stok) VALUES ('Gas LPG 3kg', 5, 'tabung', NULL, 18000, 22000, 6);

-- Pengaturan
INSERT INTO pengaturan (kunci, nilai) VALUES ('toko_nama', 'Warung Beras Jaya');
INSERT INTO pengaturan (kunci, nilai) VALUES ('toko_alamat', 'Jl. Merdeka No. 123, Jakarta');
INSERT INTO pengaturan (kunci, nilai) VALUES ('toko_telp', '0812-3456-7890');
INSERT INTO pengaturan (kunci, nilai) VALUES ('struk_footer', 'Terima kasih telah berbelanja!');
INSERT INTO pengaturan (kunci, nilai) VALUES ('kasir_nama', 'Bu Rina');
INSERT INTO pengaturan (kunci, nilai) VALUES ('printer_nama', 'POS-58 Thermal');
INSERT INTO pengaturan (kunci, nilai) VALUES ('printer_lebar', '58');
INSERT INTO pengaturan (kunci, nilai) VALUES ('auto_backup', '0');
INSERT INTO pengaturan (kunci, nilai) VALUES ('backup_terakhir', '');

-- Transaksi contoh 7 hari terakhir (tanggal relatif terhadap waktu import)
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 1 HOUR, '%Y%m%d'), '-0001'), NOW() - INTERVAL 1 HOUR, 21000, 0, 21000, 21000, 0, 'Tunai', 3500, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 1 HOUR, '%Y%m%d'), '-0001')), 6, 'Kecap Manis Bango 135ml', 'botol', 17500, 21000, 1, 21000, 3500);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 2 HOUR, '%Y%m%d'), '-0002'), NOW() - INTERVAL 2 HOUR, 9000, 0, 9000, 9000, 0, 'Tunai', 1900, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 2 HOUR, '%Y%m%d'), '-0002')), 9, 'Aqua 600ml', 'botol', 2800, 3500, 2, 7000, 1400);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 2 HOUR, '%Y%m%d'), '-0002')), 11, 'Kopi Kapal Api Special', 'sachet', 1500, 2000, 1, 2000, 500);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 3 HOUR, '%Y%m%d'), '-0003'), NOW() - INTERVAL 3 HOUR, 25500, 0, 25500, 50000, 24500, 'Tunai', 5500, 'Andi');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 3 HOUR, '%Y%m%d'), '-0003')), 8, 'Indomie Goreng', 'pcs', 2800, 3500, 5, 17500, 3500);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 3 HOUR, '%Y%m%d'), '-0003')), 10, 'Teh Pucuk Harum 350ml', 'botol', 3000, 4000, 2, 8000, 2000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 5 HOUR, '%Y%m%d'), '-0004'), NOW() - INTERVAL 5 HOUR, 72000, 0, 72000, 72000, 0, 'Tunai', 7000, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 5 HOUR, '%Y%m%d'), '-0004')), 1, 'Beras Pandan Wangi 5kg', 'sak', 65000, 72000, 1, 72000, 7000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 7 HOUR, '%Y%m%d'), '-0005'), NOW() - INTERVAL 7 HOUR, 34000, 0, 34000, 50000, 16000, 'Tunai', 4200, 'Andi');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 7 HOUR, '%Y%m%d'), '-0005')), 19, 'Sampoerna Mild 12', 'bks', 27000, 30500, 1, 30500, 3500);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 7 HOUR, '%Y%m%d'), '-0005')), 9, 'Aqua 600ml', 'botol', 2800, 3500, 1, 3500, 700);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 9 HOUR, '%Y%m%d'), '-0006'), NOW() - INTERVAL 9 HOUR, 78000, 0, 78000, 78000, 0, 'QRIS', 10000, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 9 HOUR, '%Y%m%d'), '-0006')), 3, 'Minyak Goreng 2x1L', 'pouch', 34000, 39000, 2, 78000, 10000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 11 HOUR, '%Y%m%d'), '-0007'), NOW() - INTERVAL 11 HOUR, 15500, 0, 15500, 20000, 4500, 'Tunai', 3500, 'Andi');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 11 HOUR, '%Y%m%d'), '-0007')), 17, 'Beng-Beng', 'pcs', 1500, 2000, 3, 6000, 1500);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 11 HOUR, '%Y%m%d'), '-0007')), 15, 'Oreo Vanilla 133g', 'pcs', 7500, 9500, 1, 9500, 2000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 20 HOUR, '%Y%m%d'), '-0008'), NOW() - INTERVAL 20 HOUR, 65500, 0, 65500, 100000, 34500, 'Tunai', 8500, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 20 HOUR, '%Y%m%d'), '-0008')), 4, 'Gula Pasir 1kg', 'kg', 15500, 18000, 2, 36000, 5000);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 20 HOUR, '%Y%m%d'), '-0008')), 5, 'Telur Ayam 1kg', 'kg', 26000, 29500, 1, 29500, 3500);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 23 HOUR, '%Y%m%d'), '-0009'), NOW() - INTERVAL 23 HOUR, 25000, 0, 25000, 50000, 25000, 'Tunai', 5500, 'Andi');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 23 HOUR, '%Y%m%d'), '-0009')), 14, 'Chitato Sapi Panggang 68g', 'pcs', 8500, 11000, 1, 11000, 2500);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 23 HOUR, '%Y%m%d'), '-0009')), 12, 'Susu Ultra Milk Coklat 250ml', 'kotak', 5500, 7000, 2, 14000, 3000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 26 HOUR, '%Y%m%d'), '-0010'), NOW() - INTERVAL 26 HOUR, 35000, 0, 35000, 35000, 0, 'Tunai', 7000, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 26 HOUR, '%Y%m%d'), '-0010')), 8, 'Indomie Goreng', 'pcs', 2800, 3500, 10, 35000, 7000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 30 HOUR, '%Y%m%d'), '-0011'), NOW() - INTERVAL 30 HOUR, 33500, 0, 33500, 33500, 0, 'QRIS', 3500, 'Andi');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 30 HOUR, '%Y%m%d'), '-0011')), 20, 'Gudang Garam Surya 12', 'bks', 30000, 33500, 1, 33500, 3500);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 34 HOUR, '%Y%m%d'), '-0012'), NOW() - INTERVAL 34 HOUR, 34000, 0, 34000, 50000, 16000, 'Tunai', 7000, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 34 HOUR, '%Y%m%d'), '-0012')), 23, 'Sabun Lifebuoy 250ml', 'botol', 14000, 17500, 1, 17500, 3500);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 34 HOUR, '%Y%m%d'), '-0012')), 25, 'Pasta Gigi Pepsodent 190g', 'pcs', 13000, 16500, 1, 16500, 3500);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 46 HOUR, '%Y%m%d'), '-0013'), NOW() - INTERVAL 46 HOUR, 104000, 0, 104000, 200000, 96000, 'Tunai', 12000, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 46 HOUR, '%Y%m%d'), '-0013')), 2, 'Beras Ramos 5kg', 'sak', 58000, 65000, 1, 65000, 7000);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 46 HOUR, '%Y%m%d'), '-0013')), 3, 'Minyak Goreng 2x1L', 'pouch', 34000, 39000, 1, 39000, 5000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 50 HOUR, '%Y%m%d'), '-0014'), NOW() - INTERVAL 50 HOUR, 17000, 0, 17000, 17000, 0, 'Tunai', 4000, 'Andi');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 50 HOUR, '%Y%m%d'), '-0014')), 13, 'Le Minerale 1500ml', 'botol', 5000, 6500, 2, 13000, 3000);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 50 HOUR, '%Y%m%d'), '-0014')), 17, 'Beng-Beng', 'pcs', 1500, 2000, 2, 4000, 1000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 54 HOUR, '%Y%m%d'), '-0015'), NOW() - INTERVAL 54 HOUR, 31500, 0, 31500, 50000, 18500, 'Tunai', 6500, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 54 HOUR, '%Y%m%d'), '-0015')), 24, 'Deterjen Rinso 800g', 'pcs', 18000, 22500, 1, 22500, 4500);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 54 HOUR, '%Y%m%d'), '-0015')), 26, 'Baterai ABC AA (2 pcs)', 'pack', 7000, 9000, 1, 9000, 2000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 58 HOUR, '%Y%m%d'), '-0016'), NOW() - INTERVAL 58 HOUR, 43500, 0, 43500, 43500, 0, 'Tunai', 5900, 'Andi');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 58 HOUR, '%Y%m%d'), '-0016')), 21, 'Marlboro Red 20', 'bks', 32000, 36500, 1, 36500, 4500);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 58 HOUR, '%Y%m%d'), '-0016')), 9, 'Aqua 600ml', 'botol', 2800, 3500, 2, 7000, 1400);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 70 HOUR, '%Y%m%d'), '-0017'), NOW() - INTERVAL 70 HOUR, 73500, 0, 73500, 100000, 26500, 'Tunai', 9500, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 70 HOUR, '%Y%m%d'), '-0017')), 5, 'Telur Ayam 1kg', 'kg', 26000, 29500, 2, 59000, 7000);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 70 HOUR, '%Y%m%d'), '-0017')), 7, 'Tepung Segitiga Biru 1kg', 'kg', 12000, 14500, 1, 14500, 2500);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 75 HOUR, '%Y%m%d'), '-0018'), NOW() - INTERVAL 75 HOUR, 10000, 0, 10000, 10000, 0, 'Tunai', 2500, 'Andi');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 75 HOUR, '%Y%m%d'), '-0018')), 11, 'Kopi Kapal Api Special', 'sachet', 1500, 2000, 5, 10000, 2500);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 80 HOUR, '%Y%m%d'), '-0019'), NOW() - INTERVAL 80 HOUR, 28000, 0, 28000, 50000, 22000, 'Tunai', 6000, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 80 HOUR, '%Y%m%d'), '-0019')), 16, 'SilverQueen Chunky Bar', 'pcs', 14000, 18000, 1, 18000, 4000);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 80 HOUR, '%Y%m%d'), '-0019')), 18, 'Tango Wafer Coklat', 'pcs', 8000, 10000, 1, 10000, 2000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 94 HOUR, '%Y%m%d'), '-0020'), NOW() - INTERVAL 94 HOUR, 61000, 0, 61000, 61000, 0, 'QRIS', 7000, 'Andi');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 94 HOUR, '%Y%m%d'), '-0020')), 19, 'Sampoerna Mild 12', 'bks', 27000, 30500, 2, 61000, 7000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 100 HOUR, '%Y%m%d'), '-0021'), NOW() - INTERVAL 100 HOUR, 25500, 0, 25500, 25500, 0, 'Tunai', 4700, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 100 HOUR, '%Y%m%d'), '-0021')), 27, 'Gas LPG 3kg', 'tabung', 18000, 22000, 1, 22000, 4000);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 100 HOUR, '%Y%m%d'), '-0021')), 9, 'Aqua 600ml', 'botol', 2800, 3500, 1, 3500, 700);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 118 HOUR, '%Y%m%d'), '-0022'), NOW() - INTERVAL 118 HOUR, 39500, 0, 39500, 50000, 10500, 'Tunai', 5000, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 118 HOUR, '%Y%m%d'), '-0022')), 22, 'Djarum Super 12', 'bks', 28500, 31500, 1, 31500, 3000);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 118 HOUR, '%Y%m%d'), '-0022')), 10, 'Teh Pucuk Harum 350ml', 'botol', 3000, 4000, 2, 8000, 2000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 130 HOUR, '%Y%m%d'), '-0023'), NOW() - INTERVAL 130 HOUR, 144000, 0, 144000, 200000, 56000, 'Tunai', 14000, 'Andi');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 130 HOUR, '%Y%m%d'), '-0023')), 1, 'Beras Pandan Wangi 5kg', 'sak', 65000, 72000, 2, 144000, 14000);
INSERT INTO transaksi (kode, tanggal, subtotal, diskon, total, bayar, kembalian, metode, laba, kasir) VALUES (CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 142 HOUR, '%Y%m%d'), '-0024'), NOW() - INTERVAL 142 HOUR, 37000, 0, 37000, 37000, 0, 'Tunai', 8000, 'Bu Rina');
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 142 HOUR, '%Y%m%d'), '-0024')), 15, 'Oreo Vanilla 133g', 'pcs', 7500, 9500, 2, 19000, 4000);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 142 HOUR, '%Y%m%d'), '-0024')), 14, 'Chitato Sapi Panggang 68g', 'pcs', 8500, 11000, 1, 11000, 2500);
INSERT INTO transaksi_detail (transaksi_id, produk_id, nama_produk, satuan, harga_beli, harga_jual, qty, subtotal, laba) VALUES ((SELECT id FROM transaksi WHERE kode = CONCAT('TRX-', DATE_FORMAT(NOW() - INTERVAL 142 HOUR, '%Y%m%d'), '-0024')), 12, 'Susu Ultra Milk Coklat 250ml', 'kotak', 5500, 7000, 1, 7000, 1500);
