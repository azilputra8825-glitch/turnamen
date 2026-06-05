<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a1a2e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Tournament System - @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #0f172a; /* Sleek Slate 900 */
            --sidebar-hover: rgba(255, 255, 255, 0.06);
            --accent: #6366f1; /* Premium Indigo 500 */
            --accent-glow: rgba(99, 102, 241, 0.15);
            --danger-accent: #f43f5e; /* Rose 500 */
            --card-border: rgba(226, 232, 240, 0.8);
            --bg-main: #f8fafc; /* Slate 50 */
        }

        * { 
            box-sizing: border-box; 
            font-family: 'Outfit', 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: #334155;
            margin: 0;
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ─── SIDEBAR ─────────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #020617 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            padding: 24px 16px;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1), box-shadow 0.3s;
            overflow-y: auto;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar .brand {
            color: #fff;
            font-weight: 800;
            font-size: 1.35rem;
            text-align: left;
            padding: 8px 12px 24px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar .brand i {
            background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.5rem;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            padding: 11px 16px;
            border-radius: 10px;
            font-size: .925rem;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
            border: 1px solid transparent;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: var(--sidebar-hover);
            transform: translateX(3px);
        }

        .sidebar .nav-link.active {
            background: var(--accent-glow);
            color: #a5b4fc;
            border: 1px solid rgba(99, 102, 241, 0.2);
            font-weight: 600;
        }
        
        .sidebar .nav-link.active i {
            color: #818cf8;
        }

        /* ─── OVERLAY (mobile) ────────────────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            z-index: 1039;
            backdrop-filter: blur(4px);
        }

        .sidebar-overlay.show { display: block; }

        /* ─── TOPBAR (mobile only) ────────────────────────────────── */
        .topbar {
            display: none;
            position: sticky;
            top: 0;
            z-index: 1030;
            background: #0f172a;
            padding: 12px 18px;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .topbar .brand { 
            color: #fff; 
            font-weight: 800; 
            font-size: 1.15rem; 
            flex: 1; 
            letter-spacing: -0.5px;
        }

        #sidebarToggle {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            font-size: 1.25rem;
            padding: 6px 10px;
            border-radius: 8px;
            cursor: pointer;
            line-height: 1;
            transition: all 0.2s;
        }

        #sidebarToggle:hover { 
            color: #fff; 
            background: rgba(255, 255, 255, 0.1);
        }

        /* ─── MAIN CONTENT ────────────────────────────────────────── */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 40px 48px;
            transition: margin-left 0.3s ease;
        }

        /* ─── CARDS ───────────────────────────────────────────────── */
        .card {
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
            border-radius: 16px;
            background-color: #fff;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ─── USER INFO BOX ───────────────────────────────────────── */
        .sidebar-user {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }
        
        .sidebar-user .badge {
            font-size: 0.72rem !important;
            padding: 5px 8px;
            border-radius: 6px;
            font-weight: 600;
        }

        /* ─── TABLES ──────────────────────────────────────────────── */
        .table {
            vertical-align: middle;
        }
        
        .table th {
            font-weight: 600;
            color: #475569;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
        }
        
        .table td {
            padding: 14px 16px;
            color: #334155;
            font-size: 0.925rem;
            border-bottom: 1px solid #f1f5f9;
        }

        /* ─── BUTTONS ─────────────────────────────────────────────── */
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.25);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(99, 102, 241, 0.35);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(16, 185, 129, 0.3);
        }

        /* ─── RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .sidebar.open {
                transform: translateX(0);
                box-shadow: 10px 0 30px rgba(15, 23, 42, 0.35);
            }

            .topbar { display: flex; }

            .main-content {
                margin-left: 0;
                padding: 24px 16px 40px;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            :root { --sidebar-width: 220px; }
            .main-content { padding: 30px 24px 40px; }
        }

        /* ─── TABLE RESPONSIVE IMPROVEMENTS ──────────────────────── */
        .table-responsive { 
            border-radius: 12px; 
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        /* ─── RESPONSIVE HEADER ROW ───────────────────────────────── */
        @media (max-width: 575.98px) {
            .page-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 12px !important;
            }

            .page-header .btn-group-actions {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .page-header .btn-group-actions .btn {
                flex: 1 1 auto;
                min-width: 120px;
                font-size: .85rem;
            }
        }

        /* ─── STAT CARDS ──────────────────────────────────────────── */
        .stat-card {
            transition: all .25s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        /* ─── TOUCH FRIENDLY ──────────────────────────────────────── */
        @media (max-width: 767.98px) {
            .sidebar .nav-link {
                min-height: 46px;
                display: flex;
                align-items: center;
            }

            .sidebar, .topbar { -webkit-user-select: none; user-select: none; }
            body { -webkit-text-size-adjust: 100%; }
            .alert { font-size: .9rem; border-radius: 12px; }

            .card[style*="max-width"] {
                max-width: 100% !important;
            }

            .btn { min-height: 42px; }
        }

        /* ─── SAFE AREA (iPhone X notch) ──────────────────────────── */
        @supports (padding: max(0px)) {
            .main-content {
                padding-left: max(48px, env(safe-area-inset-left));
                padding-right: max(48px, env(safe-area-inset-right));
            }

            @media (max-width: 767.98px) {
                .main-content {
                    padding-left: max(16px, env(safe-area-inset-left));
                    padding-right: max(16px, env(safe-area-inset-right));
                    padding-bottom: max(40px, env(safe-area-inset-bottom));
                }

                .topbar {
                    padding-top: max(12px, env(safe-area-inset-top));
                }
            }
        }

        /* ─── PRINT HIDE SIDEBAR ──────────────────────────────────── */
        @media print {
            .sidebar, .topbar, .sidebar-overlay { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body>

{{-- Mobile Topbar --}}
<div class="topbar" id="topbar">
    <button id="sidebarToggle" aria-label="Toggle menu">
        <i class="bi bi-list"></i>
    </button>
    <span class="brand"><i class="bi bi-trophy-fill me-1"></i> Tournament</span>
    @auth
    <span class="text-muted small text-truncate" style="max-width:100px;">{{ auth()->user()->name }}</span>
    @endauth
</div>

{{-- Sidebar Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- Sidebar --}}
<div class="sidebar" id="sidebar">
    <div class="brand">
        <i class="bi bi-trophy-fill"></i> Tournament
    </div>

    @auth
    <div class="sidebar-user mb-2">
        <div class="text-white fw-semibold small text-truncate">{{ auth()->user()->name }}</div>
        @if(auth()->user()->isAdmin())
            <span class="badge mt-1" style="background:#e94560;font-size:.65rem;">
                <i class="bi bi-shield-fill me-1"></i>Admin
            </span>
        @else
            <span class="badge bg-secondary mt-1" style="font-size:.65rem;">
                <i class="bi bi-eye-fill me-1"></i>Viewer
            </span>
        @endif
    </div>
    @endauth

    <nav class="nav flex-column gap-1 flex-grow-1">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('participants.*') ? 'active' : '' }}"
           href="{{ route('participants.index') }}">
            <i class="bi bi-people-fill me-2"></i> Peserta
        </a>
        <a class="nav-link {{ request()->routeIs('tournaments.*') ? 'active' : '' }}"
           href="{{ route('tournaments.index') }}">
            <i class="bi bi-trophy me-2"></i> Turnamen
        </a>
        <a class="nav-link {{ request()->routeIs('matches.*') ? 'active' : '' }}"
           href="{{ route('matches.index') }}">
            <i class="bi bi-controller me-2"></i> Pertandingan
        </a>

        @auth
        @if(auth()->user()->isAdmin())
        <hr class="border-secondary my-1">
        <small class="text-muted px-2" style="font-size:.65rem;letter-spacing:1px;">ADMIN</small>
        <a class="nav-link {{ request()->routeIs('participants.create') ? 'active' : '' }}"
           href="{{ route('participants.create') }}">
            <i class="bi bi-person-plus me-2"></i> Tambah Peserta
        </a>
        <a class="nav-link {{ request()->routeIs('tournaments.create') ? 'active' : '' }}"
           href="{{ route('tournaments.create') }}">
            <i class="bi bi-plus-circle me-2"></i> Buat Turnamen
        </a>
        @endif
        @endauth
    </nav>

    <div class="mt-auto">
        <hr class="border-secondary">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link btn btn-link text-danger w-100 text-start py-2">
                <i class="bi bi-box-arrow-left me-2"></i> Logout
            </button>
        </form>
    </div>
</div>

{{-- Main Content --}}
<div class="main-content" id="mainContent">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <strong>Terdapat {{ $errors->count() }} kesalahan pada input Anda:</strong>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        const toggle  = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (toggle)  toggle.addEventListener('click', openSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);

        // Close sidebar on nav link click (mobile)
        sidebar.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) closeSidebar();
            });
        });

        // Handle resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) closeSidebar();
        });
    })();
</script>
@stack('scripts')
</body>
</html>