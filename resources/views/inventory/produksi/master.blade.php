@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user && ($user->role == 'admin' || $user->role == 'super_admin');
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
                    
                    @if($isAdmin)
                        <!-- Tombol Trigger Modal Tambah Barang Baru -->
                        <button type="button" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
                            <i class="fa-solid fa-box-open me-2"></i>+ Tambah Master Barang Baru
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
                                @if($isAdmin)
                                    <th class="py-3 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangs ?? [] as $b)
                            @php 
                                $bKey = data_get($b, 'id') ?? data_get($b, 'kode_barang') ?? data_get($b, 'item_code') ?? $loop->index; 
                            @endphp
                            <tr>
                                <td class="px-3 fw-semibold" style="color: #ff6600;">{{ data_get($b, 'item_code') ?? data_get($b, 'kode_barang') ?? data_get($b, 'code') ?? '-' }}</td>
                                <td class="fw-semibold">{{ data_get($b, 'name') ?? data_get($b, 'nama_barang') ?? data_get($b, 'nama') ?? '-' }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">{{ data_get($b, 'category') ?? data_get($b, 'kategori') ?? '-' }}</span></td>
                                <td>{{ data_get($b, 'unit') ?? data_get($b, 'satuan') ?? 'Pcs' }}</td>
                                <td class="fw-bold text-dark">{{ data_get($b, 'current_stock') ?? data_get($b, 'stok') ?? 0 }}</td>
                                <td class="text-danger fw-semibold">{{ data_get($b, 'minimum_stock') ?? data_get($b, 'stok_min') ?? 0 }}</td>
                                <td><span class="badge bg-info bg-opacity-10 text-info px-2 py-1">{{ data_get($b, 'location') ?? data_get($b, 'lokasi') ?? data_get($b, 'rak') ?? '-' }}</span></td>
                                <td>Rp {{ number_format(data_get($b, 'price') ?? data_get($b, 'harga') ?? data_get($b, 'harga_satuan') ?? 0, 0, ',', '.') }}</td>
                                <td>{{ data_get($b, 'supplier_main') ?? data_get($b, 'supplier') ?? data_get($b, 'supplier_utama') ?? '-' }}</td>
                                <td>
                                    @if((data_get($b, 'status') ?? 'Aktif') == 'Aktif')
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Aktif</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">Non-Aktif</span>
                                    @endif
                                </td>
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
                    
                    @if($isAdmin)
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier">
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
                                @if($isAdmin)
                                    <th class="py-3 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers ?? [] as $s)
                            @php 
                                $sKey = data_get($s, 'id') ?? data_get($s, 'supplier_code') ?? data_get($s, 'kode_supplier') ?? $loop->index; 
                                $sCode = data_get($s, 'supplier_code') ?? data_get($s, 'kode_supplier') ?? data_get($s, 'code') ?? data_get($s, 'kode') ?? data_get($s, 'id') ?? '-';
                            @endphp
                            <tr>
                                <td class="px-3 fw-semibold" style="color: #ff6600;">{{ $sCode }}</td>
                                <td class="fw-semibold">{{ data_get($s, 'name') ?? data_get($s, 'nama') ?? data_get($s, 'nama_supplier') ?? '-' }}</td>
                                <td class="text-truncate" style="max-width: 180px;">{{ data_get($s, 'alamat') ?? data_get($s, 'address') ?? '-' }}</td>
                                <td>{{ data_get($s, 'phone') ?? data_get($s, 'no_telp') ?? data_get($s, 'telp') ?? '-' }}</td>
                                <td>{{ data_get($s, 'email') ?? '-' }}</td>
                                <td>{{ data_get($s, 'pic') ?? '-' }}</td>
                                <td>
                                    @if((data_get($s, 'status') ?? 'Aktif') == 'Aktif')
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Aktif</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">Non-Aktif</span>
                                    @endif
                                </td>
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
                            <select name="category" class="form-select form-select-sm bg-white border shadow-none" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Raw Material" {{ old('category', old('kategori')) == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                                <option value="Work In Process" {{ old('category', old('kategori')) == 'Work In Process' ? 'selected' : '' }}>Work In Process</option>
                                <option value="Finished Goods" {{ old('category', old('kategori')) == 'Finished Goods' ? 'selected' : '' }}>Finished Goods</option>
                                <option value="MRO / Sparepart" {{ old('category', old('kategori')) == 'MRO / Sparepart' ? 'selected' : '' }}>MRO / Sparepart</option>
                                <option value="Packing Material" {{ old('category', old('kategori')) == 'Packing Material' ? 'selected' : '' }}>Packing Material</option>
                            </select>
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
                        
                        <!-- Input Harga Format Rupiah -->
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
                            <select class="form-select form-select-sm bg-white border shadow-none" name="supplier_main">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers ?? [] as $s)
                                    @php 
                                        $sName = data_get($s, 'name') ?? data_get($s, 'nama_supplier') ?? data_get($s, 'nama');
                                    @endphp
                                    <option value="{{ $sName }}" {{ old('supplier_main', old('supplier')) == $sName ? 'selected' : '' }}>
                                        {{ $sName }}
                                    </option>
                                @endforeach
                            </select>
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
@endif

