<?php
/**
 * ============================================================
 *  WARUNG KELONTONG POS — Konfigurasi Database
 * ============================================================
 *  Sesuaikan kredensial di bawah dengan server Anda.
 *  Default XAMPP  : host localhost, user root, password kosong.
 * ============================================================
 */

// --- Koneksi database ---------------------------------------
// Nilai default di bawah cocok untuk XAMPP.
// Bisa ditimpa lewat environment: WK_DB_HOST, WK_DB_PORT, WK_DB_NAME, WK_DB_USER, WK_DB_PASS
define('DB_HOST', getenv('WK_DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('WK_DB_PORT') ?: '3306');
define('DB_NAME', getenv('WK_DB_NAME') ?: 'warung_kelontong');
define('DB_USER', getenv('WK_DB_USER') ?: 'root');
define('DB_PASS', getenv('WK_DB_PASS') !== false ? getenv('WK_DB_PASS') : '');

// --- Identitas aplikasi --------------------------------------
define('APP_NAME', 'Warung Kelontong');
define('APP_VERSION', '1.0.0');

date_default_timezone_set('Asia/Jakarta');

define('BASE_PATH', __DIR__);
define('EXPORT_DIR', BASE_PATH . '/exports');
define('TMP_DIR', EXPORT_DIR . '/tmp');

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/layout.php';

/**
 * Koneksi PDO utama (dengan pemilihan database).
 * Jika database/tabel belum ada, pengunjung diarahkan ke install.php.
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $isApi = strpos($_SERVER['SCRIPT_NAME'] ?? '', '/api/') !== false;

    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
        $pdo->query('SELECT 1 FROM pengaturan LIMIT 1');
        ensure_schema($pdo);
    } catch (PDOException $e) {
        $noDb = strpos((string)$e->getMessage(), '1049') !== false; // Unknown database
        if ($isApi) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(503);
            echo json_encode(['ok' => false, 'error' => 'Database belum terpasang. Buka install.php terlebih dahulu.']);
            exit;
        }
        header('Location: install.php' . ($noDb ? '?buat_db=1' : ''));
        exit;
    }

    return $pdo;
}

/**
 * Koneksi PDO tanpa memilih database (dipakai installer).
 */
function root_pdo(): PDO
{
    return new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}
