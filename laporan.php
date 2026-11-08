<?php
/** Halaman Laporan */
require_once __DIR__ . '/config.php';
maybe_auto_backup();

page_start('laporan', 'Laporan');
?>

<header class="page-head">
    <div class="head-row">
        <div>
            <h1 class="large-title">Laporan</h1>
            <p class="head-sub" id="lapLabel">Hari Ini</p>
        </div>
    </div>

    <div class="seg" id="segPeriode">
        <div class="seg-thumb"></div>
        <button class="on" data-v="hari">Hari Ini</button>
        <button data-v="minggu">Minggu</button>
        <button data-v="bulan">Bulan</button>
    </div>
</header>

<!-- Kartu ringkasan -->
<section class="stats">
    <div class="stat-card stat-big">
        <div class="stat-ico ico-blue">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"><path d="M12 2v20M17 6.5C17 4.6 14.8 3.5 12 3.5S7 4.6 7 6.5s2 3.1 5 3.5 5 1.4 5 3.5-2.2 3-5 3-5-1.1-5-3"/></svg>
        </div>
        <div class="stat-label">Omzet</div>
        <div class="stat-value" id="stOmzet">Rp 0</div>
        <div class="stat-sub" id="stRata">&mdash;</div>
    </div>
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-ico ico-orange">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 7h12l-1.2 11a2 2 0 0 1-2 1.8H9.2a2 2 0 0 1-2-1.8L6 7z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg>
            </div>
            <div class="stat-label">Transaksi</div>
            <div class="stat-value" id="stJumlah">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-ico ico-green">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
            <div class="stat-label">Laba</div>
            <div class="stat-value" id="stLaba">Rp 0</div>
        </div>
    </div>
</section>

<div class="export-row">
    <button class="btn-primary btn-export" id="btnExport">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M7 10l5 5 5-5"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>
        Ekspor Excel (.xlsx)
    </button>
    <button class="btn-ghost btn-export" id="btnPrint">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg>
        Cetak Laporan
    </button>
</div>

<!-- Grafik 7 hari -->
<section class="group">
    <div class="group-head">
        <h3>Omzet 7 Hari Terakhir</h3>
        <span class="group-sub" id="chartTotal"></span>
    </div>
    <div class="chart" id="chart">
        <div class="chart-bars" id="chartBars"></div>
        <div class="chart-labels" id="chartLabels"></div>
    </div>
</section>

<!-- Produk terlaris -->
<section class="group">
    <div class="group-head"><h3>Produk Terlaris</h3></div>
    <div class="rows" id="topList"></div>
    <div class="empty-mini" id="topEmpty" hidden>Belum ada penjualan pada periode ini.</div>
</section>

<!-- Transaksi terakhir -->
<section class="group">
    <div class="group-head"><h3>Transaksi Terakhir</h3><span class="group-sub">tap untuk struk</span></div>
    <div class="rows" id="lastList"></div>
    <div class="empty-mini" id="lastEmpty" hidden>Belum ada transaksi pada periode ini.</div>
</section>

<!-- Sheet struk (dipakai ulang) -->
<div class="sheet-wrap" id="sheetStruk">
    <div class="backdrop" data-close="sheetStruk"></div>
    <div class="sheet struk-sheet">
        <div class="grabber"></div>
        <div class="sheet-head">
            <h2>Struk</h2>
            <button class="link-x" data-close="sheetStruk" aria-label="Tutup">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm1-10l3.5-3.5-1.4-1.4L11.6 10.6 8.1 7.1 6.7 8.5l3.5 3.5-3.5 3.5 1.4 1.4 3.5-3.5 3.5 3.5 1.4-1.4L13.4 12z"/></svg>
            </button>
        </div>
        <div class="struk-scroll">
            <div id="strukBody"></div>
        </div>
        <button class="btn-primary btn-block" id="btnPrintStruk">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg>
            Cetak
        </button>
    </div>
</div>

<?php page_end(); ?>
