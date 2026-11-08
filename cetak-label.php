<?php
/**
 * Cetak Label Barcode
 * Dibuka dari halaman Produk -> tombol "Cetak Label".
 * Ukuran tersedia:
 *  - thermal58 : strip printer thermal 58 x 25 mm (1 label = 1 halaman)
 *  - a4_24     : kertas A4, 24 label per lembar (3 x 8, sel 66 x 35 mm)
 *  - a4_40     : kertas A4, 40 label per lembar (4 x 10, sel 48 x 25 mm)
 * Label berisi: nama barang, harga jual, barcode Code128, dan kode digitnya.
 */
require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/lib/barcode.php';

$UKURAN = [
    'thermal58' => ['judul' => 'Thermal 58 x 25 mm'],
    'a4_24'     => ['judul' => 'A4 66 x 35 mm (24/lembar)'],
    'a4_40'     => ['judul' => 'A4 48 x 25 mm (40/lembar)'],
];

$ukuran = $_POST['ukuran'] ?? 'thermal58';
if (!isset($UKURAN[$ukuran])) {
    $ukuran = 'thermal58';
}

// ---- Ambil daftar id dari POST (JSON array atau dipisah koma) ----
$raw = trim((string)($_POST['ids'] ?? ''));
$ids = [];
if ($raw !== '') {
    $coba = json_decode($raw, true);
    if (is_array($coba)) {
        $ids = $coba;
    } else {
        $ids = explode(',', $raw);
    }
    $ids = array_values(array_unique(array_map('intval', $ids)));
    $ids = array_filter($ids, function ($v) { return $v > 0; });
    $ids = array_slice($ids, 0, 300);
}

// ---- Jumlah label per barang ----
$copy = max(1, min(100, (int)($_POST['copy'] ?? 1)));

$s = all_settings();

