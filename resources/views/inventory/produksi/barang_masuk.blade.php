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
        <div>
            <a href="javascript:history.back()" class="btn btn-light border bg-white text-secondary px-3 py-2 fw-semibold shadow-sm rounded-3">
                <i class="fa-solid fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Form Utama -->
    <form action="{{ route('inventory.produksi.barang_masuk.store') }}" method="POST" id="transactionForm">
        @csrf
        
        <!-- CARD 1: INFORMASI HEADER TRANSAKSI (GLASS EFFECT) -->
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

        <!-- CARD 2: RINCIAN DETAIL BARANG (GLASS EFFECT) -->
        <div class="card glass-card rounded-4 mb-4 overflow-hidden">
            <div class="card-header glass-header py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-boxes-stacked fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Rincian Detail Barang Masuk</h5>
                        <p class="text-muted small mb-0">Ketik nama barang secara bebas atau pilih dari saran master data.</p>
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-sm px-3 py-2 shadow-sm rounded-3 fw-semibold" id="addRow">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Baris Barang
                </button>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table glass-table align-middle mb-0" id="detailTable">
                        <thead class="glass-header text-secondary text-uppercase fs-7" style="font-size: 0.75rem;">
                            <tr>
                                <th class="py-3 px-4" style="width: 32%;">Nama Barang <span class="text-danger">*</span></th>
                                <th class="py-3 px-3" style="width: 12%;">Qty <span class="text-danger">*</span></th>
                                <th class="py-3 px-3" style="width: 15%;">Satuan</th>
                                <th class="py-3 px-3" style="width: 18%;">Harga Satuan (Rp) <span class="text-danger">*</span></th>
                                <th class="py-3 px-3" style="width: 18%;">Total Subtotal (Rp)</th>
                                <th class="py-3 px-3 text-center" style="width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-3 px-4">
                                    <input type="text" name="detail[0][nama_barang]" class="form-control bg-white bg-opacity-75 barang-input shadow-none" list="listMasterBarang" placeholder="Ketik atau pilih nama barang..." required>
                                </td>
                                <td class="py-3 px-3">
                                    <input type="number" name="detail[0][qty]" class="form-control bg-white bg-opacity-75 qty-input shadow-none" min="1" value="1" required>
                                </td>
                                <td class="py-3 px-3">
                                    <input type="text" name="detail[0][satuan]" class="form-control satuan-input bg-white bg-opacity-50 shadow-none" placeholder="Satuan" required>
                                </td>
                                <td class="py-3 px-3">
                                    <input type="text" class="form-control bg-white bg-opacity-75 harga-input shadow-none" value="0" required>
                                    <input type="hidden" name="detail[0][harga]" class="harga-hidden" value="0">
                                </td>
                                <td class="py-3 px-3">
                                    <input type="text" class="form-control bg-white bg-opacity-50 total-display fw-bold text-dark shadow-none" readonly value="0">
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row rounded-3" disabled>
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer glass-header py-3 px-4 text-muted small">
                <i class="fa-solid fa-circle-info text-primary me-1"></i> Pastikan harga dan kuantitas sudah sesuai sebelum menyimpan transaksi untuk memperbarui stok otomatis.
            </div>
        </div>

        <!-- TOMBOL AKSI UTAMA -->
        <div class="d-flex justify-content-end align-items-center gap-3 mb-5">
            <a href="javascript:history.back()" class="btn btn-light px-4 py-2 fw-semibold border rounded-3 text-secondary">
                Batal
            </a>
            <button type="submit" class="btn btn-success px-5 py-2 fw-semibold shadow-sm rounded-3">
                <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Transaksi & Update Stock
            </button>
        </div>
    </form>
</div>

<!-- Datalist untuk opsi saran Master Barang secara opsional -->
<datalist id="listMasterBarang">
    @if(isset($barangs))
        @foreach($barangs as $barang)
            <option value="{{ $barang->name ?? $barang->nama_barang ?? $barang->nama }}" 
                    data-satuan="{{ $barang->unit ?? $barang->satuan ?? 'Pcs' }}" 
                    data-harga="{{ $barang->price ?? $barang->harga_beli ?? $barang->harga ?? 0 }}">
            </option>
        @endforeach
    @endif
