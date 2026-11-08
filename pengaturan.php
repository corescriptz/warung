<?php
/** Halaman Pengaturan */
require_once __DIR__ . '/config.php';
maybe_auto_backup();
ensure_export_dirs();

$s = all_settings();
$lastImport = null;
try {
    $r = db()->query('SELECT nama_file, total_row, jumlah_baru, jumlah_update, jumlah_gagal, dibuat_pada FROM import_log ORDER BY id DESC LIMIT 1')->fetch();
    if ($r) {
        $lastImport = $r;
    }
} catch (Throwable $e) {
}

page_start('pengaturan', 'Pengaturan');
?>

<header class="page-head">
    <div class="head-row">
        <div>
            <h1 class="large-title">Pengaturan</h1>
            <p class="head-sub" id="headDate">&nbsp;</p>
        </div>
    </div>
</header>

<!-- ================== Profil Toko ================== -->
<div class="group-title">Profil Toko</div>
<section class="group form">
    <div class="frow">
        <label for="tokoNama">Nama Toko</label>
        <input class="ti" id="tokoNama" value="<?= e($s['toko_nama']) ?>" placeholder="Nama warung Anda">
    </div>
    <div class="frow">
        <label for="tokoAlamat">Alamat</label>
        <textarea class="ti" id="tokoAlamat" rows="2" placeholder="Alamat lengkap (tampil di struk)"><?= e($s['toko_alamat']) ?></textarea>
    </div>
    <div class="frow">
        <label for="tokoTelp">No. Telepon</label>
        <input class="ti" id="tokoTelp" value="<?= e($s['toko_telp']) ?>" placeholder="08xx-xxxx-xxxx">
    </div>
    <div class="frow">
        <label for="strukFooter">Teks Bawah Struk</label>
        <input class="ti" id="strukFooter" value="<?= e($s['struk_footer']) ?>" placeholder="Terima kasih telah berbelanja!">
    </div>
    <div class="frow">
        <label for="kasirNama">Nama Kasir Aktif</label>
        <input class="ti" id="kasirNama" value="<?= e($s['kasir_nama']) ?>" placeholder="Contoh: Bu Rina">
    </div>
</section>

<!-- ================== Printer Struk ================== -->
<div class="group-title">Printer Struk</div>
<section class="group">
    <div class="frow">
        <label for="printerNama">Nama Printer</label>
        <input class="ti" id="printerNama" value="<?= e($s['printer_nama']) ?>" placeholder="POS-58 Thermal">
    </div>
    <div class="frow frow-col">
        <label>Lebar Kertas</label>
        <div class="seg seg-2" id="segLebar">
            <div class="seg-thumb"></div>
            <button class="<?= ($s['printer_lebar'] === '80') ? '' : 'on' ?>" data-v="58">58 mm</button>
            <button class="<?= ($s['printer_lebar'] === '80') ? 'on' : '' ?>" data-v="80">80 mm</button>
        </div>
    </div>
    <div class="row row-btn" id="btnTesCetak" role="button">
        <div class="row-ico ico-pink">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg>
        </div>
        <span class="row-label">Tes Cetak Struk</span>
        <svg class="chev" width="8" height="14" viewBox="0 0 10 17" fill="none"><path d="M1.5 1.5l7 7-7 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
</section>

<!-- ================== Backup ================== -->
<div class="group-title">Backup</div>
<section class="group">
    <div class="row">
        <div class="row-ico ico-green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v10M8 9l4 4 4-4"/><path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/></svg>
        </div>
        <span class="row-label">Backup Otomatis (harian)</span>
        <label class="switch">
            <input type="checkbox" id="autoBackup" <?= ($s['auto_backup'] === '1') ? 'checked' : '' ?>>
            <span class="slider"></span>
        </label>
    </div>
    <div class="row">
        <span class="row-label dim">Backup terakhir</span>
        <span class="row-value" id="lastBackup"><?= $s['backup_terakhir'] ? e(date('d/m/Y H:i', strtotime($s['backup_terakhir']))) : 'Belum pernah' ?></span>
    </div>
    <div class="row row-btn" id="btnBackupNow" role="button">
        <div class="row-ico ico-orange">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v3a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-3"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
        </div>
        <span class="row-label">Backup Sekarang (.sql)</span>
        <svg class="chev" width="8" height="14" viewBox="0 0 10 17" fill="none"><path d="M1.5 1.5l7 7-7 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <div class="row">
        <span class="row-label dim">Folder backup</span>
        <span class="row-value mono">exports/</span>
    </div>
</section>

