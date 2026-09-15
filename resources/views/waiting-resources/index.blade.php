@extends('layouts.app')

@section('content')
<div class="container-fluid px-0" data-csrf="{{ csrf_token() }}">

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
                <a href="{{ Route::has('machine-power.index') ? route('machine-power.index') : '#' }}" class="text-decoration-none fw-medium text-muted" style="font-size: 0.9rem;">
                    Machine Power
                </a>
            </div>
            <h2 class="fw-bold text-dark mb-0" style="font-size: 1.6rem;">Waiting for Resources Dashboard</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        
        <!-- KOLOM KIRI: DIREKTORI ANTREAN PRODUKSI (Gaya Flip Card) -->
        <div class="col-lg-8 border-end border-secondary border-opacity-10 pe-lg-4">
            <h5 class="fw-bold text-dark mb-3" style="font-size: 1rem;"><i class="fa-solid fa-layer-group text-warning me-2"></i> Production Queue List</h5>
            
            <div style="perspective: 1000px;">
                <div class="row" id="queueContainer">
                    @php
                        $items = $waitingList ?? collect();
                        $leftItems = $items->filter(function($i) { return $i->status !== 'ready'; });
                    @endphp

                    @forelse ($leftItems as $index => $item)
                        <div class="col-md-6 mb-4 queue-wrapper" id="item-wrapper-{{ $item->id }}" style="display: block;">
                            
                            <div style="position: relative; width: 100%; margin-top: 25px;">
                                
                                @if(auth()->check() && auth()->user()->role !== 'user')
                                <div draggable="true" 
                                     ondragstart="handleDragStart(event)" 
                                     data-id="{{ $item->id }}"
                                     data-title="{{ $item->product_name }}"
                                     data-qty="{{ $item->quantity }}"
                                     data-no="{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}"
                                     class="position-absolute start-50 translate-middle-x text-white px-3 py-1 rounded-pill shadow-sm fw-bold font-monospace d-flex align-items-center gap-1 queue-card-drag"
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
                                            <span class="badge bg-light text-secondary font-monospace item-no" style="font-size: 0.6rem;">
                                                #{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <span class="text-uppercase fw-bold text-muted" style="font-size: 0.55rem; letter-spacing: 1.5px;">QUEUE ITEM</span>
                                        </div>

                                        <div class="text-center my-auto">
                                            <div class="rounded-circle bg-light text-warning fw-bold d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 75px; height: 75px; font-size: 1.8rem; border: 3px solid #ff6600;">
                                                <i class="fa-solid fa-box-open text-warning"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark item-title mb-1" style="font-size: 1.05rem;">{{ $item->product_name }}</h5>
                                            <p class="text-muted small item-qty mb-0">Qty: <b>{{ $item->quantity }} Pcs</b></p>
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
                                                <span class="text-uppercase text-white fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">QUEUE DETAIL</span>
                                                <span class="badge bg-dark bg-opacity-25 text-white font-monospace" style="font-size: 0.6rem;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                            </div>

                                            <div class="mt-3">
                                                <div class="mb-2">
                                                    <span class="text-white text-opacity-75 d-block" style="font-size: 0.5rem; text-transform: uppercase;">PRODUK</span>
                                                    <h6 class="fw-bold text-white mb-1" style="font-size: 0.95rem;">{{ $item->product_name }}</h6>
                                                </div>

                                                <div class="mb-2">
                                                    <span class="text-white text-opacity-75 d-block" style="font-size: 0.5rem; text-transform: uppercase;">KUANTITAS</span>
                                                    <span class="fw-semibold text-white small" style="font-size: 0.75rem;">{{ $item->quantity }} Pcs</span>
                                                </div>

                                                <div class="mb-2">
                                                    <span class="text-white text-opacity-75 d-block" style="font-size: 0.5rem; text-transform: uppercase;">KETERANGAN</span>
                                                    <span class="fw-semibold text-white small fst-italic" style="font-size: 0.7rem;">{{ $item->notes ?? 'Tidak ada keterangan' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top border-white border-opacity-25">
                                            <span class="text-white text-opacity-75" style="font-size: 0.6rem;">Status: Menunggu</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4">
                            <p class="text-muted small">Tidak ada antrean produksi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: PANEL KONTROL -->
        <div class="col-lg-4 ps-lg-4 mt-4 mt-lg-0">
            <div class="sticky-top d-flex flex-column gap-4" style="top: 20px;">
                
                <!-- CARD 1: READY TO PROCESS (ZONE DROP + RETURN) -->
                <div class="p-4 rounded-4 bg-white shadow-sm border border-2 border-dashed" 
                     id="dropZone"
                     ondragover="handleDragOver(event)"
                     ondragleave="handleDragLeave(event)"
                     ondrop="handleDrop(event)"
                     style="background-color: #fffdf5 !important; border-color: #ff6600 !important; transition: 0.3s; min-height: 250px;">
                    
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;"><i class="fa-solid fa-hand-holding-hand text-warning me-2"></i> READY TO PROCESS</h5>
                        <span class="badge text-white px-2 py-1" id="readyCountBadge" style="background-color: #ff6600;">0 Items</span>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.75rem;">Drop di sini untuk status <b>Ready</b>.</p>

                    <div id="readyListContainer" class="d-flex flex-column gap-2">
                        @php
                            $rightItems = $items->filter(function($i) { return $i->status === 'ready'; });
                            $hasReady = $rightItems->count() > 0;
                        @endphp

                        @foreach ($rightItems as $ritem)
                            <div id="ready-item-{{ $ritem->id }}" class="p-2.5 bg-white rounded-3 shadow-sm border d-flex justify-content-between align-items-center ready-card-item">
                                <div>
                                    <span class="badge text-white font-monospace mb-1" style="font-size: 0.55rem; background-color: #ff6600;">#{{ str_pad($ritem->id, 3, '0', STR_PAD_LEFT) }}</span>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">{{ $ritem->product_name }}</h6>
                                    <span class="text-muted" style="font-size: 0.65rem;">Qty: {{ $ritem->quantity }} Pcs</span>
                                </div>
                                <button onclick="returnItem('{{ $ritem->id }}', '{{ $ritem->product_name }}', '{{ $ritem->quantity }}', '#{{ str_pad($ritem->id, 3, '0', STR_PAD_LEFT) }}')" class="btn btn-sm text-white fw-bold px-2 py-1" style="font-size: 0.6rem; background-color: #0b192c;">
                                    <i class="fa-solid fa-rotate-left"></i> Return
                                </button>
                            </div>
                        @endforeach

                        <div id="readyPlaceholder" class="text-center py-3 text-muted opacity-50" style="display: {{ $hasReady ? 'none' : 'block' }}; font-size: 0.8rem;">
                            <p class="small mb-0">Kosong (Drop antrean ke sini)</p>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: PINTU DELETE (BERDASARKAN ROLE) -->
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
    const csrfToken = document.querySelector('.container-fluid').getAttribute('data-csrf');
    let draggedData = null;

    document.addEventListener("DOMContentLoaded", function() {
        updateCounts();
    });

    function handleDragStart(e) {
        let el = e.currentTarget;
        draggedData = {
            id: el.getAttribute('data-id'),
            title: el.getAttribute('data-title'),
            qty: el.getAttribute('data-qty'),
            no: el.getAttribute('data-no')
        };
        e.dataTransfer.setData('text/plain', JSON.stringify(draggedData));
    }

    function handleDragOver(e) {
        e.preventDefault();
        document.getElementById('dropZone').style.backgroundColor = '#ffe5d0';
    }

    function handleDragLeave(e) {
        document.getElementById('dropZone').style.backgroundColor = '#fffdf5';
    }

    function handleDrop(e) {
        e.preventDefault();
        document.getElementById('dropZone').style.backgroundColor = '#fffdf5';

        let rawData = e.dataTransfer.getData('text/plain');
        if (!rawData) return;
        let item = JSON.parse(rawData);

        if (document.getElementById('ready-item-' + item.id)) return;

        updateDatabaseStatus(item.id, 'ready', function() {
            let leftCard = document.getElementById('item-wrapper-' + item.id);
            if (leftCard) leftCard.style.display = 'none';

            let placeholder = document.getElementById('readyPlaceholder');
            if (placeholder) placeholder.style.display = 'none';

            let readyList = document.getElementById('readyListContainer');
            let cardItem = document.createElement('div');
            cardItem.id = 'ready-item-' + item.id;
            cardItem.className = 'p-2.5 bg-white rounded-3 shadow-sm border d-flex justify-content-between align-items-center ready-card-item';
            cardItem.innerHTML = `
                <div>
                    <span class="badge text-white font-monospace mb-1" style="font-size: 0.55rem; background-color: #ff6600;">#${item.no}</span>
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">${item.title}</h6>
                    <span class="text-muted" style="font-size: 0.65rem;">Qty: ${item.qty} Pcs</span>
                </div>
                <button onclick="returnItem('${item.id}', '${item.title}', '${item.qty}', '#${item.no}')" class="btn btn-sm text-white fw-bold px-2 py-1" style="font-size: 0.6rem; background-color: #0b192c;">
                    <i class="fa-solid fa-rotate-left"></i> Return
                </button>
            `;
            readyList.appendChild(cardItem);
            updateCounts();
        });
    }

    function returnItem(id, title, qty, noFormatted) {
        updateDatabaseStatus(id, 'waiting', function() {
            let readyItem = document.getElementById('ready-item-' + id);
            if (readyItem) readyItem.remove();

            let leftCard = document.getElementById('item-wrapper-' + id);
            if (leftCard) leftCard.style.display = 'block';

            checkReadyPlaceholders();
            updateCounts();
        });
    }

    function updateDatabaseStatus(id, status, callback) {
        fetch(`/waiting-resources/update-status/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status: status })
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
        let item = JSON.parse(rawData);

        let userRole = "{{ auth()->check() ? auth()->user()->role : '' }}";
        let confirmMsg = userRole === 'super_admin' 
            ? `Yakin ingin menghapus antrean produksi ${item.title} secara permanen?` 
            : `Kirim permintaan hapus antrean produksi ${item.title} ke Super Admin?`;

        if (confirm(confirmMsg)) {
            fetch(`/waiting-resources/${item.id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ _method: 'DELETE' })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || 'Berhasil diproses.');
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                location.reload();
            });
        }
    }

    function checkReadyPlaceholders() {
        let readyList = document.getElementById('readyListContainer');
        if (readyList.querySelectorAll('.ready-card-item').length === 0) {
            document.getElementById('readyPlaceholder').style.display = 'block';
        }
    }

    function updateCounts() {
        let readyCount = document.getElementById('readyListContainer').querySelectorAll('.ready-card-item').length;
        document.getElementById('readyCountBadge').innerText = readyCount + ' Items';
    }
</script>
@endsection