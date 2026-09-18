@extends('layouts.app')

@section('content')
<!-- Header Utama: Judul & Profile Widget -->
<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <span class="badge bg-warning text-dark fw-bold mb-1 px-2 py-1" style="font-size: 11px; letter-spacing: 0.5px;">SYSTEM OVERVIEW</span>
        <h1 class="fw-bold display-6 m-0 text-dark" style="letter-spacing: -0.5px;">DASHBOARD</h1>
        <p class="text-muted small m-0 mt-1">Rekapan data operasional dari seluruh modul Capstone Industrial System.</p>
    </div>

    <div class="dropdown">
        <button class="btn bg-white px-3 py-2 rounded-3 shadow-sm border d-flex align-items-center dropdown-toggle text-start" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="text-secondary d-flex align-items-center justify-content-center me-3" style="font-size: 2.2rem;">
                <i class="fa-solid fa-circle-user"></i>
            </div>
            <div class="me-2">
                <h6 class="fw-bold m-0 text-dark" style="font-size: 0.88rem;">
                    {{ Auth::user()->name ?? 'Pengguna Capstone' }}
                </h6>
                <span class="badge bg-danger text-uppercase" style="font-size: 0.65rem;">
                    {{ str_replace('_', ' ', Auth::user()->role ?? 'USER') }}
                </span>
            </div>
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 p-2 rounded-3" aria-labelledby="profileDropdown">
            <li>
                <a class="dropdown-item d-flex align-items-center py-2 rounded-2" href="#" data-bs-toggle="offcanvas" data-bs-target="#profileOffcanvas">
                    <i class="fa-solid fa-id-card me-2 text-muted"></i> Lihat Profile
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center py-2 rounded-2" href="/logout">
                    <i class="fa-solid fa-users-between-lines me-2 text-muted"></i> Login Akun Lain
                </a>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
                <a class="dropdown-item d-flex align-items-center py-2 rounded-2 text-danger fw-semibold" href="/logout">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- 1. GRID SUMMARY CARDS -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <a href="/inventory" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 card-hover">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small text-uppercase font-monospace fw-bold">Inventory</span>
                    <div class="bg-primary bg-opacity-10 p-2 rounded text-primary">
                        <i class="fa-solid fa-boxes-stacked fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold m-0 text-dark">{{ $totalInventory ?? 0 }}</h3>
                <p class="text-muted small m-0 mt-1">Total Item Stok Aktif</p>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('produksi.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 card-hover">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small text-uppercase font-monospace fw-bold">Production</span>
                    <div class="bg-success bg-opacity-10 p-2 rounded text-success">
                        <i class="fa-solid fa-industry fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold m-0 text-dark">{{ $totalProduction ?? 0 }}</h3>
                <p class="text-muted small m-0 mt-1">Total SPK Terbit</p>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ Route::has('man-power.index') ? route('man-power.index') : '#' }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 card-hover">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small text-uppercase font-monospace fw-bold">Resources</span>
                    <div class="bg-info bg-opacity-10 p-2 rounded text-info">
                        <i class="fa-solid fa-users-gear fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold m-0 text-dark">{{ $totalResources ?? 0 }}</h3>
                <p class="text-muted small m-0 mt-1">Man Power Tersedia</p>
            </div>
        </a>
    </div>

    <div class="col-md-4 offset-md-2">
        <a href="/purchase" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 card-hover">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small text-uppercase font-monospace fw-bold">Purchase & Delivery Order</span>
                    <div class="bg-warning bg-opacity-10 p-2 rounded text-warning">
                        <i class="fa-solid fa-cart-shopping fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold m-0 text-dark">{{ $totalOrders ?? 0 }}</h3>
                <p class="text-muted small m-0 mt-1">Dokumen PO & DO Pending</p>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="/rnd" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 card-hover">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small text-uppercase font-monospace fw-bold">RnD</span>
                    <div class="bg-danger bg-opacity-10 p-2 rounded text-danger">
                        <i class="fa-solid fa-flask fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold m-0 text-dark">{{ $totalRnd ?? 0 }}</h3>
                <p class="text-muted small m-0 mt-1">Proyek Riset Aktif</p>
            </div>
        </a>
    </div>
