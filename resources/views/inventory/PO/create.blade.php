@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-box-archive text-success me-2"></i>Input Data Penyimpanan PO
            </h3>
            <p class="text-muted small mb-0">Form pencatatan produk jadi dari Purchase Order ke dalam inventory gudang.</p>
        </div>
        <div>
            <a href="{{ route('inventory.po.index') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold shadow-sm border">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Alert Validasi -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-triangle-exclamation fa-lg me-3"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">Gagal Menyimpan Data</h6>
                    <ul class="mb-0 small ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <form action="{{ route('inventory.po.store') }}" method="POST">
            @csrf
            
            <!-- Section 1: Informasi PO & Produksi -->
            <div class="mb-4 pb-2 border-bottom">
                <h6 class="fw-bold text-success text-uppercase ls-1"><i class="fa-solid fa-clipboard-list me-2"></i>Informasi PO & Produksi</h6>
            </div>
            
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary small text-uppercase">No. Purchase Order <span class="text-danger">*</span></label>
                    <div class="input-group custom-input-group">
                        <span class="input-group-text"><i class="fa-solid fa-hashtag"></i></span>
                        <input type="text" name="no_po" class="form-control @error('no_po') is-invalid @enderror" placeholder="Contoh: PO-2026-001" value="{{ old('no_po') }}" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary small text-uppercase">Produk Jadi <span class="text-danger">*</span></label>
                    <div class="input-group custom-input-group">
                        <span class="input-group-text"><i class="fa-solid fa-box-open"></i></span>
                        <input type="text" name="produk_jadi" class="form-control @error('produk_jadi') is-invalid @enderror" placeholder="Nama produk hasil produksi" value="{{ old('produk_jadi') }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary small text-uppercase">Jumlah Produksi <span class="text-danger">*</span></label>
                    <div class="input-group custom-input-group">
                        <span class="input-group-text"><i class="fa-solid fa-cubes"></i></span>
                        <input type="number" name="jumlah_produksi" class="form-control @error('jumlah_produksi') is-invalid @enderror" placeholder="Masukkan jumlah qty" min="1" value="{{ old('jumlah_produksi') }}" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary small text-uppercase">Tanggal Produksi <span class="text-danger">*</span></label>
                    <div class="input-group custom-input-group">
                        <span class="input-group-text"><i class="fa-regular fa-calendar-check"></i></span>
                        <input type="date" name="tanggal_produksi" class="form-control @error('tanggal_produksi') is-invalid @enderror" value="{{ old('tanggal_produksi', date('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>

            <!-- Section 2: Logistik & Penyimpanan -->
            <div class="mb-4 pb-2 border-bottom">
                <h6 class="fw-bold text-info text-uppercase ls-1"><i class="fa-solid fa-warehouse me-2"></i>Logistik & Penyimpanan</h6>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary small text-uppercase">Lokasi Rak / Zona <span class="text-danger">*</span></label>
                    <div class="input-group custom-input-group">
                        <span class="input-group-text"><i class="fa-solid fa-map-location-dot"></i></span>
                        <input type="text" name="lokasi_penyimpanan" class="form-control @error('lokasi_penyimpanan') is-invalid @enderror" placeholder="Contoh: Rak A-03 Gudang Barang Jadi" value="{{ old('lokasi_penyimpanan') }}" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary small text-uppercase">Deadline Pengiriman <span class="text-danger">*</span></label>
                    <div class="input-group custom-input-group">
                        <span class="input-group-text"><i class="fa-solid fa-truck-fast"></i></span>
                        <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror" value="{{ old('deadline') }}" required>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end align-items-center mt-5 gap-2 pt-3 border-top">
                <button type="reset" class="btn btn-light rounded-pill px-4 fw-semibold text-muted">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                </button>
                <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm btn-submit">
                    <i class="fa-solid fa-save me-2"></i> Simpan ke Gudang
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Styling Typography */
    .ls-1 {
        letter-spacing: 0.5px;
    }
    
    /* Custom Input Group Styling */
    .custom-input-group .input-group-text {
        background-color: #f8fafc;
        border-right: none;
        color: #94a3b8;
        transition: all 0.2s ease-in-out;
    }
    
    .custom-input-group .form-control {
        border-left: none;
        padding-left: 0;
        background-color: #f8fafc;
    }
    
    .custom-input-group .form-control:focus {
        background-color: #ffffff;
        box-shadow: none;
        border-color: #86b7fe;
    }
    
    .custom-input-group .form-control:focus + .input-group-text,
    .custom-input-group:focus-within .input-group-text {
        background-color: #ffffff;
        border-color: #86b7fe;
        color: #198754; /* Warna hijau sukses saat input aktif */
    }

    /* Input error styling override */
    .custom-input-group .form-control.is-invalid {
        border-color: #dc3545;
        background-image: none; /* Hilangkan icon seru bawaan bootstrap agar tidak menumpuk */
    }
    .custom-input-group:has(.is-invalid) .input-group-text {
        border-color: #dc3545;
        color: #dc3545;
    }

    /* Button Hover Animation */
    .btn-submit {
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #198754 0%, #157347 100%);
        border: none;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(25, 135, 84, 0.4) !important;
    }
</style>
@endsection