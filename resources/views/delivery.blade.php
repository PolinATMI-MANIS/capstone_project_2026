@extends('layouts.app')

@section('content')
<!-- Header Utama -->
<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <span class="badge bg-warning text-dark fw-bold mb-1 px-2 py-1" style="font-size: 11px; letter-spacing: 0.5px;">MODULE</span>
        <h1 class="fw-bold display-6 m-0 text-dark" style="letter-spacing: -0.5px;">PURCHASE & DELIVERY</h1>
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
                <i class="fa-solid fa-user me-2 text-warning"></i> User Biasa
            </div>
        @endauth
    </div>
</div>

<!-- Alert Notifikasi -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Tab Navigasi Tengah -->
<div class="d-flex justify-content-center align-items-center mb-4">
    <div class="sub-navbar">
        <a href="/purchase" class="sub-nav-btn">Purchase Order</a>
        <a href="/delivery" class="sub-nav-btn active">Delivery Order</a>
    </div>
</div>

<!-- Sub-Header & Tombol Aksi -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0 text-dark">Delivery Order Directory</h4>
        <p class="text-muted small mb-0">Pengawasan pengiriman barang berdasarkan data Purchase Order.</p>
    </div>
    
    <div>
        <!-- TOMBOL CETAK / DOWNLOAD: BISA UNTUK SEMUA USER -->
        <button class="btn btn-success me-2 shadow-sm" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Cetak / Download
        </button>

        <!-- TOMBOL TAMBAH DO: HANYA MUNCUL UNTUK ADMIN / SUPERADMIN -->
        @auth
            @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                <button class="btn btn-warning text-white fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addDeliveryModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Delivery Order
                </button>
            @endif
        @endauth
    </div>
</div>

<!-- Tabel Data Delivery Order -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-3">No. DO</th>
                        <th>Ref PO</th>
                        <th>Customer</th>
                        <th>Barang & Qty (Dari PO)</th>
                        <th>Deadline PO</th>
                        <th>Tgl Kirim (Actual)</th>
                        <th>Status Pengiriman</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $item)
                        @php
                            // Ambil deadline dari PO jika ada
                            $deadlinePo = $item->purchase->waktu_tgl_deadline ?? null;
                            $isLate = false;
                            if ($deadlinePo && $item->tanggal_kirim > \Carbon\Carbon::parse($deadlinePo)->format('Y-m-d')) {
                                $isLate = true;
                            }
                        @endphp
                        <tr>
                            <!-- No DO -->
                            <td class="ps-3 fw-bold text-dark">{{ $item->no_do }}</td>
                            
                            <!-- Ref PO -->
                            <td><span class="badge bg-light text-dark border fw-bold">{{ $item->ref_po }}</span></td>
                            
                            <!-- Customer (Diambil dari PO) -->
                            <td>
                                <span class="fw-semibold text-dark">{{ $item->purchase->nama_customer ?? '-' }}</span>
                            </td>

                            <!-- Nama Barang & Qty (Diambil dari PO) -->
                            <td>
                                <div class="lh-sm">
                                    <div class="fw-bold text-dark">{{ $item->purchase->nama_barang ?? 'Barang tidak ditemukan' }}</div>
                                    <small class="text-muted">Jumlah: {{ $item->purchase->kuantitas ?? 0 }} unit</small>
                                </div>
                            </td>

                            <!-- Target Deadline PO -->
                            <td>
                                @if($deadlinePo)
                                    <small class="text-muted">
                                        <i class="fa-regular fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($deadlinePo)->format('d/m/Y') }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <!-- Tanggal Kirim Actual -->
                            <td>
                                @if($item->status == 'Waiting')
                                    <!-- Jika masih Waiting, jangan tampilkan tanggal & badge Tepat Waktu dulu -->
                                    <div class="lh-sm">
                                        <span class="text-muted fw-bold">-</span>
                                        <div>
                                            <small class="text-muted fst-italic">Belum dikirim</small>
                                        </div>
                                    </div>
                                @else
                                    <!-- Jika status SUDAH diproses admin (On Progress, Shipping, Delivered, dll) -->
                                    <div class="lh-sm">
                                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($item->tanggal_kirim)->format('d/m/Y') }}</div>
                                        @if($isLate)
                                            <span class="badge bg-danger-subtle text-danger border border-danger" style="font-size: 10px;">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i>Terlambat
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success" style="font-size: 10px;">
                                                <i class="fa-solid fa-circle-check me-1"></i>Tepat Waktu
                                            </span> 
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Status Pengiriman -->
                            <td>
                                <span class="badge bg-{{ $item->status == 'Delivered' ? 'success' : ($item->status == 'Delayed' ? 'danger' : ($item->status == 'Shipping' ? 'primary' : 'warning')) }}">
                                    {{ $item->status }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="text-end pe-3">
                                <a href="/delivery/{{ $item->id }}" class="btn btn-sm btn-light border text-secondary fw-semibold">
                                    <i class="fa-solid fa-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted opacity-50">
                                    <i class="fa-solid fa-truck-fast fa-3x mb-3 text-warning"></i>
                                    <p class="fw-bold small text-uppercase m-0">Belum ada data Delivery Order tercatat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Delivery Order (Pilih PO -> Konfirmasi Tanggal Kirim) -->
@auth
    @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
    <div class="modal fade" id="addDeliveryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark">Add Delivery Order (From PO)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/delivery/store" method="POST">
                    @csrf
                    <div class="modal-body py-4">
                        <div class="row g-3">
                            <!-- Dropdown Pilih No PO -->
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Pilih Referensi Purchase Order (PO)</label>
                                <select class="form-select form-select-lg" name="ref_po" required>
                                    <option value="" selected disabled>-- Pilih Purchase Order --</option>
                                    @foreach($purchases as $po)
                                        <option value="{{ $po->no_po }}">
                                            {{ $po->no_po }} — Customer: {{ $po->nama_customer }} | Barang: {{ $po->nama_barang }} (Deadline: {{ \Carbon\Carbon::parse($po->waktu_tgl_deadline)->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text small">Data customer, barang, dan kuantitas akan otomatis terhubung dari PO yang dipilih.</div>
                            </div>

                            <!-- Konfirmasi Tanggal Kirim Actual -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Tanggal Rencana Kirim / Actual Kirim</label>
                                <input type="date" class="form-control" name="tanggal_kirim" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Status Pengiriman -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Status Pengiriman</label>
                                <select class="form-select" name="status">
                                    <option value="On Progress" selected>On Progress</option>
                                    <option value="Shipping">Shipping (Dalam Perjalanan)</option>
                                    <option value="Delivered">Delivered (Sampai)</option>
                                    <option value="Delayed">Delayed (Terlambat)</option>
                                </select>
                            </div>

                            <!-- Ekspedisi / Driver -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small text-muted">Nama Driver / Kurir / Ekspedisi</label>
                                <input type="text" class="form-control" name="driver" placeholder="Contoh: Pak Budi (Truk Box No. B 1234 CD)">
                            </div>

                            <!-- Catatan / Keterangan Keterlambatan -->
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Catatan / Alasan Keterlambatan (Opsional)</label>
                                <textarea class="form-control" name="keterangan" rows="2" placeholder="Tuliskan alasan jika ada penundaan atau kendala pengiriman..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn text-muted" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-white fw-bold px-4">Simpan Delivery Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endauth
@endsection