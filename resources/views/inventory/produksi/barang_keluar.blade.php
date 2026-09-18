@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-uppercase fs-7 fw-bold text-muted" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-secondary">Inventory</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-secondary">Produksi</a></li>
                    <li class="breadcrumb-item active text-danger" aria-current="page">Barang Keluar</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">
                <i class="fa-solid fa-cart-arrow-up text-danger me-2"></i>Form Transaksi Barang Keluar
            </h1>
        </div>
        <div>
            <a href="javascript:history.back()" class="btn btn-light border bg-white text-secondary px-3 py-2 fw-semibold shadow-sm rounded-3">
                <i class="fa-solid fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Form Utama -->
    <form action="{{ route('inventory.produksi.barang_keluar.store') }}" method="POST" id="transactionForm">
        @csrf
        
        <!-- CARD 1: INFORMASI HEADER TRANSAKSI -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center">
                <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-file-invoice fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Informasi Header Transaksi</h5>
                    <p class="text-muted small mb-0">Data umum, tujuan pengeluaran, dan nomor referensi.</p>
                </div>
            </div>
            <div class="card-body p-4 bg-light bg-opacity-25">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">No. Transaksi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-hashtag"></i></span>
                            <input type="text" name="no_transaksi" class="form-control bg-white border-start-0 ps-0 fw-bold text-dark" value="TRX-OUT-{{ date('Ymd') }}-001" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Tanggal Keluar <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-regular fa-calendar"></i></span>
                            <input type="date" name="tanggal" class="form-control bg-white border-start-0 ps-0" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Tujuan / Divisi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-building-user"></i></span>
                            <input type="text" name="tujuan" class="form-control bg-white border-start-0 ps-0" placeholder="Contoh: Divisi Produksi / Cabang A" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">No. Referensi / Surat <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-receipt"></i></span>
                            <input type="text" name="no_referensi" class="form-control bg-white border-start-0 ps-0" placeholder="Contoh: REF/OUT/2026/001" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Admin / Petugas</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-user-shield"></i></span>
                            <input type="text" name="admin" class="form-control bg-white border-start-0 ps-0 text-muted" value="{{ auth()->user()->name ?? 'Admin Gudang' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Keterangan Catatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-comment-dots"></i></span>
                            <input type="text" name="keterangan" class="form-control bg-white border-start-0 ps-0" placeholder="Catatan tambahan (opsional)...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 2: RINCIAN DETAIL BARANG KELUAR -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-boxes-stacked fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Rincian Detail Barang Keluar</h5>
                        <p class="text-muted small mb-0">Ketik nama barang secara bebas atau pilih dari saran master data.</p>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm px-3 py-2 shadow-sm rounded-3 fw-semibold" id="addRow">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Baris Barang
                </button>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="detailTable">
                        <thead class="table-light text-secondary text-uppercase fs-7" style="font-size: 0.75rem;">
                            <tr>
                                <th class="py-3 px-4" style="width: 40%;">Nama Barang <span class="text-danger">*</span></th>
                                <th class="py-3 px-3" style="width: 15%;">Qty <span class="text-danger">*</span></th>
                                <th class="py-3 px-3" style="width: 25%;">Satuan</th>
                                <th class="py-3 px-3 text-center" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-3 px-4">
                                    <input type="text" name="detail[0][nama_barang]" class="form-control barang-input shadow-none" list="listMasterBarang" placeholder="Ketik atau pilih nama barang..." required>
                                </td>
                                <td class="py-3 px-3">
                                    <input type="number" name="detail[0][qty]" class="form-control qty-input shadow-none" min="1" value="1" required>
                                </td>
                                <td class="py-3 px-3">
                                    <input type="text" name="detail[0][satuan]" class="form-control satuan-input bg-light shadow-none" placeholder="Satuan" required>
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
            
            <div class="card-footer bg-white py-3 px-4 text-muted small border-top">
                <i class="fa-solid fa-circle-info text-primary me-1"></i> Pastikan jumlah kuantitas barang keluar sudah sesuai untuk mengurangi stok otomatis.
            </div>
        </div>

        <!-- TOMBOL AKSI UTAMA -->
        <div class="d-flex justify-content-end align-items-center gap-3 mb-5">
            <a href="javascript:history.back()" class="btn btn-light px-4 py-2 fw-semibold border rounded-3 text-secondary">
                Batal
            </a>
            <button type="submit" class="btn btn-danger px-5 py-2 fw-semibold shadow-sm rounded-3">
                <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Transaksi & Kurangi Stok
            </button>
        </div>
    </form>
</div>

<!-- Datalist untuk opsi saran Master Barang secara opsional -->
<datalist id="listMasterBarang">
    @if(isset($barangs))
        @foreach($barangs as $barang)
            <option value="{{ $barang->name ?? $barang->nama_barang ?? $barang->nama }}" 
                    data-satuan="{{ $barang->unit ?? $barang->satuan ?? 'Pcs' }}">
        @endforeach
    @endif
</datalist>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let table = document.getElementById('detailTable').getElementsByTagName('tbody')[0];
        let rowIndex = 1; 

        // Tambah Baris Baru
        document.getElementById('addRow').addEventListener('click', function () {
            let newRow = table.insertRow();
            
            newRow.innerHTML = `
                <td class="py-3 px-4">
                    <input type="text" name="detail[${rowIndex}][nama_barang]" class="form-control barang-input shadow-none" list="listMasterBarang" placeholder="Ketik atau pilih nama barang..." required>
                </td>
                <td class="py-3 px-3">
                    <input type="number" name="detail[${rowIndex}][qty]" class="form-control qty-input shadow-none" min="1" value="1" required>
                </td>
                <td class="py-3 px-3">
                    <input type="text" name="detail[${rowIndex}][satuan]" class="form-control satuan-input bg-light shadow-none" placeholder="Satuan" required>
                </td>
                <td class="py-3 px-3 text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row rounded-3">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;

            initRowEvents(newRow);
            rowIndex++;
            updateDeleteButtons();
        });

        function initRowEvents(row) {
            let barangInput = row.querySelector('.barang-input');
            let satuanInput = row.querySelector('.satuan-input');
            let removeBtn = row.querySelector('.remove-row');

            // Deteksi jika user memilih opsi dari datalist master barang
            barangInput.addEventListener('input', function () {
                let val = this.value;
                let options = document.getElementById('listMasterBarang').options;
                
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === val) {
                        let satuan = options[i].getAttribute('data-satuan');
                        satuanInput.value = satuan || '';
                        return;
                    }
                }
            });

            removeBtn.onclick = function () {
                if (table.rows.length > 1) {
                    row.remove();
                    updateDeleteButtons();
                }
            };
        }

        function updateDeleteButtons() {
            let rows = table.querySelectorAll('tr');
            rows.forEach((row) => {
                let removeBtn = row.querySelector('.remove-row');
                removeBtn.disabled = rows.length === 1;
            });
        }

        if (table.rows.length > 0) {
            initRowEvents(table.rows[0]);
        }
        updateDeleteButtons();
    });
</script>
@endpush