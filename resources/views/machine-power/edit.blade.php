@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('machine-power.index') }}" class="btn btn-light text-secondary shadow-sm me-3 rounded-circle" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="fw-bold text-dark mb-0" style="font-size: 1.4rem;">Edit Machine</h2>
                <p class="text-muted small mb-0">Perbarui spesifikasi atau status data mesin.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <form action="{{ route('machine-power.update', $machinePower->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="machine_name" class="form-label fw-semibold small text-muted text-uppercase">Machine Name</label>
                    <input type="text" class="form-control @error('machine_name') is-invalid @enderror" id="machine_name" name="machine_name" value="{{ old('machine_name', $machinePower->machine_name) }}" required>
                    @error('machine_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="machine_type" class="form-label fw-semibold small text-muted text-uppercase">Machine Type</label>
                    <input type="text" class="form-control @error('machine_type') is-invalid @enderror" id="machine_type" name="machine_type" value="{{ old('machine_type', $machinePower->machine_type) }}" required>
                    @error('machine_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label fw-semibold small text-muted text-uppercase">Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $machinePower->location) }}" required>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="capacity" class="form-label fw-semibold small text-muted text-uppercase">Capacity</label>
                        <input type="text" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', $machinePower->capacity) }}" required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold small text-muted text-uppercase">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="Standby" {{ old('status', $machinePower->status) == 'Standby' ? 'selected' : '' }}>Standby</option>
                        <option value="Running" {{ old('status', $machinePower->status) == 'Running' ? 'selected' : '' }}>Running</option>
                        <option value="Breakdown" {{ old('status', $machinePower->status) == 'Breakdown' ? 'selected' : '' }}>Breakdown</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="foto" class="form-label fw-semibold small text-muted text-uppercase">Machine Photo</label>
                    @if($machinePower->foto)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $machinePower->foto) }}" alt="Current Photo" class="rounded shadow-sm" width="80" height="80" style="object-fit: cover;">
                        </div>
                    @endif
                    <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                    <div class="form-text text-muted small">Biarkan kosong jika tidak ingin mengubah foto.</div>
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('machine-power.index') }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn text-dark fw-bold px-4" style="background-color: #ffcc00; border-radius: 8px;">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection