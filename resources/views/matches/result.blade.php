@extends('layouts.tournament')
@section('title', 'Input Hasil')
@section('content')
<div class="mb-3">
    <a href="{{ route('matches.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="card p-4" style="max-width:500px">
    <h5 class="fw-bold mb-4"><i class="bi bi-trophy me-2"></i>Input Hasil Pertandingan</h5>
    <div class="alert alert-info">
        <strong>{{ $match->participant1->name }}</strong>
        <span class="mx-2">vs</span>
        <strong>{{ $match->participant2->name }}</strong><br>
        <small>Ronde {{ $match->round }} - Match #{{ $match->match_number }}</small>
    </div>
    <form action="{{ route('matches.result', $match) }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label fw-semibold">Skor {{ $match->participant1->name }}</label>
                <input type="number" name="score_participant1" class="form-control"
                       value="{{ old('score_participant1', 0) }}" min="0">
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Skor {{ $match->participant2->name }}</label>
                <input type="number" name="score_participant2" class="form-control"
                       value="{{ old('score_participant2', 0) }}" min="0">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Pemenang *</label>
            <select name="winner_id" class="form-select">
                <option value="{{ $match->participant1_id }}">{{ $match->participant1->name }}</option>
                <option value="{{ $match->participant2_id }}">{{ $match->participant2->name }}</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Catatan</label>
            <textarea name="notes" class="form-control" rows="2"
                      placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
        </div>
        <button type="submit" class="btn btn-success w-100">
            <i class="bi bi-check-circle me-1"></i> Simpan Hasil
        </button>
    </form>
</div>
@endsection