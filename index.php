<?php
/** Halaman Transaksi (kasir) */
require_once __DIR__ . '/config.php';
maybe_auto_backup();

page_start('transaksi', 'Transaksi');
?>

<header class="page-head">
    <div class="head-row">
        <div>
            <h1 class="large-title" id="tokoNama">&nbsp;</h1>
            <p class="head-sub" id="headDate">&nbsp;</p>
        </div>
        <button class="kasir-chip" id="btnKasir" title="Ketuk untuk ganti kasir">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/></svg>
            <span id="kasirNama">Kasir</span>
            <svg class="kc-pen" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.8 2.8 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5z"/></svg>
        </button>
    </div>

    <div class="searchbar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
        <input type="search" id="searchInput" placeholder="Cari barang / barcode..." autocomplete="off">
        <button type="button" class="search-clear" id="searchClear" aria-label="Hapus">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm1-10l3.5-3.5-1.4-1.4L11.6 10.6 8.1 7.1 6.7 8.5l3.5 3.5-3.5 3.5 1.4 1.4 3.5-3.5 3.5 3.5 1.4-1.4L13.4 12z"/></svg>
        </button>
        <button type="button" class="scan-btn" id="btnScan" title="Scan barcode" hidden aria-label="Scan barcode">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7V5.5A1.5 1.5 0 0 1 5.5 4H7M17 4h1.5A1.5 1.5 0 0 1 20 5.5V7M20 17v1.5a1.5 1.5 0 0 1-1.5 1.5H17M7 20H5.5A1.5 1.5 0 0 1 4 18.5V17"/><path d="M7.5 12h9"/><path d="M7.5 8.5v.01M12 8.5v.01M16.5 8.5v.01M7.5 15.5v.01M12 15.5v.01M16.5 15.5v.01"/></svg>
        </button>
    </div>

    <div class="chips" id="chips"></div>
</header>

<div class="grid" id="grid"></div>

<div class="empty" id="emptyState" hidden>
    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5M8.5 11h5"/></svg>
    <p>Barang tidak ditemukan</p>
</div>

<!-- ===================== Bar keranjang (di atas tab bar) ===================== -->
<div class="cartbar" id="cartbar">
    <div class="cartbar-info">
        <strong id="cartInfoQty">0 barang</strong>
        <span id="cartInfoTotal">Rp 0</span>
    </div>
    <button class="btn-white" id="btnOpenCart">
        Lihat keranjang
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </button>
</div>

<!-- ===================== Sheet: Keranjang ===================== -->
<div class="sheet-wrap" id="sheetCart">
    <div class="backdrop" data-close="sheetCart"></div>
    <div class="sheet">
        <div class="grabber"></div>
        <div class="sheet-head">
            <h2>Keranjang</h2>
            <button class="link-red" id="btnClearCart">Kosongkan</button>
        </div>
        <div class="cart-items" id="cartItems"></div>
        <div class="cart-summary">
            <div class="sum-row">
                <span>Subtotal</span>
                <b id="sumSubtotal">Rp 0</b>
            </div>
            <div class="sum-row sum-diskon">
                <span>Diskon (Rp)</span>
                <input type="text" id="diskonInput" inputmode="numeric" placeholder="0" value="">
            </div>
            <div class="sum-row sum-total">
                <span>Total</span>
                <b id="sumTotal">Rp 0</b>
            </div>
        </div>
        <button class="btn-primary btn-block" id="btnToPay">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2.5" y="5.5" width="19" height="13" rx="3"/><path d="M2.5 10h19"/></svg>
            Bayar
        </button>
    </div>
</div>

