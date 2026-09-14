@extends('layouts.app')

@section('content')
<style>
    /* Styling Glassmorphism Khusus Halaman Ini */
    .glass-card {
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 10px 30px 0 rgba(31, 38, 135, 0.04);
    }
    
    .table-glass {
        background: transparent !important;
    }
    
    .table-glass th, .table-glass td {
        background: transparent !important;
        border-color: rgba(226, 232, 240, 0.6) !important;
    }

    .modal-glass {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 1) !important;
    }
</style>

<div class="container-fluid px-0">
    <!-- Header & Tombol Ajukan Permintaan -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Permintaan & Persetujuan Barang</h2>
            <p class="text-muted text-sm mb-0">Kelola dan pantau pengajuan stok masuk/keluar dari berbagai departemen.</p>
        </div>
        <button type="button" class="btn btn-primary shadow-sm rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#addRequestModal">
            <i class="fa-solid fa-plus me-2"></i> Buat Pengajuan Baru
        </button>
    </div>

    <!-- Tabel Daftar Permintaan dengan Efek Glassmorphism -->
    <div class="card glass-card border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-glass table-hover align-middle mb-0">
                    <thead class="text-uppercase text-secondary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; background: rgba(241, 245, 249, 0.5);">
                        <tr>
                            <th class="py-3 px-4">Kode Request</th>
                            <th class="py-3 px-3">Departemen</th>
                            <th class="py-3 px-3">Nama Barang</th>
                            <th class="py-3 px-3">Qty</th>
                            <th class="py-3 px-3">Tipe Mutasi</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Catatan</th>
                            <th class="py-3 px-3 text-center">Aksi (Approve / Reject)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td class="py-3 px-4 fw-bold text-primary">{{ $req->request_code }}</td>
                                <td class="py-3 px-3"><span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 fw-semibold">{{ $req->department }}</span></td>
                                <td class="py-3 px-3 fw-semibold text-dark">{{ $req->nama_barang ?? 'Barang Dihapus' }}</td>
                                <td class="py-3 px-3 fw-bold text-dark">{{ $req->qty }} {{ $req->satuan ?? '' }}</td>
                                <td class="py-3 px-3">
                                    @if($req->type == 'in' || $req->type == 'create_barang')
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><i class="fa-solid fa-arrow-down me-1"></i> Masuk / Baru</span>
                                    @elseif($req->type == 'out')
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1"><i class="fa-solid fa-arrow-up me-1"></i> Keluar</span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">{{ ucfirst($req->type) }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    @if(str_contains($req->status, 'Pending'))
                                        <span class="badge bg-warning text-dark px-2 py-1">{{ $req->status }}</span>
                                    @elseif(str_contains($req->status, 'Approved'))
                                        <span class="badge bg-success px-2 py-1">{{ $req->status }}</span>
                                    @else
                                        <span class="badge bg-danger px-2 py-1">{{ $req->status }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-muted small">{{ $req->notes ?? '-' }}</td>
                                <td class="py-3 px-3 text-center">
                                    @php
                                        $userRole = Auth::user() ? Auth::user()->role : 'user';
                                    @endphp

                                    @if(str_contains($req->status, 'Pending'))
                                        @if($userRole === 'admin' || $userRole === 'super-admin')
                                            <!-- Tombol Approve -->
                                            <form action="{{ route('inventory.produksi.requests.approve', $req->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success px-2.5 py-1 me-1 shadow-sm" title="Setujui Permintaan">
                                                    <i class="fa-solid fa-check me-1"></i> Approve
                                                </button>
                                            </form>

                                            <!-- Tombol Reject -->
                                            <form action="{{ route('inventory.produksi.requests.reject', $req->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger px-2.5 py-1 shadow-sm" title="Tolak Permintaan">
                                                    <i class="fa-solid fa-xmark me-1"></i> Reject
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small fst-italic">Menunggu Persetujuan Admin</span>
                                        @endif
                                    @else
                                        <span class="text-muted small fst-italic me-2">{{ $req->status }}</span>
                                        @if($userRole === 'super-admin')
                                            <form action="{{ route('inventory.produksi.requests.destroy', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data pengajuan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 shadow-sm" title="Hapus Data">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="py-3">
                                        <i class="fa-solid fa-folder-open fs-2 mb-2 text-secondary opacity-50"></i>
                                        <p class="mb-0">Belum ada data permintaan barang tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Buat Pengajuan Baru (Glassmorphic) -->
<div class="modal fade" id="addRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-glass border-0 shadow-lg rounded-4">
            <form action="{{ route('inventory.produksi.requests.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark">Buat Permintaan Barang Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Departemen Pengaju</label>
                        <input type="text" name="department" class="form-control bg-white bg-opacity-50 border-light shadow-none" value="Produksi / Gudang" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control bg-white bg-opacity-50 border-light shadow-none" placeholder="Masukkan nama barang..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Jumlah (Qty)</label>
                        <input type="number" name="qty" class="form-control bg-white bg-opacity-50 border-light shadow-none" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Satuan</label>
                        <input type="text" name="satuan" class="form-control bg-white bg-opacity-50 border-light shadow-none" placeholder="Misal: Pcs, Kg, Meter..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Catatan / Keterangan</label>
                        <textarea name="notes" class="form-control bg-white bg-opacity-50 border-light shadow-none" rows="2" placeholder="Keperluan untuk project apa..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-3 py-2 rounded-3 text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">Kirim Permintaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection