@extends('layouts.app')

@section('content')
@php
    $isAdmin = auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin');
@endphp

<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-boxes-stacked text-orange me-2"></i>Master Data Manajemen
            </h3>
            <p class="text-muted small mb-0">Pusat pengelolaan database master barang dan data supplier rekanan produksi.</p>
        </div>
        <div>
            <a href="{{ route('inventory.produksi.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi Validasi / Error -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-triangle-exclamation fa-lg me-3"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Validasi Gagal</h5>
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

    <!-- Sub Navigation Tabs -->
    <ul class="nav nav-pills gap-2 mb-4" id="masterTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 tab-custom" id="tab-barang" data-bs-toggle="tab" data-bs-target="#pane-barang" type="button" role="tab">
                <i class="fa-solid fa-box"></i> Kelola Barang
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 tab-custom" id="tab-supplier" data-bs-toggle="tab" data-bs-target="#pane-supplier" type="button" role="tab">
                <i class="fa-solid fa-truck-field"></i> Kelola Supplier
            </button>
        </li>
    </ul>

    <!-- Main Content Card Container -->
    <div class="card border-0 shadow-sm rounded-4 glass-card p-4">
        <div class="tab-content" id="masterTabContent">
            
           <!-- Pane 1: Kelola Barang -->
            <div class="tab-pane fade show active" id="pane-barang" role="tabpanel" aria-labelledby="tab-barang">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 border-bottom pb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-boxes-packing me-2" style="color: #ff6600;"></i>Daftar Master Barang</h5>
                    
                    {{-- TOMBOL TAMBAH BARANG (Aman dari null) --}}
                    @if($isAdmin)
                        <button type="button" class="btn btn-orange text-white btn-sm rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Barang Baru
                        </button>
                    @endif
                </div>
                
                <div class="table-responsive rounded-3 border border-light overflow-hidden bg-white bg-opacity-60">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light text-uppercase fs-7 text-secondary">
                            <tr>
                                <th class="py-3 px-3">Kode Barang/ID</th>
                                <th class="py-3">Nama Barang</th>
                                <th class="py-3">Kategori</th>
                                <th class="py-3">Satuan</th>
                                <th class="py-3">Stok Saat Ini</th>
                                <th class="py-3">Stok Min</th>
                                <th class="py-3">Lokasi / Rak</th>
                                <th class="py-3">Harga Satuan</th>
                                <th class="py-3">Supplier Utama</th>
                                <th class="py-3">Status</th>
                                
                                {{-- KOLOM AKSI --}}
                                @if($isAdmin)
                                    <th class="py-3 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangs ?? [] as $b)
                            @php 
                                $bKey = $b->id ?? $b->kode_barang ?? $b->item_code ?? $loop->index; 
                            @endphp
                            <tr>
                                <td class="px-3 fw-semibold" style="color: #ff6600;">{{ $b->item_code ?? $b->kode_barang ?? $b->code ?? '-' }}</td>
                                <td class="fw-semibold">{{ $b->name ?? $b->nama_barang ?? $b->nama ?? '-' }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">{{ $b->category ?? $b->kategori ?? '-' }}</span></td>
                                <td>{{ $b->unit ?? $b->satuan ?? 'Pcs' }}</td>
                                <td class="fw-bold text-dark">{{ $b->current_stock ?? $b->stok ?? 0 }}</td>
                                <td class="text-danger fw-semibold">{{ $b->minimum_stock ?? $b->stok_min ?? 0 }}</td>
                                <td><span class="badge bg-info bg-opacity-10 text-info px-2 py-1">{{ $b->location ?? $b->lokasi ?? $b->rak ?? '-' }}</span></td>
                                <td>Rp {{ number_format($b->price ?? $b->harga ?? $b->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                <td>{{ $b->supplier_main ?? $b->supplier ?? $b->supplier_utama ?? '-' }}</td>
                                <td>
                                    @if(($b->status ?? 'Aktif') == 'Aktif')
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Aktif</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">Non-Aktif</span>
                                    @endif
                                </td>
                                
                                {{-- TOMBOL EDIT & HAPUS --}}
                                @if($isAdmin)
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-warning text-dark px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalEditBarang{{ $bKey }}" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <form action="{{ route('inventory.produksi.barang.destroy', $bKey) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? '11' : '10' }}" class="text-center py-5 text-muted">
                                    <div class="py-3">
                                        <i class="fa-solid fa-box-open fa-2x mb-2 opacity-50"></i>
                                        <p class="mb-0 small">Belum ada data barang tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pane 2: Kelola Supplier -->
            <div class="tab-pane fade {{ request('tab') == 'supplier' ? 'show active' : '' }}" id="pane-supplier" role="tabpanel" aria-labelledby="tab-supplier">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 border-bottom pb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-truck-field me-2" style="color: #ff6600;"></i>Daftar Supplier Rekanan</h5>
                    
                    {{-- TOMBOL TAMBAH SUPPLIER --}}
                    @if($isAdmin)
                        <button type="button" class="btn btn-orange text-white btn-sm rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Supplier Baru
                        </button>
                    @endif
                </div>
                
                <div class="table-responsive rounded-3 border border-light overflow-hidden bg-white bg-opacity-60">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light text-uppercase fs-7 text-secondary">
                            <tr>
                                <th class="py-3 px-3">Kode Supplier</th>
                                <th class="py-3">Nama Supplier</th>
                                <th class="py-3">Alamat</th>
                                <th class="py-3">No. Telp</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">PIC</th>
                                <th class="py-3">Status</th>
                                
                                {{-- KOLOM AKSI SUPPLIER --}}
                                @if($isAdmin)
                                    <th class="py-3 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers ?? [] as $s)
                            @php 
                                $sKey = $s->id ?? $s->kode_supplier ?? $s->supplier_code ?? $loop->index; 
                            @endphp
                            <tr>
                                <td class="px-3 fw-semibold" style="color: #ff6600;">{{ $s->supplier_code ?? $s->kode_supplier ?? '-' }}</td>
                                <td class="fw-semibold">{{ $s->name ?? $s->nama ?? '-' }}</td>
                                <td class="text-truncate" style="max-width: 180px;">{{ $s->alamat ?? $s->address ?? '-' }}</td>
                                <td>{{ $s->phone ?? $s->no_telp ?? '-' }}</td>
                                <td>{{ $s->email ?? '-' }}</td>
                                <td>{{ $s->pic ?? '-' }}</td>
                                <td>
                                    @if(($s->status ?? 'Aktif') == 'Aktif')
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Aktif</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">Non-Aktif</span>
                                    @endif
                                </td>
                                
                                {{-- TOMBOL EDIT & HAPUS SUPPLIER --}}
                                @if($isAdmin)
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-warning text-dark px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalEditSupplier{{ $sKey }}" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <form action="{{ route('inventory.produksi.supplier.destroy', $sKey) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? '8' : '7' }}" class="text-center py-5 text-muted">
                                    <div class="py-3">
                                        <i class="fa-solid fa-truck-ramp-box fa-2x mb-2 opacity-50"></i>
                                        <p class="mb-0 small">Belum ada data supplier tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ================= MODAL TAMBAH BARANG ================= -->
@if($isAdmin)
<div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 bg-white">
            <form action="{{ route('inventory.produksi.barang.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark"><i class="fa-solid fa-box text-orange me-2"></i>Tambah Data Barang Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Kode Barang / ID (Unik)</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="item_code" value="{{ old('item_code', old('kode_barang')) }}" required placeholder="Contoh: BRG-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Nama Barang (Wajib)</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="name" value="{{ old('name', old('nama_barang')) }}" required placeholder="Nama barang">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Kategori (Wajib)</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="category" value="{{ old('category', old('kategori')) }}" required placeholder="Contoh: Bahan Baku">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Satuan (Valid)</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="unit" value="{{ old('unit', old('satuan', 'Pcs')) }}" required placeholder="Pcs / Kg / Box">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Stok Saat Ini (Min: 0)</label>
                            <input type="number" class="form-control form-control-sm bg-white border shadow-none" name="current_stock" min="0" value="{{ old('current_stock', old('stok', 0)) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Stok Minimum (Min: 0)</label>
                            <input type="number" class="form-control form-control-sm bg-white border shadow-none" name="minimum_stock" min="0" value="{{ old('minimum_stock', old('stok_min', 5)) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Lokasi / Rak</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="location" value="{{ old('location', old('lokasi')) }}" placeholder="Contoh: Rak A-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Harga Satuan</label>
                            <div class="input-group input-group-sm shadow-none">
                                <span class="input-group-text bg-light border text-secondary fw-bold">Rp</span>
                                <input type="text" class="form-control form-control-sm bg-white border shadow-none rupiah-input" placeholder="0" value="{{ old('price', old('harga', 0)) }}">
                                <input type="hidden" name="price" class="rupiah-hidden" value="{{ old('price', old('harga', 0)) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Supplier Utama</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="supplier_main" value="{{ old('supplier_main', old('supplier')) }}" placeholder="Nama supplier utama">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Status Aktif</label>
                            <select class="form-select form-select-sm bg-white border shadow-none" name="status">
                                <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Non-Aktif" {{ old('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">Simpan ke DB Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT BARANG ================= -->
@foreach($barangs ?? [] as $b)
@php 
    $bKey = $b->id ?? $b->kode_barang ?? $b->item_code ?? $loop->index; 
    $bPrice = $b->price ?? $b->harga ?? $b->harga_satuan ?? 0;
@endphp
<div class="modal fade" id="modalEditBarang{{ $bKey }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 bg-white">
            <form action="{{ route('inventory.produksi.barang.update', $bKey) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark"><i class="fa-solid fa-pen-to-square text-warning me-2"></i>Edit Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Kode Barang / ID</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="item_code" value="{{ $b->item_code ?? $b->kode_barang ?? $b->code ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Nama Barang</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="name" value="{{ $b->name ?? $b->nama_barang ?? $b->nama ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Kategori</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="category" value="{{ $b->category ?? $b->kategori ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Satuan</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="unit" value="{{ $b->unit ?? $b->satuan ?? 'Pcs' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Stok Saat Ini</label>
                            <input type="number" class="form-control form-control-sm bg-white border shadow-none" name="current_stock" min="0" value="{{ $b->current_stock ?? $b->stok ?? 0 }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Stok Minimum</label>
                            <input type="number" class="form-control form-control-sm bg-white border shadow-none" name="minimum_stock" min="0" value="{{ $b->minimum_stock ?? $b->stok_min ?? 5 }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Lokasi / Rak</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="location" value="{{ $b->location ?? $b->lokasi ?? $b->rak ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Harga Satuan</label>
                            <div class="input-group input-group-sm shadow-none">
                                <span class="input-group-text bg-light border text-secondary fw-bold">Rp</span>
                                <input type="text" class="form-control form-control-sm bg-white border shadow-none rupiah-input" placeholder="0" value="{{ number_format($bPrice, 0, ',', '.') }}">
                                <input type="hidden" name="price" class="rupiah-hidden" value="{{ $bPrice }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Supplier Utama</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="supplier_main" value="{{ $b->supplier_main ?? $b->supplier ?? $b->supplier_utama ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Status Aktif</label>
                            <select class="form-select form-select-sm bg-white border shadow-none" name="status">
                                <option value="Aktif" {{ ($b->status ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Non-Aktif" {{ ($b->status ?? 'Aktif') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm rounded-pill px-4 fw-semibold text-dark shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- ================= MODAL TAMBAH SUPPLIER ================= -->
<div class="modal fade" id="modalTambahSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 bg-white">
            <form action="{{ route('inventory.produksi.supplier.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark"><i class="fa-solid fa-truck-field text-success me-2"></i>Tambah Data Supplier Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Kode Supplier (Unik)</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="supplier_code" value="{{ old('supplier_code', old('kode_supplier')) }}" required placeholder="Contoh: SUP-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Nama Supplier (Wajib)</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="name" value="{{ old('name', old('nama')) }}" required placeholder="Nama perusahaan/supplier">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">No. Telepon</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="phone" value="{{ old('phone', old('no_telp')) }}" placeholder="Contoh: 08123456789">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Email</label>
                            <input type="email" class="form-control form-control-sm bg-white border shadow-none" name="email" value="{{ old('email') }}" placeholder="email@supplier.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">PIC (Contact Person)</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="pic" value="{{ old('pic') }}" placeholder="Nama PIC">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Status</label>
                            <select class="form-select form-select-sm bg-white border shadow-none" name="status">
                                <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Non-Aktif" {{ old('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Alamat</label>
                            <textarea class="form-control form-control-sm bg-white border shadow-none" name="alamat" rows="2" placeholder="Alamat lengkap supplier">{{ old('alamat', old('address')) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 shadow-sm">Simpan ke DB Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif


<!-- Script JavaScript untuk Format Rupiah Otomatis pada Input Harga -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rupiahInputs = document.querySelectorAll('.rupiah-input');

        rupiahInputs.forEach(input => {
            input.addEventListener('input', function (e) {
                let value = this.value.replace(/[^,\d]/g, '').toString();
                let split = value.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                this.value = rupiah;

                // Update nilai ke hidden input yang akan dikirim ke backend database
                let hiddenInput = this.parentElement.querySelector('.rupiah-hidden');
                if (hiddenInput) {
                    hiddenInput.value = value.replace(/\./g, '');
                }
            });
        });
    });
</script>

<style>
    .modal {
        z-index: 1056 !important;
    }
    .modal-backdrop {
        z-index: 1055 !important;
        background-color: rgba(0, 0, 0, 0.5) !important;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
    }
    .btn-orange {
        background-color: #ff6600;
        border-color: #ff6600;
        transition: all 0.2s ease;
    }
    .btn-orange:hover {
        background-color: #e65c00;
        border-color: #e65c00;
    }
    .fs-7 {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .nav-pills .nav-link.tab-custom {
        color: #64748b;
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: all 0.2s ease;
    }
    .nav-pills .nav-link.tab-custom:hover {
        color: #ff6600;
        background: rgba(255, 102, 0, 0.08);
    }
    .nav-pills .nav-link.active.tab-custom {
        color: #ffffff !important;
        background: #ff6600 !important;
        border-color: #ff6600 !important;
        box-shadow: 0 4px 12px rgba(255, 102, 0, 0.3);
    }
</style>
@endsection