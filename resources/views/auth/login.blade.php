<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Tournament System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #020617 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', 'Segoe UI', system-ui, sans-serif;
            padding: 1.5rem;
        }
        .login-card {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        @media (max-width: 480px) {
            .login-card { padding: 2rem 1.25rem; }
        }
        .brand-logo {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f8fafc;
            border-radius: 10px;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #6366f1;
            color: #fff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }
        .form-label { color: #94a3b8; font-size: .85rem; font-weight: 500; letter-spacing: .4px; }
        .btn-login {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            letter-spacing: .5px;
            padding: 11px;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            transition: all 0.2s ease;
        }
        .btn-login:hover { 
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
        }
        .divider { border-color: rgba(255, 255, 255, 0.08); }
        .text-muted-custom { color: #94a3b8 !important; }
        a { color: #818cf8; text-decoration: none; font-weight: 500; }
        a:hover { text-decoration: underline; color: #a5b4fc; }
        .input-icon { position: relative; }
        .input-icon i {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #64748b; font-size: 1rem; pointer-events: none;
        }
        .input-icon .form-control { padding-left: 2.5rem; }
        .form-check-input {
            background-color: rgba(15, 23, 42, 0.6);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .form-check-input:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }
    </style>
</head>
<body>

<div class="login-card">
    {{-- Brand --}}
    <div class="text-center mb-4">
        <div class="brand-logo"><i class="bi bi-trophy-fill"></i></div>
        <h5 class="text-white fw-bold mt-2 mb-0">Tournament System</h5>
        <p class="text-muted-custom small mt-1">Masuk untuk mengelola turnamen</p>
    </div>

    {{-- Session status --}}
    @if(session('status'))
        <div class="alert alert-info py-2 small">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2 small">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-icon">
                <i class="bi bi-envelope"></i>
                <input type="email" id="email" name="email"
                       class="form-control"
                       value="{{ old('email') }}"
                       placeholder="admin@turnamen.com"
                       required autofocus>
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-icon">
                <i class="bi bi-lock"></i>
                <input type="password" id="password" name="password"
                       class="form-control"
                       placeholder="••••••••"
                       required>
            </div>
        </div>

        {{-- Remember + Forgot --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label text-muted-custom small" for="remember">Ingat saya</label>
            </div>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-login btn-danger w-100 py-2">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
    </form>

    <hr class="divider my-4">

    <p class="text-center text-muted-custom small mb-0">
        Belum punya akun?
        <a href="{{ route('register') }}">Daftar sekarang</a>
    </p>

    {{-- Demo credentials --}}
    <div class="mt-3 p-2 rounded" style="background:#21262d;border:1px solid #30363d;">
        <p class="text-muted-custom small mb-1 fw-semibold">🔑 Akun Demo:</p>
        <p class="text-muted-custom small mb-0">
            <strong class="text-warning">Admin:</strong> admin@turnamen.com / password123<br>
            <strong class="text-info">Viewer:</strong> viewer@turnamen.com / password123
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
