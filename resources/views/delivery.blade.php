@extends('layouts.app')
<style>
/* 1. SEMBUNYIKAN ELEMEN CETAK SAAT DI LAYAR BIASA */
.print-only { 
    display: none !important; 
}

/* 2. ATURAN KHUSUS HANYA SAAT TOMBOL PRINT/PDF DIKLIK */
@media print {
    @page {
        size: A4 landscape;
        margin: 10mm;
    }

    /* Sembunyikan elemen bawaan web */
    body * { 
        visibility: hidden !important; 
    } 
    
    /* Tampilkan hanya area printable */
    #printable-area, #printable-area * { 
        visibility: visible !important; 
    }

    /* Tarik tabel ke posisi paling atas kertas cetak */
    #printable-area {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        box-shadow: none !important;
        border: none !important;
    }

    .print-only { 
        display: block !important; 
    }

    .no-print, .no-print * { 
        display: none !important; 
    }

    /* Styling Tabel Cetak */
    #printable-area table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 15px !important;
        font-size: 11px !important;
    }

    #printable-area th {
        background-color: #1e293b !important;
        color: #ffffff !important;
        font-weight: bold !important;
        text-align: center !important;
        border: 1px solid #0f172a !important;
        padding: 8px !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    #printable-area td {
        border: 1px solid #cbd5e1 !important;
        padding: 8px !important;
        color: #0f172a !important;
        vertical-align: middle !important;
    }

    /* Sembunyikan Kolom Actions saat cetak */
    #printable-area th:last-child, 
    #printable-area td:last-child {
        display: none !important;
    }

    .signature-container {
        page-break-inside: avoid;
        margin-top: 35px !important;
    }
}
</style>
@section('content')
<div class="container-fluid py-3">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-warning text-dark px-2 py-1 mb-1">MODULE</span>
            <h2 class="fw-bold m-0">PURCHASE & DELIVERY</h2>
        </div>
        <div class="d-flex align-items-center bg-white p-2 rounded-3 shadow-sm border">
            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:38px; height:38px; font-weight:bold;">
                {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
            </div>
            <div>
                <div class="fw-bold small text-dark">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                <span class="badge bg-warning text-dark border style-badge" style="font-size:10px;">{{ strtoupper(auth()->user()->role ?? 'SUPERADMIN') }}</span>
            </div>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="d-flex justify-content-center mb-4">
        <div class="bg-white p-1 rounded-pill shadow-sm border d-inline-flex">
            <a href="/purchase" class="btn btn-sm rounded-pill px-4 text-muted border-0 fw-semibold">Purchase Order</a>
            <a href="/delivery" class="btn btn-sm rounded-pill px-4 btn-danger text-white border-0 fw-semibold active" style="background-color: #ff5722;">Delivery Order</a>
        </div>
    </div>

    <!-- Directory Card -->
    <div id="printable-area" class="card border-0 shadow-sm rounded-3 p-4 mb-4">
        
        <!-- Header Section (Judul Kiri, Tombol Kanan) -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Delivery Order Directory</h4>
                <p class="text-muted small mb-0">Sistem manajemen dan pengawasan dokumen Delivery Order.</p>
            </div>
            <div class="d-flex gap-2 no-print">
                <button type="button" class="btn btn-sm btn-outline-success fw-semibold px-3" onclick="window.print()">
                    <i class="fa-solid fa-print me-1"></i> Cetak / Download
                </button>
                <button type="button" class="btn btn-sm btn-warning text-dark fw-bold px-3" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Purchase Order
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th>NO. DO</th>
                        <th>REF PO</th>
                        <th>CUSTOMER</th>
                        <th>BARANG & QTY (DARI PO)</th>
                        <th>DEADLINE PO</th>
                        <th>TGL KIRIM (ACTUAL)</th>
                        <th>STATUS PENGIRIMAN</th>
                        <th class="text-end pe-4">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $item)
                    <tr>
                        <td class="fw-bold text-dark">{{ $item->no_do }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $item->ref_po }}</span></td>
                        <td>{{ $item->purchase->nama_customer ?? '-' }}</td>
                        <td>
                            <div class="fw-semibold">{{ $item->purchase->nama_barang ?? '-' }}</div>
                            <small class="text-muted">Jumlah: {{ $item->purchase->kuantitas ?? 0 }} unit</small>
                        </td>
                        <td>
                            @if(!empty($item->purchase->waktu_tgl_deadline))
                                <small><i class="fa-regular fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($item->purchase->waktu_tgl_deadline)->format('d/m/Y') }}</small>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td>
                            @if(!empty($item->tanggal_kirim))
                                <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($item->tanggal_kirim)->format('d/m/Y') }}</div>
                                @if($item->driver)<small class="text-muted">{{ $item->driver }}</small>@endif
                            @else
                                <span class="text-muted small">-<br><i>Belum dikirim</i></span>
                            @endif
                        </td>

                        <!-- STATUS PENGIRIMAN -->
                        <td>
                            @php
                                $statusAppr  = $item->status_approval ?? 'Pending';
                                $statusKirim = $item->status_pengiriman ?? null;

                                if ($statusAppr == 'Rejected') {
                                    $statusDisplay = 'Rejected';
                                    $badgeClass    = 'bg-danger text-white';
                                } elseif ($statusKirim) {
                                    $statusDisplay = $statusKirim;
                                    $badgeClass = match($statusKirim) {
                                        'Delivered', 'Finish' => 'bg-success text-white',
                                        'Shipping'            => 'bg-primary text-white',
                                        'Delayed'             => 'bg-danger text-white',
                                        default               => 'bg-info text-white',
                                    };
                                } elseif ($statusAppr == 'Approved') {
                                    $statusDisplay = 'Ready to Ship';
                                    $badgeClass    = 'bg-info text-white';
                                } else {
                                    $statusDisplay = 'Waiting';
                                    $badgeClass    = 'bg-warning text-dark';
                                }
                            @endphp

                            <span class="badge {{ $badgeClass }} px-2 py-1">{{ $statusDisplay }}</span>
                        </td>

                        <!-- ACTIONS -->
                        <td class="text-end pe-3">
                            <div class="btn-group" role="group">
                                <a href="/delivery/{{ $item->id }}" class="btn btn-sm btn-light border" title="Lihat Detail">
                                    <i class="fa-solid fa-eye me-1"></i> Detail
                                </a>

                                @php
                                    $statusProd = strtolower($item->purchase->status_produksi ?? 'completed');
                                    $isSelesai = in_array($statusProd, ['completed', 'selesai']);
                                @endphp

                                @if($isSelesai)
                                    <button type="button" class="btn btn-sm btn-primary text-white fw-semibold ms-1" data-bs-toggle="modal" data-bs-target="#confirmModal{{ $item->id }}">
                                        <i class="fa-solid fa-truck-fast me-1"></i> Konfirmasi Kirim
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-secondary opacity-50 ms-1" data-bs-toggle="tooltip" title="Produksi belum selesai!" disabled>
                                        <i class="fa-solid fa-lock me-1"></i> Konfirmasi Kirim
                                    </button>
                                @endif

                                {{-- Tombol Approval (Hanya muncul untuk Admin & Superadmin) --}}
                                @if(in_array(auth()->user()->role, ['admin', 'superadmin', 'super_admin']))
                                    @if($statusAppr == 'Approved')
                                        <button type="button" class="btn btn-sm btn-outline-success fw-semibold ms-1" style="font-size: 0.8rem;" disabled>
                                            <i class="fa-solid fa-check-double me-1"></i> Approved
                                        </button>
                                    @elseif($statusAppr == 'Rejected')
                                        <button type="button" class="btn btn-sm btn-outline-danger fw-semibold ms-1" style="font-size: 0.8rem;" disabled>
                                            <i class="fa-solid fa-ban me-1"></i> Rejected
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-success text-white fw-semibold ms-1" style="font-size: 0.8rem;"
                                                data-bs-toggle="modal" data-bs-target="#approvalModal{{ $item->id }}">
                                            <i class="fa-solid fa-circle-check me-1"></i> Approval
                                        </button>
                                    @endif
                                @endif

                                {{-- Tombol Hapus (Hanya muncul untuk Superadmin) --}}
                                @if(in_array(auth()->user()->role, ['superadmin', 'super_admin']))
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                @endif
                            </div>

                            <!-- MODAL CONFIRM -->
                            <div class="modal fade text-start" id="confirmModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title fw-bold">Konfirmasi Pengiriman — {{ $item->no_do }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="/delivery/{{ $item->id }}/confirm" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body py-3">
                                                <div class="mb-3">
                                                    <label class="form-label small text-muted fw-bold">Tanggal Kirim Actual</label>
                                                    <input type="date" class="form-control" name="tanggal_kirim" value="{{ $item->tanggal_kirim ?? date('Y-m-d') }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small text-muted fw-bold">Status Pengiriman</label>
                                                    <select name="status_pengiriman" class="form-select">
                                                        <option value="On Progress" {{ ($item->status_pengiriman ?? '') == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                                                        <option value="Shipping" {{ ($item->status_pengiriman ?? '') == 'Shipping' ? 'selected' : '' }}>Shipping (Dalam Perjalanan)</option>
                                                        <option value="Delivered" {{ ($item->status_pengiriman ?? '') == 'Delivered' ? 'selected' : '' }}>Delivered (Tiba di Lokasi)</option>
                                                        <option value="Delayed" {{ ($item->status_pengiriman ?? '') == 'Delayed' ? 'selected' : '' }}>Delayed (Terlambat)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small text-muted fw-bold">Driver / Kurir</label>
                                                    <input type="text" class="form-control" name="driver" value="{{ $item->driver }}" placeholder="Nama driver / Plat nomor">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary fw-bold">Simpan Jadwal Delivery</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL APPROVAL (Khusus Admin & Superadmin) -->
                            @if(in_array(auth()->user()->role, ['admin', 'superadmin', 'super_admin']))
                            <div class="modal fade text-start" id="approvalModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title fw-bold">Approval Delivery Order</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="/delivery/{{ $item->id }}/approval" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-body py-3">
                                                <div class="mb-3">
                                                    <label class="form-label small text-muted fw-bold">Keputusan Approval</label>
                                                    <select class="form-select" name="status_approval" required>
                                                        <option value="Approved" {{ ($item->status_approval ?? '') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                                        <option value="Rejected" {{ ($item->status_approval ?? '') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                        <option value="Pending" {{ ($item->status_approval ?? '') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small text-muted fw-bold">Catatan Approval</label>
                                                    <textarea class="form-control" name="catatan_approval" rows="2" placeholder="Catatan opsional...">{{ $item->catatan_approval }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success fw-bold">Simpan Approval</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- MODAL DELETE (Khusus Superadmin) -->
                            @if(in_array(auth()->user()->role, ['superadmin', 'super_admin']))
                            <div class="modal fade text-start" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-body text-center py-4">
                                            <i class="fa-solid fa-triangle-exclamation text-danger fa-3x mb-3"></i>
                                            <h6 class="fw-bold">Hapus Delivery Order?</h6>
                                            <p class="text-muted small mb-0">{{ $item->no_do }} akan terhapus secara permanen.</p>
                                        </div>
                                        <div class="modal-footer justify-content-center border-top-0 pt-0">
                                            <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                            <form action="/delivery/{{ $item->id }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm px-3">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada data Delivery Order.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection