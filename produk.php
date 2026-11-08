<?php
/**
 * Halaman Produk — manajemen barang manual:
 * daftar, cari, tambah, ubah, hapus, isi barcode.
 */
require_once __DIR__ . '/config.php';
maybe_auto_backup();

page_start('produk', 'Produk');
?>

<header class="page-head">
    <div class="head-row">
        <div>
            <h1 class="large-title">Produk</h1>
            <p class="head-sub" id="prodStat">&nbsp;</p>
        </div>
        <div class="head-actions">
            <button class="nav-plus nav-sub" id="btnLabel" title="Cetak label barcode">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M7 9.5v5M10.4 8.5v7M13.6 9.5v5M17 8.5v7"/></svg>
            </button>
            <button class="nav-plus" id="btnTambah" title="Tambah barang">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            </button>
        </div>
    </div>

    <div class="searchbar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
        <input type="search" id="searchInput" placeholder="Cari nama atau barcode..." autocomplete="off">
        <button type="button" class="search-clear" id="searchClear" aria-label="Hapus">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm1-10l3.5-3.5-1.4-1.4L11.6 10.6 8.1 7.1 6.7 8.5l3.5 3.5-3.5 3.5 1.4 1.4 3.5-3.5 3.5 3.5 1.4-1.4L13.4 12z"/></svg>
        </button>
    </div>

    <div class="chips" id="chips"></div>
</header>

<div class="plist" id="plist"></div>

<div class="empty" id="emptyState" hidden>
    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"/><path d="M4 7.5l8 4.5 8-4.5M12 12v9"/></svg>
    <p>Barang tidak ditemukan</p>
    <button class="link-blue" id="btnEmptyTambah">+ Tambah barang pertama</button>
</div>

<!-- ===================== Sheet: Cetak label barcode ===================== -->
<div class="sheet-wrap" id="sheetLabel">
    <div class="backdrop" data-close="sheetLabel"></div>
    <div class="sheet">
        <div class="grabber"></div>
        <div class="sheet-head">
            <h2>Cetak Label</h2>
            <button class="link-x" data-close="sheetLabel" aria-label="Tutup">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm1-10l3.5-3.5-1.4-1.4L11.6 10.6 8.1 7.1 6.7 8.5l3.5 3.5-3.5 3.5 1.4 1.4 3.5-3.5 3.5 3.5 1.4-1.4L13.4 12z"/></svg>
            </button>
        </div>

        <div class="form-scroll">
            <div class="fgroup">
                <div class="searchbar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                    <input type="search" id="lblSearch" placeholder="Cari barang..." autocomplete="off">
                </div>
                <div class="lbl-tools">
                    <button type="button" class="link-blue" id="lblSemua">Pilih semua</button>
                    <span id="lblTersimpan">0 barang terpilih</span>
                </div>
                <div class="lbl-list" id="lblList"></div>
            </div>

            <div class="fgroup">
                <div class="frow">
                    <label>Ukuran Label</label>
                    <div class="opt-grid" id="optUkuran">
                        <button type="button" class="opt on" data-u="thermal58"><b>Thermal 58mm</b><span>58 &times; 25 mm</span></button>
                        <button type="button" class="opt" data-u="a4_24"><b>A4 Besar</b><span>66 &times; 35 mm &middot; 24/lbr</span></button>
                        <button type="button" class="opt" data-u="a4_40"><b>A4 Kecil</b><span>48 &times; 25 mm &middot; 40/lbr</span></button>
                    </div>
                </div>
                <div class="frow-row">
                    <div class="frow">
                        <label>Label per Barang</label>
                        <div class="stepper">
                            <button type="button" id="lblMinus" aria-label="Kurangi">&minus;</button>
                            <b id="lblCopy">2</b>
                            <button type="button" id="lblPlus" aria-label="Tambah">+</button>
                        </div>
                    </div>
                    <div class="frow">
                        <label>Total Label</label>
                        <b class="ti-like" id="lblTotal">0</b>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn-primary btn-block" id="btnCetakLabel" disabled>
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg>
            <span>Cetak Label</span>
        </button>
    </div>
</div>

<!-- ===================== Sheet: Form barang ===================== -->
<div class="sheet-wrap" id="sheetForm">
    <div class="backdrop" data-close="sheetForm"></div>
    <div class="sheet">
        <div class="grabber"></div>
        <div class="sheet-head">
            <h2 id="formJudul">Tambah Barang</h2>
            <button class="link-x" data-close="sheetForm" aria-label="Tutup">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm1-10l3.5-3.5-1.4-1.4L11.6 10.6 8.1 7.1 6.7 8.5l3.5 3.5-3.5 3.5 1.4 1.4 3.5-3.5 3.5 3.5 1.4-1.4L13.4 12z"/></svg>
            </button>
        </div>

        <div class="form-scroll">
            <div class="fgroup">
                <div class="frow">
                    <label for="fNama">Nama Barang *</label>
                    <input class="ti" id="fNama" maxlength="150" placeholder="Contoh: Kopi Sachet Luwak 10g" autocomplete="off">
                </div>
                <div class="frow">
                    <label for="fKategori">Kategori</label>
                    <input class="ti" id="fKategori" list="katList" maxlength="60" placeholder="Pilih atau ketik baru" autocomplete="off">
                    <datalist id="katList"></datalist>
                </div>
                <div class="frow">
                    <label for="fBarcode">Barcode <span class="opt">(opsional)</span></label>
                    <div class="bc-row">
                        <input class="ti mono" id="fBarcode" inputmode="numeric" maxlength="64" placeholder="Tembak pakai scanner" autocomplete="off">
                        <button type="button" class="mini-btn" id="btnAutoBc">Auto</button>
                    </div>
                    <p class="fhint">Tembak langsung pakai scanner USB, atau tekan <b>Auto</b> untuk kode internal (barang curah).</p>
                </div>
            </div>

            <div class="fgroup">
                <div class="frow-row">
                    <div class="frow">
                        <label for="fBeli">Harga Beli</label>
                        <input class="ti" id="fBeli" inputmode="numeric" placeholder="0" autocomplete="off">
                    </div>
                    <div class="frow">
                        <label for="fJual">Harga Jual *</label>
                        <input class="ti" id="fJual" inputmode="numeric" placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div class="frow-row tri">
                    <div class="frow">
                        <label for="fStok">Stok</label>
                        <input class="ti" id="fStok" inputmode="numeric" placeholder="0" autocomplete="off">
                    </div>
                    <div class="frow">
                        <label for="fStokMin">Stok Minimal</label>
                        <input class="ti" id="fStokMin" inputmode="numeric" placeholder="5" autocomplete="off">
                    </div>
                    <div class="frow">
                        <label for="fSatuan">Satuan</label>
                        <input class="ti" id="fSatuan" maxlength="20" placeholder="pcs" autocomplete="off">
                    </div>
                </div>
            </div>

            <p class="fhint center" id="formInfo">Barang baru otomatis bertanda BARU selama 7 hari.</p>
        </div>

        <button class="btn-red btn-block" id="btnHapus" hidden>Hapus Barang</button>
        <button class="btn-primary btn-block" id="btnSimpan">Simpan</button>
    </div>
</div>

<?php
$extraHtml = <<<HTML
<!-- ===================== Dialog: Konfirmasi hapus ===================== -->
<div class="alert-wrap" id="alertHapus">
    <div class="backdrop show" data-alert-close></div>
    <div class="alert-box">
        <h3>Hapus Barang?</h3>
        <p id="hapusNama">Barang akan dihapus dari katalog. Riwayat transaksi lama tetap tersimpan.</p>
        <div class="alert-actions">
            <button class="alert-btn" data-alert-close>Batal</button>
            <button class="alert-btn danger" id="btnHapusYa">Hapus</button>
        </div>
    </div>
</div>
HTML;

page_end($extraHtml);
?>
