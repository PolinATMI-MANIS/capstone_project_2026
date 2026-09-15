@extends('layouts.app')

@section('content')
<!-- Header Utama -->
<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <span class="badge bg-warning text-dark fw-bold mb-1 px-2 py-1" style="font-size: 11px; letter-spacing: 0.5px;">Capstone 2026</span>
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

<!-- Tab Navigasi Tengah -->
<div class="d-flex justify-content-center align-items-center mb-4">
    <div class="sub-navbar">
        <a href="/purchase" class="sub-nav-btn active">Purchase Order</a>
        <a href="/delivery" class="sub-nav-btn">Delivery Order</a>
    </div>
</div>

<!-- Sub-Header & Tombol Aksi -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0 text-dark">Purchase Order Directory</h4>
        <p class="text-muted small mb-0">Sistem manajemen dan pengawasan dokumen Purchase Order.</p>
    </div>
    
    <div>
        <button class="btn btn-success me-2 shadow-sm" onclick="window.print()">
            <i class="fa-solid fa-download me-1"></i> Download
        </button>

        <button class="btn btn-warning text-white fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">
            <i class="fa-solid fa-plus me-1"></i> Add Purchase Order
        </button>
    </div>
</div>

<!-- Tabel Lengkap Purchase Order -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-3">No. PO</th>
                        <th>Customer</th>
                        <th>Barang & Kode</th>
                        <th>Material</th>
                        <th>Tgl PO</th>
                        <th>Deadline & Est.</th>
                        <th>Qty x Harga</th>
                        <th>Total Nilai</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $item)
                        <tr>
                            <!-- No PO -->
                            <td class="ps-3 fw-bold text-dark">{{ $item->no_po }}</td>
                            
                            <!-- Customer -->
                            <td><span class="fw-semibold text-dark">{{ $item->nama_customer }}</span></td>
                            
                            <!-- Barang & Kode -->
                            <td>
                                <div class="lh-sm">
                                    <div class="fw-bold text-dark">{{ $item->nama_barang }}</div>
                                    <small class="text-muted">Kode: {{ $item->kode_barang ?? '-' }}</small>
                                </div>
                            </td>
                            
                            <!-- Permintaan Material -->
                            <td><span class="badge bg-light text-dark border">{{ $item->permintaan_material ?? '-' }}</span></td>
                            
                            <!-- Tanggal Pemesanan -->
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pemesanan)->format('d/m/Y') }}</td>
                            
                            <!-- Deadline & Estimasi -->
                            <td>
                                <div class="lh-sm">
                                    <div><i class="fa-regular fa-calendar me-1 text-muted"></i>{{ \Carbon\Carbon::parse($item->waktu_tgl_deadline)->format('d/m/Y H:i') }}</div>
                                    <small class="text-muted">Est: {{ $item->estimasi_pengerjaan ?? '-' }}</small>
                                </div>
                            </td>
                            
                            <!-- Kuantitas & Harga Satuan -->
                            <td>{{ $item->kuantitas }} unit @ <br>Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                            
                            <!-- Total Nilai Transaksi -->
                            <td class="fw-bold text-dark">
                                Rp {{ number_format(($item->kuantitas ?? 0) * ($item->harga_satuan ?? 0), 0, ',', '.') }}
                            </td>
                            
                            <!-- Status -->
                            <td>
                                <span class="badge bg-{{ $item->status == 'Completed' ? 'success' : ($item->status == 'On Progress' ? 'warning' : ($item->status == 'Cancelled' ? 'danger' : 'secondary')) }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            
                            <!-- Actions -->
                            <td class="text-end pe-3">
                                <div class="btn-group" role="group">
                                    <!-- 1. SEMUA USER: Tombol Detail -->
                                    <!-- Pastikan mengarah ke /purchase/ BUKAN /delivery/ -->
                                    <a href="/purchase/{{ $item->id }}" class="btn btn-sm btn-light border">
                                        Detail
                                    </a>

                                    @auth
                                        <!-- 2. ADMIN & SUPER ADMIN: Tombol Update Status Pengiriman -->
                                        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                                            <button type="button" class="btn btn-sm btn-warning text-white fw-semibold ms-1" 
                                                    data-bs-toggle="modal" data-bs-target="#updateStatusModal{{ $item->id }}">
                                                <i class="fa-solid fa-pen-to-square me-1"></i> Update Status
                                            </button>
                                        @endif

                                        <!-- 3. KHUSUS SUPER ADMIN: Tombol Hapus Data -->
                                        @if(auth()->user()->role == 'superadmin')
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-1" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted opacity-50">
                                    <i class="fa-solid fa-box-open fa-3x mb-3 text-warning"></i>
                                    <p class="fw-bold small text-uppercase m-0">Belum ada data Purchase Order tercatat.</p>
                                </div>
                            </td>
                        </tr>
                        <!-- Modal Update Status (Khusus Admin & Superadmin) -->
                        @auth
                            @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                            <div class="modal fade" id="updateStatusModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title fw-bold">Update Status Delivery — {{ $item->no_do }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="/delivery/{{ $item->id }}/update-status" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body py-3">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold small text-muted">Tanggal Kirim Actual</label>
                                                    <input type="date" class="form-control" name="tanggal_kirim" value="{{ $item->tanggal_kirim ?? date('Y-m-d') }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold small text-muted">Status Pengiriman Baru</label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="On Progress" {{ $item->status == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                                                        <option value="Shipping" {{ $item->status == 'Shipping' ? 'selected' : '' }}>Shipping (Dalam Perjalanan)</option>
                                                        <option value="Delivered" {{ $item->status == 'Delivered' ? 'selected' : '' }}>Delivered (Sampai)</option>
                                                        <option value="Delayed" {{ $item->status == 'Delayed' ? 'selected' : '' }}>Delayed (Terlambat)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold small text-muted">Nama Kurir / Driver</label>
                                                    <input type="text" class="form-control" name="driver" value="{{ $item->driver }}" placeholder="Contoh: Pak Budi (Truk B 1234 CD)">
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
                            @endif
                        @endauth
                        <!-- Modal Hapus Data (Khusus Super Admin) -->
                        @auth
                            @if(auth()->user()->role == 'superadmin')
                            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-body text-center py-4">
                                            <i class="fa-solid fa-triangle-exclamation text-danger fa-3x mb-3"></i>
                                            <h6 class="fw-bold text-dark">Hapus Delivery Order?</h6>
                                            <p class="text-muted small mb-0">Tindakan ini tidak bisa dibatalkan. Data {{ $item->no_do }} akan terhapus secara permanen.</p>
                                        </div>
                                        <div class="modal-footer justify-content-center border-top-0 pt-0">
                                            <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
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
                        @endauth
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pop-up Form Add Purchase Order (Biarkan seperti aslinya) -->
<div class="modal fade" id="addPurchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <!-- Isi modal add purchase masih sama dengan kodemu sebelumnya, saya tidak ubah bagian ini agar tidak kepanjangan -->
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold text-dark">Add Purchase Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/purchase/store" method="POST">
                @csrf
                <!-- (Isi form sama seperti sebelumnya...) -->
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