</div>

<!-- 2. SECTION TABEL APPROVAL -->
<div class="row mb-4">
    <div class="col-12">
        @php
            $role = Auth::user()->role ?? 'user';
        @endphp

        @if($role === 'super_admin')
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold m-0 text-dark d-flex align-items-center">
                        <i class="fa-solid fa-trash-can text-danger me-2"></i> Permintaan Approval Hapus Data
                    </h6>
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">{{ $pendingCount ?? 0 }} Perlu Tindakan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-3">Pemohon</th>
                                    <th>Modul</th>
                                    <th>Data Yang Ingin Dihapus</th>
                                    <th>Alasan</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingApprovals ?? [] as $item)
                                    <tr>
                                        <td class="ps-3 fw-bold small">{{ $item->pemohon ?? '-' }}</td>
                                        <td><span class="badge bg-primary">{{ $item->modul ?? '-' }}</span></td>
                                        <td class="small">{{ $item->data ?? '-' }}</td>
                                        <td class="small text-muted">{{ $item->alasan ?? '-' }}</td>
                                        <td class="text-end pe-3">
                                            <div class="d-inline-flex gap-1 justify-content-end">
                                                @if(isset($item->url_approve))
                                                    <form action="{{ $item->url_approve }}" method="POST" class="m-0 p-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success rounded-2" onclick="return confirm('Setujui permintaan ini?')">
                                                            <i class="fa-solid fa-check me-1"></i> Approve
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(isset($item->url_reject))
                                                    <form action="{{ $item->url_reject }}" method="POST" class="m-0 p-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" onclick="return confirm('Tolak permintaan ini?')">
                                                            <i class="fa-solid fa-xmark me-1"></i> Tolak
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            <i class="fa-solid fa-circle-check text-success fs-5 d-block mb-1"></i>
                                            Tidak ada permintaan approval hapus data saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        @elseif($role === 'admin')
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold m-0 text-dark d-flex align-items-center">
                        <i class="fa-solid fa-clipboard-list text-warning me-2"></i> Permintaan Approval dari User (Operator)
                    </h6>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">{{ $pendingCount ?? 0 }} Perlu Tindakan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-3">Pemohon</th>
                                    <th>Modul</th>
                                    <th>Data Pengajuan</th>
                                    <th>Keterangan</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingApprovals ?? [] as $item)
                                    <tr>
                                        <td class="ps-3 fw-bold small">{{ $item->pemohon ?? '-' }}</td>
                                        <td><span class="badge bg-primary">{{ $item->modul ?? '-' }}</span></td>
                                        <td class="small fw-bold">{{ $item->data ?? '-' }}</td>
                                        <td class="small text-muted">{{ $item->alasan ?? '-' }}</td>
                                        <td class="text-end pe-3">
                                            <div class="d-inline-flex gap-1 justify-content-end">
                                                @if(isset($item->url_approve))
                                                    <form action="{{ $item->url_approve }}" method="POST" class="m-0 p-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success rounded-2">
                                                            <i class="fa-solid fa-check me-1"></i> Approve
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(isset($item->url_reject))
                                                    <form action="{{ $item->url_reject }}" method="POST" class="m-0 p-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-2">
                                                            <i class="fa-solid fa-xmark me-1"></i> Tolak
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            <i class="fa-solid fa-circle-check text-success fs-5 d-block mb-1"></i>
                                            Tidak ada permintaan approval baru saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        @else
            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                <p class="text-muted mb-0"><i class="fa-solid fa-bell-slash text-muted fs-4 d-block mb-2"></i> Belum ada notifikasi.</p>
            </div>
        @endif
    </div>
</div>

<!-- 3. SECTION CHARTS PER MODUL -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold m-0 text-dark"><i class="fa-solid fa-boxes-stacked text-primary me-2"></i> Kategori Inventory</h6>
            </div>
            <div class="card-body">
                <canvas id="chartInventory" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold m-0 text-dark"><i class="fa-solid fa-industry text-success me-2"></i> Status Production</h6>
            </div>
            <div class="card-body">
                <canvas id="chartProduction" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold m-0 text-dark"><i class="fa-solid fa-users-gear text-info me-2"></i> Status Resources</h6>
            </div>
            <div class="card-body">
                <canvas id="chartResources" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold m-0 text-dark"><i class="fa-solid fa-cart-shopping text-warning me-2"></i> Data Purchase & Delivery Order</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="width: 60%;">
                    <canvas id="chartOrder"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold m-0 text-dark"><i class="fa-solid fa-flask text-danger me-2"></i> Fase Proyek RnD</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="width: 60%;">
                    <canvas id="chartRnd"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- OFFCANVAS PROFILE SIDEBAR -->
<div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="profileOffcanvas" aria-labelledby="profileOffcanvasLabel" style="width: 380px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold text-dark d-flex align-items-center" id="profileOffcanvasLabel">
            <i class="fa-solid fa-user-circle me-2 text-danger"></i> Profile Info
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <div class="text-center mb-4">
            <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center text-warning fw-bold mb-3 shadow-sm" style="width: 90px; height: 90px; font-size: 2.5rem;">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <h5 class="fw-bold m-0 text-dark">{{ Auth::user()->name ?? 'Pengguna Capstone' }}</h5>
            <p class="text-muted small mb-2">{{ Auth::user()->email ?? 'user@capstone.co.id' }}</p>
            <span class="badge bg-danger text-uppercase px-3 py-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                {{ str_replace('_', ' ', Auth::user()->role ?? 'USER') }}
            </span>
        </div>

        <hr class="my-4 text-muted opacity-25">

        @if($role === 'super_admin')
            <div class="mb-4">
                <span class="text-muted small fw-bold text-uppercase d-block mb-3" style="letter-spacing: 0.5px;">Otoritas & Akses</span>
                <div class="d-flex align-items-start mb-3">
                    <i class="fa-solid fa-shield-halved text-danger me-3 fs-5 mt-1"></i>
                    <div>
                        <span class="fw-bold d-block small text-dark">Akses Utama</span>
                        <span class="text-muted small">Full Control & Approval Hapus Data</span>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <i class="fa-solid fa-user-shield text-primary me-3 fs-5 mt-1"></i>
                    <div>
                        <span class="fw-bold d-block small text-dark">Manajemen Pengguna</span>
                        <span class="text-muted small">Kelola Akun Admin & User</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <span class="text-muted small fw-bold text-uppercase d-block mb-3" style="letter-spacing: 0.5px;">Status Sistem</span>
                <div class="bg-light p-3 rounded-3 border">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">Status Server:</span>
                        <span class="badge bg-success">Online</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">Permintaan Hapus Data:</span>
                        <span class="fw-bold text-dark small">{{ $pendingCount ?? 0 }} Pending</span>
                    </div>
                </div>
            </div>

        @elseif($role === 'admin')
            <div class="mb-4">
                <span class="text-muted small fw-bold text-uppercase d-block mb-3" style="letter-spacing: 0.5px;">Tanggung Jawab Modul</span>
                <div class="d-flex align-items-start mb-3">
                    <i class="fa-solid fa-briefcase text-warning me-3 fs-5 mt-1"></i>
                    <div>
                        <span class="fw-bold d-block small text-dark">Divisi Operasional</span>
                        <span class="text-muted small">Production & Inventory Manager</span>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <i class="fa-solid fa-check-double text-success me-3 fs-5 mt-1"></i>
                    <div>
                        <span class="fw-bold d-block small text-dark">Wewenang Approval</span>
                        <span class="text-muted small">Approve Pengajuan Order & Input Data</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <span class="text-muted small fw-bold text-uppercase d-block mb-3" style="letter-spacing: 0.5px;">Statistik Kerja</span>
                <div class="bg-light p-3 rounded-3 border">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">Pengajuan Butuh Approval:</span>
                        <span class="badge bg-warning text-dark">{{ $pendingCount ?? 0 }} Pengajuan</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">Dokumen Disetujui:</span>
                        <span class="fw-bold text-dark small">0 Minggu Ini</span>
                    </div>
                </div>
            </div>

        @else
            <div class="mb-4">
                <span class="text-muted small fw-bold text-uppercase d-block mb-3" style="letter-spacing: 0.5px;">Informasi Karyawan</span>
                <div class="d-flex align-items-start mb-3">
                    <i class="fa-solid fa-id-badge text-info me-3 fs-5 mt-1"></i>
                    <div>
                        <span class="fw-bold d-block small text-dark">NIP / ID Staff</span>
                        <span class="text-muted small">EMP-2026-089</span>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <i class="fa-solid fa-clock text-secondary me-3 fs-5 mt-1"></i>
                    <div>
                        <span class="fw-bold d-block small text-dark">Shift Kerja</span>
                        <span class="text-muted small">Shift 1 (08.00 - 17.00 WIB)</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <span class="text-muted small fw-bold text-uppercase d-block mb-3" style="letter-spacing: 0.5px;">Aktivitas Pengajuan</span>
                <div class="bg-light p-3 rounded-3 border">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">Pengajuan Pending:</span>
                        <span class="badge bg-info">{{ $pendingCount ?? 0 }} Dokumen</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">Hak Akses Modul:</span>
                        <span class="fw-bold text-dark small">View & Print Only</span>
                    </div>
                </div>
            </div>
        @endif

        <div class="border-top pt-3 mt-4 text-center">
            <p class="text-muted m-0" style="font-size: 0.75rem;">
                <i class="fa-solid fa-lock me-1"></i> Terenkripsi & Terkoneksi Capstone System
            </p>
        </div>
    </div>
</div>

<style>
    .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card-hover:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.08)!important; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartOptions = {
            responsive: true,
            cutout: '60%', 
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }
        };

        new Chart(document.getElementById('chartInventory').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Raw Material', 'Work In Process', 'Finished Goods'],
                datasets: [{
                    data: [{{ $invRaw ?? 0 }}, {{ $invWip ?? 0 }}, {{ $invFg ?? 0 }}],
                    backgroundColor: ['#0d6efd', '#6ea8fe', '#b6d4fe'],
                    borderWidth: 0
                }]
            },
            options: chartOptions
        });

        new Chart(document.getElementById('chartProduction').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Pending / Trouble', 'Running', 'Selesai'],
                datasets: [{
                    data: [{{ $prodPending ?? 0 }}, {{ $prodRunning ?? 0 }}, {{ $prodDone ?? 0 }}], 
                    backgroundColor: ['#ffc107', '#198754', '#75b798'],
                    borderWidth: 0
                }]
            },
            options: chartOptions
        });

        new Chart(document.getElementById('chartResources').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Idle', 'On Leave'],
                datasets: [{
                    data: [{{ $resActive ?? 0 }}, {{ $resIdle ?? 0 }}, {{ $resLeave ?? 0 }}],
                    backgroundColor: ['#0dcaf0', '#6edff6', '#b6effb'],
                    borderWidth: 0
                }]
            },
            options: chartOptions
        });

        new Chart(document.getElementById('chartOrder').getContext('2d'), {
            type: 'pie', 
            data: {
                labels: ['Purchase Order', 'Delivery Order', 'Invoice'],
                datasets: [{
                    data: [{{ $orderPo ?? 0 }}, {{ $orderDo ?? 0 }}, {{ $orderInv ?? 0 }}],
                    backgroundColor: ['#ffc107', '#ffda6a', '#fff3cd'],
                    borderWidth: 0
                }]
            },
            options: chartOptions
        });

        new Chart(document.getElementById('chartRnd').getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Research', 'Prototyping', 'Testing'],
                datasets: [{
                    data: [{{ $rndResearch ?? 0 }}, {{ $rndProto ?? 0 }}, {{ $rndTesting ?? 0 }}],
                    backgroundColor: ['#dc3545', '#ea868f', '#f5c2c7'],
                    borderWidth: 0
                }]
            },
            options: chartOptions
        });
    });
</script>
@endsection