<!-- ===================== Sheet: Pembayaran ===================== -->
<div class="sheet-wrap" id="sheetPay">
    <div class="backdrop" data-close="sheetPay"></div>
    <div class="sheet">
        <div class="grabber"></div>
        <div class="sheet-head">
            <h2>Pembayaran</h2>
            <button class="link-x" data-close="sheetPay" aria-label="Tutup">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm1-10l3.5-3.5-1.4-1.4L11.6 10.6 8.1 7.1 6.7 8.5l3.5 3.5-3.5 3.5 1.4 1.4 3.5-3.5 3.5 3.5 1.4-1.4L13.4 12z"/></svg>
            </button>
        </div>

        <div class="seg pay-methods" id="segMetode">
            <button class="on" data-v="Tunai"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="6" width="19" height="12" rx="3"/><circle cx="12" cy="12" r="2.6"/></svg><span>Tunai</span></button>
            <button data-v="QRIS"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7.5" height="7.5" rx="1.8"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="1.8"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="1.8"/><path d="M14 14h2v2h-2zM18 18h3M18 14h3M14 19h.01"/></svg><span>QRIS</span></button>
            <button data-v="Transfer"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9h14l-3-3M20 15H6l3 3"/></svg><span>Transfer</span></button>
        </div>

        <div id="payTunai">
            <div class="pay-label">Total belanja</div>
            <div class="pay-total" id="payTotal">Rp 0</div>

            <div class="quick-chips" id="quickChips">
                <button data-cash="pas">Uang pas</button>
                <button data-cash="50000">50.000</button>
                <button data-cash="100000">100.000</button>
                <button data-cash="200000">200.000</button>
            </div>

            <div class="kembali-box" id="kembaliBox">
                <span>Kembalian</span>
                <b id="payKembali">Rp 0</b>
            </div>

            <div class="keypad" id="keypad">
                <button data-k="1">1</button><button data-k="2">2</button><button data-k="3">3</button>
                <button data-k="4">4</button><button data-k="5">5</button><button data-k="6">6</button>
                <button data-k="7">7</button><button data-k="8">8</button><button data-k="9">9</button>
                <button data-k="000" class="k-zero0">000</button><button data-k="0">0</button>
                <button data-k="back" class="k-back">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 6H8.5L2.8 12l5.7 6H21a1.6 1.6 0 0 0 1.6-1.6V7.6A1.6 1.6 0 0 0 21 6z"/><path d="M12 9.5l5 5M17 9.5l-5 5"/></svg>
                </button>
            </div>
        </div>

        <div id="payNonTunai" hidden>
            <div class="nontunai-card">
                <div class="nontunai-ico">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><path d="M14.5 14.5h1.8v1.8h-1.8zM18 18h2v2h-2zM14.5 18h.2v.2h-.2z"/></svg>
                </div>
                <p>Minta pembeli membayar dengan QRIS atau transfer sebesar</p>
                <div class="nontunai-total" id="payTotalNon">Rp 0</div>
                <p class="nontunai-note">Pembayaran dianggap pas &mdash; tidak ada kembalian.</p>
            </div>
        </div>

        <button class="btn-primary btn-block" id="btnConfirmPay" disabled>Selesaikan Transaksi</button>
    </div>
</div>

<!-- ===================== Overlay sukses ===================== -->
<div class="success-overlay" id="successOverlay">
    <div class="success-card">
        <div class="check-wrap">
            <svg viewBox="0 0 72 72" width="84" height="84">
                <circle class="ck-circle" cx="36" cy="36" r="31"/>
                <path class="ck-check" d="M22.5 37.5l9.5 9.5 18.5-21"/>
            </svg>
        </div>
        <h2>Transaksi Berhasil</h2>
        <p class="success-kode" id="successKode">TRX-00000000-0000</p>
        <div class="kembali-card">
            <span id="successKembaliLabel">Kembalian</span>
            <div id="successKembali">Rp 0</div>
        </div>
        <div class="success-actions">
            <button class="btn-white" id="btnSuccessStruk">Lihat Struk</button>
            <button class="btn-white" id="btnSuccessPrint">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg>
                Cetak Struk
            </button>
            <button class="btn-primary" id="btnNewTx">Transaksi Baru</button>
        </div>
    </div>
</div>

<!-- ===================== Sheet: Struk ===================== -->
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

<?php
// Dialog ganti kasir + sheet kamera scanner (di luar shell agar tidak terpotong)
$extraHtml = <<<HTML
<!-- ===================== Dialog: Ganti Kasir ===================== -->
<div class="alert-wrap" id="alertKasir">
    <div class="backdrop show" data-alert-close></div>
    <div class="alert-box">
        <h3>Ganti Kasir</h3>
        <p>Nama ini tercatat pada transaksi &amp; struk.</p>
        <input class="ti" id="kasirInput" maxlength="30" placeholder="Contoh: Bu Rina" autocomplete="off">
        <div class="alert-actions">
            <button class="alert-btn" data-alert-close>Batal</button>
            <button class="alert-btn primary" id="btnKasirSimpan">Simpan</button>
        </div>
    </div>
</div>

<!-- ===================== Sheet: Scan barcode (kamera) ===================== -->
<div class="sheet-wrap" id="sheetScan">
    <div class="backdrop" data-close="sheetScan"></div>
    <div class="sheet">
        <div class="grabber"></div>
        <div class="sheet-head">
            <h2>Scan Barcode</h2>
            <button class="link-x" data-close="sheetScan" aria-label="Tutup">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm1-10l3.5-3.5-1.4-1.4L11.6 10.6 8.1 7.1 6.7 8.5l3.5 3.5-3.5 3.5 1.4 1.4 3.5-3.5 3.5 3.5 1.4-1.4L13.4 12z"/></svg>
            </button>
        </div>
        <div class="scan-stage">
            <video id="scanVideo" playsinline muted></video>
            <div class="scan-frame"></div>
            <div class="scan-msg" id="scanMsg">Arahkan barcode ke dalam kotak</div>
        </div>
        <p class="scan-note">Belum punya scanner USB? Gunakan kamera ini, atau tembak scanner langsung ke halaman ini.</p>
    </div>
</div>
HTML;

page_end($extraHtml);
?>