<!-- ================= MODAL EDIT BARANG ================= -->
@foreach($barangs ?? [] as $b)
@php 
    $bKey = data_get($b, 'id') ?? data_get($b, 'kode_barang') ?? data_get($b, 'item_code') ?? $loop->index; 
    $bPrice = data_get($b, 'price') ?? data_get($b, 'harga') ?? data_get($b, 'harga_satuan') ?? 0;
    $bCategory = data_get($b, 'category') ?? data_get($b, 'kategori') ?? '';
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
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="item_code" value="{{ data_get($b, 'item_code') ?? data_get($b, 'kode_barang') ?? data_get($b, 'code') ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Nama Barang</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="name" value="{{ data_get($b, 'name') ?? data_get($b, 'nama_barang') ?? data_get($b, 'nama') ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Kategori</label>
                            <select name="category" class="form-select form-select-sm bg-white border shadow-none" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Raw Material" {{ $bCategory == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                                <option value="Work In Process" {{ $bCategory == 'Work In Process' ? 'selected' : '' }}>Work In Process</option>
                                <option value="Finished Goods" {{ $bCategory == 'Finished Goods' ? 'selected' : '' }}>Finished Goods</option>
                                <option value="MRO / Sparepart" {{ $bCategory == 'MRO / Sparepart' ? 'selected' : '' }}>MRO / Sparepart</option>
                                <option value="Packing Material" {{ $bCategory == 'Packing Material' ? 'selected' : '' }}>Packing Material</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Satuan</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="unit" value="{{ data_get($b, 'unit') ?? data_get($b, 'satuan') ?? 'Pcs' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Stok Saat Ini</label>
                            <input type="number" class="form-control form-control-sm bg-white border shadow-none" name="stok" min="0" value="{{ data_get($b, 'current_stock') ?? data_get($b, 'stok') ?? 0 }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Stok Minimum</label>
                            <input type="number" class="form-control form-control-sm bg-white border shadow-none" name="minimum_stock" min="0" value="{{ data_get($b, 'minimum_stock') ?? data_get($b, 'stok_min') ?? 5 }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Lokasi / Rak</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="location" value="{{ data_get($b, 'location') ?? data_get($b, 'lokasi') ?? data_get($b, 'rak') ?? '' }}">
                        </div>

                        <!-- Input Harga Format Rupiah Edit -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Harga Satuan</label>
                            <div class="input-group input-group-sm shadow-none">
                                <span class="input-group-text bg-light border text-secondary fw-bold">Rp</span>
                                <input type="text" class="form-control form-control-sm bg-white border shadow-none rupiah-input" placeholder="0" value="{{ old('price', $bPrice) }}">
                                <input type="hidden" name="price" class="rupiah-hidden" value="{{ old('price', $bPrice) }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Supplier Utama</label>
                            <select class="form-select form-select-sm bg-white border shadow-none" name="supplier_main">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers ?? [] as $s)
                                    @php 
                                        $sName = data_get($s, 'name') ?? data_get($s, 'nama_supplier') ?? data_get($s, 'nama');
                                        $currentSupplier = data_get($b, 'supplier_main') ?? data_get($b, 'supplier') ?? data_get($b, 'supplier_utama');
                                    @endphp
                                    <option value="{{ $sName }}" {{ $currentSupplier == $sName ? 'selected' : '' }}>
                                        {{ $sName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Status Aktif</label>
                            <select class="form-select form-select-sm bg-white border shadow-none" name="status">
                                <option value="Aktif" {{ (data_get($b, 'status') ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Non-Aktif" {{ (data_get($b, 'status') ?? 'Aktif') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
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
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="supplier_code" value="{{ old('supplier_code') }}" required placeholder="Contoh: SUP-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Nama Supplier (Wajib)</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="name" value="{{ old('name') }}" required placeholder="Nama perusahaan/supplier">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-secondary">Alamat</label>
                            <textarea class="form-control form-control-sm bg-white border shadow-none" name="alamat" rows="2" placeholder="Alamat lengkap supplier">{{ old('alamat') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">No. Telepon</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 08123456789">
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
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT SUPPLIER ================= -->
@foreach($suppliers ?? [] as $s)
@php 
    $sId      = data_get($s, 'id');
    $sCode    = data_get($s, 'supplier_code') ?? data_get($s, 'kode_supplier') ?? data_get($s, 'code') ?? '';
    $sName    = data_get($s, 'name') ?? data_get($s, 'nama_supplier') ?? '';
    $sAddress = data_get($s, 'alamat') ?? data_get($s, 'address') ?? '';
    $sPhone   = data_get($s, 'phone') ?? data_get($s, 'kontak') ?? '';
    $sEmail   = data_get($s, 'email') ?? '';
    $sPic     = data_get($s, 'pic') ?? '';
    $sStatus  = data_get($s, 'status') ?? 'Aktif';
@endphp
<div class="modal fade" id="modalEditSupplier{{ $sId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 bg-white">
            <form action="{{ route('inventory.produksi.supplier.update', $sId) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark"><i class="fa-solid fa-pen-to-square text-warning me-2"></i>Edit Data Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Kode Supplier (Unik)</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="supplier_code" value="{{ $sCode }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Nama Supplier</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="name" value="{{ $sName }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-secondary">Alamat</label>
                            <textarea class="form-control form-control-sm bg-white border shadow-none" name="alamat" rows="2">{{ $sAddress }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">No. Telepon</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="phone" value="{{ $sPhone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Email</label>
                            <input type="email" class="form-control form-control-sm bg-white border shadow-none" name="email" value="{{ $sEmail }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">PIC (Contact Person)</label>
                            <input type="text" class="form-control form-control-sm bg-white border shadow-none" name="pic" value="{{ $sPic }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Status Aktif</label>
                            <select class="form-select form-select-sm bg-white border shadow-none" name="status">
                                <option value="Aktif" {{ $sStatus == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Non-Aktif" {{ $sStatus == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Handling Input Rupiah Visual Mask & Real Value
    const rupiahInputs = document.querySelectorAll('.rupiah-input');
    
    rupiahInputs.forEach(input => {
        const hiddenInput = input.parentElement.querySelector('.rupiah-hidden');
        
        if (input.value) {
            input.value = formatRupiah(input.value);
        }
        
        input.addEventListener('keyup', function () {
            let cleanNumber = this.value.replace(/[^,\d]/g, '').toString();
            this.value = formatRupiah(cleanNumber);
            if (hiddenInput) {
                hiddenInput.value = cleanNumber;
            }
        });
    });

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

    // Retain Tab Active state jika ada URL query ?tab=supplier
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'supplier') {
        const supplierTabTrigger = document.querySelector('#tab-supplier');
        if (supplierTabTrigger) {
            const tab = new bootstrap.Tab(supplierTabTrigger);
            tab.show();
        }
    }
});
</script>
@endsection