<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen - Capstone Project</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- SweetAlert2 CSS / JS CDN (Wajib untuk Toast Notifikasi) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #1e293b;
            overflow-x: hidden;
            min-height: 100vh;
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
            pointer-events: none;
        }

        /* Glassmorphism Sidebar - Bening & Transparan */
        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-right: 1px solid rgba(255, 255, 255, 0.6);
            padding-top: 25px;
            z-index: 100;
            box-shadow: 8px 0 32px 0 rgba(31, 38, 135, 0.04);
            overflow-y: auto;
        }

        .sidebar .brand {
            font-size: 1.2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 25px;
            color: #0f172a;
            letter-spacing: 1px;
        }

        .sidebar-category {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            padding: 10px 25px 5px 25px;
            font-weight: 700;
        }

        /* Styling Menu Utama dengan Efek Kaca Bening saat di-hover */
        .sidebar a {
            padding: 12px 20px;
            text-decoration: none;
            font-size: 0.9rem;
            color: #334155;
            display: block;
            transition: all 0.3s ease;
            border-radius: 10px;
            margin: 5px 14px;
            position: relative;
            font-weight: 500;
            border: 1px solid transparent;
        }

        /* Efek Hover Kaca Bening Bersih */
        .sidebar a:hover, .sidebar a.active {
            color: #ff6600;
            background: rgba(255, 255, 255, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            transform: translateX(3px);
            font-weight: 600;
        }

        .sidebar a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        /* Styling Sub-menu */
        .sidebar .submenu {
            padding-left: 35px;
            font-size: 0.85rem;
            background: rgba(255, 255, 255, 0.2);
            margin: 3px 10px 3px 18px;
            border-radius: 8px;
            color: #475569;
        }

        .sidebar .submenu:hover, .sidebar .submenu.active {
            color: #ff6600 !important;
            background: rgba(255, 255, 255, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.8);
            transform: translateX(3px);
        }

        /* Rotasi panah dropdown */
        [data-bs-toggle="collapse"] .fa-chevron-down {
            transition: transform 0.3s ease;
        }
        [data-bs-toggle="collapse"].collapsed .fa-chevron-down {
            transform: rotate(-90deg);
        }

        .main-content {
            margin-left: 260px;
            padding: 40px;
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

        <!-- Production -->
        <a href="#" class="{{ request()->is('production*') ? 'active' : '' }}">
            <i class="fa-solid fa-industry"></i> Production
        </a>

        <!-- Inventory (Dropdown Utama dengan Panah) -->
        <a href="#inventoryDropdown" data-bs-toggle="collapse" 
           class="d-flex justify-content-between align-items-center {{ request()->is('inventory*') ? 'active' : '' }}" 
           aria-expanded="{{ request()->is('inventory*') ? 'true' : 'false' }}">
            <div>
                <i class="fa-solid fa-boxes-stacked"></i> Inventory
            </div>
            <i class="fa-solid fa-chevron-down small"></i>
        </a>
        
        <!-- Isi Sub-menu Inventory -->
        <div class="collapse {{ request()->is('inventory*') ? 'show' : '' }}" id="inventoryDropdown">
            <!-- 1. Produksi (Bahan Baku) -->
            <a href="{{ Route::has('inventory.produksi.index') ? route('inventory.produksi.index') : '#' }}" class="submenu {{ request()->is('inventory/produksi') && !request()->is('*/requests*') ? 'active' : '' }}">
                <i class="fa-solid fa-gears"></i> Produksi 
            </a>

            <!-- Menu Request & Approval -->
            <a href="{{ Route::has('inventory.produksi.requests.index') ? route('inventory.produksi.requests.index') : '#' }}" class="submenu {{ request()->is('inventory/produksi/requests*') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-check"></i> Request & Approval
            </a>
            
            <!-- 2. Purchase Order (Barang Jadi) -->
            <a href="{{ Route::has('inventory.po.index') ? route('inventory.po.index') : '#' }}" class="submenu {{ request()->is('inventory/po*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice"></i> Purchase Order
            </a>
        </div>

        <!-- Resources -->
        <a href="{{ Route::has('man-power.index') ? route('man-power.index') : '#' }}" class="{{ request()->is('man-power*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i> Resources
        </a>

        <!-- Purchase Order -->
        <a href="{{ Route::has('inventory.po.index') ? route('inventory.po.index') : '#' }}" class="{{ request()->is('purchase-order*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar"></i> Purchase Order
        </a>

        <!-- RnD -->
        <a href="#" class="{{ request()->is('rnd*') ? 'active' : '' }}">
            <i class="fa-solid fa-flask"></i> RnD
        </a>
    </div>

    <div class="main-content">
        {{-- Panggil Komponen Notifikasi Toast Otomatis di Sini (Gunakan Blade Component jika ada, atau fallback alert aman) --}}
        @if(View::exists('components.notification'))
            <x-notification />
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>