@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 900px;">
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-folder-plus text-warning me-2 fs-5"></i>
                <h5 class="fw-bold m-0" style="color: #0f172a;">Form Pengajuan Konsep Produk Baru</h5>
            </div>
        </div>
        <div class="card-body p-4">
            
            <!-- Tambahkan attribute novalidate dan class needs-validation -->
            <form action="{{ route('rnd.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf

                <!-- NAMA PRODUK -->
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">
                        NAMA PRODUK <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="nama_produk" 
                           class="form-control @error('nama_produk') is-invalid @enderror" 
                           placeholder="Contoh: Sensor" 
                           value="{{ old('nama_produk') }}"
                           required 
                           style="border-radius: 8px;">
                    <!-- Pesan teks merah kecil di bawah kolom -->
                    <div class="invalid-feedback" style="font-size: 0.8rem;">
                        @error('nama_produk') {{ $message }} @else Nama produk wajib diisi! @enderror
                    </div>
                </div>

                <!-- NAMA PENGAJU -->
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">
                        NAMA PENGAJU / TIM R&D <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="nama_pengaju" 
                           class="form-control @error('nama_pengaju') is-invalid @enderror" 
                           placeholder="Contoh: Alexsander" 
                           value="{{ old('nama_pengaju') }}"
                           required 
                           style="border-radius: 8px;">
                    <!-- Pesan teks merah kecil di bawah kolom -->
                    <div class="invalid-feedback" style="font-size: 0.8rem;">
                        @error('nama_pengaju') {{ $message }} @else Nama pengaju wajib diisi! @enderror
                    </div>
                </div>

                <!-- DESKRIPSI KONSEP -->
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">
                        DESKRIPSI KONSEP <span class="text-danger">*</span>
                    </label>
                    <textarea name="deskripsi_konsep" 
                              rows="4" 
                              class="form-control @error('deskripsi_konsep') is-invalid @enderror" 
                              placeholder="Jelaskan detail konsep dan spesifikasi awal produk..." 
                              required 
                              style="border-radius: 8px;">{{ old('deskripsi_konsep') }}</textarea>
                    <!-- Pesan teks merah kecil di bawah kolom -->
                    <div class="invalid-feedback" style="font-size: 0.8rem;">
                        @error('deskripsi_konsep') {{ $message }} @else Deskripsi konsep wajib diisi! @enderror
                    </div>
                </div>

                <!-- GAMBAR KONSEP -->
                <div class="mb-4">
                    <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">
                        GAMBAR KONSEP / SKETSA (OPSIONAL)
                    </label>
                    <input type="file" 
                           name="gambar" 
                           class="form-control @error('gambar') is-invalid @enderror" 
                           accept="image/*"
                           style="border-radius: 8px;">
                    <small class="text-muted" style="font-size: 0.75rem;">Format: JPG, JPEG, PNG, WEBP (Maks: 2MB)</small>
                    @error('gambar')
                        <div class="invalid-feedback d-block" style="font-size: 0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- BUTTONS -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-machine px-4 py-2" style="background-color: #f97316; color: #fff; font-weight: 600; border-radius: 8px; border: none;">
                        <i class="fa-solid fa-paper-plane me-1"></i> Simpan Pengajuan
                    </button>
                    <a href="{{ route('rnd.index') }}" class="btn btn-light px-4 py-2" style="border-radius: 8px; color: #475569; font-weight: 500;">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Script Bootstrap untuk trigger tampilan teks merah jika ada kolom kosong -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>
@endsection