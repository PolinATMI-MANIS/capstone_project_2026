@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom border-secondary border-opacity-10">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <a href="{{ route('waiting-resources.index') }}" class="text-decoration-none fw-medium text-muted" style="font-size: 0.9rem;">Waiting for Resources</a>
                <span class="text-muted">/</span>
                <a href="{{ route('man-power.index') }}" class="text-decoration-none fw-bold text-dark pb-1 border-bottom border-2 border-danger" style="font-size: 0.9rem;">Man Power</a>
                <span class="text-muted">/</span>
                <a href="{{ Route::has('machine-power.index') ? route('machine-power.index') : '#' }}" class="text-decoration-none fw-medium text-muted" style="font-size: 0.9rem;">Machine Power</a>
            </div>
            <h2 class="fw-bold text-dark mb-0" style="font-size: 1.6rem;">Man Power Allocation Dashboard</h2>
        </div>
        <a href="{{ route('man-power.create') }}" class="btn fw-bold text-white px-3 py-2 shadow-sm" style="background-color: #ff6600; border: none; border-radius: 8px;">
            <i class="fa-solid fa-plus me-1"></i> Add Man Power
        </a>
    </div>

    <div class="row">
        
        <!-- KOLOM KIRI: DIREKTORI PEKERJA IDLE -->
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
                                @if(auth()->check() && auth()->user()->role !== 'user')
                                <div draggable="true" ondragstart="handleDragStart(event)" data-id="{{ $item->id }}" data-nama="{{ $item->nama }}" data-posisi="{{ $item->posisi }}" class="position-absolute start-50 translate-middle-x text-white px-3 py-1 rounded-pill shadow-sm fw-bold font-monospace d-flex align-items-center gap-1" style="top: -15px; z-index: 10; font-size: 0.65rem; cursor: grab; background-color: #ff6600; border: 2px solid #ffffff; user-select: none;">
                                    <i class="fa-solid fa-grip-lines"></i> DRAG ME
                                </div>
                                @endif
                                <div style="position: relative; width: 100%; height: 330px; transition: transform 0.6s; transform-style: preserve-3d;" class="card-flipper shadow-sm" onmouseover="this.style.transform='rotateY(180deg)'" onmouseout="this.style.transform='rotateY(0deg)'">
                                    <!-- FRONT CARD -->
                                    <div class="card border-0 h-100 p-3 pt-4 d-flex flex-column justify-content-between bg-white" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; border-radius: 14px;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.6rem;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                            <span class="text-uppercase fw-bold text-muted" style="font-size: 0.55rem; letter-spacing: 1.5px;">CAPSTONE ID</span>
                                        </div>
                                        <div class="text-center my-auto">
                                            @if(!empty($item->foto))
                                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}" class="rounded-circle shadow-sm mx-auto mb-2" style="width: 75px; height: 75px; object-fit: cover; border: 3px solid #ff6600;">
                                            @else
                                                <div class="rounded-circle bg-light text-warning fw-bold d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 75px; height: 75px; font-size: 1.8rem; border: 3px solid #ff6600;">{{ strtoupper(substr($item->nama, 0, 1)) }}</div>
                                            @endif
                                            <h6 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">{{ $item->nama }}</h6>
                                            <p class="text-uppercase fw-semibold text-muted mb-0" style="font-size: 0.6rem; letter-spacing: 1px;">{{ $item->posisi }}</p>
                                        </div>
                                        <div class="text-center pt-2 border-top d-flex justify-content-end align-items-center">
                                            <span class="text-muted opacity-75" style="font-size: 0.55rem;"><i class="fa-solid fa-rotate text-warning me-1"></i> Hover detail</span>
                                        </div>
                                    </div>
                                    <!-- BACK CARD -->
                                    <div class="card border-0 h-100 p-3 pt-4 d-flex flex-column justify-content-between text-white" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; transform: rotateY(180deg); border-radius: 14px; background-color: #ff6600;">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white border-opacity-25">
                                                <span class="text-uppercase text-white fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">PROFILE</span>
                                                <span class="badge bg-dark bg-opacity-25 text-white font-monospace" style="font-size: 0.6rem;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                            <div class="row align-items-center mt-3">
                                                <div class="col-7">
                                                    <div class="mb-2"><span class="text-white text-opacity-75 d-block" style="font-size: 0.5rem; text-transform: uppercase;">NAMA</span><h6 class="fw-bold text-white mb-1" style="font-size: 0.95rem;">{{ $item->nama }}</h6></div>
                                                    <div class="mb-2"><span class="text-white text-opacity-75 d-block" style="font-size: 0.5rem; text-transform: uppercase;">POSISI</span><span class="fw-semibold text-white small" style="font-size: 0.75rem;">{{ $item->posisi }}</span></div>
                                                </div>
                                                <div class="col-5 text-center">
                                                    @if(!empty($item->foto))
                                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}" class="rounded-4 shadow-sm" style="width: 95px; height: 110px; object-fit: cover; border: 3px solid rgba(255, 255, 255, 0.6);">
                                                    @else
                                                        <div class="rounded-4 bg-dark text-warning fw-bold d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 95px; height: 110px; font-size: 2.2rem; border: 3px solid rgba(255, 255, 255, 0.6);">{{ strtoupper(substr($item->nama, 0, 1)) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top border-white border-opacity-25">
                                            @if(auth()->check()) <a href="{{ route('man-power.edit', $item->id) }}" class="btn btn-sm btn-light text-primary fw-semibold px-2 py-1 shadow-sm" style="font-size: 0.65rem;">Edit</a> @endif
                                            @if(auth()->check() && auth()->user()->role === 'super_admin')
                                                <form action="{{ route('man-power.destroy', $item->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-light text-danger fw-semibold px-2 py-1 border-0 shadow-sm" style="font-size: 0.65rem;" onclick="return confirm('Hapus permanen?')">Delete</button></form>
                                            @elseif(auth()->check() && auth()->user()->role === 'admin')
                                                <form action="{{ route('man-power.destroy', $item->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-light text-warning fw-semibold px-2 py-1 border-0 shadow-sm" style="font-size: 0.65rem;" onclick="return confirm('Req hapus?')">Req Delete</button></form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                    <div class="col-12 text-center py-4"><p class="text-muted small">Tidak ada data man power.</p></div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: 3 CARDS CONTROL PANEL -->
        <div class="col-lg-4 ps-lg-4 mt-4 mt-lg-0">
            <div class="sticky-top d-flex flex-column gap-4" style="top: 20px;">
                
                @php
                    $readyProductions = class_exists('\App\Models\ProductionOrder') ? \App\Models\ProductionOrder::where('status', 'ready')->get() : collect();
                    
                    $activeProdIds = [];
                    foreach($manPowers as $mp) {
                        if (trim(ucfirst(strtolower($mp->status ?? ''))) === 'Kerja' && !empty($mp->production_order_id)) {
                            $activeProdIds[] = $mp->production_order_id;
                        }
                    }
                    $activeProdIds = array_unique($activeProdIds);

                    $idleProductions = $readyProductions->filter(fn($p) => !in_array($p->id, $activeProdIds));
                    $activeProductions = $readyProductions->filter(fn($p) => in_array($p->id, $activeProdIds));
                    
                    $generalWorkers = $manPowers->filter(fn($w) => trim(ucfirst(strtolower($w->status ?? ''))) === 'Kerja' && empty($w->production_order_id));
                @endphp

                <!-- CARD 1: PRODUCTION ASSIGNMENT (IDLE SPK) -->
                <div class="p-4 rounded-4 bg-white shadow-sm border border-secondary border-opacity-25" id="assignmentCard">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1rem;"><i class="fa-solid fa-list-check text-primary me-2"></i> ASSIGNMENT ZONE</h5>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.75rem;">Barang yang siap diproduksi. <b>Drop pekerja ke SPK untuk memindahkan SPK ke Work Zone.</b></p>

                    <div id="readySpkContainer" style="min-height: 50px;">
                        @foreach($idleProductions as $prod)
                            <div class="p-3 mb-3 rounded-4 bg-white shadow-sm border border-2 border-dashed spk-box" 
                                 id="spk-{{ $prod->id }}"
                                 ondragover="handleSpkDragOver(event, 'spk-{{ $prod->id }}')"
                                 ondragleave="handleSpkDragLeave(event, 'spk-{{ $prod->id }}')"
                                 ondrop="handleSpkDrop(event, '{{ $prod->id }}')"
                                 style="border-color: #0d6efd !important; transition: 0.3s; cursor: default;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-25">
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">{{ $prod->produk }}</h6>
                                    <span class="badge bg-dark bg-opacity-10 text-dark font-monospace" style="font-size: 0.55rem;">#{{ $prod->no_po }}</span>
                                </div>
                                <div class="mb-2"><span class="text-muted d-block" style="font-size: 0.6rem;">Target Qty: <b>{{ $prod->jumlah_produksi }} Pcs</b></span></div>
                                <div id="spk-workers-{{ $prod->id }}" class="d-flex flex-column gap-2 spk-workers-list">
                                    <div id="spk-ph-{{ $prod->id }}" class="text-center py-2 text-primary opacity-75 placeholder-worker" style="font-size: 0.7rem; background-color: #e7f1ff; border-radius: 6px;">
                                        <i class="fa-solid fa-arrow-down me-1"></i> Drop Man Power Here
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div id="emptySpkState" class="text-center py-4 border rounded-3 bg-light" style="display: {{ $idleProductions->count() > 0 ? 'none' : 'block' }}">
                            <i class="fa-solid fa-box-open text-muted mb-2 fs-4 opacity-50"></i>
                            <p class="text-muted small mb-0" style="font-size: 0.75rem;">Semua produksi sudah berjalan atau kosong.</p>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: WORK ZONE (ACTIVE SPK) -->
                <div class="p-4 rounded-4 bg-white shadow-sm border border-2 border-dashed" id="workZoneCard" style="border-color: #ff6600 !important; background-color: #fffdf5 !important; transition: 0.3s;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;"><i class="fa-solid fa-industry text-warning me-2"></i> WORK ZONE</h5>
                        <span class="badge text-white px-2 py-1" id="workCount" style="background-color: #ff6600;">Active</span>
                    </div>
                    
                    <div id="activeSpkContainer" class="d-flex flex-column mb-3" style="min-height: 60px;">
                        @foreach($activeProductions as $prod)
                            <div class="p-3 mb-3 rounded-4 shadow-sm border border-2 border-dashed spk-box" 
                                 id="spk-{{ $prod->id }}"
                                 ondragover="handleSpkDragOver(event, 'spk-{{ $prod->id }}')"
                                 ondragleave="handleSpkDragLeave(event, 'spk-{{ $prod->id }}')"
                                 ondrop="handleSpkDrop(event, '{{ $prod->id }}')"
                                 style="border-color: #ff6600 !important; background-color: #fffdf5 !important; transition: 0.3s; cursor: default;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-25">
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">{{ $prod->produk }}</h6>
                                    <span class="badge bg-dark bg-opacity-10 text-dark font-monospace" style="font-size: 0.55rem;">#{{ $prod->no_po }}</span>
                                </div>
                                <div class="mb-2"><span class="text-muted d-block" style="font-size: 0.6rem;">Target Qty: <b>{{ $prod->jumlah_produksi }} Pcs</b></span></div>
                                <div id="spk-workers-{{ $prod->id }}" class="d-flex flex-column gap-2 spk-workers-list">
                                    @php
                                        $workersInThisSpk = $manPowers->filter(fn($w) => trim(ucfirst(strtolower($w->status ?? ''))) === 'Kerja' && $w->production_order_id == $prod->id);
                                    @endphp
                                    @foreach($workersInThisSpk as $worker)
                                        <div id="spk-worker-{{ $worker->id }}" class="p-2 bg-light rounded-3 border border-primary border-opacity-25 d-flex justify-content-between align-items-center mt-1 active-spk-worker">
                                            <div>
                                                <span class="badge font-monospace mb-1" style="font-size: 0.5rem; background-color: #0d6efd; color: white;">#{{ str_pad($worker->id, 3, '0', STR_PAD_LEFT) }}</span>
                                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.75rem;">{{ $worker->nama }}</h6>
                                                <span class="text-muted" style="font-size: 0.6rem;">{{ $worker->posisi }}</span>
                                            </div>
                                            @if(auth()->check() && auth()->user()->role !== 'user')
                                            <button onclick="returnWorkerFromSpk('{{ $worker->id }}', '{{ $prod->id }}')" class="btn btn-sm text-white fw-bold px-2 py-1" style="font-size: 0.55rem; background-color: #0b192c;"><i class="fa-solid fa-rotate-left"></i> Return</button>
                                            @endif
                                        </div>
                                    @endforeach
                                    <div id="spk-ph-{{ $prod->id }}" class="text-center py-2 text-primary opacity-75 placeholder-worker" style="display: none; font-size: 0.7rem; background-color: #e7f1ff; border-radius: 6px;">
                                        <i class="fa-solid fa-arrow-down me-1"></i> Drop Man Power Here
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div id="activeSpkPlaceholder" class="text-center py-4 border border-warning border-opacity-25 rounded-3" style="display: {{ $activeProductions->count() > 0 ? 'none' : 'block' }}; background-color: #fffaf0;">
                            <i class="fa-solid fa-gears text-warning mb-2 fs-4 opacity-50"></i>
                            <p class="text-muted small mb-0" style="font-size: 0.75rem;">Belum ada produksi berjalan.</p>
                        </div>
                    </div>

                    <div class="p-3 border rounded-3" id="generalWorkZone" ondragover="handleGeneralDragOver(event)" ondragleave="handleGeneralDragLeave(event)" ondrop="handleGeneralDrop(event)" style="background-color: white;">
                        <h6 class="fw-bold text-dark mb-2" style="font-size: 0.75rem;">General Work (Tanpa SPK)</h6>
                        <div id="generalWorkList" class="d-flex flex-column gap-2">
                            @foreach ($generalWorkers as $item)
                                <div id="gen-assigned-{{ $item->id }}" class="p-2 bg-light rounded-3 shadow-sm border border-warning border-opacity-25 d-flex justify-content-between align-items-center gen-worker-item">
                                    <div><span class="badge text-white font-monospace mb-1" style="font-size: 0.5rem; background-color: #ff6600;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span><h6 class="fw-bold text-dark mb-0" style="font-size: 0.75rem;">{{ $item->nama }}</h6></div>
                                    @if(auth()->check() && auth()->user()->role !== 'user')
                                    <button onclick="returnGeneralWorker('{{ $item->id }}')" class="btn btn-sm text-white fw-bold px-1 py-1" style="font-size: 0.55rem; background-color: #0b192c;"><i class="fa-solid fa-rotate-left"></i></button>
                                    @endif
                                </div>
                            @endforeach
                            <div id="genWorkPlaceholder" class="text-center py-2 text-muted opacity-50 gen-placeholder" style="display: {{ $generalWorkers->count() > 0 ? 'none' : 'block' }}; font-size: 0.65rem;">
                                Drop ke sini untuk General Work
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: CUTI ZONE -->
                <div class="p-4 rounded-4 bg-white shadow-sm border border-2 border-dashed border-secondary" id="cutiZone" ondragover="handleDragOver(event, 'cutiZone')" ondragleave="handleDragLeave(event, 'cutiZone')" ondrop="handleCutiDrop(event)" style="background-color: #f8fafc !important; transition: 0.3s;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;"><i class="fa-solid fa-umbrella-beach text-secondary me-2"></i> CUTI ZONE</h5>
                        <span class="badge bg-secondary text-white px-2 py-1" id="cutiCount">0 Cuti</span>
                    </div>
                    
                    <div id="cutiList" class="d-flex flex-column gap-2">
                        @php $hasCuti = false; @endphp
                        @foreach ($manPowers as $item)
                            @if(trim(ucfirst(strtolower($item->status ?? ''))) === 'Cuti')
                                @php $hasCuti = true; @endphp
                                <div id="cuti-assigned-{{ $item->id }}" class="p-2.5 bg-white rounded-3 shadow-sm border d-flex justify-content-between align-items-center cuti-item">
                                    <div><span class="badge bg-secondary text-white font-monospace mb-1" style="font-size: 0.55rem;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span><h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">{{ $item->nama }}</h6><span class="text-muted" style="font-size: 0.65rem;">{{ $item->posisi }}</span></div>
                                    @if(auth()->check() && auth()->user()->role !== 'user')
                                    <button onclick="returnCutiWorker('{{ $item->id }}')" class="btn btn-sm text-white fw-bold px-2 py-1" style="font-size: 0.6rem; background-color: #0b192c;"><i class="fa-solid fa-rotate-left"></i> Return</button>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                        <div id="cutiPlaceholder" class="text-center py-3 text-muted opacity-50 cuti-ph" style="display: {{ $hasCuti ? 'none' : 'block' }}; font-size: 0.8rem;">Kosong (Drop pekerja cuti ke sini)</div>
                    </div>
                </div>

                @if(auth()->check() && auth()->user()->role !== 'user')
                <div class="px-3 py-3 rounded-pill shadow-sm border border-2 border-danger text-center d-flex align-items-center justify-content-center gap-2" id="deleteZone" ondragover="handleDeleteDragOver(event)" ondragleave="handleDeleteDragLeave(event)" ondrop="handleDeleteDrop(event)" style="background-color: #fff5f5 !important; transition: 0.3s; cursor: pointer;">
                    <div class="text-danger d-flex align-items-center"><i class="fa-solid fa-door-open fs-5"></i></div>
                    <span class="fw-bold text-danger text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.3px;">{{ auth()->user()->role === 'super_admin' ? 'DROP DI SINI UNTUK HAPUS' : 'DROP DI SINI UNTUK REQ HAPUS' }}</span>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        checkAllPlaceholders();
    });

    let draggedData = null;

    function handleDragStart(e) {
        draggedData = {
            id: e.currentTarget.getAttribute('data-id'),
            nama: e.currentTarget.getAttribute('data-nama'),
            posisi: e.currentTarget.getAttribute('data-posisi')
        };
        e.dataTransfer.setData('text/plain', JSON.stringify(draggedData));
    }

    // ==== SPK DROP ====
    function handleSpkDragOver(e, spkId) {
        e.preventDefault(); 
        document.getElementById(spkId).style.backgroundColor = '#e7f1ff'; 
        document.getElementById(spkId).style.transform = 'scale(1.02)';
    }
    function handleSpkDragLeave(e, spkId) {
        document.getElementById(spkId).style.backgroundColor = document.getElementById(spkId).parentElement.id === 'readySpkContainer' ? '#ffffff' : '#fffdf5';
        document.getElementById(spkId).style.transform = 'scale(1)';
    }

    function handleSpkDrop(e, prodId) {
        e.preventDefault();
        let spkBox = document.getElementById('spk-' + prodId);
        spkBox.style.transform = 'scale(1)';

        let rawData = e.dataTransfer.getData('text/plain');
        if (!rawData) return;
        let worker = JSON.parse(rawData);

        if (document.getElementById('spk-worker-' + worker.id)) return;

        updateWorkerStatusInDatabase(worker.id, 'Kerja', prodId, function() {
            let leftCard = document.getElementById('worker-' + worker.id);
            if (leftCard) leftCard.style.display = 'none';

            let listContainer = document.getElementById('spk-workers-' + prodId);
            let cardItem = document.createElement('div');
            cardItem.id = 'spk-worker-' + worker.id;
            cardItem.className = 'p-2 bg-light rounded-3 border border-primary border-opacity-25 d-flex justify-content-between align-items-center mt-1 active-spk-worker';
            cardItem.innerHTML = `
                <div>
                    <span class="badge font-monospace mb-1" style="font-size: 0.5rem; background-color: #0d6efd; color: white;">#${worker.id.toString().padStart(3, '0')}</span>
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.75rem;">${worker.nama}</h6>
                    <span class="text-muted" style="font-size: 0.6rem;">${worker.posisi}</span>
                </div>
                <button onclick="returnWorkerFromSpk('${worker.id}', '${prodId}')" class="btn btn-sm text-white fw-bold px-2 py-1" style="font-size: 0.55rem; background-color: #0b192c;">
                    <i class="fa-solid fa-rotate-left"></i> Return
                </button>
            `;
            listContainer.appendChild(cardItem);

            if (spkBox.parentElement.id !== 'activeSpkContainer') {
                document.getElementById('activeSpkContainer').appendChild(spkBox);
                spkBox.style.borderColor = '#ff6600'; 
                spkBox.style.backgroundColor = '#fffdf5';
            }
            checkAllPlaceholders();
        });
    }

    function returnWorkerFromSpk(workerId, prodId) {
        updateWorkerStatusInDatabase(workerId, 'Idle', null, function() {
            let wCard = document.getElementById('spk-worker-' + workerId);
            if(wCard) wCard.remove();

            let leftCard = document.getElementById('worker-' + workerId);
            if (leftCard) leftCard.style.display = 'block';

            let listContainer = document.getElementById('spk-workers-' + prodId);
            if (listContainer.querySelectorAll('.active-spk-worker').length === 0) {
                let spkBox = document.getElementById('spk-' + prodId);
                if (spkBox.parentElement.id !== 'readySpkContainer') {
                    document.getElementById('readySpkContainer').appendChild(spkBox);
                    spkBox.style.borderColor = '#0d6efd'; 
                    spkBox.style.backgroundColor = '#ffffff';
                }
            }
            checkAllPlaceholders();
        });
    }

    // ==== GENERAL DROP & CUTI DROP ====
    function handleGeneralDragOver(e) { e.preventDefault(); document.getElementById('generalWorkZone').style.backgroundColor = '#ffe5d0'; }
    function handleGeneralDragLeave(e) { document.getElementById('generalWorkZone').style.backgroundColor = '#ffffff'; }
    
    function handleGeneralDrop(e) {
        e.preventDefault(); document.getElementById('generalWorkZone').style.backgroundColor = '#ffffff';
        let rawData = e.dataTransfer.getData('text/plain'); if (!rawData) return; let worker = JSON.parse(rawData);
        if (document.getElementById('gen-assigned-' + worker.id)) return;

        updateWorkerStatusInDatabase(worker.id, 'Kerja', null, function() {
            let leftCard = document.getElementById('worker-' + worker.id); if (leftCard) leftCard.style.display = 'none';
            let listContainer = document.getElementById('generalWorkList');
            let cardItem = document.createElement('div');
            cardItem.id = 'gen-assigned-' + worker.id; cardItem.className = 'p-2 bg-light rounded-3 shadow-sm border border-warning border-opacity-25 d-flex justify-content-between align-items-center gen-worker-item';
            cardItem.innerHTML = `<div><span class="badge text-white font-monospace mb-1" style="font-size: 0.5rem; background-color: #ff6600;">#${worker.id.toString().padStart(3, '0')}</span><h6 class="fw-bold text-dark mb-0" style="font-size: 0.75rem;">${worker.nama}</h6></div><button onclick="returnGeneralWorker('${worker.id}')" class="btn btn-sm text-white fw-bold px-1 py-1" style="font-size: 0.55rem; background-color: #0b192c;"><i class="fa-solid fa-rotate-left"></i></button>`;
            listContainer.appendChild(cardItem); checkAllPlaceholders();
        });
    }
    
    function returnGeneralWorker(id) {
        updateWorkerStatusInDatabase(id, 'Idle', null, function() {
            let item = document.getElementById('gen-assigned-' + id); if(item) item.remove();
            let leftCard = document.getElementById('worker-' + id); if (leftCard) leftCard.style.display = 'block'; checkAllPlaceholders();
        });
    }

    function handleDragOver(e, zoneId) { e.preventDefault(); if(zoneId === 'cutiZone') document.getElementById('cutiZone').style.backgroundColor = '#e2e8f0'; }
    function handleDragLeave(e, zoneId) { if(zoneId === 'cutiZone') document.getElementById('cutiZone').style.backgroundColor = '#f8fafc'; }
    function handleCutiDrop(e) {
        e.preventDefault(); document.getElementById('cutiZone').style.backgroundColor = '#f8fafc';
        let rawData = e.dataTransfer.getData('text/plain'); if (!rawData) return; let worker = JSON.parse(rawData);
        if (document.getElementById('cuti-assigned-' + worker.id)) return;

        updateWorkerStatusInDatabase(worker.id, 'Cuti', null, function() {
            let leftCard = document.getElementById('worker-' + worker.id); if (leftCard) leftCard.style.display = 'none';
            let listContainer = document.getElementById('cutiList');
            let cardItem = document.createElement('div');
            cardItem.id = 'cuti-assigned-' + worker.id; cardItem.className = 'p-2.5 bg-white rounded-3 shadow-sm border d-flex justify-content-between align-items-center cuti-item';
            cardItem.innerHTML = `<div><span class="badge bg-secondary text-white font-monospace mb-1" style="font-size: 0.55rem;">#${worker.id.toString().padStart(3, '0')}</span><h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">${worker.nama}</h6><span class="text-muted" style="font-size: 0.65rem;">${worker.posisi}</span></div><button onclick="returnCutiWorker('${worker.id}')" class="btn btn-sm text-white fw-bold px-2 py-1" style="font-size: 0.6rem; background-color: #0b192c;"><i class="fa-solid fa-rotate-left"></i> Return</button>`;
            listContainer.appendChild(cardItem); checkAllPlaceholders();
        });
    }
    
    function returnCutiWorker(id) {
        updateWorkerStatusInDatabase(id, 'Idle', null, function() {
            let item = document.getElementById('cuti-assigned-' + id); if(item) item.remove();
            let leftCard = document.getElementById('worker-' + id); if (leftCard) leftCard.style.display = 'block'; checkAllPlaceholders();
        });
    }

    function updateWorkerStatusInDatabase(id, newStatus, prodId, callback) {
        fetch(`/man-power/${id}/update-status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ status: newStatus, production_order_id: prodId })
        }).then(r => r.json()).then(data => {
            if(data.success) callback(); else alert('Gagal memperbarui status ke database.');
        }).catch(err => console.error(err));
    }

    function checkAllPlaceholders() {
        let cList = document.getElementById('cutiList'), cPh = document.getElementById('cutiPlaceholder');
        if(cPh) cPh.style.display = cList.querySelectorAll('.cuti-item').length === 0 ? 'block' : 'none';

        let gList = document.getElementById('generalWorkList'), gPh = document.getElementById('genWorkPlaceholder');
        if(gPh) gPh.style.display = gList.querySelectorAll('.gen-worker-item').length === 0 ? 'block' : 'none';

        document.querySelectorAll('.spk-workers-list').forEach(container => {
            let ph = container.querySelector('.placeholder-worker');
            if (ph) ph.style.display = container.querySelectorAll('.active-spk-worker').length === 0 ? 'block' : 'none';
        });

        let wSpkList = document.getElementById('activeSpkContainer'), wPh = document.getElementById('activeSpkPlaceholder');
        if(wPh) wPh.style.display = wSpkList.querySelectorAll('.spk-box').length === 0 ? 'block' : 'none';

        let emptySpk = document.getElementById('emptySpkState'), idleList = document.getElementById('readySpkContainer');
        if(emptySpk) emptySpk.style.display = idleList.querySelectorAll('.spk-box').length === 0 ? 'block' : 'none';

        let cutiCount = document.getElementById('cutiList').querySelectorAll('.cuti-item').length;
        document.getElementById('cutiCount').innerText = cutiCount + ' Cuti';
    }

    // ==== FIXED DELETE DROP LOGIC ====
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
        
        let w = JSON.parse(rawData); 
        if (!w.id) return;

        if (confirm(`Yakin ingin mengajukan penghapusan pekerja ${w.nama}?`)) {
            fetch(`/man-power/${w.id}`, { 
                method: 'POST', 
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }, 
                body: JSON.stringify({ _method: 'DELETE' }) 
            })
            .then(res => res.json())
            .then(data => { 
                alert(data.message); 
                if (data.success) {
                    location.reload(); 
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal mengirim pengajuan hapus.');
            });
        }
    }
</script>
@endsection