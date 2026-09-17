<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources - Capstone Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9; /* Background terang bersih */
            color: #1e293b;
            overflow-x: hidden;
        }

        /* Background Vektor Gear Tipis di Tema Terang */
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

        /* Sidebar Kiri Terang */
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

        /* Sub-Navbar Atas Terang */
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

        /* Tombol Aksi */
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="gear-bg"></div>

    <div class="sidebar">
        <div class="brand">
            <i class="fa-solid fa-cube text-danger me-2"></i> CAPSTONE 2026
        </div>
        
        <div class="sidebar-category">Modules</div>
        
        <!-- List Menu Bersih Tanpa Duplikat -->
        <a href="/dashboard" class="{{ Request::is('dashboard*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
        <a href="/production" class="{{ Request::is('production*') ? 'active' : '' }}">
            <i class="fa-solid fa-industry"></i> Production
        </a>
        <a href="/inventory" class="{{ Request::is('inventory*') ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-stacked"></i> Inventory
        </a>
        <!-- Arahkan href ke URL/Route Hub -->
        <a href="/purchase-delivery" class="nav-link d-flex justify-content-between align-items-center {{ request()->is('purchase-delivery*') ? 'active' : '' }}">
            <div>
                <i class="fa-solid fa-cart-shopping me-2"></i>
                <span>Purchase & Delivery</span>
            </div>
        </a>
        <a href="/rnd" class="{{ Request::is('rnd*') ? 'active' : '' }}">
            <i class="fa-solid fa-flask"></i> RnD
        </a>
        <a href="/resources" class="{{ Request::is('resources*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i> Resources
        </a>

        <!-- Footer / Support Box -->
        <div style="position: absolute; bottom: 20px; width: 100%; padding: 0 20px;">
            <div class="p-3 rounded-3" style="background-color: #f8f9fa; border: 1px solid #eaedf1;">
                <div class="d-flex align-items-center mb-2">
                    <i class="fa-solid fa-headset text-danger me-2"></i>
                    <span class="fw-bold text-dark small">Capstone Support</span>
                </div>
                <p class="text-muted m-0" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-envelope me-1"></i> info@capstone.co.id
                </p>
                <p class="text-muted m-0 mt-1" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-phone me-1"></i> +62 12 3456 789
                </p>
            </div>
        </div>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    <!-- Cukup pakai 1 script Bootstrap terbaru -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>