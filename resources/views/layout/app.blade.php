<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #1a1a2e; }
        .sidebar .nav-link { color: #ccc; padding: 10px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.1); border-radius: 8px; }
        .sidebar .brand { color: #e94560; font-weight: bold; font-size: 1.2rem; }
        .main-content { padding: 20px; }
        .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-radius: 12px; }
        .stat-card { border-left: 4px solid; }
        .table th { background: #f1f3f5; font-weight: 600; }
        .badge-upcoming { background: #17a2b8; }
        .badge-ongoing { background: #28a745; }
        .badge-completed { background: #6c757d; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar py-3 px-2">
            <div class="brand text-center mb-4">
                <i class="bi bi-trophy-fill"></i> Tournament
            </div>
            <nav class="nav flex-column gap-1">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('participants.*') ? 'active' : '' }}" href="{{ route('participants.index') }}">
                    <i class="bi bi-people-fill me-2"></i> Peserta
                </a>
                <a class="nav-link {{ request()->routeIs('tournaments.*') ? 'active' : '' }}" href="{{ route('tournaments.index') }}">
                    <i class="bi bi-trophy me-2"></i> Turnamen
                </a>
                <a class="nav-link {{ request()->routeIs('matches.*') ? 'active' : '' }}" href="{{ route('matches.index') }}">
                    <i class="bi bi-controller me-2"></i> Pertandingan
                </a>
                <hr class="border-secondary">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-danger">
                        <i class="bi bi-box-arrow-left me-2"></i> Logout
                    </button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 main-content">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-2">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-2">
                    <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>