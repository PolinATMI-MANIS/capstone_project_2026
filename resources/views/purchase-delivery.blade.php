@extends('layouts.app')

@section('content')
<div class="container-fluid py-3 px-4">
    <!-- Header Hub dengan Profil User di Pojok Kanan -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Kiri: Judul Halaman -->
        <div>
            <span class="badge bg-warning text-dark font-weight-bold px-2 py-1 mb-1">CAPSTONE 2026</span>
            <h2 class="fw-bold m-0">PURCHASE & DELIVERY HUB</h2>
            <p class="text-muted small m-0">Pilih modul operasi yang ingin kamu kelola atau lihat ringkasan status di bawah.</p>
        </div>

        <!-- Kanan: Card Profil User Login -->
        @auth
        <div class="d-flex align-items-center bg-white px-3 py-2 rounded-4 shadow-sm border">
            <!-- Inisial Nama User -->
            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 38px; height: 38px; font-size: 16px;">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            
            <!-- Nama & Role User -->
            <div>
                <div class="fw-bold text-dark small lh-1 mb-1">
                    {{ Auth::user()->name ?? 'User Capstone' }}
                </div>
                <span class="badge bg-warning-subtle text-warning border border-warning text-uppercase" style="font-size: 9px; padding: 2px 6px;">
                    {{ Auth::user()->role ?? 'GUEST' }}
                </span>
            </div>
        </div>
        @endauth
    </div>

    <!-- 1. Row 4 Card Ringkasan Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Total Purchase Order</small>
                        <h3 class="fw-bold mb-0 mt-1">{{ $totalPo }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-25 p-3 rounded-circle text-warning">
                        <i class="fa-solid fa-cart-shopping fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Total Delivery Order</small>
                        <h3 class="fw-bold mb-0 mt-1">{{ $totalDo }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-25 p-3 rounded-circle text-info">
                        <i class="fa-solid fa-truck-fast fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-danger border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">PO Waiting Approval</small>
                        <h3 class="fw-bold mb-0 mt-1">{{ $waitingPo }}</h3>
                    </div>
                    <div class="bg-danger bg-opacity-25 p-3 rounded-circle text-danger">
                        <i class="fa-solid fa-clock fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Total Nilai PO</small>
                        <h3 class="fw-bold text-success mb-0 mt-1">Rp {{ number_format($totalNilaiPo ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-25 p-3 rounded-circle text-success">
                        <i class="fa-solid fa-wallet fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Tombol Pintas Pilihan Modul (Hub Navigation) -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-25 p-3 rounded-4 me-3 text-warning">
                                <i class="fa-solid fa-file-invoice-dollar fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Purchase Order</h4>
                                <span class="badge bg-light text-dark border mt-1">{{ $totalPo }} Total Data</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Kelola pemesanan barang dari customer, buat dokumen PO baru, serta pantau status dan estimasi deadline pengiriman.</p>
                    </div>
                    <a href="/purchase" class="btn btn-warning text-white fw-bold w-100 rounded-3 mt-3">
                        Buka Purchase Order Directory <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-25 p-3 rounded-4 me-3 text-info">
                                <i class="fa-solid fa-truck-fast fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Delivery Order</h4>
                                <span class="badge bg-light text-dark border mt-1">{{ $totalDo }} Total Data</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Pantau proses pengiriman barang, update status jalan kurir, dan atur dokumen jalan pengiriman ke lokasi tujuan.</p>
                    </div>
                    <a href="/delivery" class="btn btn-info text-white fw-bold w-100 rounded-3 mt-3">
                        Buka Delivery Order Directory <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Rincian Purchase Order -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-list-check text-warning me-2"></i>Rincian Informasi Purchase Order</h6>
                <a href="/purchase" class="btn btn-sm btn-outline-warning fw-bold">Lihat Semua PO</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase">
                        <tr>
                            <th>No. PO</th>
                            <th>Customer</th>
                            <th>Barang</th>
                            <th>Qty x Harga</th>
                            <th>Total Nilai</th>
                            <th>Deadline</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPurchases as $po)
                        <tr>
                            <td class="fw-bold">{{ $po->no_po }}</td>
                            <td>{{ $po->nama_customer }}</td>
                            <td>{{ $po->nama_barang }}</td>
                            <td>{{ $po->kuantitas }} unit @ Rp {{ number_format($po->harga_satuan ?? 0, 0, ',', '.') }}</td>
                            <td class="fw-bold">Rp {{ number_format($po->total_nilai ?? 0, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($po->waktu_tgl_deadline)->format('d/m/Y') }}</td>
                            <td><span class="badge bg-secondary">{{ $po->status }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Belum ada data Purchase Order.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 4. Tabel Rincian Delivery Order -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-truck text-info me-2"></i>Rincian Informasi Delivery Order</h6>
                <a href="/delivery" class="btn btn-sm btn-outline-info fw-bold">Lihat Semua DO</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase">
                        <tr>
                            <th>No. DO</th>
                            <th>Ref PO</th>
                            <th>Tanggal Kirim</th>
                            <th>Driver</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDeliveries as $do)
                        <tr>
                            <td class="fw-bold">{{ $do->no_do }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $do->ref_po }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($do->tanggal_kirim)->format('d/m/Y') }}</td>
                            <td>{{ $do->driver ?? '-' }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $do->status }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada data Delivery Order.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection