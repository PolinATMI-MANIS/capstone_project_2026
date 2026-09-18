@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Tombol Kembali -->
    <a href="/delivery" class="btn btn-outline-secondary btn-sm mb-4">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Delivery
    </a>

    <!-- Card Detail Delivery -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-warning text-white py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-truck-fast me-2"></i> Detail Delivery Order: {{ $delivery->no_do }}</h5>
            <span class="badge bg-light text-dark fw-bold">{{ $delivery->status }}</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- Kolom Informasi PO & Customer -->
                <div class="col-md-6 border-end">
                    <h6 class="fw-bold text-muted text-uppercase small mb-3">Informasi Purchase Order</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted" style="width: 140px;">Ref No. PO</td>
                            <td class="fw-bold text-dark">: {{ $delivery->ref_po }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Customer</td>
                            <td class="fw-bold text-dark">: {{ $delivery->purchase->nama_customer ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Barang</td>
                            <td class="fw-bold text-dark">: {{ $delivery->purchase->nama_barang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jumlah Barang</td>
                            <td class="fw-bold text-dark">: {{ $delivery->purchase->kuantitas ?? 0 }} unit</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Deadline PO</td>
                            <td class="fw-bold text-danger">: {{ \Carbon\Carbon::parse($delivery->purchase->waktu_tgl_deadline ?? now())->format('d F Y') }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Kolom Pengiriman -->
                <div class="col-md-6 ps-md-4">
                    <h6 class="fw-bold text-muted text-uppercase small mb-3">Informasi Pengiriman</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted" style="width: 140px;">Tgl Kirim (Actual)</td>
                            <td class="fw-bold text-dark">: {{ \Carbon\Carbon::parse($delivery->tanggal_kirim)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted" style="width: 140px;">Tgl Kirim (Actual)</td>
                            <td class="fw-bold text-dark">: 
                                @if($delivery->status == 'Waiting')
                                    <span class="text-muted fst-italic fw-normal">- (Belum dikirim)</span>
                                @else
                                    {{ \Carbon\Carbon::parse($delivery->tanggal_kirim)->format('d F Y') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Catatan/Keterangan</td>
                            <td class="fw-bold text-dark">: {{ $delivery->keterangan ?? 'Tidak ada catatan' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection