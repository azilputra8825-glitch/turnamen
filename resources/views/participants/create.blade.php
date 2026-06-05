@extends('layouts.tournament')
@section('title', 'Tambah Peserta')
@section('content')
<div class="mb-3">
    <a href="{{ route('participants.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="card p-3 p-md-4" style="max-width:620px">
    <h5 class="fw-bold mb-4"><i class="bi bi-person-plus me-2"></i>Tambah Peserta Baru</h5>
    <form action="{{ route('participants.store') }}" method="POST" id="formAddParticipant" novalidate>
        @csrf
        {{-- Nama --}}
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" placeholder="Contoh: Budi Santoso"
                   minlength="2" maxlength="255">
            <div class="invalid-feedback" id="name-error">
                @error('name'){{ $message }}@enderror
            </div>
        </div>
        {{-- Username --}}
        <div class="mb-3">
            <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
            <input type="text" id="username" name="username"
                   class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username') }}" placeholder="Contoh: budi123 (huruf, angka, _)"
                   minlength="3" maxlength="50">
            <div class="invalid-feedback" id="username-error">
                @error('username'){{ $message }}@enderror
            </div>
            <div class="form-text">Hanya huruf, angka, dan underscore (_).</div>
        </div>
        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
            <input type="email" id="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="budi@email.com">
            <div class="invalid-feedback" id="email-error">
                @error('email'){{ $message }}@enderror
            </div>
        </div>
        {{-- No HP --}}
        <div class="mb-3">
            <label for="phone" class="form-label fw-semibold">No. HP</label>
            <input type="text" id="phone" name="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone') }}" placeholder="08xx atau +628xx"
                   maxlength="20">
            <div class="invalid-feedback" id="phone-error">
                @error('phone'){{ $message }}@enderror
            </div>
            <div class="form-text">Opsional. Format: 08xxx atau +628xxx.</div>
        </div>
        {{-- Game ID --}}
        <div class="mb-3">
            <label for="game_id" class="form-label fw-semibold">Game ID</label>
            <input type="text" id="game_id" name="game_id"
                   class="form-control @error('game_id') is-invalid @enderror"
                   value="{{ old('game_id') }}" placeholder="ID dalam game (misal: Budi#1234)"
                   maxlength="100">
            <div class="invalid-feedback" id="game_id-error">
                @error('game_id'){{ $message }}@enderror
            </div>
        </div>
        {{-- Status --}}
        <div class="mb-4">
            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                <option value="active"   {{ old('status', 'active') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
            <i class="bi bi-check-circle me-1"></i> Simpan Peserta
        </button>
    </form>
</div>
@endsection
@push('scripts')
<script>
(function () {
    'use strict';

    const rules = {
        name: {
            validate(v) {
                if (!v) return 'Nama lengkap wajib diisi.';
                if (v.length < 2) return 'Nama minimal 2 karakter.';
                if (v.length > 255) return 'Nama maksimal 255 karakter.';
                return '';
            }
        },
        username: {
            validate(v) {
                if (!v) return 'Username wajib diisi.';
                if (v.length < 3) return 'Username minimal 3 karakter.';
                if (v.length > 50) return 'Username maksimal 50 karakter.';
                if (!/^[a-zA-Z0-9_]+$/.test(v)) return 'Username hanya boleh berisi huruf, angka, dan underscore (_).';
                return '';
            }
        },
        email: {
            validate(v) {
                if (!v) return 'Email wajib diisi.';
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return 'Format email tidak valid (contoh: nama@email.com).';
                return '';
            }
        },
        phone: {
            validate(v) {
                if (!v) return ''; // opsional
                if (!/^(\+62|0)[0-9]{8,13}$/.test(v)) return 'Format nomor HP tidak valid (contoh: 08123456789).';
                return '';
            }
        },
        game_id: {
            validate(v) {
                if (v.length > 100) return 'Game ID maksimal 100 karakter.';
                return '';
            }
        }
    };

    function setFieldState(fieldId, errorMsg) {
        const input = document.getElementById(fieldId);
        const errEl = document.getElementById(fieldId + '-error') || input.nextElementSibling;
        if (!input) return;
        if (errorMsg) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            if (errEl) errEl.textContent = errorMsg;
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            if (errEl) errEl.textContent = '';
        }
    }

    function validateField(fieldId) {
        const input = document.getElementById(fieldId);
        if (!input || !rules[fieldId]) return true;
        const error = rules[fieldId].validate(input.value.trim());
        setFieldState(fieldId, error);
        return !error;
    }

    // Attach real-time events
    Object.keys(rules).forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('input', () => validateField(id));
        el.addEventListener('blur',  () => validateField(id));
        // Pre-validate existing old() values
        if (el.value) validateField(id);
    });

    // On submit
    document.getElementById('formAddParticipant').addEventListener('submit', function (e) {
        let valid = true;
        Object.keys(rules).forEach(id => {
            if (!validateField(id)) valid = false;
        });
        if (!valid) {
            e.preventDefault();
            e.stopPropagation();
            // Scroll ke error pertama
            const firstInvalid = this.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
})();
</script>
@endpush