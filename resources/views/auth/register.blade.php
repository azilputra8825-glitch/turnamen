<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar – Tournament System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: #0d1117;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
            padding: 1.5rem 1rem;
        }
        .login-card {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 16px;
            padding: 2rem 1.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 16px 48px rgba(0,0,0,.5);
        }
        @media (min-width: 480px) {
            .login-card { padding: 2.5rem 2rem; }
        }
        .brand-logo {
            font-size: 2.2rem;
            background: linear-gradient(135deg, #e94560, #f0c040);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .form-control {
            background: #21262d;
            border: 1px solid #30363d;
            color: #e6edf3;
            border-radius: 8px;
        }
        .form-control:focus {
            background: #21262d;
            border-color: #58a6ff;
            color: #e6edf3;
            box-shadow: 0 0 0 3px rgba(88,166,255,.15);
        }
        .form-label { color: #8b949e; font-size: .85rem; font-weight: 600; letter-spacing: .4px; }
        .btn-register {
            background: linear-gradient(135deg, #238636, #196c2e);
            border: none;
            border-radius: 8px;
            font-weight: 700;
            letter-spacing: .5px;
            transition: opacity .2s;
        }
        .btn-register:hover { opacity: .88; }
        .divider { border-color: #30363d; }
        .text-muted-custom { color: #8b949e !important; }
        a { color: #58a6ff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .input-icon { position: relative; }
        .input-icon i {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #484f58; font-size: .95rem; pointer-events: none;
        }
        .input-icon .form-control { padding-left: 2.2rem; }
        .note-box {
            background: rgba(56,139,253,.1);
            border: 1px solid rgba(56,139,253,.3);
            border-radius: 8px;
            padding: .6rem .9rem;
            font-size: .78rem;
            color: #8b949e;
        }
    </style>
</head>
<body>

<div class="login-card">
    {{-- Brand --}}
    <div class="text-center mb-4">
        <div class="brand-logo"><i class="bi bi-trophy-fill"></i></div>
        <h5 class="text-white fw-bold mt-2 mb-0">Buat Akun</h5>
        <p class="text-muted-custom small mt-1">Daftar sebagai viewer turnamen</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 small mb-3">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Nama --}}
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <div class="input-icon">
                <i class="bi bi-person"></i>
                <input type="text" id="name" name="name"
                       class="form-control"
                       value="{{ old('name') }}"
                       placeholder="Nama Anda"
                       required>
            </div>
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-icon">
                <i class="bi bi-envelope"></i>
                <input type="email" id="email" name="email"
                       class="form-control"
                       value="{{ old('email') }}"
                       placeholder="email@contoh.com"
                       required>
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-icon">
                <i class="bi bi-lock"></i>
                <input type="password" id="password" name="password"
                       class="form-control"
                       placeholder="Min. 8 karakter"
                       required>
            </div>
        </div>

        {{-- Konfirmasi Password --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <div class="input-icon">
                <i class="bi bi-lock-fill"></i>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-control"
                       placeholder="Ulangi password"
                       required>
            </div>
        </div>

        {{-- Info role --}}
        <div class="note-box mb-4">
            <i class="bi bi-info-circle me-1"></i>
            Akun yang dibuat memiliki role <strong>Viewer</strong> — hanya bisa melihat data.
            Role Admin hanya bisa ditetapkan oleh Administrator.
        </div>

        <button type="submit" class="btn btn-register btn-success w-100 py-2 text-white">
            <i class="bi bi-person-plus me-2"></i>Buat Akun
        </button>
    </form>

    <hr class="divider my-4">
    <p class="text-center text-muted-custom small mb-0">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
