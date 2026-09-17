@extends('layouts.app')

@push('styles')
<style>
    /* Styling Efek Glassmorphism */
    .glass-card {
        background: rgba(255, 255, 255, 0.75) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.08) !important;
    }

    .glass-header {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
    }

    .glass-table {
        background: transparent !important;
    }

    .glass-table th, 
    .glass-table td {
        background: rgba(255, 255, 255, 0.4) !important;
        backdrop-filter: blur(4px);
    }

    /* Fix z-index modal dan dropdown agar tidak tertutup glassmorphism */
    .modal {
        z-index: 1055 !important;
    }
    .modal-backdrop {
        z-index: 1050 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-uppercase fs-7 fw-bold text-muted" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-secondary">Inventory</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-secondary">Produksi</a></li>
                    <li class="breadcrumb-item active text-success" aria-current="page">Barang Masuk</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">
                <i class="fa-solid fa-cart-arrow-down text-success me-2"></i>Form Transaksi Barang Masuk
            </h1>
        </div>
    </div>

<!-- Form Utama -->
    <form action="{{ route('inventory.produksi.barang_masuk.store') }}" method="POST" id="transactionForm">
        @csrf
        
        <!-- CARD 1: INFORMASI HEADER TRANSAKSI -->
        <div class="card glass-card rounded-4 mb-4 overflow-hidden">
            <div class="card-header glass-header py-3 px-4 d-flex align-items-center">
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-file-invoice fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Informasi Header Transaksi</h5>
                    <p class="text-muted small mb-0">Data umum, nomor surat jalan, dan relasi supplier.</p>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">No. Transaksi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white bg-opacity-75 text-muted border-end-0"><i class="fa-solid fa-hashtag"></i></span>
                            <input type="text" name="no_transaksi" class="form-control bg-white bg-opacity-75 border-start-0 ps-0 fw-bold text-dark" value="TRX-IN-{{ date('Ymd') }}-001" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Tanggal Masuk <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white bg-opacity-75 text-muted border-end-0"><i class="fa-regular fa-calendar"></i></span>
                            <input type="date" name="tanggal" class="form-control bg-white bg-opacity-75 border-start-0 ps-0" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Supplier <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white bg-opacity-75 text-muted border-end-0"><i class="fa-solid fa-truck-field"></i></span>
                            <select name="supplier_id" class="form-select bg-white bg-opacity-75 border-start-0 ps-0" required>
                                <option value="">-- Pilih dari Master Supplier --</option>
                                @if(isset($suppliers))
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id ?? $supplier->supplier_id }}">
                                            {{ $supplier->name ?? $supplier->nama_supplier ?? $supplier->nama ?? 'Supplier Tanpa Nama' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">No. Surat Jalan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white bg-opacity-75 text-muted border-end-0"><i class="fa-solid fa-receipt"></i></span>
                            <input type="text" name="no_surat_jalan" class="form-control bg-white bg-opacity-75 border-start-0 ps-0" placeholder="Contoh: SJ/2026/09/001" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Admin / Petugas</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white bg-opacity-75 text-muted border-end-0"><i class="fa-solid fa-user-shield"></i></span>
                            <input type="text" name="admin" class="form-control bg-white bg-opacity-75 border-start-0 ps-0 text-muted" value="{{ auth()->user()->name ?? 'Admin Gudang' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Keterangan Catatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white bg-opacity-75 text-muted border-end-0"><i class="fa-solid fa-comment-dots"></i></span>
                            <input type="text" name="keterangan" class="form-control bg-white bg-opacity-75 border-start-0 ps-0" placeholder="Catatan tambahan (opsional)...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 2: RINCIAN DETAIL BARANG -->
        <div class="card glass-card rounded-4 mb-4 overflow-hidden">
            <div class="card-header glass-header py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-boxes-stacked fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Rincian Detail Barang Masuk</h5>
                        <p class="text-muted small mb-0">Pilih dari master barang untuk mengisi kode, kategori, satuan, harga, dan lokasi otomatis.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm px-3 py-2 shadow-sm rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
                        <i class="fa-solid fa-plus-circle me-1"></i> Tambah Master Barang Baru
                    </button>
                    <button type="button" class="btn btn-success btn-sm px-3 py-2 shadow-sm rounded-3 fw-semibold" id="addRow">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Baris Barang
                    </button>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table glass-table align-middle mb-0" id="detailTable">
                        <thead class="glass-header text-secondary text-uppercase fs-7" style="font-size: 0.75rem;">
                            <tr>
                                <th class="py-3 px-2" style="width: 10%;">Kode Barang</th>
                                <th class="py-3 px-2" style="width: 16%;">Nama Barang <span class="text-danger">*</span></th>
                                <th class="py-3 px-2" style="width: 12%;">Kategori</th>
                                <th class="py-3 px-2" style="width: 7%;">Qty <span class="text-danger">*</span></th>
                                <th class="py-3 px-2" style="width: 8%;">Satuan</th>
                                <th class="py-3 px-2" style="width: 11%;">Lokasi Penyimpanan</th>
                                <th class="py-3 px-2" style="width: 11%;">Harga Satuan (Rp) <span class="text-danger">*</span></th>
                                <th class="py-3 px-2" style="width: 11%;">Subtotal (Rp)</th>
                                <th class="py-3 px-2" style="width: 9%;">Keterangan</th>
                                <th class="py-3 px-2 text-center" style="width: 5%;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="item-row">
                                <!-- Kode Barang -->
                                <td class="py-3 px-2">
                                    <input type="text" name="items[0][kode_barang]" class="form-control bg-white bg-opacity-50 kode-input shadow-none fw-bold text-secondary" placeholder="Otomatis" readonly>
                                </td>

                                <!-- Nama Barang / Item ID -->
                                <td class="py-3 px-2">
                                    <select name="items[0][item_id]" class="form-select bg-white bg-opacity-75 barang-select shadow-none" required>
                                        <option value="">-- Pilih dari Master Barang --</option>
                                        @if(isset($barangs))
                                            @foreach($barangs as $barang)
                                                @php
                                                    $idVal = $barang->id ?? $barang->barang_id;
                                                    $kodeVal = $barang->code ?? $barang->kode_barang ?? $barang->kode;
                                                    $namaVal = $barang->name ?? $barang->nama_barang ?? $barang->nama;
                                                    $katVal  = $barang->category ?? $barang->kategori;
                                                    $satVal  = $barang->unit ?? $barang->satuan;
                                                    $hrgVal  = $barang->price ?? $barang->harga_beli ?? $barang->harga ?? 0;
                                                    $lokVal  = $barang->lokasi_rak ?? $barang->lokasi ?? $barang->rak ?? $barang->location ?? '';
                                                @endphp
                                                <option value="{{ $idVal }}" 
                                                    data-kode="{{ $kodeVal }}" 
                                                    data-nama="{{ $namaVal }}" 
                                                    data-kategori="{{ $katVal }}" 
                                                    data-satuan="{{ $satVal }}" 
                                                    data-harga="{{ $hrgVal }}" 
                                                    data-lokasi="{{ $lokVal }}">
                                                    [{{ $kodeVal }}] - {{ $namaVal }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="items[0][nama_barang]" class="nama-barang-hidden">
                                </td>

                                <!-- Kategori -->
                                <td class="py-3 px-2">
                                    <select name="items[0][category]" class="form-select bg-white bg-opacity-75 kategori-input shadow-none">
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Raw Material">Raw Material</option>
                                        <option value="Work In Process">Work In Process</option>
                                        <option value="Finished Goods">Finished Goods</option>
                                        <option value="MRO / Sparepart">MRO / Sparepart</option>
                                        <option value="Packing Material">Packing Material</option>
                                    </select>
                                </td>

                                <!-- Qty -->
                                <td class="py-3 px-2">
                                    <input type="number" name="items[0][qty]" class="form-control bg-white bg-opacity-75 qty-input shadow-none" min="1" value="1" required>
                                </td>

                                <!-- Satuan -->
                                <td class="py-3 px-2">
                                    <input type="text" name="items[0][satuan]" class="form-control satuan-input bg-white bg-opacity-50 shadow-none" placeholder="Satuan" required>
                                </td>

                                <!-- Lokasi -->
                                <td class="py-3 px-2">
                                    <input type="text" name="items[0][lokasi]" class="form-control bg-white bg-opacity-75 lokasi-input shadow-none" placeholder="Cth: Rak A-01">
                                </td>

                                <!-- Harga -->
                                <td class="py-3 px-2">
                                    <input type="text" class="form-control bg-white bg-opacity-75 harga-input shadow-none" value="0" required>
                                    <input type="hidden" name="items[0][harga]" class="harga-hidden" value="0">
                                </td>

                                <!-- Subtotal -->
                                <td class="py-3 px-2">
                                    <input type="text" class="form-control bg-white bg-opacity-50 total-display fw-bold text-dark shadow-none" readonly value="0">
                                </td>

                                <!-- Keterangan / Notes -->
                                <td class="py-3 px-2">
                                    <input type="text" name="items[0][notes]" class="form-control bg-white bg-opacity-75 shadow-none" placeholder="Ket...">
                                </td>

                                <!-- Aksi -->
                                <td class="py-3 px-2 text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row rounded-3" disabled>
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>

                        <tfoot class="glass-header">
                            <tr>
                                <td colspan="7" class="text-end fw-bold py-3">GRAND TOTAL (Rp) :</td>
                                <td colspan="3" class="py-3">
                                    <input type="text" id="grandTotalDisplay" class="form-control fw-bold text-dark bg-white" readonly value="0">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="card-footer glass-header py-3 px-4 text-muted small">
                <i class="fa-solid fa-circle-info text-primary me-1"></i> Kode barang, kategori, satuan, harga, dan lokasi akan terisi otomatis mengikuti data yang ada di Master Barang.
            </div>
        </div>

        <!-- TOMBOL AKSI UTAMA -->
        <div class="d-flex justify-content-end align-items-center gap-3 mb-5">
            <a href="{{ route('inventory.produksi.index') }}" class="btn btn-light rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <button type="submit" class="btn btn-success px-5 py-2 fw-semibold shadow-sm rounded-3">
                <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Transaksi & Update Stock
            </button>
        </div>
    </form>

<!-- Modal Tambah Barang Baru -->
<div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-labelledby="modalTambahBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTambahBarangLabel">Tambah Data Barang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahBarang" action="{{ route('inventory.produksi.master_barang.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Kode Barang / ID (Unik) <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="Contoh: BRG-001" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Nama Barang (Wajib) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama barang" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Kategori (Wajib) <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Raw Material">Raw Material</option>
                                <option value="Work In Process">Work In Process</option>
                                <option value="Finished Goods">Finished Goods</option>
                                <option value="MRO / Sparepart">MRO / Sparepart</option>
                                <option value="Packing Material">Packing Material</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Satuan (Valid) <span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control" placeholder="Pcs" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Stok Saat Ini (Min: 0)</label>
                            <input type="number" name="current_stock" class="form-control" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Stok Minimum (Min: 0)</label>
                            <input type="number" name="stok_min" class="form-control" value="5">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Lokasi / Rak</label>
                            <input type="text" name="lokasi_rak" class="form-control" placeholder="Contoh: Rak A-1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Harga Satuan</label>
                            <input type="number" name="price" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Supplier Utama</label>
                            <input type="text" name="supplier_utama" class="form-control" placeholder="Nama supplier utama">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Status Aktif</label>
                            <select name="status" class="form-select">
                                <option value="Aktif">Aktif</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanBarang">Simpan ke DB Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let tableBody = document.getElementById('detailTable').getElementsByTagName('tbody')[0];

        function formatRupiah(angka) {
            let number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        }

        function calculateGrandTotal() {
            let totalRows = tableBody.querySelectorAll('tr');
            let grandTotal = 0;

            totalRows.forEach(row => {
                let qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
                let harga = parseFloat(row.querySelector('.harga-hidden')?.value) || 0;
                grandTotal += (qty * harga);
            });

            let grandTotalDisplay = document.getElementById('grandTotalDisplay');
            if (grandTotalDisplay) {
                grandTotalDisplay.value = formatRupiah(Math.round(grandTotal));
            }
        }

        function reindexRows() {
            let rows = tableBody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                let barangSelect = row.querySelector('select[name*="[item_id]"]');
                let barangIdHidden = row.querySelector('input[name*="[barang_id]"]');
                let namaBarangHidden = row.querySelector('input[name*="[nama_barang]"]');
                let kodeBarang = row.querySelector('input[name*="[kode_barang]"]');
                let kategori = row.querySelector('select[name*="[category]"]');
                let qty = row.querySelector('input[name*="[qty]"]');
                let satuan = row.querySelector('input[name*="[satuan]"]');
                let lokasi = row.querySelector('input[name*="[lokasi]"]');
                let harga = row.querySelector('input[name*="[harga]"]');
                let keterangan = row.querySelector('input[name*="[notes]"]');

                if (barangSelect) barangSelect.name = `items[${index}][item_id]`;
                if (barangIdHidden) barangIdHidden.name = `items[${index}][barang_id]`;
                if (namaBarangHidden) namaBarangHidden.name = `items[${index}][nama_barang]`;
                if (kodeBarang) kodeBarang.name = `items[${index}][kode_barang]`;
                if (kategori) kategori.name = `items[${index}][category]`;
                if (qty) qty.name = `items[${index}][qty]`;
                if (satuan) satuan.name = `items[${index}][satuan]`;
                if (lokasi) lokasi.name = `items[${index}][lokasi]`;
                if (harga) harga.name = `items[${index}][harga]`;
                if (keterangan) keterangan.name = `items[${index}][notes]`;
            });
        }

        function updateDeleteButtons() {
            let rows = tableBody.querySelectorAll('tr');
            rows.forEach((row) => {
                let removeBtn = row.querySelector('.remove-row');
                if (removeBtn) {
                    removeBtn.disabled = rows.length === 1;
                }
            });
        }

        function initRowEvents(row) {
            let barangSelect = row.querySelector('.barang-select');
            let barangIdHidden = row.querySelector('.barang-id-hidden');
            let namaBarangHidden = row.querySelector('.nama-barang-hidden');
            let kodeInput = row.querySelector('.kode-input');
            let kategoriSelect = row.querySelector('.kategori-input');
            let qtyInput = row.querySelector('.qty-input');
            let satuanInput = row.querySelector('.satuan-input');
            let lokasiInput = row.querySelector('.lokasi-input');
            let hargaInput = row.querySelector('.harga-input');
            let hargaHidden = row.querySelector('.harga-hidden');
            let totalDisplay = row.querySelector('.total-display');
            let removeBtn = row.querySelector('.remove-row');

            if (barangSelect) {
                barangSelect.addEventListener('change', function () {
                    let selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        let barangId = selectedOption.value;
                        let kode = selectedOption.getAttribute('data-kode') || '';
                        let nama = selectedOption.getAttribute('data-nama') || '';
                        let kategori = selectedOption.getAttribute('data-kategori') || '';
                        let satuan = selectedOption.getAttribute('data-satuan') || 'Pcs';
                        let harga = selectedOption.getAttribute('data-harga') || 0;
                        let lokasi = selectedOption.getAttribute('data-lokasi') || '';

                        if (barangIdHidden) barangIdHidden.value = barangId;
                        if (namaBarangHidden) namaBarangHidden.value = nama;
                        if (kodeInput) kodeInput.value = kode;
                        if (kategoriSelect && kategori) kategoriSelect.value = kategori;
                        if (satuanInput) satuanInput.value = satuan;
                        if (lokasiInput) lokasiInput.value = lokasi;

                        let cleanHarga = harga ? Math.round(parseFloat(harga)) : 0;
                        if (hargaInput) hargaInput.value = formatRupiah(cleanHarga);
                        if (hargaHidden) hargaHidden.value = cleanHarga;
                        calculateRow();
                    } else {
                        if (barangIdHidden) barangIdHidden.value = '';
                        if (namaBarangHidden) namaBarangHidden.value = '';
                        if (kodeInput) kodeInput.value = '';
                        if (satuanInput) satuanInput.value = '';
                        if (lokasiInput) lokasiInput.value = '';
                        if (hargaInput) hargaInput.value = '0';
                        if (hargaHidden) hargaHidden.value = '0';
                        calculateRow();
                    }
                });
            }

            function calculateRow() {
                let qty = parseFloat(qtyInput?.value) || 0;
                let harga = parseFloat(hargaHidden?.value) || 0;
                let total = Math.round(qty * harga);

                if (totalDisplay) totalDisplay.value = formatRupiah(total);
                calculateGrandTotal();
            }

            if (hargaInput) {
                hargaInput.addEventListener('keyup', function () {
                    let cleanVal = this.value.replace(/[^0-9]/g, '');
                    let parsedVal = cleanVal === '' ? 0 : parseInt(cleanVal, 10);
                    
                    hargaInput.value = formatRupiah(parsedVal);
                    hargaHidden.value = parsedVal;
                    calculateRow();
                });
            }

            if (qtyInput) {
                qtyInput.addEventListener('input', calculateRow);
            }

            if (removeBtn) {
                removeBtn.onclick = function () {
                    if (tableBody.rows.length > 1) {
                        row.remove();
                        reindexRows();
                        updateDeleteButtons();
                        calculateGrandTotal();
                    }
                };
            }
        }

        if (tableBody.rows.length > 0) {
            initRowEvents(tableBody.rows[0]);
            updateDeleteButtons();
        }

        // Tambah Baris Baru
        document.getElementById('addRow').addEventListener('click', function () {
            let firstRowSelect = tableBody.rows[0].querySelector('.barang-select');
            let optionsHTML = firstRowSelect ? firstRowSelect.innerHTML : '<option value="">-- Pilih dari Master Barang --</option>';
            
            let newRow = tableBody.insertRow();
            let newIndex = tableBody.rows.length - 1;
            
            newRow.classList.add('item-row');
            newRow.innerHTML = `
                <td class="py-3 px-2">
                    <input type="text" name="items[${newIndex}][kode_barang]" class="form-control bg-white bg-opacity-50 kode-input shadow-none fw-bold text-secondary" placeholder="Otomatis" readonly>
                    <input type="hidden" name="items[${newIndex}][barang_id]" class="barang-id-hidden">
                </td>
                <td class="py-3 px-2">
                    <select name="items[${newIndex}][item_id]" class="form-select bg-white bg-opacity-75 barang-select shadow-none" required>
                        ${optionsHTML}
                    </select>
                    <input type="hidden" name="items[${newIndex}][nama_barang]" class="nama-barang-hidden">
                </td>
                <td class="py-3 px-2">
                    <select name="items[${newIndex}][category]" class="form-select bg-white bg-opacity-75 kategori-input shadow-none">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Raw Material">Raw Material</option>
                        <option value="Work In Process">Work In Process</option>
                        <option value="Finished Goods">Finished Goods</option>
                        <option value="MRO / Sparepart">MRO / Sparepart</option>
                        <option value="Packing Material">Packing Material</option>
                    </select>
                </td>
                <td class="py-3 px-2">
                    <input type="number" name="items[${newIndex}][qty]" class="form-control bg-white bg-opacity-75 qty-input shadow-none" min="1" value="1" required>
                </td>
                <td class="py-3 px-2">
                    <input type="text" name="items[${newIndex}][satuan]" class="form-control satuan-input bg-white bg-opacity-50 shadow-none" placeholder="Satuan" required>
                </td>
                <td class="py-3 px-2">
                    <input type="text" name="items[${newIndex}][lokasi]" class="form-control bg-white bg-opacity-75 lokasi-input shadow-none" placeholder="Cth: Rak A-01">
                </td>
                <td class="py-3 px-2">
                    <input type="text" class="form-control bg-white bg-opacity-75 harga-input shadow-none" value="0" required>
                    <input type="hidden" name="items[${newIndex}][harga]" class="harga-hidden" value="0">
                </td>
                <td class="py-3 px-2">
                    <input type="text" class="form-control bg-white bg-opacity-50 total-display fw-bold text-dark shadow-none" readonly value="0">
                </td>
                <td class="py-3 px-2">
                    <input type="text" name="items[${newIndex}][notes]" class="form-control bg-white bg-opacity-75 shadow-none" placeholder="Ket...">
                </td>
                <td class="py-3 px-2 text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row rounded-3">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;

            initRowEvents(newRow);
            reindexRows();
            updateDeleteButtons();
            calculateGrandTotal();
        });
    });
</script>
@endpush