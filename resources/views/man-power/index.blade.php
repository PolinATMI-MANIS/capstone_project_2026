@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom border-secondary border-opacity-10">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <a href="{{ route('waiting-resources.index') }}" class="text-decoration-none fw-medium text-muted" style="font-size: 0.9rem;">
                    Waiting for Resources
                </a>
                <span class="text-muted">/</span>
                <a href="{{ route('man-power.index') }}" class="text-decoration-none fw-bold text-dark pb-1 border-bottom border-2 border-danger" style="font-size: 0.9rem;">
                    Man Power
                </a>
                <span class="text-muted">/</span>
                <a href="{{ Route::has('machine-power.index') ? route('machine-power.index') : '#' }}" class="text-decoration-none fw-medium text-muted" style="font-size: 0.9rem;">
                    Machine Power
                </a>
            </div>
            <h2 class="fw-bold text-dark mb-0" style="font-size: 1.6rem;">Man Power Allocation Dashboard</h2>
        </div>

        {{-- Tombol Add dimunculkan untuk SEMUA role (User akan masuk approval) --}}
        <a href="{{ route('man-power.create') }}" class="btn fw-bold text-white px-3 py-2 shadow-sm" style="background-color: #ff6600; border: none; border-radius: 8px;">
            <i class="fa-solid fa-plus me-1"></i> Add Man Power
        </a>
    </div>

    <div class="row">
        
        <!-- KOLOM KIRI: DIREKTORI PEKERJA -->
        <div class="col-lg-8 border-end border-secondary border-opacity-10 pe-lg-4">
            <h5 class="fw-bold text-dark mb-3" style="font-size: 1rem;"><i class="fa-solid fa-users text-warning me-2"></i> Available / Idle Man Power</h5>
            
            <div style="perspective: 1000px;">
                <div class="row" id="directoryList">
                    @forelse ($manPowers as $item)
                        @php 
                            $statusClean = trim(ucfirst(strtolower($item->status ?? '')));
                        @endphp

                        <div class="col-md-6 mb-4 worker-card-item" id="worker-{{ $item->id }}" style="display: {{ ($statusClean === 'Kerja' || $statusClean === 'Cuti') ? 'none' : 'block' }};">
                            
                            <div style="position: relative; width: 100%; margin-top: 25px;">
                                
                                {{-- Tombol Drag Me (Hanya Admin & Super Admin) --}}
                                @if(auth()->check() && auth()->user()->role !== 'user')
                                <div draggable="true" 
                                     ondragstart="handleDragStart(event)" 
                                     data-id="{{ $item->id }}"
                                     data-nama="{{ $item->nama }}"
                                     data-posisi="{{ $item->posisi }}"
                                     class="position-absolute start-50 translate-middle-x text-white px-3 py-1 rounded-pill shadow-sm fw-bold font-monospace d-flex align-items-center gap-1"
                                     style="top: -15px; z-index: 10; font-size: 0.65rem; cursor: grab; background-color: #ff6600; border: 2px solid #ffffff; user-select: none;">
                                    <i class="fa-solid fa-grip-lines"></i> DRAG ME
                                </div>
                                @endif

                                <div style="position: relative; width: 100%; height: 330px; transition: transform 0.6s; transform-style: preserve-3d;" 
                                     class="card-flipper shadow-sm" 
                                     onmouseover="this.style.transform='rotateY(180deg)'" 
                                     onmouseout="this.style.transform='rotateY(0deg)'">
                                    
                                    <div class="card border-0 h-100 p-3 pt-4 d-flex flex-column justify-content-between bg-white" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; border-radius: 14px;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.6rem;">
                                                #{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <span class="text-uppercase fw-bold text-muted" style="font-size: 0.55rem; letter-spacing: 1.5px;">CAPSTONE ID</span>
                                        </div>

                                        <div class="text-center my-auto">
                                            @if(!empty($item->foto))
                                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}" class="rounded-circle shadow-sm mx-auto mb-2" style="width: 75px; height: 75px; object-fit: cover; border: 3px solid #ff6600;">
                                            @else
                                                <div class="rounded-circle bg-light text-warning fw-bold d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 75px; height: 75px; font-size: 1.8rem; border: 3px solid #ff6600;">
                                                    {{ strtoupper(substr($item->nama, 0, 1)) }}
                                                </div>
                                            @endif
                                            <h6 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">{{ $item->nama }}</h6>
                                            <p class="text-uppercase fw-semibold text-muted mb-0" style="font-size: 0.6rem; letter-spacing: 1px;">
                                                {{ $item->posisi }}
                                            </p>
                                        </div>

                                        <div class="text-center pt-2 border-top d-flex justify-content-end align-items-center">
                                            <span class="text-muted opacity-75" style="font-size: 0.55rem;">
                                                <i class="fa-solid fa-rotate text-warning me-1"></i> Hover detail
                                            </span>
                                        </div>
                                    </div>

                                    <div class="card border-0 h-100 p-3 pt-4 d-flex flex-column justify-content-between text-white" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; transform: rotateY(180deg); border-radius: 14px; background-color: #ff6600;">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white border-opacity-25">
                                                <span class="text-uppercase text-white fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">PROFILE</span>
                                                <span class="badge bg-dark bg-opacity-25 text-white font-monospace" style="font-size: 0.6rem;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                            </div>

                                            <div class="row align-items-center mt-3">
                                                <div class="col-7">
                                                    <div class="mb-2">
                                                        <span class="text-white text-opacity-75 d-block" style="font-size: 0.5rem; text-transform: uppercase;">NAMA</span>
                                                        <h6 class="fw-bold text-white mb-1" style="font-size: 0.95rem;">{{ $item->nama }}</h6>
                                                    </div>

                                                    <div class="mb-2">
                                                        <span class="text-white text-opacity-75 d-block" style="font-size: 0.5rem; text-transform: uppercase;">POSISI</span>
                                                        <span class="fw-semibold text-white small" style="font-size: 0.75rem;">{{ $item->posisi }}</span>
                                                    </div>
                                                </div>

                                                <div class="col-5 text-center">
                                                    @if(!empty($item->foto))
                                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}" class="rounded-4 shadow-sm" style="width: 95px; height: 110px; object-fit: cover; border: 3px solid rgba(255, 255, 255, 0.6);">
                                                    @else
                                                        <div class="rounded-4 bg-dark text-warning fw-bold d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 95px; height: 110px; font-size: 2.2rem; border: 3px solid rgba(255, 255, 255, 0.6);">
                                                            {{ strtoupper(substr($item->nama, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top border-white border-opacity-25">
                                            @if(auth()->check())
                                                <a href="{{ route('man-power.edit', $item->id) }}" class="btn btn-sm btn-light text-primary fw-semibold px-2 py-1 shadow-sm" style="font-size: 0.65rem;">Edit</a>
                                            @else
                                                <span></span>
                                            @endif

                                            @if(auth()->check() && auth()->user()->role === 'super_admin')
                                                <form action="{{ route('man-power.destroy', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light text-danger fw-semibold px-2 py-1 border-0 shadow-sm" style="font-size: 0.65rem;" onclick="return confirm('Hapus data secara permanen?')">Delete</button>
                                                </form>
                                            @elseif(auth()->check() && auth()->user()->role === 'admin')
                                                <form action="{{ route('man-power.destroy', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light text-warning fw-semibold px-2 py-1 border-0 shadow-sm" style="font-size: 0.65rem;" onclick="return confirm('Kirim permintaan hapus ke Super Admin?')">Req Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                    <div class="col-12 text-center py-4">
                        <p class="text-muted small">Tidak ada data man power.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: PANEL KONTROL -->
        <div class="col-lg-4 ps-lg-4 mt-4 mt-lg-0">
            <div class="sticky-top d-flex flex-column gap-4" style="top: 20px;">
                
                <!-- CARD 1: WAITING FOR RESOURCES -->
                <div class="p-4 rounded-4 bg-white shadow-sm border border-secondary border-opacity-25">
                    @php
                        // PERBAIKAN: Menarik data SPK langsung dari tabel Produksi Anda
                        $waitingResources = class_exists('\App\Models\ProductionOrder') 
                            ? \App\Models\ProductionOrder::whereIn('status', ['Menunggu Bahan Baku', 'Proses Produksi Berjalan'])->latest()->take(5)->get() 
                            : collect();
                    @endphp

                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1rem;"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> WAITING FOR RESOURCES</h5>
                        <span class="badge bg-primary text-white px-2 py-1" id="waitingResourcesCount">{{ $waitingResources->count() }} Queue</span>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.75rem;">Daftar produksi masuk yang menanti alokasi sumber daya.</p>

                    <div id="waitingResourcesList" class="d-flex flex-column gap-2" style="max-height: 250px; overflow-y: auto;">
                        @forelse($waitingResources as $res)
                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="badge bg-warning bg-opacity-25 text-dark font-monospace mb-1" style="font-size: 0.55rem;">#{{ $res->no_po }}</span>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.8rem;">{{ $res->produk }}</h6>
                                    <span class="text-muted" style="font-size: 0.65rem;">Qty: <b>{{ $res->jumlah_produksi }} Pcs</b></span>
                                </div>
                                <a href="{{ route('waiting-resources.index') }}" class="btn btn-sm btn-outline-primary py-1 px-2 fw-bold" style="font-size: 0.65rem;">Alokasikan</a>
                            </div>
                        @empty
                            <div class="text-center py-2 text-muted opacity-50" style="font-size: 0.75rem;">
                                Belum ada antrean produksi.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- CARD 2: WORK ZONE -->
                <div class="p-4 rounded-4 bg-white shadow-sm border border-2 border-dashed" 
                     id="workZone"
                     ondragover="handleDragOver(event, 'workZone')"
                     ondragleave="handleDragLeave(event, 'workZone')"
                     ondrop="handleDrop(event, 'Kerja')"
                     style="background-color: #fffdf5 !important; border-color: #ff6600 !important; transition: 0.3s;">
                    
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;"><i class="fa-solid fa-industry text-warning me-2"></i> WORK ZONE</h5>
                        <span class="badge text-white px-2 py-1" id="workCount" style="background-color: #ff6600;">0 Active</span>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.75rem;">Drop di sini untuk status <b>Kerja</b>.</p>

                    <div id="workList" class="d-flex flex-column gap-2">
                        @php $hasWorking = false; @endphp
                        @foreach ($manPowers as $item)
                            @php $statusClean = trim(ucfirst(strtolower($item->status ?? ''))); @endphp
                            @if($statusClean == 'Kerja')
                                @php $hasWorking = true; @endphp
                                <div id="assigned-{{ $item->id }}" class="p-2.5 bg-white rounded-3 shadow-sm border d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge text-white font-monospace mb-1" style="font-size: 0.55rem; background-color: #ff6600;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">{{ $item->nama }}</h6>
                                        <span class="text-muted" style="font-size: 0.65rem;">{{ $item->posisi }}</span>
                                    </div>
                                    @if(auth()->check() && auth()->user()->role !== 'user')
                                    <button onclick="returnWorker('{{ $item->id }}')" class="btn btn-sm text-white fw-bold px-2 py-1" title="Kembalikan ke Idle" style="font-size: 0.6rem; background-color: #0b192c;">
                                        <i class="fa-solid fa-rotate-left"></i> Return
                                    </button>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        <div id="workPlaceholder" class="text-center py-3 text-muted opacity-50" style="display: {{ $hasWorking ? 'none' : 'block' }}; font-size: 0.8rem;">
                            <p class="small mb-0">Kosong (Drop pekerja ke sini)</p>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: CUTI ZONE -->
                <div class="p-4 rounded-4 bg-white shadow-sm border border-2 border-dashed border-secondary" 
                     id="cutiZone"
                     ondragover="handleDragOver(event, 'cutiZone')"
                     ondragleave="handleDragLeave(event, 'cutiZone')"
                     ondrop="handleDrop(event, 'Cuti')"
                     style="background-color: #f8fafc !important; transition: 0.3s;">
                    
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;"><i class="fa-solid fa-umbrella-beach text-secondary me-2"></i> CUTI ZONE</h5>
                        <span class="badge bg-secondary text-white px-2 py-1" id="cutiCount">0 Cuti</span>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.75rem;">Drop di sini untuk status <b>Cuti</b>.</p>

                    <div id="cutiList" class="d-flex flex-column gap-2">
                        @php $hasCuti = false; @endphp
                        @foreach ($manPowers as $item)
                            @php $statusClean = trim(ucfirst(strtolower($item->status ?? ''))); @endphp
                            @if($statusClean == 'Cuti')
                                @php $hasCuti = true; @endphp
                                <div id="assigned-{{ $item->id }}" class="p-2.5 bg-white rounded-3 shadow-sm border d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-secondary text-white font-monospace mb-1" style="font-size: 0.55rem;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">{{ $item->nama }}</h6>
                                        <span class="text-muted" style="font-size: 0.65rem;">{{ $item->posisi }}</span>
                                    </div>
                                    @if(auth()->check() && auth()->user()->role !== 'user')
                                    <button onclick="returnWorker('{{ $item->id }}')" class="btn btn-sm text-white fw-bold px-2 py-1" title="Kembalikan ke Idle" style="font-size: 0.6rem; background-color: #0b192c;">
                                        <i class="fa-solid fa-rotate-left"></i> Return
                                    </button>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        <div id="cutiPlaceholder" class="text-center py-3 text-muted opacity-50" style="display: {{ $hasCuti ? 'none' : 'block' }}; font-size: 0.8rem;">
                            <p class="small mb-0">Kosong (Drop pekerja cuti ke sini)</p>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: PINTU DELETE -->
                @if(auth()->check() && auth()->user()->role !== 'user')
                <div class="px-3 py-3 rounded-pill shadow-sm border border-2 border-danger text-center d-flex align-items-center justify-content-center gap-2" 
                     id="deleteZone"
                     ondragover="handleDeleteDragOver(event)"
                     ondragleave="handleDeleteDragLeave(event)"
                     ondrop="handleDeleteDrop(event)"
                     style="background-color: #fff5f5 !important; transition: 0.3s; cursor: pointer;">
                    <div class="text-danger d-flex align-items-center">
                        <i class="fa-solid fa-door-open fs-5"></i>
                    </div>
                    <span class="fw-bold text-danger text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.3px;">
                        {{ auth()->user()->role === 'super_admin' ? 'DROP DI SINI UNTUK HAPUS' : 'DROP DI SINI UNTUK REQ HAPUS' }}
                    </span>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        updateCounts();
    });

    let draggedData = null;

    function handleDragStart(e) {
        let el = e.currentTarget;
        draggedData = {
            id: el.getAttribute('data-id'),
            nama: el.getAttribute('data-nama'),
            posisi: el.getAttribute('data-posisi')
        };
        e.dataTransfer.setData('text/plain', JSON.stringify(draggedData));
    }

    function handleDragOver(e, zoneId) {
        e.preventDefault();
        if(zoneId === 'workZone') document.getElementById('workZone').style.backgroundColor = '#ffe5d0';
        if(zoneId === 'cutiZone') document.getElementById('cutiZone').style.backgroundColor = '#e2e8f0';
    }

    function handleDragLeave(e, zoneId) {
        if(zoneId === 'workZone') document.getElementById('workZone').style.backgroundColor = '#fffdf5';
        if(zoneId === 'cutiZone') document.getElementById('cutiZone').style.backgroundColor = '#f8fafc';
    }

    function handleDrop(e, targetStatus) {
        e.preventDefault();
        document.getElementById('workZone').style.backgroundColor = '#fffdf5';
        document.getElementById('cutiZone').style.backgroundColor = '#f8fafc';

        let rawData = e.dataTransfer.getData('text/plain');
        if (!rawData) return;
        let worker = JSON.parse(rawData);

        if (document.getElementById('assigned-' + worker.id)) return;

        updateWorkerStatusInDatabase(worker.id, targetStatus, function() {
            let leftCard = document.getElementById('worker-' + worker.id);
            if (leftCard) leftCard.style.display = 'none';

            let containerId = targetStatus === 'Kerja' ? 'workList' : 'cutiList';
            let placeholderId = targetStatus === 'Kerja' ? 'workPlaceholder' : 'cutiPlaceholder';

            let placeholder = document.getElementById(placeholderId);
            if (placeholder) placeholder.style.display = 'none';

            let targetList = document.getElementById(containerId);
            let cardItem = document.createElement('div');
            cardItem.id = 'assigned-' + worker.id;
            cardItem.className = 'p-2.5 bg-white rounded-3 shadow-sm border d-flex justify-content-between align-items-center';
            cardItem.innerHTML = `
                <div>
                    <span class="badge font-monospace mb-1" style="font-size: 0.55rem; background-color: ${targetStatus === 'Kerja' ? '#ff6600' : '#64748b'}; color: white;">#${worker.id.toString().padStart(3, '0')}</span>
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">${worker.nama}</h6>
                    <span class="text-muted" style="font-size: 0.65rem;">${worker.posisi}</span>
                </div>
                <button onclick="returnWorker('${worker.id}')" class="btn btn-sm text-white fw-bold px-2 py-1" style="font-size: 0.6rem; background-color: #0b192c;">
                    <i class="fa-solid fa-rotate-left"></i> Return
                </button>
            `;
            targetList.appendChild(cardItem);
            updateCounts();
        });
    }

    function returnWorker(id) {
        updateWorkerStatusInDatabase(id, 'Idle', function() {
            let assignedItem = document.getElementById('assigned-' + id);
            if (assignedItem) assignedItem.remove();

            let leftCard = document.getElementById('worker-' + id);
            if (leftCard) leftCard.style.display = 'block';

            checkPlaceholders();
            updateCounts();
        });
    }

    function updateWorkerStatusInDatabase(id, newStatus, callback) {
        fetch(`/man-power/${id}/update-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                callback();
            } else {
                alert('Gagal memperbarui status ke database.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function handleDeleteDragOver(e) {
        e.preventDefault();
        e.currentTarget.style.backgroundColor = '#fecaca';
        e.currentTarget.style.transform = 'scale(1.02)';
    }

    function handleDeleteDragLeave(e) {
        e.currentTarget.style.backgroundColor = '#fff5f5';
        e.currentTarget.style.transform = 'scale(1)';
    }

    function handleDeleteDrop(e) {
        e.preventDefault();
        e.currentTarget.style.backgroundColor = '#fff5f5';
        e.currentTarget.style.transform = 'scale(1)';

        let rawData = e.dataTransfer.getData('text/plain');
        if (!rawData) return;
        let worker = JSON.parse(rawData);

        if (confirm(`Yakin memproses hapus data pekerja ${worker.nama}?`)) {
            fetch(`/man-power/${worker.id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ _method: 'DELETE' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus data pekerja.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    function checkPlaceholders() {
        let workList = document.getElementById('workList');
        if (workList.querySelectorAll('div[id^="assigned-"]').length === 0) {
            document.getElementById('workPlaceholder').style.display = 'block';
        }

        let cutiList = document.getElementById('cutiList');
        if (cutiList.querySelectorAll('div[id^="assigned-"]').length === 0) {
            document.getElementById('cutiPlaceholder').style.display = 'block';
        }
    }

    function updateCounts() {
        let workCount = document.getElementById('workList').querySelectorAll('div[id^="assigned-"]').length;
        document.getElementById('workCount').innerText = workCount + ' Active';

        let cutiCount = document.getElementById('cutiList').querySelectorAll('div[id^="assigned-"]').length;
        document.getElementById('cutiCount').innerText = cutiCount + ' Cuti';
    }
</script>
@endsection