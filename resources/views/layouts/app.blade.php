<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aset IMC - Sistem Inventarisasi Aset')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
            color: white;
            min-height: 100vh;
            padding: 20px;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .sidebar .brand {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 30px;
            padding: 10px;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 15px;
            border-radius: 6px;
            margin: 5px 0;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .topbar {
            background: white;
            padding: 15px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-stat {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .card-stat .icon {
            font-size: 32px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: #f1f5f9;
            border-bottom: 2px solid #e2e8f0;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
                display: flex;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar">
        <div class="brand">
            <i class="fas fa-cube"></i> Aset IMC
        </div>

        <nav class="navbar-nav">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>

            @if(auth()->user()->hasPermission('asset.view'))
            <a href="{{ route('assets.index') }}" class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                <i class="fas fa-cube"></i> Data Aset
            </a>
            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Kategori Aset
            </a>
            <a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i> Lokasi
            </a>
            @endif

            @if(auth()->user()->hasPermission('loan.view'))
            <a href="{{ route('loans.index') }}" class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                <i class="fas fa-handshake"></i> Peminjaman
            </a>
            @endif

            @if(auth()->user()->hasPermission('maintenance.view'))
            <a href="{{ route('maintenance.index') }}" class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i> Perawatan
            </a>
            @endif

            @if(auth()->user()->hasPermission('report.view'))
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-pdf"></i> Laporan
            </a>
            @endif

            <hr style="border-color: rgba(255,255,255,0.2); margin: 20px 0;">

            <a href="{{ route('profile.edit') }}" class="nav-link">
                <i class="fas fa-user-cog"></i> Profil
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="nav-link w-100 text-start" style="border:none; background:none;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h1 class="mb-0">@yield('page-title', 'Dashboard')</h1>
            <div class="d-flex align-items-center gap-3">
                <span>{{ auth()->user()->name }}</span>
                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=3b82f6&color=fff" 
                     alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%;">
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <h5>Terjadi kesalahan:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    @stack('scripts')
</body>
</html>
