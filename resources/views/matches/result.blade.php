@extends('layouts.tournament')
@section('title', 'Input Hasil')
@section('content')
<div class="mb-3">
    <a href="{{ route('matches.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="card p-3 p-md-4" style="max-width:520px">
    <h5 class="fw-bold mb-4"><i class="bi bi-trophy me-2"></i>Input Hasil Pertandingan</h5>
    <div class="alert alert-info">
        <strong>{{ $match->participant1->name }}</strong>
        <span class="mx-2">vs</span>
        <strong>{{ $match->participant2->name }}</strong><br>
        <small>Ronde {{ $match->round }} - Match #{{ $match->match_number }}</small>
    </div>
    <form action="{{ route('matches.result', $match) }}" method="POST" id="formResult" novalidate>
        @csrf
        {{-- Skor --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
                <label for="score_p1" class="form-label fw-semibold">
                    Skor <span class="text-primary text-truncate d-inline-block" style="max-width:140px;vertical-align:bottom;">{{ $match->participant1->name }}</span>
                </label>
                <input type="number" id="score_p1" name="score_participant1"
                       class="form-control @error('score_participant1') is-invalid @enderror"
                       value="{{ old('score_participant1', 0) }}" min="0" max="9999">
                <div class="invalid-feedback" id="score_p1-error">
                    @error('score_participant1'){{ $message }}@enderror
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label for="score_p2" class="form-label fw-semibold">
                    Skor <span class="text-danger text-truncate d-inline-block" style="max-width:140px;vertical-align:bottom;">{{ $match->participant2->name }}</span>
                </label>
                <input type="number" id="score_p2" name="score_participant2"
                       class="form-control @error('score_participant2') is-invalid @enderror"
                       value="{{ old('score_participant2', 0) }}" min="0" max="9999">
                <div class="invalid-feedback" id="score_p2-error">
                    @error('score_participant2'){{ $message }}@enderror
                </div>
            </div>
        </div>
        {{-- Pemenang --}}
        <div class="mb-3">
            <label for="winner_id" class="form-label fw-semibold">Pemenang <span class="text-danger">*</span></label>
            <select id="winner_id" name="winner_id"
                    class="form-select @error('winner_id') is-invalid @enderror">
                <option value="">-- Pilih Pemenang --</option>
                <option value="{{ $match->participant1_id }}" {{ old('winner_id') == $match->participant1_id ? 'selected' : '' }}>
                    {{ $match->participant1->name }}
                </option>
                <option value="{{ $match->participant2_id }}" {{ old('winner_id') == $match->participant2_id ? 'selected' : '' }}>
                    {{ $match->participant2->name }}
                </option>
            </select>
            <div class="invalid-feedback" id="winner_id-error">
                @error('winner_id'){{ $message }}@enderror
            </div>
        </div>
        {{-- Catatan --}}
        <div class="mb-4">
            <label for="notes" class="form-label fw-semibold">Catatan</label>
            <textarea id="notes" name="notes"
                      class="form-control @error('notes') is-invalid @enderror"
                      rows="2" maxlength="500"
                      placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
            <div class="invalid-feedback" id="notes-error">
                @error('notes'){{ $message }}@enderror
            </div>
            <div class="form-text text-end"><span id="notes-count">0</span>/500</div>
        </div>
        <button type="submit" class="btn btn-success w-100">
            <i class="bi bi-check-circle me-1"></i> Simpan Hasil
        </button>
    </form>
</div>
@endsection
@push('scripts')
<script>
(function () {
    'use strict';

    // Counter karakter catatan
    const notesEl    = document.getElementById('notes');
    const notesCount = document.getElementById('notes-count');
    if (notesEl && notesCount) {
        notesCount.textContent = notesEl.value.length;
        notesEl.addEventListener('input', () => notesCount.textContent = notesEl.value.length);
    }

    const p1Id = '{{ $match->participant1_id }}';
    const p2Id = '{{ $match->participant2_id }}';

    const rules = {
        score_p1: {
            validate(v) {
                if (v === '' || v === null) return 'Skor peserta 1 wajib diisi.';
                const n = parseInt(v);
                if (isNaN(n)) return 'Skor peserta 1 harus berupa angka.';
                if (n < 0) return 'Skor tidak boleh bernilai negatif.';
                if (n > 9999) return 'Skor terlalu besar (maks: 9999).';
                return '';
            }
        },
        score_p2: {
            validate(v) {
                if (v === '' || v === null) return 'Skor peserta 2 wajib diisi.';
                const n = parseInt(v);
                if (isNaN(n)) return 'Skor peserta 2 harus berupa angka.';
                if (n < 0) return 'Skor tidak boleh bernilai negatif.';
                if (n > 9999) return 'Skor terlalu besar (maks: 9999).';
                return '';
            }
        },
        winner_id: {
            validate(v) {
                if (!v) return 'Pemenang wajib dipilih.';
                if (v !== p1Id && v !== p2Id) return 'Pemenang yang dipilih tidak valid.';
                return '';
            }
        },
        notes: {
            validate(v) {
                if (v.length > 500) return 'Catatan maksimal 500 karakter.';
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
        const error = rules[fieldId].validate(input.value);
        setFieldState(fieldId, error);
        return !error;
    }

    Object.keys(rules).forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('input',  () => validateField(id));
        el.addEventListener('change', () => validateField(id));
        el.addEventListener('blur',   () => validateField(id));
        if (el.value) validateField(id);
    });

    document.getElementById('formResult').addEventListener('submit', function (e) {
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