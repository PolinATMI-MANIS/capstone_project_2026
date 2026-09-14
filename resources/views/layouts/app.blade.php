<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources - Capstone Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <a href="#" class="{{ Request::is('production*') ? 'active' : '' }}">
            <i class="fa-solid fa-industry"></i> Production
        </a>
        <a href="#" class="{{ Request::is('inventory*') ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-stacked"></i> Inventory
        </a>
        
        <!-- Menu Resources Utama dengan indikator jumlah approval pending -->
        @php
            $pendingApprovalCount = class_exists('\App\Models\ApprovalRequest') ? \App\Models\ApprovalRequest::count() : 0;
        @endphp
        <a href="{{ route('man-power.index') }}" class="{{ Request::is('man-power*') || Request::is('machine-power*') || Request::is('waiting-resources*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
            <div><i class="fa-solid fa-users-gear"></i> Resources</div>
            @if($pendingApprovalCount > 0 && auth()->check() && auth()->user()->role === 'super_admin')
                <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">{{ $pendingApprovalCount }}</span>
            @endif
        </a>

        <a href="/purchase" class="{{ Request::is('purchase*') || Request::is('delivery*') ? 'active' : '' }}">
            <i class="fa-solid fa-cart-shopping"></i> Order Here !
        </a>
        <a href="#" class="{{ Request::is('rnd*') ? 'active' : '' }}">
            <i class="fa-solid fa-flask"></i> RnD
        </a>

        <!-- Info Profile User yang Sedang Login & Tombol Logout -->
        <div style="position: absolute; bottom: 20px; width: 100%; padding: 0 20px;">
            <div class="p-3 rounded-3 bg-white shadow-sm border">
                @auth
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark small text-truncate" style="max-width: 130px;">{{ auth()->user()->name }}</span>
                        <span class="badge bg-dark font-monospace" style="font-size: 0.55rem;">{{ auth()->user()->role }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>

    </div>

    <div class="main-content">
        <!-- Notifikasi Flash Message Global -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>