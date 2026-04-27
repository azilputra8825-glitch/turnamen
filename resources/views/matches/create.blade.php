@extends('layouts.tournament')
@section('title', 'Jadwalkan Match')
@section('content')
<div class="mb-3">
    <a href="{{ route('matches.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="card p-4" style="max-width:600px">
    <h5 class="fw-bold mb-4"><i class="bi bi-controller me-2"></i>Jadwalkan Pertandingan</h5>
    <form action="{{ route('matches.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Turnamen *</label>
            <select name="tournament_id" class="form-select" id="tournamentSelect">
                <option value="">-- Pilih Turnamen --</option>
                @foreach($tournaments as $t)
                <option value="{{ $t->id }}"
                    {{ request('tournament_id') == $t->id ? 'selected' : '' }}>
                    {{ $t->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Peserta 1 *</label>
            <select name="participant1_id" class="form-select" id="participant1">
                <option value="">-- Pilih Peserta 1 --</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Peserta 2 *</label>
            <select name="participant2_id" class="form-select" id="participant2">
                <option value="">-- Pilih Peserta 2 --</option>
            </select>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label fw-semibold">Ronde *</label>
                <input type="number" name="round" class="form-control" value="{{ old('round', 1) }}" min="1">
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Nomor Match *</label>
                <input type="number" name="match_number" class="form-control" value="{{ old('match_number', 1) }}" min="1">
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Jadwal Pertandingan</label>
            <input type="datetime-local" name="scheduled_at" class="form-control"
                   value="{{ old('scheduled_at') }}">
        </div>
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-check-circle me-1"></i> Jadwalkan
        </button>
    </form>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('tournamentSelect').addEventListener('change', function() {
    const id = this.value;
    if (!id) return;
    fetch(`/tournaments/${id}/participants-list`)
        .then(r => r.json())
        .then(data => {
            const opts = data.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
            document.getElementById('participant1').innerHTML = '<option value="">-- Pilih --</option>' + opts;
            document.getElementById('participant2').innerHTML = '<option value="">-- Pilih --</option>' + opts;
        });
});
// Auto-trigger jika ada tournament_id di URL
const sel = document.getElementById('tournamentSelect');
if (sel.value) sel.dispatchEvent(new Event('change'));
</script>
@endpush