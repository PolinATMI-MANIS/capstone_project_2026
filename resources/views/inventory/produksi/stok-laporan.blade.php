@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Top Header Banner Card (Ditambahkan position-relative & z-index agar dropdown tidak tertutup) -->
    <div class="card border-0 shadow-sm rounded-4 glass-card p-4 mb-4" style="position: relative; z-index: 10;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 bg-success bg-opacity-10 text-success p-3 rounded-3 me-3">
                    <i class="fa-solid fa-file-lines fa-xl"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-dark mb-1">Stok & Laporan Transaksi</h3>
                    <p class="text-muted small mb-0">Rekapitulasi riwayat mutasi barang masuk dan barang keluar produksi</p>
                </div>
            </div>
            <div>
                <!-- Dropdown Export / Cetak -->
                <div class="dropdown">
                    <button class="btn btn-dark btn-sm px-4 rounded-pill fw-semibold shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-download me-1"></i> Cetak / Export
                    </button>
                    <ul class="dropdown-menu shadow border-0 rounded-3 py-2">
                        <li>
                            <button onclick="window.print()" class="dropdown-item py-2 px-3 text-dark fw-semibold">
                                <i class="fa-solid fa-print me-2 text-secondary"></i> Print / Cetak PDF
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 px-3 text-danger fw-semibold" href="{{ route('inventory.produksi.laporan.pdf', request()->all()) }}" target="_blank">
                                <i class="fa-solid fa-file-pdf me-2"></i> Export ke PDF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 text-success fw-semibold" href="{{ route('inventory.produksi.laporan.excel', request()->all()) }}">
                                <i class="fa-solid fa-file-excel me-2"></i> Export ke Excel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 text-primary fw-semibold" href="{{ route('inventory.produksi.laporan.word', request()->all()) }}">
                                <i class="fa-solid fa-file-word me-2"></i> Export ke Word
                            </a>
                        </li>
                    </ul>
                </div>
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
            <!-- Diubah menggunakan underscore '_' agar sesuai dengan route web.php -->
            <a href="{{ route('inventory.produksi.barang_masuk') }}" class="nav-link px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 tab-custom {{ request()->routeIs('inventory.produksi.barang_masuk') ? 'active' : '' }}">
                <i class="fa-solid fa-truck-ramp-box"></i> Barang Masuk
            </a>
        </li>
        <li class="nav-item">
            <!-- Diubah menggunakan underscore '_' agar sesuai dengan route web.php -->
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

    <!-- Filter Card Container -->
    <div class="card border-0 shadow-sm rounded-4 glass-card p-4 mb-4">
        <form action="{{ route('inventory.produksi.laporan') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" class="form-control bg-white shadow-none" value="{{ request('tanggal_mulai', date('Y-m-01')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" class="form-control bg-white shadow-none" value="{{ request('tanggal_selesai', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Jenis Transaksi</label>
                    <select name="jenis" class="form-select bg-white shadow-none">
                        <option value="">Semua (Masuk & Keluar)</option>
                        <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Barang Masuk</option>
                        <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-orange text-white w-100 fw-semibold shadow-sm py-2">
                        <i class="fa-solid fa-filter me-1"></i> Filter Laporan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Main Content Table Container -->
    <div class="card border-0 shadow-sm rounded-4 glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 border-bottom pb-3">
            <div>
                <h5 class="fw-bold text-dark mb-0">Riwayat Mutasi & Laporan</h5>
                <p class="text-muted small mb-0">Daftar transaksi logistik yang tercatat dalam sistem.</p>
            </div>
        </div>
        <div class="table-responsive rounded-3 border border-light overflow-hidden bg-white bg-opacity-60">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-secondary">
                    <tr>
                        <th class="py-3 px-3">Tanggal</th>
                        <th class="py-3">No. Transaksi</th>
                        <th class="py-3">Tipe</th>
                        <th class="py-3">Keterangan / Supplier / Tujuan</th>
                        <th class="py-3">Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporans ?? [] as $l)
                    <tr>
                        <td class="px-3 text-secondary">{{ $l->date ?? $l->created_at ?? '-' }}</td>
                        <td class="fw-semibold text-dark">{{ $l->no_transaksi ?? $l->id ?? '-' }}</td>
                        <td>
                            @if(($l->type ?? '') == 'IN')
                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Masuk</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">Keluar</span>
                            @endif
                        </td>
                        <td>{{ $l->keterangan ?? '-' }}</td>
                        <td class="text-muted">{{ $l->admin ?? $l->user->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="fa-solid fa-folder-open fa-2x mb-2 opacity-50"></i>
                                <p class="mb-0 small">Belum ada data transaksi pada rentang tanggal tersebut.</p>
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