<!-- ================== Import Excel ================== -->
<div class="group-title">Data Barang</div>
<section class="group">
    <div class="row row-btn" id="btnOpenImport" role="button">
        <div class="row-ico ico-green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6z"/><path d="M14 3v6h6M9 13h6M9 17h6"/></svg>
        </div>
        <span class="row-label">Import Excel</span>
        <svg class="chev" width="8" height="14" viewBox="0 0 10 17" fill="none"><path d="M1.5 1.5l7 7-7 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <div class="row">
        <span class="row-label dim">Template Excel</span>
        <a class="row-link" href="api/import.php?template=1">Unduh template</a>
    </div>
    <?php if ($lastImport): ?>
    <div class="row">
        <span class="row-label dim">Import terakhir</span>
        <span class="row-value" style="text-align:right"><?= e($lastImport['nama_file']) ?><br>
            <span style="color:var(--green)"><?= (int)$lastImport['jumlah_baru'] ?> baru</span> &middot;
            <span style="color:var(--blue)"><?= (int)$lastImport['jumlah_update'] ?> diperbarui</span>
            <?= ((int)$lastImport['jumlah_gagal'] > 0) ? ' &middot; <span style="color:var(--red)">' . (int)$lastImport['jumlah_gagal'] . ' gagal</span>' : '' ?>
        </span>
    </div>
    <?php endif; ?>
</section>

<!-- ================== Tentang ================== -->
<div class="group-title">Tentang</div>
<section class="group">
    <div class="row">
        <span class="row-label">Aplikasi</span>
        <span class="row-value">Warung Kelontong POS</span>
    </div>
    <div class="row">
        <span class="row-label">Versi</span>
        <span class="row-value"><?= e(APP_VERSION) ?></span>
    </div>
    <div class="row">
        <span class="row-label">Basis Data</span>
        <span class="row-value">MySQL</span>
    </div>
</section>

<div style="height:110px"></div>

<!-- Tombol simpan (di atas tab bar) -->
<div class="savebar" id="savebar">
    <button class="btn-primary btn-block" id="btnSave">Simpan Perubahan</button>
</div>

<!-- ================== Modal Import (4 langkah) ================== -->
<div class="import-overlay" id="importOverlay">
    <div class="import-panel">
        <div class="import-head">
            <button class="link-x" id="importClose" aria-label="Tutup">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm1-10l3.5-3.5-1.4-1.4L11.6 10.6 8.1 7.1 6.7 8.5l3.5 3.5-3.5 3.5 1.4 1.4 3.5-3.5 3.5 3.5 1.4-1.4L13.4 12z"/></svg>
            </button>
            <h2>Import Excel</h2>
            <div class="step-dots" id="stepper">
                <span class="step-dot on"></span><span class="step-dot"></span><span class="step-dot"></span><span class="step-dot"></span>
            </div>
        </div>

        <!-- Langkah 1: pilih file -->
        <div class="import-step" id="impStep1">
            <div class="dropzone" id="dropzone">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6z"/><path d="M14 3v6h6M12 17v-6M9.5 13.5L12 11l2.5 2.5"/></svg>
                <p><b>Ketuk untuk pilih file</b></p>
                <p class="dz-sub">.xlsx atau .csv, maksimal 8 MB</p>
            </div>
            <input type="file" id="importFile" accept=".xlsx,.csv" hidden>
            <p class="import-hint">Barang dengan <b>nama yang sama</b> akan diperbarui, nama baru akan ditambahkan dan ditandai <b>Baru</b> di katalog.</p>
        </div>

        <!-- Langkah 2: petakan kolom -->
        <div class="import-step" id="impStep2" hidden>
            <p class="import-file" id="impFileLabel">file.xlsx &middot; 0 barang</p>
            <div class="map-list" id="mapList"></div>
            <div class="group-title" style="margin-top:18px">Pratinjau</div>
            <div class="preview-wrap"><table class="preview" id="previewTable"></table></div>
            <button class="btn-primary btn-block" id="btnStartImport" style="margin-top:18px">Mulai Import</button>
        </div>

        <!-- Langkah 3: progress -->
        <div class="import-step" id="impStep3" hidden>
            <div class="ring-wrap">
                <svg class="ring" viewBox="0 0 120 120" width="150" height="150">
                    <circle class="ring-bg" cx="60" cy="60" r="52"/>
                    <circle class="ring-fg" id="ringFg" cx="60" cy="60" r="52"/>
                </svg>
                <div class="ring-txt"><b id="ringPct">0%</b><span id="ringInfo">Menyiapkan...</span></div>
            </div>
            <p class="import-status" id="impStatus">Mengimpor barang...</p>
        </div>

        <!-- Langkah 4: hasil -->
        <div class="import-step" id="impStep4" hidden>
            <div class="check-wrap small">
                <svg viewBox="0 0 72 72" width="74" height="74">
                    <circle class="ck-circle" cx="36" cy="36" r="31"/>
                    <path class="ck-check" d="M22.5 37.5l9.5 9.5 18.5-21"/>
                </svg>
            </div>
            <h2 style="text-align:center;margin-bottom:4px">Import Selesai</h2>
            <div class="hasil-grid">
                <div class="hasil-item"><b id="hasilBaru">0</b><span>Barang Baru</span></div>
                <div class="hasil-item"><b id="hasilUpdate">0</b><span>Diperbarui</span></div>
                <div class="hasil-item"><b id="hasilGagal">0</b><span>Dilewati</span></div>
            </div>
            <button class="btn-primary btn-block" id="btnGoTransaksi">Lihat di Transaksi</button>
            <button class="btn-ghost btn-block" id="btnImportDone">Selesai</button>
        </div>
    </div>
</div>

<?php page_end(); ?>