// ---- Ambil produk: hanya yang aktif dan punya barcode ----
$labels = [];
$skipTanpaBc = [];
if ($ids) {
    $pdo = db();
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = $pdo->prepare("SELECT nama, barcode, harga_jual, satuan
                         FROM produk WHERE aktif = 1 AND id IN ($in) ORDER BY nama");
    $st->execute($ids);
    foreach ($st->fetchAll() as $p) {
        if ($p['barcode'] === null || trim($p['barcode']) === '') {
            $skipTanpaBc[] = $p['nama'];
            continue;
        }
        for ($i = 0; $i < $copy; $i++) {
            $labels[] = $p;
        }
    }
}

// Batas aman: maksimal 600 label sekali cetak
$labels = array_slice($labels, 0, 600);

/** Lebar maksimum barcode (mm) per ukuran */
function bc_max_w(string $u): float
{
    if ($u === 'thermal58') return 50.0;
    if ($u === 'a4_24') return 58.0;
    return 40.0; // a4_40
}

/** Tinggi garis barcode (mm) per ukuran */
function bc_h(string $u): float
{
    if ($u === 'thermal58') return 7.2;
    if ($u === 'a4_24') return 10.0;
    return 6.4;
}

/** Satu label utuh (nama + harga + barcode + kode) */
function label_html(array $p, string $u): string
{
    $kode = trim((string)$p['barcode']);
    $bc = barcode_svg($kode, bc_h($u), bc_max_w($u));
    $harga = 'Rp ' . number_format((float)$p['harga_jual'], 0, ',', '.');
    $sat = ($p['satuan'] && $p['satuan'] !== 'pcs') ? '/' . e($p['satuan']) : '';
    $cls = 'lab ' . $u;

    return '<div class="' . $cls . '">'
        . '<div class="lab-nama">' . e($p['nama']) . '</div>'
        . '<div class="lab-harga">' . $harga . '<span>' . $sat . '</span></div>'
        . '<div class="lab-bc">' . $bc . '</div>'
        . '<div class="lab-kode">' . e($kode) . '</div>'
        . '</div>';
}

// Kelompokkan label per halaman untuk mode A4
$perPage = $ukuran === 'a4_24' ? 24 : ($ukuran === 'a4_40' ? 40 : 1);
$pages = array_chunk($labels, $perPage);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak Label Barcode &mdash; <?= e($s['toko_nama']) ?></title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: -apple-system, "SF Pro Text", "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: #000;
        background: #E8E8ED;
        padding: 66px 12px 60px;
    }

    /* ---------- Toolbar layar (tidak ikut tercetak) ---------- */
    .toolbar {
        position: fixed; top: 14px; left: 50%; transform: translateX(-50%);
        display: flex; gap: 8px; z-index: 10;
    }
    .toolbar button {
        font-family: inherit; font-size: 13px; font-weight: 700;
        padding: 9px 18px; border-radius: 10px; border: none; cursor: pointer;
        background: #000; color: #fff;
    }
    .toolbar button.ghost { background: #fff; color: #000; border: 1.4px solid #000; }

    .info {
        max-width: 900px; margin: 0 auto 14px;
        font-size: 12.5px; color: #555; text-align: center; line-height: 1.5;
    }
    .info b { color: #000; }
    .peringatan {
        max-width: 900px; margin: 0 auto 14px;
        background: #FFF3E0; border: 1px solid #E6A23C; color: #8a5300;
        border-radius: 10px; padding: 9px 12px; font-size: 12.5px; line-height: 1.5;
    }

    /* ---------- Kertas & label ---------- */
    .paper {
        width: 210mm; min-height: 100mm;
        margin: 0 auto 18px;
        background: #fff;
        box-shadow: 0 6px 30px rgba(0, 0, 0, .18);
        padding: 6mm 4mm;
    }
    .paper.thermal { width: 58mm; padding: 0; min-height: 0; }

    .grid24 { display: flex; flex-wrap: wrap; gap: 1.5mm 1.5mm; }
    .grid40 { display: flex; flex-wrap: wrap; gap: .5mm 1mm; }

    /* --- Sel label --- */
    .lab {
        border: .3px dashed #B9B9C0;   /* garis bantu layar, hilang saat dicetak */
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        text-align: center; overflow: hidden;
    }
    .lab.thermal58 { width: 58mm; height: 25mm; padding: 2mm 3mm; page-break-after: always; }
    .lab.thermal58:last-child { page-break-after: auto; }
    .lab.a4_24 { width: 66mm; height: 35mm; padding: 2mm 2.5mm; }
    .lab.a4_40 { width: 48mm; height: 25mm; padding: 1.5mm 2mm; }

    .lab-nama {
        font-weight: 700; line-height: 1.15;
        max-width: 100%;
        overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    }
    .lab.thermal58 .lab-nama { font-size: 8.2px; -webkit-line-clamp: 1; white-space: nowrap; text-overflow: ellipsis; }
    .lab.a4_24 .lab-nama { font-size: 10.5px; }
    .lab.a4_40 .lab-nama { font-size: 8px; }

    .lab-harga { font-weight: 800; letter-spacing: -.2px; }
    .lab-harga span { font-weight: 600; font-size: .72em; }
    .lab.thermal58 .lab-harga { font-size: 11.5px; margin: .6mm 0 .8mm; }
    .lab.a4_24 .lab-harga { font-size: 13px; margin: .8mm 0 1.2mm; }
    .lab.a4_40 .lab-harga { font-size: 9.5px; margin: .4mm 0 .6mm; }

    .lab-bc { display: flex; justify-content: center; align-items: flex-end; }
    .lab-bc svg { display: block; }

    .lab-kode { font-family: ui-monospace, Menlo, monospace; color: #000; }
    .lab.thermal58 .lab-kode { font-size: 5.8px; letter-spacing: .9px; margin-top: .5mm; }
    .lab.a4_24 .lab-kode { font-size: 7px; letter-spacing: 1.1px; margin-top: .7mm; }
    .lab.a4_40 .lab-kode { font-size: 5.2px; letter-spacing: .7px; margin-top: .4mm; }

    .kosong {
        max-width: 900px; margin: 40px auto; text-align: center;
        font-size: 14px; color: #555; line-height: 1.7;
    }
    .kosong b { color: #000; }

    /* Pratinjau di layar kecil: kecilkan agar muat (cetak tetap ukuran asli) */
    @media screen and (max-width: 860px) {
        body { padding: 66px 8px 40px; }
        .paper { zoom: .46; margin-bottom: 8px; }
    }

    /* ---------- Print ---------- */
    <?php if ($ukuran === 'thermal58'): ?>
    @page { size: 58mm 25mm; margin: 0; }
    <?php else: ?>
    @page { size: A4; margin: 6mm 4mm; }
    <?php endif; ?>
    @media print {
        body { background: #fff; padding: 0; }
        .toolbar, .info, .peringatan { display: none !important; }
        .paper { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
        .lab { border: none; }
        .lab.a4_24 { break-inside: avoid; }
        .lab.a4_40 { break-inside: avoid; }
    }
</style>
</head>
<body>

<div class="toolbar">
    <button onclick="window.print()">Cetak</button>
    <button class="ghost" onclick="window.close()">Tutup</button>
</div>

<?php if (!$labels): ?>
    <div class="kosong">
        <b>Tidak ada label untuk dicetak.</b><br>
        Pilih barang yang sudah memiliki barcode di halaman Produk,<br>
        atau isi barcode barang terlebih dahulu.
    </div>
<?php else: ?>
    <p class="info">
        <b><?= count($labels) ?> label</b> &middot; <?= $copy ?> label / barang &middot;
        <?= e($UKURAN[$ukuran]['judul']) ?> &middot; <?= e($s['toko_nama']) ?>
    </p>
    <?php if ($skipTanpaBc): ?>
    <div class="peringatan">
        Dilewati karena belum punya barcode: <b><?= e(implode(', ', array_slice($skipTanpaBc, 0, 6))) ?></b><?= count($skipTanpaBc) > 6 ? ' dan ' . (count($skipTanpaBc) - 6) . ' lainnya' : '' ?>.
    </div>
    <?php endif; ?>

    <?php foreach ($pages as $pg): ?>
    <div class="paper<?= $ukuran === 'thermal58' ? ' thermal' : '' ?>">
        <?php if ($perPage > 1): ?><div class="grid<?= $perPage ?>"><?php endif; ?>
        <?php foreach ($pg as $p) { echo label_html($p, $ukuran); } ?>
        <?php if ($perPage > 1): ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
// Dialog cetak muncul otomatis saat halaman siap
window.addEventListener('load', function () {
    setTimeout(function () { window.print(); }, 350);
});
</script>
</body>
</html>
