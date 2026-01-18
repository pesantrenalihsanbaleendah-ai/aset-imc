<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteName = 'Aset IMC - Sistem Inventarisasi Aset';
        $siteFavicon = null;
        $siteLogo = null;

        try {
            $nameSetting = \App\Models\Setting::where('key', 'site_name')->first();
            $faviconSetting = \App\Models\Setting::where('key', 'site_favicon')->first();
            $logoSetting = \App\Models\Setting::where('key', 'site_logo')->first();

            if ($nameSetting && $nameSetting->value) {
                $siteName = $nameSetting->value;
            }

            if ($logoSetting && $logoSetting->value) {
                $siteLogo = $logoSetting->value;
            }

            if ($faviconSetting && $faviconSetting->value) {
                $siteFavicon = $faviconSetting->value;
            } elseif ($siteLogo) {
                // Use logo as favicon if no favicon uploaded
                $siteFavicon = $siteLogo;
            }
        } catch (\Exception $e) {
            // Keep defaults
        }
    @endphp

    <title>@yield('title', $siteName)</title>

    {{-- Dynamic Favicon --}}
    @if($siteFavicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $siteFavicon) }}">
    @else
        <link rel="icon"
            href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📦</text></svg>">
    @endif

    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
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
            max-height: 100vh;
            padding: 20px;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .sidebar .brand {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 30px;
            padding: 10px;
            flex-shrink: 0;
            color: white;
            display: flex;
            align-items: center;
        }

        .sidebar .brand i,
        .sidebar .brand span {
            color: white;
        }

        .sidebar .navbar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 10px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 15px;
            border-radius: 6px;
            margin: 5px 0;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-stat {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
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

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
                max-height: 100vh;
                height: 100vh;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .topbar {
                padding: 15px 20px;
                margin-left: 60px;
            }

            .topbar h1 {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 280px;
                max-height: 100vh;
                height: 100vh;
            }

            .sidebar .brand {
                font-size: 18px;
                margin-bottom: 20px;
            }

            .sidebar .nav-link {
                padding: 10px 12px;
                font-size: 0.9rem;
            }

            .main-content {
                padding: 15px;
            }

            .topbar {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
                padding: 15px;
            }

            .topbar h1 {
                font-size: 1.1rem;
            }

            .card-stat {
                margin-bottom: 15px;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                width: 100%;
                margin: 2px 0;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
            }

            .topbar {
                margin-left: 50px;
            }

            .container-fluid {
                padding: 10px;
            }

            h1.h3 {
                font-size: 1.25rem;
            }

            .card {
                margin-bottom: 15px;
            }

            .form-label {
                font-size: 0.875rem;
            }

            .btn {
                padding: 8px 12px;
                font-size: 0.875rem;
            }
        }

        /* Sidebar Overlay for Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Notification Dropdown Custom Styling */
        .notification-dropdown {
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-radius: 12px;
            padding: 0;
        }

        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
            cursor: pointer;
            text-decoration: none;
            display: block;
            color: #1e293b;
        }

        .notification-item:hover {
            background-color: #f8fafc;
        }

        .notification-item.unread {
            background-color: #f0f7ff;
        }

        .notification-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 10px;
            padding: 2px 5px;
        }
    </style>
    @stack('styles')
</head>

<body>
    {{-- Mobile Menu Toggle --}}
    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    {{-- Sidebar Overlay --}}
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <div class="brand">
            <div style="display: flex; align-items: center; gap: 10px;">
                @if($siteLogo)
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $siteName }}"
                        style="max-height: 35px; width: auto; object-fit: contain;">
                @else
                    <i class="fas fa-cube"></i>
                @endif
                <span style="color: white; font-size: 1.1rem; line-height: 1.2;">{{ $siteName }}</span>
            </div>
        </div>

        <nav class="navbar-nav">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>

            @if(auth()->user()->hasPermission('asset.view'))
                <a href="{{ route('assets.index') }}" class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                    <i class="fas fa-cube"></i> Data Aset
                </a>
                <a href="{{ route('categories.index') }}"
                    class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-list"></i> Kategori Aset
                </a>
                <a href="{{ route('locations.index') }}"
                    class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt"></i> Lokasi
                </a>
            @endif

            @if(auth()->user()->hasPermission('loan.view'))
                <a href="{{ route('loans.index') }}" class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                    <i class="fas fa-handshake"></i> Peminjaman
                </a>
            @endif

            @if(auth()->user()->hasPermission('maintenance.view'))
                <a href="{{ route('maintenance.index') }}"
                    class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                    <i class="fas fa-tools"></i> Perawatan
                </a>
            @endif

            @if(auth()->user()->hasPermission('report.view'))
                <a href="{{ route('reports.index') }}"
                    class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-file-pdf"></i> Laporan
                </a>
            @endif

            @if(auth()->user() && auth()->user()->role && auth()->user()->role->name === 'super_admin')
                <hr style="border-color: rgba(255,255,255,0.2); margin: 20px 0;">

                <div class="text-white-50 small px-3 mb-2">ADMIN</div>

                <a href="{{ route('admin.users.index') }}"
                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Manajemen User
                </a>

                <a href="{{ route('admin.settings.index') }}"
                    class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Pengaturan
                </a>
            @endif

            <hr style="border-color: rgba(255,255,255,0.2); margin: 20px 0;">

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
                
                {{-- Notification Bell --}}
                <div class="dropdown me-3">
                    <button class="btn btn-link link-dark position-relative p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fs-5"></i>
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">Notifikasi</h6>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <a href="#" class="small text-decoration-none">Tandai semua dibaca</a>
                            @endif
                        </div>
                        <div style="max-height: 300px; overflow-y: auto;">
                            @if(isset($notifications) && $notifications->count() > 0)
                                @foreach($notifications as $notif)
                                    <a href="#" class="notification-item {{ !$notif->read ? 'unread' : '' }}">
                                        <div class="d-flex gap-3">
                                            <div class="notification-icon {{ str_contains($notif->type, 'approved') ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                <i class="fas {{ str_contains($notif->type, 'approved') ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold small">{{ $notif->title }}</div>
                                                <div class="text-muted small text-wrap" style="max-width: 220px;">{{ $notif->message }}</div>
                                                <div class="text-muted mt-1" style="font-size: 10px;">{{ $notif->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-bell-slash d-block fs-2 mb-2 opacity-25"></i>
                                    <span class="small">Belum ada notifikasi</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-2 border-top text-center">
                            <a href="#" class="small text-decoration-none">Lihat Semua</a>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=3b82f6&color=fff"
                        alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%;">
                </div>
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

    <script>
        // Toggle Sidebar for Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Close sidebar when clicking on a link (mobile)
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 992) {
                        toggleSidebar();
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>