@extends('layouts.app')
@section('content')
<div class="container-fluid px-4 py-4 text-center py-5">
    <div class="card border-0 shadow-sm p-5 mx-auto" style="max-width: 600px;">
        <i class="fa-solid fa-triangle-exclamation text-warning fs-1 mb-3"></i>
        <h4 class="fw-bold">Kapasitas Gudang Tidak Tersedia</h4>
        <p class="text-muted">Sistem mendeteksi kapasitas penuh. Silakan pindah lokasi atau tunggu approval lanjutan.</p>
        <a href="{{ route('inventory.po.index') }}" class="btn btn-secondary mt-3">Kembali ke Dashboard PO</a>
    </div>
</div>
@endsection