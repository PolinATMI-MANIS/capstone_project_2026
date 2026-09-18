@extends('layouts.app')

@section('content')
<style>
    /* Styling Glassmorphism Khusus Halaman Ini */
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.9);
        box-shadow: 0 12px 35px 0 rgba(31, 38, 135, 0.06);
    }
    
    .table-glass {
        background: transparent !important;
    }
    
    .table-glass th, .table-glass td {
        background: transparent !important;
        border-color: rgba(226, 232, 240, 0.6) !important;
    }

    .modal-glass {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 1) !important;
    }

    /* Animasi dan Style untuk Action Bar Melayang (Bulk Delete) */
    .floating-bulk-bar {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #fff;
        border-radius: 50rem;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.4);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        transform: translateY(20px);
        opacity: 0;
        pointer-events: none;
    }

    .floating-bulk-bar.show {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .btn-gradient-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        color: white;
        transition: all 0.2s ease;
    }

    .btn-gradient-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        color: white;
    }

    .btn-gradient-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        border: none;
        color: white;
        transition: all 0.2s ease;
    }

    .btn-gradient-primary:hover {
        background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.35);
        color: white;
    }
</style>

<div class="container-fluid px-0">
    <!-- Header & Tombol Aksi Utama -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Permintaan & Persetujuan Barang</h2>
            <p class="text-muted text-sm mb-0">Kelola dan pantau pengajuan stok masuk/keluar dari berbagai departemen.</p>
        </div>
        
        <!-- Area Tombol di Sebelah Kanan -->
        <div class="d-flex align-items-center gap-2">
            @php
                $userRole = strtolower(Auth::user() ? Auth::user()->role : 'user');
            @endphp

            @if(in_array($userRole, ['super-admin', 'super_admin']))
                <!-- Tombol / Ikon Hapus Semua / Terpilih -->
                <button type="button" id="btnHapusSemua" class="btn btn-gradient-danger shadow-sm rounded-pill px-3.5 py-2.5 fw-semibold d-inline-flex align-items-center gap-2" title="Hapus Data Terpilih">
                    <i class="fa-solid fa-trash-can"></i> 
                    <span>Hapus Semua</span>
                </button>
            @endif

            <button type="button" class="btn btn-gradient-primary shadow-sm rounded-pill px-4 py-2.5 fw-semibold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addRequestModal">
                <i class="fa-solid fa-plus-circle"></i> 
                <span>Buat Pengajuan Baru</span>
            </button>
        </div>
    </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-3 border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-3 border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabel Daftar Permintaan dengan Efek Glassmorphism -->
    <div class="card glass-card border-0 rounded-4 overflow-hidden position-relative">
        
        <!-- Floating Bulk Delete Toolbar (Muncul di atas tabel saat ada checkbox yang dicentang) -->
        @if(in_array($userRole, ['super-admin', 'super_admin']))
        <div id="bulkDeleteBar" class="floating-bulk-bar position-sticky top-0 z-3 mx-4 mt-3 p-2.5 px-4 d-flex justify-content-between align-items-center shadow-lg">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger rounded-pill px-2.5 py-1.5 fw-bold" id="selectedCount">0</span>
                <span class="fw-semibold small text-light">item dipilih untuk dihapus</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="cancelSelectionBtn" class="btn btn-sm btn-link text-light text-decoration-none small px-2">Batal</button>
                <button type="button" id="bulkDeleteBtn" class="btn btn-gradient-danger btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-trash-can fa-xs"></i> 
                    <span>Hapus Terpilih</span>
                </button>
            </div>
        </div>
        @endif

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-glass table-hover align-middle mb-0">
                    <thead class="text-uppercase text-secondary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; background: rgba(241, 245, 249, 0.6);">
                        <tr>
                            @if(in_array($userRole, ['super-admin', 'super_admin']))
                                <th class="py-3 ps-4" style="width: 40px;">
                                    <input type="checkbox" id="selectAll" class="form-check-input rounded-2 cursor-pointer">
                                </th>
                            @endif
                            <th class="py-3 px-3">Kode Request</th>
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
                                @if(in_array($userRole, ['super-admin', 'super_admin']))
                                    <td class="py-3 ps-4">
                                        <input type="checkbox" name="ids[]" value="{{ $req->id }}" class="form-check-input request-checkbox rounded-2 cursor-pointer">
                                    </td>
                                @endif
                                <td class="py-3 px-3 fw-bold text-primary">{{ $req->request_code }}</td>
                                <td class="py-3 px-3"><span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1.5 rounded-pill fw-semibold">{{ $req->department }}</span></td>
                                <td class="py-3 px-3 fw-semibold text-dark">{{ $req->nama_barang ?? 'Barang Dihapus' }}</td>
                                <td class="py-3 px-3 fw-bold text-dark">{{ $req->qty }} {{ $req->satuan ?? '' }}</td>
                                <td class="py-3 px-3">
                                    @if($req->type == 'in' || $req->type == 'create_barang')
                                        <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1.5 rounded-pill"><i class="fa-solid fa-arrow-down me-1"></i> Masuk / Baru</span>
                                    @elseif($req->type == 'out')
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-2.5 py-1.5 rounded-pill"><i class="fa-solid fa-arrow-up me-1"></i> Keluar</span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1.5 rounded-pill">{{ ucfirst($req->type) }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    @if(str_contains($req->status, 'Pending'))
                                        <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-semibold">Pending</span>
                                    @elseif(str_contains($req->status, 'Approved'))
                                        <span class="badge bg-success px-3 py-1.5 rounded-pill fw-semibold">{{ $req->status }}</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-1.5 rounded-pill fw-semibold">{{ $req->status }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-muted small">{{ $req->notes ?? '-' }}</td>
                                <td class="py-3 px-3 text-center">
                                    @if(str_contains($req->status, 'Pending'))
                                        @if(in_array($userRole, ['admin', 'super-admin', 'super_admin']))
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <!-- Tombol Approve -->
                                                <form action="{{ route('inventory.produksi.requests.approve', $req->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 0.75rem; font-weight: 600;">
                                                        <i class="fa-solid fa-check"></i> Approve
                                                    </button>
                                                </form>

                                                <!-- Tombol Reject -->
                                                <form action="{{ route('inventory.produksi.requests.reject', $req->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 py-1.5 shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 0.75rem; font-weight: 600;">
                                                        <i class="fa-solid fa-xmark"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted small fst-italic">Menunggu Persetujuan Admin</span>
                                        @endif
                                    @else
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <span class="text-muted small fst-italic">{{ $req->status }}</span>
                                            @if(in_array($userRole, ['super-admin', 'super_admin']))
                                                <form action="{{ route('inventory.produksi.requests.destroy', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data pengajuan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 28px; height: 28px;" title="Hapus Data">
                                                        <i class="fa-solid fa-trash fa-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ in_array($userRole, ['super-admin', 'super_admin']) ? '9' : '8' }}" class="text-center py-5 text-muted">
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
                    <button type="submit" class="btn btn-gradient-primary px-4 py-2 rounded-3 shadow-sm">Kirim Permintaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.request-checkbox');
        const btnHapusSemua = document.getElementById('btnHapusSemua');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                const isChecked = this.checked;  
                checkboxes.forEach(cb => cb.checked = isChecked);
            });
        }
  
        if (btnHapusSemua) {  
            btnHapusSemua.addEventListener('click', function () {
                const selectedIds = Array.from(document.querySelectorAll('.request-checkbox:checked')).map(cb => cb.value);
                
                if (selectedIds.length === 0) {
                    alert('Silakan centang minimal satu data yang ingin dihapus terlebih dahulu.');
                    return;
                }

                if (confirm('Yakin ingin menghapus ' + selectedIds.length + ' data yang dipilih?')) {
                    fetch("{{ route('inventory.produksi.requests.destroyBulk') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'  
                        },
                        body: JSON.stringify({ ids: selectedIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus data.');
                    });
                }
            });
        }
    });
</script>
@endpush