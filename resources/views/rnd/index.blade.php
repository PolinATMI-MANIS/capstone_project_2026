@extends('layouts.app')

@section('title', 'Research & Development (R&D)')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0" style="color: #0f172a;">Research & Development (R&D)</h4>
            <small class="text-muted">Monitoring alur pengajuan & pengujian konsep produk baru</small>
        </div>
        <a href="{{ route('rnd.create') }}" class="btn btn-machine">
            <i class="fa-solid fa-plus me-1"></i> Ajukan Konsep Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 8px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="bg-light" style="font-size: 0.8rem; color: #64748b;">
                        <tr>
                            <th class="ps-4">NO</th>
                            <th>NAMA PRODUK</th>
                            <th>DESKRIPSI KONSEP</th>
                            <th>STATUS ALUR R&D</th>
                            <th>TANGGAL PRAPRODUKSI</th>
                            <th class="text-end pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.9rem;">
                        @forelse($features as $index => $item)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->nama_produk }}</div>
                            </td>
                            <td>{{ Str::limit($item->deskripsi_konsep, 60) }}</td>
                            <td>
                                @php
                                    $badgeClass = match($item->status) {
                                        'Peluncuran Produk Baru' => 'bg-success',
                                        'Ide Dihentikan' => 'bg-danger',
                                        'Konsep Perlu Revisi', 'Rekayasa Ulang', 'Remedial Uji' => 'bg-warning text-dark',
                                        default => 'bg-primary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2" style="border-radius: 6px; font-weight: 500;">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $item->created_at->format('d M Y') }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Tombol Detail & Process (Semua role bisa lihat) -->
                                    <a href="{{ route('rnd.show', $item->id) }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px;">
                                        <i class="fa-solid fa-eye me-1"></i> Detail & Process
                                    </a>

                                    <!-- Form & Tombol Hapus (Hanya Admin / SuperAdmin) -->
                                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                        <form id="delete-form-{{ $item->id }}" action="{{ route('rnd.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $item->id }}" data-nama="{{ $item->nama_produk }}" style="border-radius: 6px;">
                                                <i class="fa-solid fa-trash-can me-1"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-flask-vial fa-2x mb-3 text-secondary"></i>
                                <p class="m-0">Belum ada pengajuan konsep produk R&D.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Library SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Data R&D "${nama}" akan dihapus secara permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`delete-form-${id}`).submit();
                    }
                });
            });
        });
    });
</script>
@endsection