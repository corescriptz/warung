<?php
/**
 * CONTOH config.php  —  salin ke config.php lalu sesuaikan.
 *
 * Catatan: config.php TIDAK ada di zip yang dikirim, padahal semua halaman &
 * API memuatnya. Jika Anda sudah punya config.php sendiri, abaikan file ini;
 * cukup pastikan konstanta & fungsi di bawah tersedia.
 */

define('APP_VERSION', '2.0');
define('BASE_PATH', __DIR__);
define('EXPORT_DIR', BASE_PATH . '/exports');
define('TMP_DIR', EXPORT_DIR . '/tmp');

date_default_timezone_set('Asia/Jakarta');

require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/layout.php';

/** Koneksi PDO (singleton) */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=warung_kelontong;charset=utf8mb4',
            'root',   // user
            '',       // password
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
        ensure_schema($pdo);
    }
    return $pdo;
}