</datalist>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let table = document.getElementById('detailTable').getElementsByTagName('tbody')[0];
        let rowIndex = 1; 

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

        document.getElementById('addRow').addEventListener('click', function () {
            let newRow = table.insertRow();
            
            newRow.innerHTML = `
                <td class="py-3 px-4">
                    <input type="text" name="detail[${rowIndex}][nama_barang]" class="form-control bg-white bg-opacity-75 barang-input shadow-none" list="listMasterBarang" placeholder="Ketik atau pilih nama barang..." required>
                </td>
                <td class="py-3 px-3">
                    <input type="number" name="detail[${rowIndex}][qty]" class="form-control bg-white bg-opacity-75 qty-input shadow-none" min="1" value="1" required>
                </td>
                <td class="py-3 px-3">
                    <input type="text" name="detail[${rowIndex}][satuan]" class="form-control satuan-input bg-white bg-opacity-50 shadow-none" placeholder="Satuan" required>
                </td>
                <td class="py-3 px-3">
                    <input type="text" class="form-control bg-white bg-opacity-75 harga-input shadow-none" value="0" required>
                    <input type="hidden" name="detail[${rowIndex}][harga]" class="harga-hidden" value="0">
                </td>
                <td class="py-3 px-3">
                    <input type="text" class="form-control bg-white bg-opacity-50 total-display fw-bold text-dark shadow-none" readonly value="0">
                </td>
                <td class="py-3 px-3 text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row rounded-3">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;

            initRowEvents(newRow);
            rowIndex++;
            reindexRows();
            updateDeleteButtons();
        });

        function initRowEvents(row) {
            let barangInput = row.querySelector('.barang-input');
            let qtyInput = row.querySelector('.qty-input');
            let satuanInput = row.querySelector('.satuan-input');
            let hargaInput = row.querySelector('.harga-input');
            let hargaHidden = row.querySelector('.harga-hidden');
            let totalDisplay = row.querySelector('.total-display');
            let removeBtn = row.querySelector('.remove-row');

            barangInput.addEventListener('input', function () {
                let val = this.value;
                let options = document.getElementById('listMasterBarang').options;
                
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === val) {
                        let satuan = options[i].getAttribute('data-satuan');
                        let harga = options[i].getAttribute('data-harga');

                        satuanInput.value = satuan || '';
                        let cleanHarga = harga ? Math.round(parseFloat(harga)) : 0;
                        hargaInput.value = formatRupiah(cleanHarga);
                        hargaHidden.value = cleanHarga;
                        calculate();
                        return;
                    }
                }
            });

            function calculate() {
                let qty = parseFloat(qtyInput.value) || 0;
                let harga = parseFloat(hargaHidden.value) || 0;
                let total = Math.round(qty * harga);

                totalDisplay.value = formatRupiah(total);
            }

            hargaInput.addEventListener('keyup', function () {
                let cleanVal = this.value.replace(/[^0-9]/g, '');
                let parsedVal = cleanVal === '' ? 0 : parseInt(cleanVal, 10);
                
                hargaInput.value = formatRupiah(parsedVal);
                hargaHidden.value = parsedVal;
                calculate();
            });

            qtyInput.addEventListener('input', calculate);

            removeBtn.onclick = function () {
                if (table.rows.length > 1) {
                    row.remove();
                    reindexRows();
                    updateDeleteButtons();
                }
            };
        }

        // Fungsi reindex input name biar array detail[0], detail[1], dst. urut rapi saat disubmit ke backend
        function reindexRows() {
            let rows = table.querySelectorAll('tr');
            rows.forEach((row, index) => {
                let namaBarang = row.querySelector('input[name*="[nama_barang]"]');
                let qty = row.querySelector('input[name*="[qty]"]');
                let satuan = row.querySelector('input[name*="[satuan]"]');
                let harga = row.querySelector('input[name*="[harga]"]');

                if (namaBarang) namaBarang.name = `detail[${index}][nama_barang]`;
                if (qty) qty.name = `detail[${index}][qty]`;
                if (satuan) satuan.name = `detail[${index}][satuan]`;
                if (harga) harga.name = `detail[${index}][harga]`;
            });
        }

        function updateDeleteButtons() {
            let rows = table.querySelectorAll('tr');
            rows.forEach((row) => {
                let removeBtn = row.querySelector('.remove-row');
                if (removeBtn) {
                    removeBtn.disabled = rows.length === 1;
                }
            });
        }

        if (table.rows.length > 0) {
            initRowEvents(table.rows[0]);
        }
        updateDeleteButtons();
    });
</script>
@endpush