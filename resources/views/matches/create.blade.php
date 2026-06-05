@extends('layouts.tournament')
@section('title', 'Jadwalkan Match')
@section('content')
<div class="mb-3">
    <a href="{{ route('matches.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="card p-3 p-md-4" style="max-width:620px">
    <h5 class="fw-bold mb-4"><i class="bi bi-controller me-2"></i>Jadwalkan Pertandingan</h5>
    <form action="{{ route('matches.store') }}" method="POST" id="formCreateMatch" novalidate>
        @csrf
        {{-- Turnamen --}}
        <div class="mb-3">
            <label for="tournamentSelect" class="form-label fw-semibold">Turnamen <span class="text-danger">*</span></label>
            <select name="tournament_id" class="form-select @error('tournament_id') is-invalid @enderror" id="tournamentSelect">
                <option value="">-- Pilih Turnamen --</option>
                @foreach($tournaments as $t)
                <option value="{{ $t->id }}"
                    {{ request('tournament_id') == $t->id ? 'selected' : '' }}>
                    {{ $t->name }}
                </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="tournamentSelect-error">
                @error('tournament_id'){{ $message }}@enderror
            </div>
        </div>
        {{-- Peserta 1 --}}
        <div class="mb-3">
            <label for="participant1" class="form-label fw-semibold">Peserta 1 <span class="text-danger">*</span></label>
            <select name="participant1_id" class="form-select @error('participant1_id') is-invalid @enderror" id="participant1">
                <option value="">-- Pilih Peserta 1 --</option>
            </select>
            <div class="invalid-feedback" id="participant1-error">
                @error('participant1_id'){{ $message }}@enderror
            </div>
        </div>
        {{-- Peserta 2 --}}
        <div class="mb-3">
            <label for="participant2" class="form-label fw-semibold">Peserta 2 <span class="text-danger">*</span></label>
            <select name="participant2_id" class="form-select @error('participant2_id') is-invalid @enderror" id="participant2">
                <option value="">-- Pilih Peserta 2 --</option>
            </select>
            <div class="invalid-feedback" id="participant2-error">
                @error('participant2_id'){{ $message }}@enderror
            </div>
        </div>
        {{-- Ronde & Nomor Match --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
                <label for="round" class="form-label fw-semibold">Ronde <span class="text-danger">*</span></label>
                <input type="number" id="round" name="round"
                       class="form-control @error('round') is-invalid @enderror"
                       value="{{ old('round', 1) }}" min="1">
                <div class="invalid-feedback" id="round-error">
                    @error('round'){{ $message }}@enderror
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label for="match_number" class="form-label fw-semibold">Nomor Match <span class="text-danger">*</span></label>
                <input type="number" id="match_number" name="match_number"
                       class="form-control @error('match_number') is-invalid @enderror"
                       value="{{ old('match_number', 1) }}" min="1">
                <div class="invalid-feedback" id="match_number-error">
                    @error('match_number'){{ $message }}@enderror
                </div>
            </div>
        </div>
        {{-- Jadwal --}}
        <div class="mb-4">
            <label for="scheduled_at" class="form-label fw-semibold">Jadwal Pertandingan</label>
            <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                   class="form-control @error('scheduled_at') is-invalid @enderror"
                   value="{{ old('scheduled_at') }}">
            @error('scheduled_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-check-circle me-1"></i> Jadwalkan
        </button>
    </form>
</div>
@endsection
@push('scripts')
<script>
(function () {
    'use strict';

    // ── Load peserta berdasarkan turnamen ──────────────────────────────
    function loadParticipants(tournamentId) {
        if (!tournamentId) {
            resetSelect('participant1', '-- Pilih Peserta 1 --');
            resetSelect('participant2', '-- Pilih Peserta 2 --');
            return;
        }
        fetch(`/tournaments/${tournamentId}/participants-list`)
            .then(r => r.json())
            .then(data => {
                let opts = '<option value="">-- Pilih Peserta --</option>';
                data.forEach(p => {
                    opts += `<option value="${p.id}">${p.name} (${p.username})</option>`;
                });
                document.getElementById('participant1').innerHTML = opts;
                document.getElementById('participant2').innerHTML = opts;
            })
            .catch(err => console.error('Error:', err));
    }

    function resetSelect(id, placeholder) {
        document.getElementById(id).innerHTML = `<option value="">${placeholder}</option>`;
    }

    document.getElementById('tournamentSelect').addEventListener('change', function () {
        loadParticipants(this.value);
        validateField('tournamentSelect');
    });

    window.addEventListener('load', function () {
        const val = document.getElementById('tournamentSelect').value;
        if (val) loadParticipants(val);
    });

    // ── Validasi real-time ─────────────────────────────────────────────
    const rules = {
        tournamentSelect: {
            validate(v) {
                if (!v) return 'Turnamen wajib dipilih.';
                return '';
            }
        },
        participant1: {
            validate(v) {
                if (!v) return 'Peserta 1 wajib dipilih.';
                return '';
            }
        },
        participant2: {
            validate(v) {
                if (!v) return 'Peserta 2 wajib dipilih.';
                const p1 = document.getElementById('participant1').value;
                if (v && p1 && v === p1) return 'Peserta 1 dan Peserta 2 tidak boleh sama.';
                return '';
            }
        },
        round: {
            validate(v) {
                if (!v) return 'Ronde wajib diisi.';
                if (parseInt(v) < 1) return 'Ronde minimal adalah 1.';
                return '';
            }
        },
        match_number: {
            validate(v) {
                if (!v) return 'Nomor match wajib diisi.';
                if (parseInt(v) < 1) return 'Nomor match minimal adalah 1.';
                return '';
            }
        }
    };

    function setFieldState(fieldId, errorMsg) {
        const input = document.getElementById(fieldId);
        const errEl = document.getElementById(fieldId + '-error');
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

    // Re-validasi peserta 2 jika peserta 1 berubah
    document.getElementById('participant1').addEventListener('change', () => {
        validateField('participant1');
        if (document.getElementById('participant2').value) validateField('participant2');
    });

    Object.keys(rules).forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('change', () => validateField(id));
        el.addEventListener('input',  () => validateField(id));
        el.addEventListener('blur',   () => validateField(id));
    });

    document.getElementById('formCreateMatch').addEventListener('submit', function (e) {
        let valid = true;
        Object.keys(rules).forEach(id => { if (!validateField(id)) valid = false; });
        if (!valid) {
            e.preventDefault();
            e.stopPropagation();
            const firstInvalid = this.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
})();
</script>
@endpush