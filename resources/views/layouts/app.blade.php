<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capstone Project - Industrial System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            overflow-x: hidden;
        }

        .gear-bg {
            position: fixed;
            top: 0;
            left: 260px;
            width: calc(100vw - 260px);
            height: 100vh;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1000 1000' width='1000' height='1000'%3E%3Cg fill='%23ff6600' fill-opacity='0.03'%3E%3Cpath d='M750 200l24-48 44 16 16 44 48 24-8 48 36 36-48 8-24 44-32 40 16 48-44 24-16 48-48 16-24-44-44 16-36 36 8 48-48 24-16-44-44-16-24 48-48-24 8-48-36-36 48-8 24-44 32-40-16-48 44-24 16-48 48-16 24 44 44-16 36-36-8-48 48-24 16 44z'/%3E%3Ccircle cx='750' cy='330' r='120' fill='%23f1f5f9'/%3E%3Ccircle cx='750' cy='330' r='120' stroke='%23ff6600' stroke-width='16' fill='none' stroke-opacity='0.1'/%3E%3Ccircle cx='750' cy='330' r='45' fill='%23ff6600' fill-opacity='0.1'/%3E%3Cpath d='M300 550l18-36 33 12 12 33 36 18-6 36 27 27 36-6 18 33-24 30 12 36-33 18-12 36-36 12-18-33-33 12-27 27 6 36-36 18-12-33-33-12-18 36-36-18 6-36-27-27-36 6-18-33 24-30-12-36 33-18 12-36 36-12 18 33 33-12 27-27-6-36 36-18z'/%3E%3Ccircle cx='300' cy='680' r='90' fill='%23f1f5f9'/%3E%3Ccircle cx='300' cy='680' r='90' stroke='%23ff6600' stroke-width='12' fill='none' stroke-opacity='0.1'/%3E%3Ccircle cx='300' cy='680' r='35' fill='%23ff6600' fill-opacity='0.1'/%3E%3C/g%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right bottom;
            z-index: -1;
        }

        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            padding-top: 25px;
            z-index: 100;
            box-shadow: 4px 0 15px rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
        }

        .sidebar .brand {
            font-size: 1.2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 35px;
            color: #0f172a;
            letter-spacing: 1px;
        }

        .sidebar-category {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            padding: 10px 25px;
        }

        .sidebar a {
            padding: 12px 25px;
            text-decoration: none;
            font-size: 0.9rem;
            color: #64748b;
            display: block;
            transition: 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar a:hover, .sidebar a.active {
            color: #ff6600;
            background-color: #fff7ed;
            border-left: 3px solid #ff6600;
            font-weight: 600;
        }

        .sidebar a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px;
        }

        .sub-navbar {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 6px;
            border-radius: 12px;
            display: inline-flex;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .sub-nav-btn {
            color: #64748b;
            text-decoration: none;
            padding: 8px 20px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sub-nav-btn.active, .sub-nav-btn:hover {
            background-color: #ff6600;
            color: #ffffff;
        }

        .btn-machine {
            background-color: #ff6600;
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 22px;
            font-size: 0.85rem;
            border: none;
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(255, 102, 0, 0.2);
        }
        .btn-machine:hover {
            background-color: #e55c00;
            transform: translateY(-2px);
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="gear-bg"></div>

    <div class="sidebar">
        <div class="brand">
            <i class="fa-solid fa-cube text-danger me-2"></i> CAPSTONE 2026
        </div>
        <div class="sidebar-category">Modules</div>
        
        <a href="/dashboard" class="{{ Request::is('dashboard*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
        
        <a href="{{ Route::has('produksi.index') ? route('produksi.index') : '/produksi' }}" class="{{ request()->routeIs('produksi.*') || Request::is('produksi*') ? 'active' : '' }}">
            <i class="fa-solid fa-industry"></i> Production
        </a>
        
        <a href="/inventory" class="{{ Request::is('inventory*') ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-stacked"></i> Inventory
        </a>

        @php
            $pendingApprovalCount = 0;
            try {
                if (class_exists('\App\Models\ApprovalRequest') && \Illuminate\Support\Facades\Schema::hasTable('approval_requests')) {
                    $pendingApprovalCount = \App\Models\ApprovalRequest::where('status', 'pending')->count();
                }
            } catch (\Exception $e) {
                $pendingApprovalCount = 0;
            }
        @endphp

        <a href="{{ Route::has('man-power.index') ? route('man-power.index') : '#' }}" class="{{ request()->routeIs('man-power.*') || request()->is('man-power*') || request()->is('machine*') ? 'active' : '' }}">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div><i class="fa-solid fa-users-gear"></i> Resources</div>
                @if($pendingApprovalCount > 0)
                    <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">{{ $pendingApprovalCount }}</span>
                @endif
            </div>
        </a>
        
        <a href="/purchase" class="{{ Request::is('purchase*') || Request::is('delivery*') ? 'active' : '' }}">
            <i class="fa-solid fa-cart-shopping"></i> Order Here !
        </a>
        
        <a href="{{ Route::has('rnd.index') ? route('rnd.index') : '/rnd' }}" class="{{ request()->routeIs('rnd.*') || Request::is('rnd*') ? 'active' : '' }}">
            <i class="fa-solid fa-flask"></i> RnD
        </a>

        <!-- MENU LAPORAN OPERASIONAL -->
        <a href="{{ Route::has('reports.index') ? route('reports.index') : '#' }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-lines"></i> Laporan Operasional
        </a>

        <div class="mt-auto px-3 pb-3">
            <div class="p-3 rounded-4 bg-white shadow-sm border border-secondary border-opacity-25">
                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; font-size: 0.8rem;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div style="overflow: hidden;">
                            <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 0.75rem;">{{ Auth::user()->name ?? 'Guest' }}</h6>
                            <span class="badge bg-dark text-uppercase font-monospace" style="font-size: 0.45rem;">{{ str_replace('_', ' ', Auth::user()->role ?? 'user') }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-1">
                    <a href="{{ route('login') }}" class="btn btn-sm btn-light border fw-semibold text-dark text-start d-flex align-items-center justify-content-between py-1 px-2" style="font-size: 0.7rem;">
                        <span><i class="fa-solid fa-user-gear text-warning me-1"></i> Ganti Akun</span>
                        <i class="fa-solid fa-arrow-right-to-bracket text-muted" style="font-size: 0.6rem;"></i>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-light border fw-semibold text-danger text-start d-flex align-items-center justify-content-between py-1 px-2 w-100" style="font-size: 0.7rem;">
                            <span><i class="fa-solid fa-power-off text-danger me-1"></i> Logout</span>
                            <i class="fa-solid fa-right-from-bracket text-danger" style="font-size: 0.6rem;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>