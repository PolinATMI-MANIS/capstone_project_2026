@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Tombol Kembali -->
    <a href="/purchase" class="btn btn-outline-secondary btn-sm mb-4">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Purchase Order
    </a>

    <!-- Card Detail Purchase Order -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Detail Purchase Order: {{ $purchase->no_po }}</h5>
            <span class="badge bg-light text-primary fw-bold">Active PO</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- Informasi Customer & Order -->
                <div class="col-md-6 border-end">
                    <h6 class="fw-bold text-muted text-uppercase small mb-3">Informasi Pesanan</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted" style="width: 150px;">Nomor PO</td>
                            <td class="fw-bold text-dark">: {{ $purchase->no_po }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Customer</td>
                            <td class="fw-bold text-dark">: {{ $purchase->nama_customer ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Barang</td>
                            <td class="fw-bold text-dark">: {{ $purchase->nama_barang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jumlah / Kuantitas</td>
                            <td class="fw-bold text-dark">: {{ $purchase->kuantitas ?? 0 }} unit</td>
                        </tr>
                    </table>
                </div>

                <!-- Informasi Tanggal & Status -->
                <div class="col-md-6 ps-md-4">
                    <h6 class="fw-bold text-muted text-uppercase small mb-3">Tenggat Waktu & Jadwal</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted" style="width: 150px;">Tanggal PO dibuat</td>
                            <td class="fw-bold text-dark">: {{ \Carbon\Carbon::parse($purchase->created_at)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Deadline Pengiriman</td>
                            <td class="fw-bold text-danger">: {{ \Carbon\Carbon::parse($purchase->waktu_tgl_deadline)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Catatan Tambahan</td>
                            <td class="fw-bold text-dark">: {{ $purchase->keterangan ?? 'Tidak ada catatan' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection