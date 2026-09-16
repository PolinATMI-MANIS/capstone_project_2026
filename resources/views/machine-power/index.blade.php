@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom border-secondary border-opacity-10">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <a href="{{ route('waiting-resources.index') }}" class="text-decoration-none fw-medium text-muted" style="font-size: 0.9rem;">Waiting for Resources</a>
                <span class="text-muted">/</span>
                <a href="{{ route('man-power.index') }}" class="text-decoration-none fw-medium text-muted" style="font-size: 0.9rem;">Man Power</a>
                <span class="text-muted">/</span>
                <a href="{{ route('machine-power.index') }}" class="text-decoration-none fw-bold text-dark pb-1 border-bottom border-2 border-danger" style="font-size: 0.9rem;">Machine Power</a>
            </div>
            <h2 class="fw-bold text-dark mb-0" style="font-size: 1.6rem;">Machine & Operator Integration Dashboard</h2>
        </div>
        <a href="{{ route('machine-power.create') }}" class="btn fw-bold text-white px-3 py-2 shadow-sm" style="background-color: #ff6600; border: none; border-radius: 8px;">
            <i class="fa-solid fa-plus me-1"></i> Add Machine
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 border-end border-secondary border-opacity-10 pe-lg-4">
            <h5 class="fw-bold text-dark mb-3" style="font-size: 1rem;"><i class="fa-solid fa-gears text-warning me-2"></i> Available / Standby Machines</h5>
            <div style="perspective: 1000px;">
                <div class="row" id="directoryList">
                    @forelse ($machinePowers as $item)
                        @php $statusClean = trim(ucfirst(strtolower($item->status ?? ''))); @endphp
                        <div class="col-md-6 mb-4 machine-card-item" id="machine-{{ $item->id }}" style="display: {{ ($statusClean === 'Running' || $statusClean === 'Breakdown') ? 'none' : 'block' }};">
                            <div style="position: relative; width: 100%; margin-top: 25px; perspective: 1000px;">
                                <div draggable="true" ondragstart="handleDragStart(event)" data-id="{{ $item->id }}" data-name="{{ $item->machine_name }}" data-type="{{ $item->machine_type }}" class="position-absolute start-50 translate-middle-x text-white px-3 py-1 rounded-pill shadow-sm fw-bold font-monospace d-flex align-items-center gap-1" style="top: -15px; z-index: 10; font-size: 0.65rem; cursor: grab; background-color: #ff6600; border: 2px solid #ffffff; user-select: none;">
                                    <i class="fa-solid fa-grip-lines"></i> DRAG MACHINE
                                </div>
                                <div style="position: relative; width: 100%; height: 330px; transition: transform 0.6s; transform-style: preserve-3d;" class="card-flipper shadow-sm" onmouseover="this.style.transform='rotateY(180deg)'" onmouseout="this.style.transform='rotateY(0deg)'">
                                    <div class="card border-0 h-100 p-3 pt-4 d-flex flex-column justify-content-between bg-white" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; -webkit-backface-visibility: hidden; border-radius: 14px;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.6rem;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                            <span class="text-uppercase fw-bold text-muted" style="font-size: 0.55rem; letter-spacing: 1.5px;">MACHINE ID</span>
                                        </div>
                                        <div class="text-center my-auto">
                                            @if(!empty($item->foto))
                                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->machine_name }}" class="rounded-circle shadow-sm mx-auto mb-2" style="width: 75px; height: 75px; object-fit: cover; border: 3px solid #ff6600;">
                                            @else
                                                <div class="rounded-circle bg-light text-warning fw-bold d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 75px; height: 75px; font-size: 1.8rem; border: 3px solid #ff6600;">
                                                    <i class="fa-solid fa-gears"></i>
                                                </div>
                                            @endif
                                            <h6 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">{{ $item->machine_name }}</h6>
                                            <span class="badge bg-light text-dark border mb-1">{{ $item->machine_type }}</span>
                                        </div>
                                        <div class="text-center pt-2 border-top d-flex justify-content-end align-items-center">
                                            <span class="text-muted opacity-75" style="font-size: 0.55rem;"><i class="fa-solid fa-rotate text-warning me-1"></i> Hover detail</span>
                                        </div>
                                    </div>
                                    <div class="card border-0 h-100 p-3 pt-4 d-flex flex-column justify-content-between text-white" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; -webkit-backface-visibility: hidden; transform: rotateY(180deg); border-radius: 14px; background-color: #ff6600;">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white border-opacity-25">
                                                <span class="text-uppercase text-white fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">SPECIFICATION</span>
                                                <span class="badge bg-dark bg-opacity-25 text-white font-monospace" style="font-size: 0.6rem;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                            <div class="row align-items-center mt-3">
                                                <div class="col-7">
                                                    <div class="mb-2">
                                                        <span class="text-white text-opacity-75 d-block" style="font-size: 0.5rem; text-transform: uppercase;">NAMA MESIN</span>
                                                        <h6 class="fw-bold text-white mb-1" style="font-size: 0.95rem;">{{ $item->machine_name }}</h6>
                                                    </div>
                                                    <div class="mb-2">
                                                        <span class="text-white text-opacity-75 d-block" style="font-size: 0.5rem; text-transform: uppercase;">LOKASI</span>
                                                        <span class="fw-semibold text-white small" style="font-size: 0.75rem;">{{ $item->location ?? '-' }}</span>
                                                    </div>
                                                    <div class="mb-1">
                                                        <span class="text-white text-opacity-75 d-block mb-1" style="font-size: 0.5rem; text-transform: uppercase;">KAPASITAS</span>
                                                        <span class="badge bg-white text-dark px-2 py-1 fw-bold" style="font-size: 0.6rem;">{{ $item->capacity ?? '-' }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-5 text-center">
                                                    @if(!empty($item->foto))
                                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->machine_name }}" class="rounded-4 shadow-sm" style="width: 95px; height: 110px; object-fit: cover; border: 3px solid rgba(255, 255, 255, 0.6);">
                                                    @else
                                                        <div class="rounded-4 bg-dark text-warning fw-bold d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 95px; height: 110px; font-size: 2.2rem; border: 3px solid rgba(255, 255, 255, 0.6);">
                                                            <i class="fa-solid fa-gears"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top border-white border-opacity-25">
                                            <a href="{{ route('machine-power.edit', $item->id) }}" class="btn btn-sm btn-light text-primary fw-semibold px-2 py-1 shadow-sm" style="font-size: 0.65rem;">Edit</a>
                                            <form action="{{ route('machine-power.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data mesin ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light text-danger fw-semibold px-2 py-1 border-0 shadow-sm" style="font-size: 0.65rem;">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                    <div class="col-12 text-center py-4"><p class="text-muted small">Tidak ada data machine power.</p></div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4 ps-lg-4 mt-4 mt-lg-0">
            <div class="sticky-top d-flex flex-column gap-4" style="top: 20px;">
                <div class="p-4 rounded-4 bg-white shadow-sm border border-secondary border-opacity-25" id="manWaitingBox">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1rem;"><i class="fa-solid fa-user-clock text-primary me-2"></i> MAN WAITING</h5>
                        <span class="badge bg-primary text-white px-2 py-1" id="waitingCount">{{ count($waitingOperators ?? []) }} Ready</span>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.75rem;">Operator status <b>Kerja</b> menantikan alokasi mesin.</p>
                    <div id="waitingList" class="d-flex flex-column gap-2" style="max-height: 250px; overflow-y: auto;">
                        @forelse($waitingOperators ?? [] as $op)
                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center justify-content-between operator-drop-target" id="operator-{{ $op->id }}" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDropOnOperator(event, '{{ $op->id }}', '{{ $op->nama }}')">
                                <div class="d-flex align-items-center gap-2">
                                    @if($op->foto)
                                        <img src="{{ asset('storage/' . $op->foto) }}" class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; font-size: 0.8rem;">{{ strtoupper(substr($op->nama, 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.8rem;">{{ $op->nama }}</h6>
                                        <span class="text-muted" style="font-size: 0.6rem;">{{ $op->posisi }}</span>
                                    </div>
                                </div>
                                <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.55rem;">Drop Machine Here</span>
                            </div>
                        @empty
                            <div id="waitingPlaceholder" class="text-center py-2 text-muted opacity-50" style="font-size: 0.75rem;">Tidak ada operator menunggu mesin.</div>
                        @endforelse
                    </div>
                </div>

                <!-- WORK ZONE (UPGRADED CARD) -->
                <div class="p-4 rounded-4 bg-white shadow-sm border border-2 border-success" style="background-color: #f0fdf4 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;"><i class="fa-solid fa-industry text-success me-2"></i> WORK ZONE</h5>
                        <span class="badge bg-success text-white px-2 py-1" id="runningCount">0 Active</span>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.75rem;">Lini produksi aktif (Operator & Mesin terintegrasi).</p>
                    <div id="runningList" class="d-flex flex-column gap-2">
                        @php $hasRunning = false; @endphp
                        @foreach ($machinePowers as $item)
                            @php $statusClean = trim(ucfirst(strtolower($item->status ?? ''))); @endphp
                            @if($statusClean == 'Running' && $item->operator)
                                @php $hasRunning = true; @endphp
                                <div id="assigned-{{ $item->id }}" class="p-3 bg-white rounded-3 shadow-sm border d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-success text-white mb-1" style="font-size: 0.55rem;">RUNNING</span>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;"><i class="fa-solid fa-user me-1 text-primary"></i> {{ $item->operator->nama }}</h6>
                                        <span class="text-muted d-block mt-1" style="font-size: 0.65rem;">
                                            <i class="fa-solid fa-gears text-secondary me-1"></i> Mesin: <b>{{ $item->machine_name }}</b> ({{ $item->machine_type }})
                                            @if($item->start_time) <br><i class="fa-solid fa-clock text-warning me-1"></i> Mulai Pukul: <b class="text-dark">{{ $item->start_time }}</b> @endif
                                        </span>

                                        {{-- Informasi Produk / SPK yang Sedang Dikerjakan --}}
                                        <div class="p-2 rounded-2 bg-light border border-secondary border-opacity-10 mt-2">
                                            <span class="text-uppercase text-muted d-block" style="font-size: 0.5rem; letter-spacing: 0.5px;">Sedang Mengerjakan Produksi:</span>
                                            <span class="fw-bold text-primary" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-box-open me-1"></i> 
                                                {{ optional($item->operator->productionOrder)->produk ?? 'Tidak ada SPK aktif' }} 
                                                @if(optional($item->operator->productionOrder)->no_po)
                                                    <span class="badge bg-dark bg-opacity-10 text-dark font-monospace ms-1" style="font-size: 0.55rem;">
                                                        #{{ optional($item->operator->productionOrder)->no_po }}
                                                    </span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <button onclick="returnMachine('{{ $item->id }}')" class="btn btn-sm text-white fw-bold px-2 py-1 align-self-start" title="Selesaikan / Lepas" style="font-size: 0.6rem; background-color: #0b192c;">
                                        <i class="fa-solid fa-rotate-left"></i> Return
                                    </button>
                                </div>
                            @endif
                        @endforeach
                        <div id="runningPlaceholder" class="text-center py-3 text-muted opacity-50" style="display: {{ $hasRunning ? 'none' : 'block' }}; font-size: 0.8rem;">
                            <p class="small mb-0">Belum ada lini aktif.</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-4 bg-white shadow-sm border border-2 border-danger" id="breakdownZone" ondragover="handleDragOverZone(event)" ondragleave="handleDragLeaveZone(event)" ondrop="handleDropBreakdown(event)" style="background-color: #fff5f5 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> BREAKDOWN ZONE</h5>
                        <span class="badge bg-danger text-white px-2 py-1" id="breakdownCount">0 Breakdown</span>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.75rem;">Drop mesin langsung ke sini jika mengalami kerusakan.</p>
                    <div id="breakdownList" class="d-flex flex-column gap-2">
                        @php $hasBreakdown = false; @endphp
                        @foreach ($machinePowers as $item)
                            @php $statusClean = trim(ucfirst(strtolower($item->status ?? ''))); @endphp
                            @if($statusClean == 'Breakdown')
                                @php $hasBreakdown = true; @endphp
                                <div id="assigned-{{ $item->id }}" class="p-2.5 bg-white rounded-3 shadow-sm border d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-danger text-white font-monospace mb-1" style="font-size: 0.55rem;">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">{{ $item->machine_name }}</h6>
                                        <span class="text-muted" style="font-size: 0.65rem;">{{ $item->machine_type }}</span>
                                    </div>
                                    <button onclick="returnMachine('{{ $item->id }}')" class="btn btn-sm text-white fw-bold px-2 py-1" title="Perbaiki / Standby" style="font-size: 0.6rem; background-color: #0b192c;">
                                        <i class="fa-solid fa-rotate-left"></i> Repair
                                    </button>
                                </div>
                            @endif
                        @endforeach
                        <div id="breakdownPlaceholder" class="text-center py-3 text-muted opacity-50" style="display: {{ $hasBreakdown ? 'none' : 'block' }}; font-size: 0.8rem;">
                            <p class="small mb-0">Kosong (Drop mesin rusak ke sini)</p>
                        </div>
                    </div>
                </div>

                <div class="px-3 py-3 rounded-pill shadow-sm border border-2 border-danger text-center d-flex align-items-center justify-content-center gap-2" id="deleteZone" ondragover="handleDeleteDragOver(event)" ondragleave="handleDeleteDragLeave(event)" ondrop="handleDeleteDrop(event)" style="background-color: #fff5f5 !important; transition: 0.3s; cursor: pointer;">
                    <div class="text-danger d-flex align-items-center"><i class="fa-solid fa-door-open fs-5"></i></div>
                    <span class="fw-bold text-danger text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.3px;">DROP DI SINI UNTUK HAPUS MESIN</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="timeModal" tabindex="-1" aria-labelledby="timeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="timeModalLabel">
                    <i class="fa-solid fa-clock text-warning me-2"></i> Tentukan Jam Mulai Operasional
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="text-muted small mb-3" id="modalMachineOperatorText">Silakan masukkan jam mulai operasi:</p>
                <div class="mb-3">
                    <label for="startTimeInput" class="form-label fw-semibold small text-secondary">Jam Mulai Kerja</label>
                    <input type="time" class="form-control form-control-lg rounded-3 border-secondary border-opacity-25" id="startTimeInput" value="08:00">
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 pt-0">
                <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-semibold text-muted" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn text-white px-4 py-2 rounded-3 fw-bold" id="confirmTimeBtn" style="background-color: #ff6600;">Mulai Operasi</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        updateCounts();
    });

    let draggedMachine = null;
    let pendingDropData = null;

    function handleDragStart(e) {
        let el = e.currentTarget;
        draggedMachine = {
            id: el.getAttribute('data-id'),
            name: el.getAttribute('data-name'),
            type: el.getAttribute('data-type')
        };
        e.dataTransfer.setData('text/plain', JSON.stringify(draggedMachine));
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.style.backgroundColor = '#e0f2fe';
    }

    function handleDragLeave(e) {
        e.currentTarget.style.backgroundColor = '#f8f9fa';
    }

    function handleDropOnOperator(e, operatorId, operatorName) {
        e.preventDefault();
        e.currentTarget.style.backgroundColor = '#f8f9fa';

        let rawData = e.dataTransfer.getData('text/plain');
        if (!rawData) return;
        let machine = JSON.parse(rawData);

        pendingDropData = {
            machineId: machine.id,
            operatorId: operatorId
        };

        document.getElementById('modalMachineOperatorText').innerText = 
            `Mengintegrasikan mesin "${machine.name}" dengan operator "${operatorName}".`;

        let myModal = new bootstrap.Modal(document.getElementById('timeModal'));
        myModal.show();
    }

    document.getElementById('confirmTimeBtn').addEventListener('click', function() {
        let startTime = document.getElementById('startTimeInput').value;
        if (!startTime) {
            alert('Silakan pilih jam mulai terlebih dahulu!');
            return;
        }

        if (!pendingDropData) return;

        let modalEl = document.getElementById('timeModal');
        let modalObj = bootstrap.Modal.getInstance(modalEl);
        modalObj.hide();

        fetch(`/machine-power/${pendingDropData.machineId}/update-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                status: 'Running',
                man_power_id: pendingDropData.operatorId,
                start_time: startTime 
            })
        })
        .then(res => {
            const contentType = res.headers.get("content-type");
            if (res.ok && contentType && contentType.includes("application/json")) {
                return res.json();
            }
            return res.text().then(text => { throw new Error(text); });
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Gagal memperbarui status.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan pada server.');
        });
    });

    function handleDragOverZone(e) { e.preventDefault(); e.currentTarget.style.backgroundColor = '#fecaca'; }
    function handleDragLeaveZone(e) { e.currentTarget.style.backgroundColor = '#fff5f5'; }
    
    function handleDropBreakdown(e) {
        e.preventDefault();
        e.currentTarget.style.backgroundColor = '#fff5f5';
        let rawData = e.dataTransfer.getData('text/plain');
        if (!rawData) return;
        let machine = JSON.parse(rawData);

        fetch(`/machine-power/${machine.id}/update-status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ status: 'Breakdown' })
        }).then(res => res.json()).then(data => { if(data.success) location.reload(); });
    }

    function returnMachine(id) {
        fetch(`/machine-power/${id}/update-status`, {
            method: 'PATCH',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
            },
            body: JSON.stringify({ status: 'Standby' })
        })
        .then(res => {
            const contentType = res.headers.get("content-type");
            if (res.ok && contentType && contentType.includes("application/json")) {
                return res.json();
            }
            return res.text().then(text => { 
                throw new Error("Server Error HTML: " + text.substring(0, 300)); 
            });
        })
        .then(data => { 
            if(data.success) {
                location.reload();
            } else {
                alert(data.message || 'Gagal mereset status mesin.');
            }
        })
        .catch(err => {
            console.error('Error details:', err.message);
            alert('Terjadi kesalahan di server.');
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
        let machine = JSON.parse(rawData);

        if (confirm(`Yakin ingin memproses hapus data mesin ${machine.name}?`)) {
            fetch(`/machine-power/${machine.id}`, {
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
                    alert(data.message || 'Gagal menghapus data mesin.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    function updateCounts() {
        let runningCount = document.getElementById('runningList').querySelectorAll('div[id^="assigned-"]').length;
        document.getElementById('runningCount').innerText = runningCount + ' Active';

        let breakdownCount = document.getElementById('breakdownList').querySelectorAll('div[id^="assigned-"]').length;
        document.getElementById('breakdownCount').innerText = breakdownCount + ' Breakdown';
        
        let waitingCount = document.getElementById('waitingList').querySelectorAll('.operator-drop-target').length;
        document.getElementById('waitingCount').innerText = waitingCount + ' Ready';
    }
</script>
@endsection