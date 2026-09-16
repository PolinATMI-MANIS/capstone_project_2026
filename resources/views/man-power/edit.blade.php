@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('man-power.index') }}" class="btn btn-light text-secondary shadow-sm me-3 rounded-circle" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="fw-bold text-dark mb-0" style="font-size: 1.4rem;">Edit Man Power Record</h2>
                <p class="text-muted small mb-0">Perbarui informasi atau foto profil pekerja.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <!-- Diperbaiki: Menggunakan route man-power.update dan method PUT -->
            <form action="{{ route('man-power.update', $manPower->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama" class="form-label fw-semibold small text-muted text-uppercase">Full Name</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $manPower->nama) }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="posisi" class="form-label fw-semibold small text-muted text-uppercase">Position / Role</label>
                    <input type="text" class="form-control @error('posisi') is-invalid @enderror" id="posisi" name="posisi" value="{{ old('posisi', $manPower->posisi) }}" required>
                    @error('posisi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold small text-muted text-uppercase">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="Kerja" {{ old('status', $manPower->status) == 'Kerja' ? 'selected' : '' }}>Kerja</option>
                        <option value="Idle" {{ old('status', $manPower->status) == 'Idle' ? 'selected' : '' }}>Idle</option>
                        <option value="Cuti" {{ old('status', $manPower->status) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="foto" class="form-label fw-semibold small text-muted text-uppercase">Profile Photo</label>
                    @if($manPower->foto)
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <img src="{{ asset('storage/' . $manPower->foto) }}" alt="Current Photo" class="rounded-circle shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                            <span class="text-muted small">Foto saat ini aktif</span>
                        </div>
                    @endif
                    <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                    <div class="form-text text-muted small">Biarkan kosong jika tidak ingin mengubah foto.</div>
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('man-power.index') }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn text-dark fw-bold px-4" style="background-color: #ffcc00; border-radius: 8px;">Update Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection