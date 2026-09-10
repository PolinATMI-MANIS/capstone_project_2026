@extends('layouts.app')

@section('content')
<!-- Tab Navigasi Atas -->
<div class="mb-4 d-flex align-items-center gap-3">
    <span class="fw-bold text-dark fs-5">Order Here !</span>
    <div class="sub-navbar">
        <a href="/purchase" class="sub-nav-btn active">Purchase Order</a> <!-- Pindah kelas active sesuai file -->
        <a href="/delivery" class="sub-nav-btn">Delivery Order</a>
    </div>
</div>

<!-- Header & Tombol Tambah -->
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Delivery Order Directory</h3>
        <p class="text-muted small m-0 mt-1">Sistem manajemen dan pengawasan dokumen Delivery Order.</p>
    </div>
    <button class="btn btn-machine">
        <i class="fa-solid fa-plus me-1"></i> Add Delivery Order
    </button>
</div>

<!-- Tabel Data -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4">No. DO</th>
                        <th>Ref PO</th>
                        <th>Tanggal Kirim</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted opacity-50">
                                <i class="fa-solid fa-gear fa-3x mb-3 text-warning"></i>
                                <p class="fw-bold small text-uppercase m-0">Belum ada data Delivery Order tercatat.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection