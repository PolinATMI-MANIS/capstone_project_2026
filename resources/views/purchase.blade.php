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
<div class="container-fluid px-4 py-3">
    <!-- 1. HEADER UTAMA & WIDGET USER -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <span class="badge bg-warning text-dark fw-bold mb-1 px-2 py-1" style="font-size: 11px; letter-spacing: 0.5px;">MODULE</span>
            <h2 class="fw-bold text-uppercase text-dark m-0" style="letter-spacing: 1px;">PURCHASE & DELIVERY</h2>
        </div>

        <div>
            @auth
                <div class="d-flex align-items-center bg-white shadow-sm px-3 py-2 rounded-4 border">
                    <div class="bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 18px;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="lh-sm">
                        <div class="fw-bold text-dark small mb-0">{{ auth()->user()->name ?? 'User Biasa' }}</div>
                        <span class="badge bg-light text-warning border border-warning" style="font-size: 10px; padding: 2px 6px;">
                            {{ strtoupper(auth()->user()->role ?? 'USER') }}
                        </span>
                    </div>
                </div>
            @else
                <div class="d-flex align-items-center bg-white shadow-sm px-3 py-2 rounded-4 border text-muted small">
                    <i class="fa-solid fa-user me-2 text-warning"></i> User Biasa
                </div>
            @endauth
        </div>
    </div>

    <!-- 2. TAB NAVIGASI TENGAH (SWITCHER) -->
    <div class="d-flex justify-content-center align-items-center mb-4">
        <div class="bg-white p-1 rounded-pill shadow-sm d-inline-flex border">
            <a href="/purchase" class="btn btn-danger text-white rounded-pill px-4 fw-semibold shadow-sm" style="background-color: #ff5722; border: none;">
                Purchase Order
            </a>
            <a href="/delivery" class="btn btn-link text-secondary text-decoration-none px-4 fw-semibold">
                Delivery Order
            </a>
        </div>
    </div>

    <!-- ALERT NOTIFIKASI -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-info me-2"></i><strong>Informasi:</strong> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 3. DIRECTORY CARD & TABLE -->
    <div id="printable-area" class="card border-0 shadow-sm rounded-3 p-4 mb-4">
        
        <!-- Header Section (Judul Kiri, Tombol Kanan) -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Purchase Order Directory</h4>
                <p class="text-muted small mb-0">Sistem manajemen dan pengawasan dokumen Purchase Order.</p>
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

        <!-- TABEL DATA PURCHASE -->
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0" style="font-size: 13px;">
                <thead class="bg-light text-secondary text-uppercase fw-bold">
                    <tr>
                        <th class="ps-3">No. PO</th>
                        <th>Customer</th>
                        <th>Barang & Kode</th>
                        <th>Material</th>
                        <th>Tgl PO</th>
                        <th>Deadline & Est.</th>
                        <th>Qty x Harga</th>
                        <th>Total Nilai</th>
                        <th>Status</th>
                        <th class="text-center pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($purchases) && count($purchases) > 0)
                        @foreach($purchases as $item)
                            <tr>
                                <td class="ps-3 fw-bold text-dark">{{ $item->no_po }}</td>
                                <td><span class="fw-semibold text-dark">{{ $item->nama_customer }}</span></td>
                                <td>
                                    <div class="lh-sm">
                                        <div class="fw-bold text-dark">{{ $item->nama_barang }}</div>
                                        <small class="text-muted">Kode: {{ $item->kode_barang ?? '-' }}</small>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border rounded-pill px-2 py-1">{{ $item->permintaan_material ?? '-' }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pemesanan)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="lh-sm">
                                        <div><i class="fa-regular fa-calendar me-1 text-muted"></i>{{ \Carbon\Carbon::parse($item->waktu_tgl_deadline)->format('d/m/Y H:i') }}</div>
                                        <small class="text-muted">Est: {{ $item->estimasi_pengerjaan ?? '-' }}</small>
                                    </div>
                                </td>
                                <td>{{ $item->kuantitas }} unit @ <br>Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                <td class="fw-bold text-dark">
                                    Rp {{ number_format(($item->kuantitas ?? 0) * ($item->harga_satuan ?? 0), 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->status == 'Approved' || $item->status == 'Completed' ? 'success' : ($item->status == 'Waiting' || $item->status == 'On Progress' ? 'warning' : ($item->status == 'Rejected' || $item->status == 'Cancelled' ? 'danger' : 'secondary')) }} px-2 py-1">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="d-inline-flex gap-1" role="group">
                                        <!-- Detail Button -->
                                        <a href="/purchase/{{ $item->id }}" class="btn btn-sm btn-light border fw-semibold" style="font-size: 0.8rem;" title="Lihat Detail">
                                            <i class="fa-regular fa-eye me-1"></i> Detail
                                        </a>

                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-sm btn-warning text-dark fw-semibold" style="font-size: 0.8rem;"
                                                data-bs-toggle="modal" data-bs-target="#editPoModal{{ $item->id }}">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                        </button>

                                        <!-- Approval Button (Bisa diakses Admin & Superadmin) -->
                                        @if(in_array(auth()->user()?->role, ['admin', 'superadmin']))
                                            @if($item->status == 'Approved')
                                                <button type="button" class="btn btn-sm btn-outline-success fw-semibold" style="font-size: 0.8rem;" disabled>
                                                    <i class="fa-solid fa-check-double me-1"></i> Approved
                                                </button>
                                            @elseif($item->status == 'Rejected' || $item->status == 'Cancelled')
                                                <button type="button" class="btn btn-sm btn-outline-danger fw-semibold" style="font-size: 0.8rem;" disabled>
                                                    <i class="fa-solid fa-ban me-1"></i> Rejected
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-success text-white fw-semibold" style="font-size: 0.8rem;"
                                                        data-bs-toggle="modal" data-bs-target="#approvalModal{{ $item->id }}">
                                                    <i class="fa-solid fa-circle-check me-1"></i> Approval
                                                </button>
                                            @endif
                                        @endif

                                        <!-- Delete Button (Tersedia untuk Admin & Superadmin) -->
                                        @if(in_array(auth()->user()?->role, ['admin', 'superadmin']))
                                            <button type="button" class="btn btn-sm btn-outline-danger" style="font-size: 0.8rem;" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}" title="Hapus Data">
                                                <i class="fa-solid fa-trash-can"></i> Delete
                                            </button>
                                        @endif
                                    </div>

                                    <!-- MODAL EDIT PURCHASE ORDER -->
                                    <div class="modal fade text-start" id="editPoModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title fw-bold">
                                                        <i class="fa-solid fa-pen-to-square me-2"></i>Edit Purchase Order — {{ $item->no_po }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="/purchase/{{ $item->id }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body py-3">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-muted">Nama Customer</label>
                                                                <input type="text" class="form-control" name="nama_customer" value="{{ $item->nama_customer }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-muted">Nama Barang</label>
                                                                <input type="text" class="form-control" name="nama_barang" value="{{ $item->nama_barang }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-muted">Kode Barang</label>
                                                                <input type="text" class="form-control" name="kode_barang" value="{{ $item->kode_barang }}">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-muted">Permintaan Material</label>
                                                                <input type="text" class="form-control" name="permintaan_material" value="{{ $item->permintaan_material }}">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-muted">Tanggal Pemesanan</label>
                                                                <input type="date" class="form-control" name="tanggal_pemesanan" value="{{ \Carbon\Carbon::parse($item->tanggal_pemesanan)->format('Y-m-d') }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-muted">Deadline</label>
                                                                <input type="datetime-local" class="form-control" name="waktu_tgl_deadline" value="{{ \Carbon\Carbon::parse($item->waktu_tgl_deadline)->format('Y-m-d\TH:i') }}" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-semibold small text-muted">Kuantitas</label>
                                                                <input type="number" class="form-control" name="kuantitas" value="{{ $item->kuantitas }}" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-semibold small text-muted">Harga Satuan (Rp)</label>
                                                                <input type="number" class="form-control" name="harga_satuan" value="{{ $item->harga_satuan }}" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-semibold small text-muted">Estimasi Pengerjaan</label>
                                                                <input type="text" class="form-control" name="estimasi_pengerjaan" value="{{ $item->estimasi_pengerjaan }}" placeholder="Contoh: 50 jam">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning text-white fw-bold">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- MODAL APPROVAL ADMIN FOR USER DATA -->
                                    <div class="modal fade text-start" id="approvalModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title fw-bold">
                                                        <i class="fa-solid fa-circle-check me-2"></i>Approval Purchase Order
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="/purchase/{{ $item->id }}/approval" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-body py-4 text-center">
                                                        <div class="mb-3">
                                                            <span class="badge bg-light text-dark border fs-6 px-3 py-2">{{ $item->no_po }}</span>
                                                        </div>
                                                        <p class="text-muted small mb-3">Pilih tindakan approval untuk dokumen Purchase Order dari customer <strong>{{ $item->nama_customer }}</strong>.</p>
                                                        
                                                        <div class="mb-3 text-start">
                                                            <label class="form-label fw-semibold small text-muted">Keputusan Status</label>
                                                            <select class="form-select" name="status" required>
                                                                <option value="Approved" {{ $item->status == 'Approved' ? 'selected' : '' }}>Approve (Disetujui)</option>
                                                                <option value="Rejected" {{ $item->status == 'Rejected' ? 'selected' : '' }}>Reject (Ditolak)</option>
                                                            </select>
                                                        </div>
                                                        <div class="text-start">
                                                            <label class="form-label fw-semibold small text-muted">Catatan Approval (Opsional)</label>
                                                            <textarea class="form-control" name="catatan_approval" rows="2" placeholder="Masukkan alasan atau catatan pendukung..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 justify-content-end">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success fw-bold px-4">Proses Approval</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- MODAL DELETE (ADAPTIF: ADMIN = REQUEST SUPERADMIN, SUPERADMIN = DIRECT DELETE) -->
                                    @if(in_array(auth()->user()?->role, ['admin', 'superadmin']))
                                        <div class="modal fade text-start" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    @if(auth()->user()?->role == 'admin')
                                                        <!-- Admin kirim permohonan ke Superadmin -->
                                                        <form action="/purchase/{{ $item->id }}/request-delete" method="POST">
                                                            @csrf
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title fw-bold fs-6">
                                                                    <i class="fa-solid fa-triangle-exclamation me-2"></i>Pengajuan Hapus Purchase Order
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body py-3">
                                                                <p class="text-dark small mb-2">
                                                                    Penghapusan PO <strong>{{ $item->no_po }}</strong> memerlukan persetujuan dari Superadmin. Silakan isi alasan penghapusan:
                                                                </p>
                                                                <textarea name="reason" class="form-control form-control-sm" rows="3" placeholder="Contoh: Terjadi kesalahan duplikasi data..." required></textarea>
                                                            </div>
                                                            <div class="modal-footer border-top-0 pt-0">
                                                                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger btn-sm px-3">Kirim Pengajuan Hapus</button>
                                                            </div>
                                                        </form>
                                                    @else
                                                        <!-- Superadmin langsung menghapus data -->
                                                        <div class="modal-body text-center py-4">
                                                            <i class="fa-solid fa-triangle-exclamation text-danger fa-3x mb-3"></i>
                                                            <h6 class="fw-bold text-dark">Hapus Purchase Order?</h6>
                                                            <p class="text-muted small mb-0">Data <strong>{{ $item->no_po }}</strong> akan terhapus secara permanen dari sistem.</p>
                                                        </div>
                                                        <div class="modal-footer justify-content-center border-top-0 pt-0">
                                                            <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                                            <form action="/purchase/{{ $item->id }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm px-3">Hapus Permanen</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted opacity-50">
                                    <i class="fa-solid fa-box-open fa-3x mb-3 text-warning"></i>
                                    <p class="fw-bold small text-uppercase m-0">Belum ada data Purchase Order tercatat.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pop-up Form Add Purchase Order -->
<div class="modal fade" id="addPurchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold text-dark">Add Purchase Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/purchase/store" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Nama Customer</label>
                            <input type="text" name="nama_customer" class="form-control" placeholder="Masukkan nama customer" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Permintaan Material</label>
                            <input type="text" name="permintaan_material" class="form-control" placeholder="Contoh: Plat Besi 5mm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Kode Barang</label>
                            <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: BRG-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Tanggal Pemesanan</label>
                            <input type="date" name="tanggal_pemesanan" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Gear Custom Type A" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Waktu & Tanggal Deadline</label>
                            <input type="datetime-local" name="waktu_tgl_deadline" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Kuantitas</label>
                            <input type="number" name="kuantitas" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Harga Satuan (Rp)</label>
                            <input type="number" name="harga_satuan" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Estimasi Pengerjaan</label>
                            <input type="text" name="estimasi_pengerjaan" class="form-control" placeholder="Contoh: 14 Hari" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Status Order</label>
                            <select name="status" class="form-select" required>
                                <option value="Waiting" selected>Waiting</option>
                                <option value="On Progress">On Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Keterangan Tambahan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan catatan khusus (opsional)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-white fw-bold px-4">Save PO</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection