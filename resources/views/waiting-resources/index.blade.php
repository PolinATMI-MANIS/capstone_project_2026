@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom border-secondary border-opacity-10">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <a href="{{ route('waiting-resources.index') }}" class="text-decoration-none fw-bold text-dark pb-1 border-bottom border-2 border-danger" style="font-size: 0.9rem;">
                    Waiting for Resources
                </a>
                <span class="text-muted">/</span>
                <a href="{{ route('man-power.index') }}" class="text-decoration-none fw-medium text-muted" style="font-size: 0.9rem;">
                    Man Power
                </a>
                <span class="text-muted">/</span>
                <a href="{{ route('machine-power.index') }}" class="text-decoration-none fw-medium text-muted" style="font-size: 0.9rem;">
                    Machine Power
                </a>
            </div>
            <h2 class="fw-bold text-dark mb-0" style="font-size: 1.6rem;">Production Queue / Waiting for Resources</h2>
        </div>
    </div>

    <div class="row">
        @forelse($waitingList as $item)
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-warning bg-opacity-25 text-dark font-monospace px-2 py-1" style="font-size: 0.7rem;">#{{ $item->production_code }}</span>
                            <span class="badge bg-primary bg-opacity-15 text-primary" style="font-size: 0.65rem;">{{ $item->status }}</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.1rem;">{{ $item->product_name }}</h5>
                        <p class="text-muted small mb-2">Kuantitas: <b class="text-dark">{{ $item->quantity }} Pcs</b></p>
                        
                        @if($item->notes)
                            <div class="p-2.5 rounded-3 bg-light text-muted small fst-italic mb-3" style="font-size: 0.75rem;">
                                "{{ $item->notes }}"
                            </div>
                        @endif
                    </div>

                    <div class="pt-3 border-top mt-2">
                        <button class="btn btn-sm text-white fw-bold w-100 py-2 shadow-sm" style="background-color: #ff6600; border-radius: 8px; font-size: 0.75rem;">
                            <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Alokasikan Sumber Daya
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-4 shadow-sm border border-secondary border-opacity-10">
                    <i class="fa-solid fa-clipboard-list text-muted fs-1 mb-3 opacity-50"></i>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Antrean Produksi</h6>
                    <p class="text-muted small mb-0">Antrean dari modul produksi temanmu akan muncul otomatis di sini.</p>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection