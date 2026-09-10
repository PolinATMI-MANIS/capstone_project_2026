@extends('layouts.app')

@section('content')
<!-- Header Dashboard & Profile Badge -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Dashboard Overview</h3>
        <p class="text-muted small m-0 mt-1">Rekapan data operasional dari seluruh modul Capstone Industrial System.</p>
    </div>

    <!-- Badge Profil Pengguna (Kanan Atas) -->
    <div class="d-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm border">
        <div class="bg-warning bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center text-warning fw-bold me-3" style="width: 40px; height: 40px;">
            <i class="fa-solid fa-user-gear"></i>
        </div>
        <div>
            <h6 class="fw-bold m-0 text-dark" style="font-size: 0.88rem;">
                {{ Auth::user()->name ?? 'Admin Capstone' }}
            </h6>
            <span class="badge bg-danger text-uppercase" style="font-size: 0.65rem;">
                {{ Auth::user()->role ?? 'Super Admin' }}
            </span>
        </div>
    </div>
</div>

<!-- Grid Cards Ringkasan Modul (Clickable) -->
<div class="row g-4">
    
    <!-- Card Inventory -->
    <div class="col-md-4">
        <a href="/inventory" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 card-hover">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small text-uppercase font-monospace fw-bold">Inventory</span>
                    <div class="bg-primary bg-opacity-10 p-2 rounded text-primary">
                        <i class="fa-solid fa-boxes-stacked fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold m-0 text-dark">{{ $totalInventory ?? 0 }}</h3>
                <p class="text-muted small m-0 mt-1">Total Item Stok Aktif</p>
            </div>
        </a>
    </div>

    <!-- Card Production -->
    <div class="col-md-4">
        <a href="/production" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 card-hover">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small text-uppercase font-monospace fw-bold">Production</span>
                    <div class="bg-success bg-opacity-10 p-2 rounded text-success">
                        <i class="fa-solid fa-industry fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold m-0 text-dark">{{ $totalProduction ?? 0 }}</h3>
                <p class="text-muted small m-0 mt-1">Batch Produksi Berjalan</p>
            </div>
        </a>
    </div>

    <!-- Card Resources -->
    <div class="col-md-4">
        <a href="/resources" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 card-hover">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small text-uppercase font-monospace fw-bold">Resources</span>
                    <div class="bg-info bg-opacity-10 p-2 rounded text-info">
                        <i class="fa-solid fa-users-gear fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold m-0 text-dark">{{ $totalResources ?? 0 }}</h3>
                <p class="text-muted small m-0 mt-1">Man Power Tersedia</p>
            </div>
        </a>
    </div>

    <!-- Card Order Here (PO & DO) -->
    <div class="col-md-4">
        <a href="/purchase" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 card-hover">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small text-uppercase font-monospace fw-bold">Order Here !</span>
                    <div class="bg-warning bg-opacity-10 p-2 rounded text-warning">
                        <i class="fa-solid fa-cart-shopping fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold m-0 text-dark">{{ $totalOrders ?? 0 }}</h3>
                <p class="text-muted small m-0 mt-1">Dokumen PO & DO Pending</p>
            </div>
        </a>
    </div>

    <!-- Card RnD -->
    <div class="col-md-4">
        <a href="/rnd" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 card-hover">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small text-uppercase font-monospace fw-bold">RnD</span>
                    <div class="bg-danger bg-opacity-10 p-2 rounded text-danger">
                        <i class="fa-solid fa-flask fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold m-0 text-dark">{{ $totalRnd ?? 0 }}</h3>
                <p class="text-muted small m-0 mt-1">Proyek Riset Aktif</p>
            </div>
        </a>
    </div>

</div>

<!-- CSS Tambahan Efek Hover Kartu -->
<style>
    .card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.08)!important;
    }
</style>
@endsection