@extends('layouts.app')

@section('content')

@php
    $inventoryItems = class_exists('\App\Models\Item') ? \App\Models\Item::all() : [];
    $manPowerList = class_exists('\App\Models\ManPower') ? \App\Models\ManPower::all() : [];
    $machineList = class_exists('\App\Models\MachinePower') ? \App\Models\MachinePower::all() : [];
@endphp

<!-- WADAH UTAMA UNTUK MENCEGAH KONTEN TERDORONG KE BAWAH -->
<div class="container-fluid p-0 m-0 w-100">

    <style>
        .btn-brand-orange { background: linear-gradient(135deg, #ff6600 0%, #e55c00 100%); color: white; font-weight: 600; border-radius: 8px; border: none; box-shadow: 0 4px 12px rgba(255, 102, 0, 0.2); transition: all 0.2s ease; }
        .btn-brand-orange:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(255, 102, 0, 0.3); color: white; }
        .card-custom { border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: none; background: white; }
        .table-custom th { text-transform: uppercase; font-size: 0.75rem; color: #94a3b8; font-weight: 700; letter-spacing: 1px; border-bottom: 2px solid #f1f5f9; padding: 18px 20px; }
        .table-custom td { padding: 18px 20px; vertical-align: middle; color: #475569; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; font-weight: 500;}
        .badge-soft-success { background-color: #dcfce7; color: #166534; font-weight: 600; padding: 8px 12px; }
        .badge-soft-primary { background-color: #e0e7ff; color: #3730a3; font-weight: 600; padding: 8px 12px; }
        .badge-soft-danger { background-color: #fee2e2; color: #991b1b; font-weight: 600; padding: 8px 12px; }
        .badge-soft-warning { background-color: #ffedd5; color: #9a3412; font-weight: 600; padding: 8px 12px; }
        .widget-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    </style>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center" style="background-color: #dcfce7; color: #166534;" role="alert">
            <i class="fa-solid fa-circle-check fs-5 me-3"></i> 
            <div><strong>Berhasil!</strong> {{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center" style="background-color: #fef08a; color: #854d0e;" role="alert">
            <i class="fa-solid fa-triangle-exclamation fs-5 me-3"></i> 
            <div><strong>Perhatian!</strong> {{ session('warning') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-bolder mb-1" style="color: #0f172a; letter-spacing: -0.5px;">Production Directory</h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Hak Akses Saat Ini: <span class="badge bg-dark ms-1 text-uppercase">{{ str_replace('_', ' ', $role ?? 'guest') }}</span>
                | <span class="text-info ms-2 fw-semibold"><i class="fa-solid fa-link me-1"></i> Terhubung dengan Sistem PO</span>
            </p>
        </div>
        <!-- TOMBOL BUAT SPK DIHAPUS TOTAL SESUAI SKEMA MAKE-TO-ORDER BARU -->
    </div>

    <!-- KPI Widgets -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-custom p-4 d-flex flex-row align-items-center">
                <div class="widget-icon bg-light text-secondary me-3"><i class="fa-solid fa-layer-group"></i></div>
                <div>
                    <h4 class="fw-bolder mb-0" style="color:#0f172a;">{{ $totalSpk ?? 0 }}</h4>
                    <span class="text-muted small fw-semibold text-uppercase letter-spacing-1">Total Order Masuk</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-4 d-flex flex-row align-items-center">
                <div class="widget-icon" style="background-color: #e0e7ff; color: #3730a3;"><i class="fa-solid fa-gears"></i></div>
                <div>
                    <h4 class="fw-bolder mb-0" style="color:#0f172a;">{{ $wip ?? 0 }}</h4>
                    <span class="text-muted small fw-semibold text-uppercase letter-spacing-1">Work In Process</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-4 d-flex flex-row align-items-center">
                <div class="widget-icon" style="background-color: #dcfce7; color: #166534;"><i class="fa-solid fa-check-double"></i></div>
                <div>
                    <h4 class="fw-bolder mb-0" style="color:#0f172a;">{{ $selesai ?? 0 }}</h4>
                    <span class="text-muted small fw-semibold text-uppercase letter-spacing-1">Produksi Selesai</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form action="{{ route('produksi.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" name="search" placeholder="Cari No. PO..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Menunggu Approval Admin" {{ request('status') == 'Menunggu Approval Admin' ? 'selected' : '' }}>Menunggu Approval Admin</option>
                        <option value="Menunggu Bahan Baku" {{ request('status') == 'Menunggu Bahan Baku' ? 'selected' : '' }}>Menunggu Bahan Baku</option>
                        <option value="Proses Produksi Berjalan" {{ request('status') == 'Proses Produksi Berjalan' ? 'selected' : '' }}>Proses Produksi Berjalan</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary w-100 fw-semibold">Filter Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Container -->
    <div class="card card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No. PO Ref</th>
                            <th>Produk (Dari PO)</th>
                            <th>Qty Order</th>
                            <th>TH (Target/Jam)</th>
                            <th>Status Produksi</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="fw-bold ps-4" style="color: #0f172a;">
                                    <i class="fa-solid fa-file-invoice text-warning me-1"></i> {{ $order->no_po }}
                                </td>
                                <td>{{ $order->produk }}</td>
                                <td>{{ $order->jumlah_produksi }} Pcs</td>
                                
                                <!-- RUMUS TH DITERAPKAN DI SINI -->
                                <td>
                                    <span class="fw-bold text-primary">{{ ceil($order->jumlah_produksi / 8) }} Pcs</span><small class="text-muted">/jam</small>
                                </td>

                                <td>
                                    @if($order->status == 'Proses Produksi Berjalan') 
                                        <span class="badge rounded-pill badge-soft-primary">{{ $order->status }}</span>
                                    @elseif($order->status == 'Selesai') 
                                        <span class="badge rounded-pill badge-soft-success">{{ $order->status }}</span>
                                    @elseif($order->status == 'Menunggu Approval Admin' || $order->status == 'Menunggu Dihapus') 
                                        <span class="badge rounded-pill badge-soft-warning">{{ $order->status }}</span>
                                    @else 
                                        <span class="badge rounded-pill badge-soft-danger">{{ $order->status }}</span> 
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm">
                                        
                                        <!-- HAK AKSES ADMIN PRODUKSI -->
                                        @if(isset($role) && $role == 'admin')
                                            @if($order->status == 'Menunggu Approval Admin')
                                                <form action="{{ route('produksi.approve_spk', $order->id) }}" method="POST" class="d-inline m-0 p-0">
                                                    @csrf 
                                                    <button type="submit" class="btn btn-sm btn-success fw-semibold border rounded-start"><i class="fa-solid fa-check me-1"></i> Terima Order</button>
                                                </form>
                                                <form action="{{ route('produksi.reject_spk', $order->id) }}" method="POST" class="d-inline m-0 p-0">
                                                    @csrf 
                                                    <button type="submit" class="btn btn-sm btn-danger fw-semibold border"><i class="fa-solid fa-xmark me-1"></i> Tolak Order</button>
                                                </form>
                                            @elseif($order->status == 'Menunggu Bahan Baku')
                                                <!-- TOMBOL MULAI PLOTTING RESOURCES -->
                                                <button class="btn btn-sm btn-light border fw-semibold text-primary" data-bs-toggle="modal" data-bs-target="#checkModal{{ $order->id }}">
                                                    <i class="fa-solid fa-users-gear me-1"></i> Plotting & Eksekusi
                                                </button>
                                            @elseif($order->status == 'Proses Produksi Berjalan')
                                                <button class="btn btn-sm btn-light border fw-semibold text-danger" data-bs-toggle="modal" data-bs-target="#andonModal{{ $order->id }}">
                                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                                </button>
                                                <button class="btn btn-sm btn-light border fw-semibold text-success" data-bs-toggle="modal" data-bs-target="#finalQcModal{{ $order->id }}">
                                                    <i class="fa-solid fa-shield-halved me-1"></i> Final QC
                                                </button>
                                            @elseif($order->status == 'Pending - Trouble')
                                                <form action="{{ route('produksi.resolve', $order->id) }}" method="POST" class="d-inline m-0 p-0">
                                                    @csrf 
                                                    <button type="submit" class="btn btn-sm btn-warning fw-semibold border"><i class="fa-solid fa-wrench me-1"></i> Lanjut Produksi</button>
                                                </form>
                                            @endif
                                            
                                            <!-- Tombol Request Hapus -->
                                            @if($order->status != 'Menunggu Dihapus')
                                            <form action="{{ route('produksi.request_delete', $order->id) }}" method="POST" class="d-inline m-0 p-0">
                                                @csrf 
                                                <button type="submit" onclick="return confirm('Minta persetujuan Super Admin untuk menghapus ini?')" class="btn btn-sm btn-light border text-danger" title="Minta Hapus">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif

                                        <!-- HAK AKSES SUPER ADMIN -->
                                        @if(isset($role) && $role == 'super_admin' && $order->status == 'Menunggu Dihapus')
                                            <form action="{{ route('produksi.approve_delete', $order->id) }}" method="POST" class="d-inline m-0 p-0">
                                                @csrf 
                                                <button type="submit" onclick="return confirm('Hapus data ini secara permanen?')" class="btn btn-sm btn-danger fw-semibold border">
                                                    <i class="fa-solid fa-trash me-1"></i> Setuju Hapus
                                                </button>
                                            </form>
                                        @endif

                                        <!-- TOMBOL VIEW DETAIL -->
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#detailModal{{ $order->id }}" title="Lihat Detail">
                                            <i class="fa-solid fa-eye text-dark"></i>
                                        </button>
                                    </div>

                                    <!-- ============================== -->
                                    <!-- AREA MODAL-MODAL TERSEMBUNYI -->
                                    <!-- ============================== -->

                                    @if(isset($role) && $role == 'admin')
                                        <!-- MODAL PLOTTING RESOURCES (EKSEKUSI PRODUKSI) -->
                                        @if($order->status == 'Menunggu Bahan Baku')
                                        <div class="modal fade text-start" id="checkModal{{ $order->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('produksi.material.request', $order->id) }}" method="POST">
                                                        @csrf 
                                                        <div class="modal-header border-0">
                                                            <h5 class="modal-title fw-bold">Eksekusi Order & Plotting</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="alert alert-warning border-0 small mb-4">
                                                                <i class="fa-solid fa-circle-info me-1"></i> Verifikasi ketersediaan material di Inventory sebelum memulai produksi.
                                                            </div>

                                                            <div class="row g-3 mb-4">
                                                                <!-- VERIFIKASI INVENTORY -->
                                                                <div class="col-md-7">
                                                                    <label class="form-label small fw-bold text-primary">Verifikasi Material (Inventory)</label>
                                                                    <select class="form-select bg-light border-primary shadow-sm" name="item_code" required>
                                                                        <option value="">-- Pastikan Material Ada --</option>
                                                                        @foreach($inventoryItems as $item)
                                                                            @php
                                                                                $kode = $item->item_code ?? $item->kode_barang ?? $item->kode ?? '-';
                                                                                $nama = $item->name ?? $item->nama_barang ?? $item->nama ?? '-';
                                                                                $stok = $item->stock ?? $item->stok ?? $item->stok_saat_ini ?? 0;
                                                                                $isCukup = $stok >= $order->jumlah_produksi;
                                                                            @endphp
                                                                            <option value="{{ $kode }}" {{ !$isCukup ? 'disabled' : '' }} class="{{ !$isCukup ? 'text-danger' : 'text-success fw-semibold' }}">
                                                                                {{ $kode }} - {{ $nama }} (Stok: {{ $stok }}) {{ !$isCukup ? '[KURANG]' : '' }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-5">
                                                                    <label class="form-label small text-muted">Kebutuhan PO</label>
                                                                    <input type="text" class="form-control bg-light text-danger fw-bold" value="{{ $order->jumlah_produksi }} Pcs" readonly>
                                                                    <input type="hidden" name="qty_required" value="{{ $order->jumlah_produksi }}">
                                                                </div>
                                                            </div>

                                                            <h6 class="fw-bold mb-3 fs-6 border-top pt-3">Plotting Mesin & Operator</h6>
                                                            
                                                            <!-- Input Hidden untuk bypass error system lama -->
                                                            <input type="hidden" name="is_material_ready" value="1">
                                                            <input type="hidden" name="is_machine_ready" value="1">

                                                            <!-- INTEGRASI DROPDOWN MESIN DARI RESOURCES -->
                                                            <div class="mb-3">
                                                                <label class="form-label small mb-1 fw-bold">Pilih Mesin Produksi</label>
                                                                <select name="mesin_id" class="form-select shadow-sm" required>
                                                                    <option value="">-- Pilih Mesin --</option>
                                                                    @forelse($machineList as $mc)
                                                                        @php
                                                                            $namaMesin = $mc->nama_mesin ?? $mc->kode_mesin ?? $mc->nama ?? 'Mesin ' . $mc->id;
                                                                            $statusMesin = $mc->status ?? 'Active';
                                                                            $isRusak = strtolower($statusMesin) == 'maintenance' || strtolower($statusMesin) == 'rusak';
                                                                        @endphp
                                                                        <option value="{{ $namaMesin }}" {{ $isRusak ? 'disabled' : '' }} class="{{ $isRusak ? 'text-danger' : 'text-success' }}">
                                                                            {{ $namaMesin }} ({{ $statusMesin }})
                                                                        </option>
                                                                    @empty
                                                                        <option value="Mesin Default">Mesin Default</option>
                                                                    @endforelse
                                                                </select>
                                                            </div>

                                                            <!-- INTEGRASI DROPDOWN OPERATOR DARI RESOURCES -->
                                                            <div class="mb-2">
                                                                <label class="form-label small mb-1 fw-bold">Pilih Operator Bertugas</label>
                                                                <select name="operator_name" class="form-select shadow-sm" required>
                                                                    <option value="">-- Pilih Operator --</option>
                                                                    @forelse($manPowerList as $mp)
                                                                        @php
                                                                            $namaPekerja = $mp->nama_pekerja ?? $mp->nama ?? $mp->name ?? 'Pekerja ' . $mp->id;
                                                                            $statusMp = $mp->status ?? 'Active';
                                                                            $isCuti = strtolower($statusMp) == 'on leave' || strtolower($statusMp) == 'cuti' || strtolower($statusMp) == 'inactive';
                                                                        @endphp
                                                                        <option value="{{ $namaPekerja }}" {{ $isCuti ? 'disabled' : '' }} class="{{ $isCuti ? 'text-danger' : 'text-success' }}">
                                                                            {{ $namaPekerja }} ({{ $statusMp }})
                                                                        </option>
                                                                    @empty
                                                                        @if(isset($operators) && count($operators) > 0)
                                                                            @foreach($operators as $op)
                                                                                <option value="{{ $op->nama_pekerja ?? 'Operator' }}">{{ $op->nama_pekerja ?? 'Operator' }}</option>
                                                                            @endforeach
                                                                        @else
                                                                            <option value="Operator Default">Operator Default</option>
                                                                        @endif
                                                                    @endforelse
                                                                </select>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="submit" class="btn btn-primary px-4 w-100 py-2 rounded-2 fw-bold">Mulai Proses Produksi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <!-- Modal Final QC & Andon -->
                                        @if($order->status == 'Proses Produksi Berjalan')
                                        <div class="modal fade text-start" id="finalQcModal{{ $order->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('produksi.final.qc', $order->id) }}" method="POST">
                                                        @csrf 
                                                        <div class="modal-header border-0">
                                                            <h5 class="modal-title fw-bold">Laporan Final QC</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="alert alert-light border mb-4"><strong>Target Produksi:</strong> {{ $order->jumlah_produksi }} Pcs</div>
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-bold text-success small">Good Product (Lolos)</label>
                                                                    <input type="number" class="form-control" name="good_qty" required placeholder="Contoh: {{ $order->jumlah_produksi }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-bold text-danger small">Reject / Cacat</label>
                                                                    <input type="number" class="form-control" name="reject_qty" required value="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label fw-semibold small text-muted">Catatan QC</label>
                                                                <textarea class="form-control bg-light" name="catatan_qc" rows="2"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="submit" class="btn btn-success px-4 fw-bold w-100 py-2">Selesaikan SPK</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="modal fade text-start" id="andonModal{{ $order->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('produksi.trouble', $order->id) }}" method="POST">
                                                        @csrf 
                                                        <div class="modal-header border-0 pb-0">
                                                            <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Lapor Trouble (Andon)</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-2">
                                                                <label class="form-label fw-bold small">Pilih Alasan Kendala</label>
                                                                <select name="alasan_trouble" class="form-select bg-light mb-2" required>
                                                                    <option value="Mesin Rusak / Maintenance">Mesin Rusak / Maintenance</option>
                                                                    <option value="Material Baku Cacat/Kurang">Material Baku Cacat/Kurang</option>
                                                                    <option value="Kecelakaan Kerja">Kecelakaan Kerja</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="submit" class="btn btn-danger px-4 fw-bold w-100 py-2">Hentikan Produksi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endif

                                    <!-- Modal View Detail -->
                                    <div class="modal fade text-start" id="detailModal{{ $order->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold" style="color:#0f172a;">Detail Order Produksi</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <span class="badge bg-warning text-dark mb-2">Ref PO: {{ $order->no_po }}</span>
                                                    <h5 class="fw-bold mb-0 mt-1">{{ $order->produk }}</h5>
                                                    <p class="text-muted small mb-3">Target Produksi: {{ $order->jumlah_produksi }} Pcs</p>
                                                    <div class="text-start p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.85rem; white-space: pre-wrap; color: #475569; max-height: 150px; overflow-y: auto;">
                                                        <strong>Riwayat Status & Catatan:</strong><br>{{ $order->keterangan ?? 'Belum ada catatan.' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-inline-flex justify-content-center align-items-center rounded-circle mb-3" style="width: 80px; height: 80px; background-color: #f1f5f9;">
                                        <i class="fa-solid fa-inbox fs-2 text-muted"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1" style="color:#0f172a;">Belum Ada Order dari PO</h6>
                                    <p class="text-muted small">Pesanan akan otomatis muncul di sini setelah dibuat dari modul Purchase Order.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

</div>
<!-- AKHIR WADAH UTAMA -->

@endsection