@extends('layouts.tournament')
@section('title', 'Edit Turnamen')
@section('content')
<div class="mb-3">
    <a href="{{ route('tournaments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="card p-3 p-md-4" style="max-width:680px">
    <h5 class="fw-bold mb-4"><i class="bi bi-pencil me-2"></i>Edit Turnamen</h5>
    <form action="{{ route('tournaments.update', $tournament) }}" method="POST" id="formEditTournament" novalidate enctype="multipart/form-data">
        @csrf @method('PUT')
        {{-- Nama Turnamen --}}
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Nama Turnamen <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $tournament->name) }}"
                   minlength="3" maxlength="255">
            <div class="invalid-feedback" id="name-error">
                @error('name'){{ $message }}@enderror
            </div>
        </div>
        {{-- Nama Game --}}
        <div class="mb-3">
            <label for="game_name" class="form-label fw-semibold">Nama Game <span class="text-danger">*</span></label>
            <input type="text" id="game_name" name="game_name"
                   class="form-control @error('game_name') is-invalid @enderror"
                   value="{{ old('game_name', $tournament->game_name) }}"
                   minlength="2" maxlength="100">
            <div class="invalid-feedback" id="game_name-error">
                @error('game_name'){{ $message }}@enderror
            </div>
        </div>
        {{-- Deskripsi --}}
        <div class="mb-3">
            <label for="description" class="form-label fw-semibold">Deskripsi</label>
            <textarea id="description" name="description"
                      class="form-control @error('description') is-invalid @enderror"
                      rows="3" maxlength="1000">{{ old('description', $tournament->description) }}</textarea>
            <div class="invalid-feedback" id="description-error">
                @error('description'){{ $message }}@enderror
            </div>
            <div class="form-text text-end"><span id="desc-count">0</span>/1000</div>
        </div>
        {{-- Tanggal --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
                <label for="start_date" class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" id="start_date" name="start_date"
                       class="form-control @error('start_date') is-invalid @enderror"
                       value="{{ old('start_date', $tournament->start_date->format('Y-m-d')) }}">
                <div class="invalid-feedback" id="start_date-error">
                    @error('start_date'){{ $message }}@enderror
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label for="end_date" class="form-label fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                <input type="date" id="end_date" name="end_date"
                       class="form-control @error('end_date') is-invalid @enderror"
                       value="{{ old('end_date', $tournament->end_date->format('Y-m-d')) }}">
                <div class="invalid-feedback" id="end_date-error">
                    @error('end_date'){{ $message }}@enderror
                </div>
            </div>
        </div>
        {{-- Maks Peserta & Prize Pool & Biaya Pendaftaran --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-4">
                <label for="max_participants" class="form-label fw-semibold">Maks Peserta <span class="text-danger">*</span></label>
                <input type="number" id="max_participants" name="max_participants"
                       class="form-control @error('max_participants') is-invalid @enderror"
                       value="{{ old('max_participants', $tournament->max_participants) }}"
                       min="2" max="256">
                <div class="invalid-feedback" id="max_participants-error">
                    @error('max_participants'){{ $message }}@enderror
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <label for="prize_pool" class="form-label fw-semibold">Total Hadiah (Rp)</label>
                <input type="number" id="prize_pool" name="prize_pool"
                       class="form-control @error('prize_pool') is-invalid @enderror"
                       value="{{ old('prize_pool', $tournament->prize_pool) }}"
                       min="0" step="1000">
                <div class="invalid-feedback" id="prize_pool-error">
                    @error('prize_pool'){{ $message }}@enderror
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <label for="entry_fee" class="form-label fw-semibold">Biaya Pendaftaran (Rp)</label>
                <input type="number" id="entry_fee" name="entry_fee"
                       class="form-control @error('entry_fee') is-invalid @enderror"
                       value="{{ old('entry_fee', $tournament->entry_fee) }}" min="0" step="1000">
                <div class="invalid-feedback" id="entry_fee-error">
                    @error('entry_fee'){{ $message }}@enderror
                </div>
            </div>
        </div>
        {{-- Format --}}
        <div class="mb-3">
            <label for="format" class="form-label fw-semibold">Format <span class="text-danger">*</span></label>
            <select id="format" name="format" class="form-select @error('format') is-invalid @enderror">
                <option value="single_elimination" {{ old('format', $tournament->format) === 'single_elimination' ? 'selected' : '' }}>Single Elimination</option>
                <option value="double_elimination" {{ old('format', $tournament->format) === 'double_elimination' ? 'selected' : '' }}>Double Elimination</option>
                <option value="round_robin"        {{ old('format', $tournament->format) === 'round_robin'        ? 'selected' : '' }}>Round Robin</option>
            </select>
            @error('format')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        {{-- Status --}}
        <div class="mb-4">
            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                <option value="upcoming"  {{ old('status', $tournament->status) === 'upcoming'  ? 'selected' : '' }}>Upcoming</option>
                <option value="ongoing"   {{ old('status', $tournament->status) === 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ old('status', $tournament->status) === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Upload QRIS --}}
        <div class="mb-4">
            <label for="qris_image" class="form-label fw-semibold"><i class="bi bi-qr-code me-1"></i>Gambar QRIS Pembayaran <span class="text-muted fw-normal small">(Kosongkan jika tidak ingin mengubah)</span></label>
            @if($tournament->qris_image)
            <div class="mb-2 d-flex align-items-center gap-3">
                <img src="{{ asset('storage/' . $tournament->qris_image) }}" alt="QRIS Saat Ini"
                     class="rounded border shadow-sm" style="width:100px;height:100px;object-fit:contain;">
                <span class="text-muted small"><i class="bi bi-check-circle-fill text-success me-1"></i>QRIS sudah terpasang. Upload baru untuk mengganti.</span>
            </div>
            @endif
            <input type="file" id="qris_image" name="qris_image"
                   class="form-control @error('qris_image') is-invalid @enderror"
                   accept="image/jpeg,image/png,image/jpg">
            @error('qris_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text text-muted small">Format JPG/PNG, maks 2MB.</div>
            <div id="qris-preview" class="mt-2" style="display:none;">
                <img id="qris-preview-img" src="" alt="Preview QRIS" class="rounded border shadow-sm" style="max-width:160px;max-height:160px;object-fit:contain;">
            </div>
        </div>

        <button type="submit" class="btn btn-warning w-100">
            <i class="bi bi-check-circle me-1"></i> Update Turnamen
        </button>
    </form>
</div>
@endsection
@push('scripts')
<script>
(function () {
    'use strict';

    // Preview QRIS image
    const qrisInput = document.getElementById('qris_image');
    const qrisPreview = document.getElementById('qris-preview');
    const qrisImg = document.getElementById('qris-preview-img');
    if (qrisInput) {
        qrisInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => { qrisImg.src = e.target.result; qrisPreview.style.display = 'block'; };
                reader.readAsDataURL(this.files[0]);
            } else {
                qrisPreview.style.display = 'none';
            }
        });
    }

    // Counter karakter deskripsi
    const descEl    = document.getElementById('description');
    const descCount = document.getElementById('desc-count');
    if (descEl && descCount) {
        descCount.textContent = descEl.value.length;
        descEl.addEventListener('input', () => descCount.textContent = descEl.value.length);
    }

    const rules = {
        name: {
            validate(v) {
                if (!v) return 'Nama turnamen wajib diisi.';
                if (v.length < 3) return 'Nama turnamen minimal 3 karakter.';
                if (v.length > 255) return 'Nama turnamen maksimal 255 karakter.';
                return '';
            }
        },
        game_name: {
            validate(v) {
                if (!v) return 'Nama game wajib diisi.';
                if (v.length < 2) return 'Nama game minimal 2 karakter.';
                if (v.length > 100) return 'Nama game maksimal 100 karakter.';
                return '';
            }
        },
        description: {
            validate(v) {
                if (v.length > 1000) return 'Deskripsi maksimal 1000 karakter.';
                return '';
            }
        },
        start_date: {
            validate(v) {
                if (!v) return 'Tanggal mulai wajib diisi.';
                return '';
            }
        },
        end_date: {
            validate(v) {
                const startVal = document.getElementById('start_date').value;
                if (!v) return 'Tanggal selesai wajib diisi.';
                if (startVal && v < startVal) return 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.';
                return '';
            }
        },
        max_participants: {
            validate(v) {
                if (!v) return 'Maksimal peserta wajib diisi.';
                const n = parseInt(v);
                if (isNaN(n) || !Number.isInteger(n)) return 'Maksimal peserta harus berupa angka bulat.';
                if (n < 2) return 'Minimal peserta adalah 2 orang.';
                if (n > 256) return 'Maksimal peserta tidak boleh lebih dari 256.';
                return '';
            }
        },
        prize_pool: {
            validate(v) {
                if (!v) return '';
                if (isNaN(parseFloat(v))) return 'Total hadiah harus berupa angka.';
                if (parseFloat(v) < 0) return 'Total hadiah tidak boleh bernilai negatif.';
                return '';
            }
        },
        entry_fee: {
            validate(v) {
                if (!v) return ''; // opsional
                if (isNaN(parseFloat(v))) return 'Biaya pendaftaran harus berupa angka.';
                if (parseFloat(v) < 0) return 'Biaya pendaftaran tidak boleh bernilai negatif.';
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

    document.getElementById('start_date').addEventListener('change', () => {
        const startVal = document.getElementById('start_date').value;
        document.getElementById('end_date').setAttribute('min', startVal);
        validateField('start_date');
        if (document.getElementById('end_date').value) validateField('end_date');
    });

    Object.keys(rules).forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('input',  () => validateField(id));
        el.addEventListener('change', () => validateField(id));
        el.addEventListener('blur',   () => validateField(id));
        if (el.value) validateField(id); // pre-validate
    });

    document.getElementById('formEditTournament').addEventListener('submit', function (e) {
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