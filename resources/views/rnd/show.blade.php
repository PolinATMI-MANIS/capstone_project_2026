@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <a href="{{ route('rnd.index') }}" class="text-decoration-none text-muted" style="font-size: 0.85rem;">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar RnD
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 8px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Info Produk -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold m-0" style="color: #0f172a; font-size: 1rem;">
                        <i class="fa-solid fa-circle-info me-2 text-primary"></i>Detail Konsep Produk
                    </h5>
                </div>
                <div class="card-body p-4">
                    <h3 class="fw-bold text-dark mb-1">{{ $feature->nama_produk }}</h3>
                    <p class="text-muted mb-3" style="font-size: 0.9rem;">
                        <i class="fa-solid fa-user me-1"></i> Pengaju: <strong>{{ $feature->nama_pengaju ?? 'Tidak diketahui' }}</strong>
                    </p>
                    
                    <div class="mb-4">
                        @php
                            $badgeClass = match($feature->status) {
                                'Peluncuran Produk Baru' => 'bg-success',
                                'Ide Dihentikan' => 'bg-danger',
                                'Konsep Perlu Revisi', 'Rekayasa Ulang', 'Remedial Uji' => 'bg-warning text-dark',
                                default => 'bg-primary'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} px-3 py-2" style="border-radius: 6px; font-size: 0.85rem; font-weight: 500;">
                            Status Saat Ini: {{ $feature->status }}
                        </span>
                    </div>
                    
                    <h6 class="fw-bold mt-3 text-secondary" style="font-size: 0.85rem;">DESKRIPSI KONSEP:</h6>
                    <p class="text-dark" style="white-space: pre-line; font-size: 0.95rem;">{{ $feature->deskripsi_konsep }}</p>

                    @if($feature->gambar)
                        <h6 class="fw-bold mt-4 text-secondary" style="font-size: 0.85rem;">SKETSA / LAMPIRAN GAMBAR:</h6>
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $feature->gambar) }}" class="img-fluid rounded border shadow-sm" style="max-height: 250px; object-fit: cover;" alt="Sketsa Produk">
                        </div>
                    @endif

                    @if($feature->catatan_revisi)
                        <div class="alert alert-warning mt-4 mb-0 border-0 shadow-sm" style="border-radius: 8px;">
                            <strong><i class="fa-solid fa-triangle-exclamation me-1"></i> Catatan Revisi / Evaluasi:</strong>
                            <p class="m-0 mt-1" style="font-size: 0.9rem;">{{ $feature->catatan_revisi }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel Kontrol Alur Flowchart -->
        <div class="col-lg-5">
            {{-- HANYA BISA DIAKSES OLEH ADMIN DAN SUPERADMIN --}}
            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold m-0" style="color: #0f172a; font-size: 1rem;">
                            <i class="fa-solid fa-diagram-project me-2 text-warning"></i>Aksi / Evaluasi Flowchart
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('rnd.updateStatus', $feature->id) }}" method="POST" enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">PILIH AKSI KEPUTUSAN</label>
                                <select name="action" class="form-select @error('action') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="" selected disabled>-- Pilih Keputusan --</option>
                                    
                                    <!-- Tahap 1: Decision Komite -->
                                    <optgroup label="1. Evaluation Komite R&D">
                                        <option value="komite_approve" {{ old('action') == 'komite_approve' ? 'selected' : '' }}> Ya (Lanjut ke Desain & Rekayasa)</option>
                                        <option value="komite_revisi" {{ old('action') == 'komite_revisi' ? 'selected' : '' }}> Konsep Perlu Revisi</option>
                                        <option value="komite_reject" {{ old('action') == 'komite_reject' ? 'selected' : '' }}> Ide Dihentikan (END)</option>
                                    </optgroup>

                                    <!-- Tahap 2: Progres & Uji Teknis -->
                                    <optgroup label="2. Progres Prototype & Uji Teknis">
                                        <option value="to_prototype" {{ old('action') == 'to_prototype' ? 'selected' : '' }}> Masuk Pembuatan Prototype</option>
                                        <option value="to_uji_internal" {{ old('action') == 'to_uji_internal' ? 'selected' : '' }}> Masuk Uji Teknis Internal</option>
                                        <option value="teknis_approve" {{ old('action') == 'teknis_approve' ? 'selected' : '' }}> Lulus Review Teknis (Lanjut Validasi)</option>
                                        <option value="teknis_revisi" {{ old('action') == 'teknis_revisi' ? 'selected' : '' }}> Rekayasa Ulang & Revisi</option>
                                    </optgroup>

                                    <!-- Tahap 3: Sertifikasi & Produksi -->
                                    <optgroup label="3. Sertifikasi & Komersial">
                                        <option value="sertifikasi_approve" {{ old('action') == 'sertifikasi_approve' ? 'selected' : '' }}> Lulus Sertifikasi (Persiapan Produksi)</option>
                                        <option value="sertifikasi_revisi" {{ old('action') == 'sertifikasi_revisi' ? 'selected' : '' }}> Remedial & Uji Ulang</option>
                                        <option value="to_produksi" {{ old('action') == 'to_produksi' ? 'selected' : '' }}> Masuk Produksi Skala Komersial</option>
                                        <option value="to_launch" {{ old('action') == 'to_launch' ? 'selected' : '' }}> Output: Peluncuran Produk Baru (END)</option>
                                    </optgroup>
                                </select>
                                
                                @error('action')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">CATATAN EVALUASI / REVISI</label>
                                <textarea name="catatan_revisi" class="form-control @error('catatan_revisi') is-invalid @enderror" rows="3" placeholder="Isi jika ada instruksi revisi atau alasan ide dihentikan..." style="border-radius: 8px;">{{ old('catatan_revisi') }}</textarea>
                                
                                @error('catatan_revisi')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-machine w-100 py-2" style="background-color: #f97316; color: #fff; font-weight: 600; border-radius: 8px; border: none;">
                                <i class="fa-solid fa-rotate me-1"></i> Update Status R&D
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <!-- Tampilan Informasi jika dikunjungi oleh User biasa -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold m-0" style="color: #0f172a; font-size: 1rem;">
                            <i class="fa-solid fa-clock-rotate-left me-2 text-info"></i>Status Evaluasi
                        </h5>
                    </div>
                    <div class="card-body p-4 text-center">
                        <i class="fa-solid fa-user-gear fa-3x text-muted mb-3"></i>
                        <h6 class="fw-bold text-dark">Pengajuan Sedang Diproses</h6>
                        <p class="text-muted small m-0">
                            Hanya tim Admin / Komite R&D yang dapat mengubah status dan memberikan instruksi evaluasi keputusan alur produk ini.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection