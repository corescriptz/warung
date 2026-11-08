<?php
/**
 * Cetak Laporan Penjualan (A4)
 * Dibuka dari halaman Laporan -> tombol "Cetak".
 * Render sisi server, otomatis memanggil dialog cetak browser.
 */
require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/includes/laporan_data.php';

$pdo = db();
$d = laporan_data($pdo, $_GET['periode'] ?? 'hari');
$s = all_settings();
$r = $d['ringkasan'];

function rp_cetak(float $n): string
{
    $neg = $n < 0;
    return ($neg ? '-' : '') . 'Rp ' . number_format(abs((float)$n), 0, ',', '.');
}

/** Format singkat utk nilai grafik di cetak (salinan rpShort) */
function rpShort_chart(float $n): string
{
    if ($n >= 1000000) {
        $jt = $n / 1000000;
        return ($jt >= 10 ? round($jt) : rtrim(rtrim(number_format($jt, 1, ',', ''), '0'), ',')) . 'jt';
    }
    if ($n >= 1000) {
        return round($n / 1000) . 'rb';
    }
    return (string)round($n);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Penjualan <?= e($d['label']) ?> &mdash; <?= e($s['toko_nama']) ?></title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: -apple-system, "SF Pro Text", "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 12px;
        color: #000;
        background: #E8E8ED;
        padding: 24px 12px 60px;
    }
    .paper {
        width: 210mm;
        min-height: 260mm;
        margin: 0 auto;
        background: #fff;
        padding: 16mm 15mm 18mm;
        box-shadow: 0 6px 30px rgba(0,0,0,.18);
    }

    /* ---------- Kop ---------- */
    .kop { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2.5px solid #000; padding-bottom: 12px; }
    .kop-toko h1 { font-size: 21px; font-weight: 800; letter-spacing: -.3px; }
    .kop-toko p { font-size: 11px; color: #333; margin-top: 3px; line-height: 1.45; }
    .kop-judul { text-align: right; }
    .kop-judul h2 { font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px; }
    .kop-judul p { font-size: 11px; color: #333; margin-top: 3px; }

    /* ---------- Ringkasan ---------- */
    .ringkasan { display: flex; gap: 8px; margin-top: 16px; }
    .rk-box { flex: 1; border: 1.2px solid #000; border-radius: 8px; padding: 9px 10px; }
    .rk-box .lb { font-size: 9.5px; text-transform: uppercase; letter-spacing: .5px; color: #444; }
    .rk-box .vl { font-size: 15px; font-weight: 800; margin-top: 3px; }
    .rk-box .vl.small { font-size: 12.5px; }

    /* ---------- Grafik ---------- */
    h3.sec { font-size: 12.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .6px; margin: 18px 0 8px; }
    .grafik { border: 1.2px solid #000; border-radius: 8px; padding: 12px 12px 8px; }
    .g-bars { display: flex; align-items: flex-end; gap: 8px; height: 110px; }
    .g-col { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; }
    .g-val { font-size: 9px; color: #333; margin-bottom: 2px; }
    .g-bar { width: 68%; max-width: 46px; background: #000; border-radius: 4px 4px 0 0; min-height: 2px; }
    .g-col.today .g-bar { background: #555; outline: 1.2px solid #000; }
    .g-labels { display: flex; gap: 8px; border-top: 1px solid #999; margin-top: 6px; padding-top: 5px; }
    .g-labels span { flex: 1; text-align: center; font-size: 9.5px; color: #333; }
    .g-labels span.today { font-weight: 800; }

    /* ---------- Tabel ---------- */
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #999; padding: 5px 7px; font-size: 11px; text-align: left; vertical-align: top; }
    th { background: #EFEFF2; font-weight: 700; font-size: 10.5px; text-transform: uppercase; letter-spacing: .3px; }
    td.num, th.num { text-align: right; white-space: nowrap; }
    td.ctr, th.ctr { text-align: center; }
    tr.total td { background: #EFEFF2; font-weight: 800; border-top: 1.6px solid #000; }
    .kosong { text-align: center; color: #555; font-style: italic; padding: 14px !important; }

    /* ---------- Footer ---------- */
    .catat { display: flex; justify-content: space-between; margin-top: 34px; page-break-inside: avoid; }
    .catat .ttd { text-align: center; font-size: 11px; width: 200px; }
    .catat .ttd .sp { height: 52px; }
    .foot { margin-top: 26px; padding-top: 8px; border-top: 1px solid #999; display: flex; justify-content: space-between; font-size: 9.5px; color: #555; }

    /* ---------- Toolbar (tidak ikut tercetak) ---------- */
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

    /* ---------- Print ---------- */
    @page { size: A4; margin: 12mm 11mm; }
    @media print {
        body { background: #fff; padding: 0; }
        .paper { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
        .toolbar { display: none; }
        .g-col.today .g-bar { background: #000; }
        tr { page-break-inside: avoid; }
    }
</style>
</head>
<body>

<div class="toolbar">
    <button onclick="window.print()">Cetak</button>
    <button class="ghost" onclick="window.close()">Tutup</button>
</div>

<div class="paper">

    <!-- Kop laporan -->
    <div class="kop">
        <div class="kop-toko">
            <h1><?= e($s['toko_nama']) ?></h1>
            <?php if ($s['toko_alamat']): ?><p><?= nl2br(e($s['toko_alamat'])) ?></p><?php endif; ?>
            <?php if ($s['toko_telp']): ?><p>Telp. <?= e($s['toko_telp']) ?></p><?php endif; ?>
        </div>
        <div class="kop-judul">
            <h2>Laporan Penjualan</h2>
            <p>Periode: <b><?= e($d['label']) ?></b></p>
            <p><?= tanggal_indo(date('Y-m-d H:i:s'), true) ?></p>
        </div>
    </div>

    <!-- Ringkasan -->
    <div class="ringkasan">
        <div class="rk-box">
            <div class="lb">Omzet</div>
            <div class="vl"><?= rp_cetak($r['omzet']) ?></div>
        </div>
        <div class="rk-box">
            <div class="lb">Transaksi</div>
            <div class="vl"><?= number_format($r['jumlah'], 0, ',', '.') ?></div>
        </div>
        <div class="rk-box">
            <div class="lb">Laba</div>
            <div class="vl"><?= rp_cetak($r['laba']) ?></div>
        </div>
        <div class="rk-box">
            <div class="lb">Produk Terjual</div>
            <div class="vl"><?= number_format($r['produk'], 0, ',', '.') ?></div>
        </div>
        <div class="rk-box">
            <div class="lb">Rata-rata / Trx</div>
            <div class="vl small"><?= rp_cetak((float)$r['rata']) ?></div>
        </div>
    </div>

    <!-- Grafik 7 hari -->
    <h3 class="sec">Omzet 7 Hari Terakhir</h3>
    <div class="grafik">
        <div class="g-bars">
            <?php
            $max = 0.0;
            foreach ($d['chart'] as $c) { $max = max($max, $c['omzet']); }
            foreach ($d['chart'] as $c):
                $h = $max > 0 ? max(2, (int)round($c['omzet'] / $max * 100)) : 2;
            ?>
            <div class="g-col<?= $c['hari_ini'] ? ' today' : '' ?>">
                <div class="g-val"><?= $c['omzet'] > 0 ? e(rpShort_chart($c['omzet'])) : '' ?></div>
                <div class="g-bar" style="height:<?= $h ?>%"></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="g-labels">
            <?php foreach ($d['chart'] as $c): ?>
            <span class="<?= $c['hari_ini'] ? 'today' : '' ?>"><?= e($c['label']) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Produk terlaris -->
    <h3 class="sec">Produk Terlaris</h3>
    <table>
        <tr><th class="ctr" style="width:34px">No</th><th>Produk</th><th class="num" style="width:90px">Qty Terjual</th><th class="num" style="width:110px">Omzet</th></tr>
        <?php if (!$d['top']): ?>
        <tr><td colspan="4" class="kosong">Belum ada penjualan pada periode ini.</td></tr>
        <?php else: foreach ($d['top'] as $i => $t): ?>
        <tr>
            <td class="ctr"><?= $i + 1 ?></td>
            <td><?= e($t['nama']) ?></td>
            <td class="num"><?= number_format($t['qty'], 0, ',', '.') ?></td>
            <td class="num"><?= rp_cetak($t['omzet']) ?></td>
        </tr>
        <?php endforeach; endif; ?>
    </table>

    <!-- Daftar transaksi -->
    <h3 class="sec">Daftar Transaksi (<?= count($d['semua']) ?>)</h3>
    <table>
        <tr>
            <th class="ctr" style="width:30px">No</th>
            <th style="width:78px">Waktu</th>
            <th style="width:124px">Kode</th>
            <th>Kasir</th>
            <th style="width:62px">Metode</th>
            <th class="num">Subtotal</th>
            <th class="num">Diskon</th>
            <th class="num">Total</th>
            <th class="num">Laba</th>
        </tr>
        <?php if (!$d['semua']): ?>
        <tr><td colspan="9" class="kosong">Belum ada transaksi pada periode ini.</td></tr>
        <?php else: foreach ($d['semua'] as $i => $t): ?>
        <tr>
            <td class="ctr"><?= $i + 1 ?></td>
            <td><?= e(date('d/m H:i', strtotime($t['tanggal']))) ?></td>
            <td><?= e($t['kode']) ?></td>
            <td><?= e($t['kasir'] ?: '-') ?></td>
            <td><?= e($t['metode']) ?></td>
            <td class="num"><?= rp_cetak($t['subtotal']) ?></td>
            <td class="num"><?= $t['diskon'] > 0 ? '-' . rp_cetak($t['diskon']) : '&mdash;' ?></td>
            <td class="num"><?= rp_cetak($t['total']) ?></td>
            <td class="num"><?= rp_cetak($t['laba']) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total">
            <td colspan="5">TOTAL <?= e(strtoupper($d['label'])) ?></td>
            <td class="num"><?= rp_cetak(array_sum(array_column($d['semua'], 'subtotal'))) ?></td>
            <td class="num"><?= rp_cetak(array_sum(array_column($d['semua'], 'diskon'))) ?></td>
            <td class="num"><?= rp_cetak(array_sum(array_column($d['semua'], 'total'))) ?></td>
            <td class="num"><?= rp_cetak(array_sum(array_column($d['semua'], 'laba'))) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <!-- Tanda tangan -->
    <div class="catat">
        <div class="ttd">
            <p>Diperiksa oleh,</p>
            <div class="sp"></div>
            <p>( ............................ )</p>
        </div>
        <div class="ttd">
            <p><?= e($s['toko_nama']) ?></p>
            <div class="sp"></div>
            <p>( ............................ )</p>
        </div>
    </div>

    <div class="foot">
        <span>Dicetak <?= date('d/m/Y H:i') ?> &middot; Warung Kelontong POS v<?= e(APP_VERSION) ?></span>
        <span>Total omzet: <b><?= rp_cetak($r['omzet']) ?></b></span>
    </div>
</div>

<script>
// Dialog cetak muncul otomatis saat halaman siap
window.addEventListener('load', function () {
    setTimeout(function () { window.print(); }, 350);
});
</script>
</body>
</html>
