@extends('layouts.app')

@section('content')
<!-- Header Utama: Judul & Profile Widget -->
<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <span class="badge bg-warning text-dark fw-bold mb-1 px-2 py-1" style="font-size: 11px; letter-spacing: 0.5px;">SYSTEM OVERVIEW</span>
        <h1 class="fw-bold display-6 m-0 text-dark" style="letter-spacing: -0.5px;">DASHBOARD</h1>
    </div>

    <!-- Widget Info Login User -->
    <div>
        @auth
            <div class="d-flex align-items-center bg-white shadow-sm px-3 py-2 rounded-4 border">
                <div class="bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 18px;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="lh-sm">
                    <div class="fw-bold text-dark small mb-0">{{ auth()->user()->name ?? 'User' }}</div>
                    <span class="badge bg-light text-warning border border-warning" style="font-size: 10px; padding: 2px 6px;">
                        {{ strtoupper(auth()->user()->role ?? 'GUEST') }}
                    </span>
                </div>
            </div>
        @else
            <div class="d-flex align-items-center bg-white shadow-sm px-3 py-2 rounded-4 border text-muted small">
                <i class="fa-solid fa-user-slash me-2 text-warning"></i> Mode Testing (Belum Login)
            </div>
        @endauth
    </div>
</div>

<!-- Cards Ringkasan Statistik -->
<div class="row g-3 mb-4">
    <!-- Total Purchase Orders -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Purchase Order</span>
                    <h3 class="fw-bold text-dark m-0 mt-1">{{ $totalPO }}</h3>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                    <i class="fa-solid fa-cart-shopping fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Delivery Orders -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Delivery Order</span>
                    <h3 class="fw-bold text-dark m-0 mt-1">{{ $totalDO }}</h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle">
                    <i class="fa-solid fa-truck-fast fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- PO Menunggu Approval -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">PO Waiting Approval</span>
                    <h3 class="fw-bold text-dark m-0 mt-1">{{ $pendingPO }}</h3>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-circle">
                    <i class="fa-solid fa-clock fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Est. Transaksi -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Nilai PO</span>
                    <h4 class="fw-bold text-success m-0 mt-1">Rp {{ number_format($totalNilaiPO, 0, ',', '.') }}</h4>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle">
                    <i class="fa-solid fa-wallet fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informasi Detail Data yang Sudah Diinput -->
<div class="row g-4">
    <!-- Tabel Informasi Purchase Order Terbaru -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                <h5 class="fw-bold m-0 text-dark">
                    <i class="fa-solid fa-list-check me-2 text-warning"></i>Rincian Informasi Purchase Order
                </h5>
                <a href="/purchase" class="btn btn-sm btn-outline-warning fw-bold">Lihat Semua PO</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-light">
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4">No. PO</th>
                                <th>Customer</th>
                                <th>Barang (Kode/Nama)</th>
                                <th>Material Request</th>
                                <th>Qty x Harga</th>
                                <th>Total Nilai</th>
                                <th>Deadline & Estimasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $item->no_po }}</td>
                                    <td><span class="fw-semibold text-dark">{{ $item->nama_customer }}</span></td>
                                    <td>
                                        <div class="lh-sm">
                                            <div class="fw-bold text-dark">{{ $item->nama_barang }}</div>
                                            <small class="text-muted">Kode: {{ $item->kode_barang ?? '-' }}</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $item->permintaan_material ?? '-' }}</span></td>
                                    <td>{{ $item->kuantitas }} unit @ Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                    <td class="fw-bold text-dark">Rp {{ number_format(($item->kuantitas ?? 0) * ($item->harga_satuan ?? 0), 0, ',', '.') }}</td>
                                    <td>
                                        <div class="lh-sm small">
                                            <div><i class="fa-regular fa-calendar me-1 text-muted"></i>{{ \Carbon\Carbon::parse($item->waktu_tgl_deadline)->format('d/m/Y H:i') }}</div>
                                            <span class="text-muted">Est: {{ $item->estimasi_pengerjaan ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $item->status == 'Completed' ? 'success' : ($item->status == 'On Progress' ? 'warning' : ($item->status == 'Cancelled' ? 'danger' : 'secondary')) }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data Purchase Order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Informasi Delivery Order Terbaru -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                <h5 class="fw-bold m-0 text-dark">
                    <i class="fa-solid fa-truck-ramp-box me-2 text-info"></i>Rincian Informasi Delivery Order
                </h5>
                <a href="/delivery" class="btn btn-sm btn-outline-info fw-bold">Lihat Semua DO</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-light">
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4">No. DO</th>
                                <th>Ref PO</th>
                                <th>Tanggal Kirim</th>
                                <th>Driver / Ekspedisi</th>
                                <th>Alamat Tujuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveries as $do)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $do->no_do }}</td>
                                    <td><span class="badge bg-light text-secondary border">{{ $do->ref_po }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($do->tanggal_kirim)->format('d/m/Y') }}</td>
                                    <td><i class="fa-solid fa-user-gear me-1 text-muted"></i> {{ $do->driver ?? '-' }}</td>
                                    <td class="small text-muted">{{ Str::limit($do->alamat_tujuan ?? '-', 40) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $do->status == 'Delivered' ? 'success' : ($do->status == 'Shipping' ? 'primary' : ($do->status == 'Cancelled' ? 'danger' : 'warning')) }}">
                                            {{ $do->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data Delivery Order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection