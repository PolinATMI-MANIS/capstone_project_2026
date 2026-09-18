@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4 glass-container">

    <!-- Panggil Komponen Notifikasi Toast di Sini -->
    @if(View::exists('components.notification'))
        <x-notification />
    @endif

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-file-invoice text-success me-2"></i>Dashboard Inventory PO
            </h3>
            <p class="text-muted small mb-0">Kelola dan pantau penyimpanan produk hasil produksi PO secara real-time.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @php
                $userRole = auth()->user()->role ?? 'user';
            @endphp

            <!-- Indikator Role yang Sedang Login -->
            <span class="badge bg-dark bg-opacity-10 text-dark px-3 py-2 rounded-pill small">
                Role: <strong class="text-uppercase">{{ $userRole }}</strong>
            </span>

            <!-- Tombol Input Data PO Baru (Hanya untuk Admin & Super Admin) -->
            @if($userRole === 'admin' || $userRole === 'super_admin')
                @if(Route::has('inventory.po.create'))
                    <a href="{{ route('inventory.po.create') }}" class="btn glass-btn-success rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        <i class="fa-solid fa-plus me-2"></i> Input Data PO Baru
                    </a>
                @endif
            @endif
        </div>
    </div>

    <!-- Glass Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center">
                <div class="glass-icon-box bg-success-subtle text-success me-3">
                    <i class="fa-solid fa-boxes-stacked fa-lg"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold d-block text-uppercase">Total Item PO</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $inventoryPos->count() ?? 0 }} <small class="fs-6 text-muted fw-normal">Batch</small></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center">
                <div class="glass-icon-box bg-warning-subtle text-warning me-3">
                    <i class="fa-solid fa-clock-rotate-left fa-lg"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold d-block text-uppercase">Mendekati Deadline</span>
                    <h4 class="fw-bold text-dark mb-0">
                        {{ $inventoryPos->where('deadline', '<=', now()->addDays(3))->count() ?? 0 }} 
                        <small class="fs-6 text-muted fw-normal">PO</small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center">
                <div class="glass-icon-box bg-info-subtle text-info me-3">
                    <i class="fa-solid fa-warehouse fa-lg"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold d-block text-uppercase">Total Qty Produksi</span>
                    <h4 class="fw-bold text-dark mb-0">
                        {{ number_format($inventoryPos->sum('jumlah_produksi') ?? 0) }} 
                        <small class="fs-6 text-muted fw-normal">Pcs</small>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Glass Main Table Card -->
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h6 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-layer-group text-success me-2"></i>Daftar Hasil Produksi PO
            </h6>
            
            <!-- Glass Search Bar -->
            @if(Route::has('inventory.po.index'))
                <form action="{{ route('inventory.po.index') }}" method="GET" class="input-group glass-search-box" style="max-width: 280px;">
                    <span class="input-group-text border-0 bg-transparent text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control border-0 bg-transparent text-dark" placeholder="Cari No. PO / Produk..." value="{{ request('search') }}">
                </form>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table align-middle glass-table mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="ps-3">No</th>
                        <th scope="col">No. PO</th>
                        <th scope="col">Produk Produksi</th>
                        <th scope="col" class="text-center">Jumlah Produksi</th>
                        <th scope="col">Lokasi Penyimpanan</th>
                        <th scope="col" class="text-center">Deadline</th>
                        <th scope="col" class="text-center pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventoryPos as $index => $item)
                        <tr>
                            <td class="ps-3 fw-bold text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-bold text-success">{{ $item->no_po ?? '-' }}</span>
                            </td>
                            <td>
                                {{-- Mengambil data dari relasi produksi jika ada, fallback ke atribut produk_jadi --}}
                                <span class="fw-semibold text-dark">
                                    {{ $item->produksi->nama_produk ?? $item->produk_jadi ?? '-' }}
                                </span>
                                @if(isset($item->produksi->kode_produksi))
                                    <br><small class="text-muted">Kode: {{ $item->produksi->kode_produksi }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="glass-badge-pill glass-badge-success">
                                    {{ number_format($item->jumlah_produksi ?? $item->qty ?? 0) }} Pcs
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-location-dot text-danger me-2"></i>
                                    <span>{{ $item->lokasi_penyimpanan ?? 'Gudang Utama' }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $isUrgent = false;
                                    if(!empty($item->deadline)) {
                                        $isUrgent = \Carbon\Carbon::parse($item->deadline)->isPast() || \Carbon\Carbon::parse($item->deadline)->diffInDays(now()) <= 3;
                                    }
                                @endphp
                                @if(!empty($item->deadline))
                                    <span class="badge {{ $isUrgent ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-warning-subtle text-warning-emphasis border-warning-subtle' }} border rounded-pill px-3 py-1 fw-semibold">
                                        {{ \Carbon\Carbon::parse($item->deadline)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- Tombol Detail (Bisa diakses semua role) -->
                                    @if(Route::has('inventory.po.show'))
                                        <a href="{{ route('inventory.po.show', $item->id) }}" class="btn btn-sm glass-btn-action text-primary" title="Detail"><i class="fa-solid fa-eye"></i></a>
                                    @endif

                                    <!-- Tombol Edit & Hapus Berdasarkan Role -->
                                    @if($userRole === 'super_admin' || $userRole === 'admin')
                                        @if(Route::has('inventory.po.edit'))
                                            <a href="{{ route('inventory.po.edit', $item->id) }}" class="btn btn-sm glass-btn-action text-warning" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        @endif

                                        @if($userRole === 'super_admin')
                                            <!-- Super Admin Bisa Hapus Permanen -->
                                            @if(Route::has('inventory.po.destroy'))
                                                <form action="{{ route('inventory.po.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini secara permanen?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm glass-btn-action text-danger" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                                                </form>
                                            @endif
                                        @elseif($userRole === 'admin')
                                            <!-- Admin Mengirim Permintaan Hapus -->
                                            <button type="button" class="btn btn-sm glass-btn-action text-secondary" title="Request Hapus" onclick="alert('Permintaan hapus data PO telah dikirim ke Super Admin.')">
                                                <i class="fa-solid fa-triangle-exclamation"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-3">
                                    <i class="fa-solid fa-box-open fa-3x text-muted opacity-50 mb-3"></i>
                                    <h6 class="fw-bold text-dark mb-1">Belum Ada Data Penyimpanan PO</h6>
                                    <p class="text-muted small">Data barang produksi yang dimasukkan ke gudang akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination (Jika Ada) -->
        @if(method_exists($inventoryPos, 'links'))
            <div class="mt-4 d-flex justify-content-end">
                {{ $inventoryPos->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.65) !important;
        backdrop-filter: blur(16px) saturate(180%);
        -webkit-backdrop-filter: blur(16px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 20px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.06);
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1);
    }

    .glass-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .glass-btn-success {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.9) 0%, rgba(21, 115, 71, 0.95) 100%);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .glass-btn-success:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(25, 135, 84, 0.35) !important;
    }

    .glass-search-box {
        background: rgba(241, 245, 249, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 30px;
        padding: 2px 8px;
        transition: all 0.3s ease;
    }

    .glass-search-box:focus-within {
        background: rgba(255, 255, 255, 0.95);
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.15);
    }

    .glass-table {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .glass-table thead th {
        border: none;
        color: #64748b;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 12px 16px;
    }

    .glass-table tbody tr {
        background: rgba(255, 255, 255, 0.5);
        transition: all 0.2s ease;
    }

    .glass-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: scale(1.003);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }

    .glass-table tbody td {
        border-top: 1px solid rgba(241, 245, 249, 0.8);
        border-bottom: 1px solid rgba(241, 245, 249, 0.8);
        padding: 14px 16px;
    }

    .glass-table tbody tr td:first-child {
        border-left: 1px solid rgba(241, 245, 249, 0.8);
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }

    .glass-table tbody tr td:last-child {
        border-right: 1px solid rgba(241, 245, 249, 0.8);
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .glass-badge-pill {
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.825rem;
    }

    .glass-badge-success {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }

    .glass-btn-action {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 10px;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .glass-btn-action:hover {
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }
</style>
@endsection