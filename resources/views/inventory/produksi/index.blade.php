@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Panggil Komponen Notifikasi Toast di Sini -->
    <x-notification />

    <!-- Top Header Banner Card -->
    <div class="card border-0 shadow-sm rounded-4 glass-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3">
                    <i class="fa-solid fa-boxes-stacked fa-xl"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-dark mb-1">Dashboard Inventory Produksi</h3>
                    <p class="text-muted small mb-0">Sistem Monitoring Stok & Ketersediaan Bahan Produksi</p>
                </div>
            </div>
            <div>
                <!-- Tombol Switch langsung ke Dashboard PO -->
                <a href="{{ route('inventory.po.index') }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-semibold shadow-sm">
                    <i class="fa-solid fa-arrow-rotate-right me-1"></i> Switch PO Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Links -->
    <ul class="nav nav-pills gap-2 mb-4">
        <li class="nav-item">
            <a href="{{ route('inventory.produksi.index') }}" class="nav-link px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 tab-custom {{ request()->routeIs('inventory.produksi.index') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Dashboard Utama
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('inventory.produksi.master') }}" class="nav-link px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 tab-custom {{ request()->routeIs('inventory.produksi.master') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked"></i> Master Data
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('inventory.produksi.barang_masuk') }}" class="nav-link px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 tab-custom {{ request()->routeIs('inventory.produksi.barang_masuk') ? 'active' : '' }}">
                <i class="fa-solid fa-truck-ramp-box"></i> Barang Masuk
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('inventory.produksi.barang_keluar') }}" class="nav-link px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 tab-custom {{ request()->routeIs('inventory.produksi.barang_keluar') ? 'active' : '' }}">
                <i class="fa-solid fa-dolly"></i> Barang Keluar
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('inventory.produksi.laporan') }}" class="nav-link px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 tab-custom {{ request()->routeIs('inventory.produksi.laporan') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i> Stok & Laporan
            </a>
        </li>
    </ul>

    <!-- Main Content Card Container (Tabel Data Barang) -->
    <div class="card border-0 shadow-sm rounded-4 glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 border-bottom pb-3">
            <div class="d-flex gap-2">
                <span class="text-muted small fw-semibold text-uppercase align-self-center">Ringkasan Data & Inventaris Produksi</span>
            </div>
            <a href="{{ route('inventory.produksi.master') }}" class="btn btn-orange text-white btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Kelola Master Data
            </a>
        </div>
        <div class="table-responsive rounded-3 border border-light overflow-hidden bg-white bg-opacity-60">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-secondary">
                    <tr>
                        <th class="py-3 px-3">Kode Barang</th>
                        <th class="py-3">Nama Barang</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3">Stok Saat Ini</th>
                        <th class="py-3">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs ?? [] as $b)
                    <tr>
                        <td class="px-3 fw-semibold text-primary">{{ $b->item_code ?? '-' }}</td>
                        <td>{{ $b->name ?? '-' }}</td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">{{ $b->category ?? '-' }}</span></td>
                        <td class="fw-bold text-dark">{{ $b->stok ?? 0 }}</td>
                        <td>{{ $b->unit ?? 'Pcs' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="fa-solid fa-box-open fa-2x mb-2 opacity-50"></i>
                                <p class="mb-0 small">Belum ada data barang tersedia pada dashboard.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
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