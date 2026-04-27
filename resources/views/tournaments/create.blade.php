@extends('layouts.tournament')
@section('title', 'Buat Turnamen')
@section('content')
<div class="mb-3">
    <a href="{{ route('tournaments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="card p-4" style="max-width:650px">
    <h5 class="fw-bold mb-4"><i class="bi bi-trophy me-2"></i>Buat Turnamen Baru</h5>
    <form action="{{ route('tournaments.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Turnamen *</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" placeholder="Contoh: ML Championship 2025">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Game *</label>
            <input type="text" name="game_name" class="form-control @error('game_name') is-invalid @enderror"
                   value="{{ old('game_name') }}" placeholder="Contoh: Mobile Legends">
            @error('game_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3"
                      placeholder="Deskripsi turnamen...">{{ old('description') }}</textarea>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label fw-semibold">Tanggal Mulai *</label>
                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                       value="{{ old('start_date') }}">
                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Tanggal Selesai *</label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                       value="{{ old('end_date') }}">
                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label fw-semibold">Maks Peserta *</label>
                <input type="number" name="max_participants" class="form-control" value="{{ old('max_participants', 8) }}" min="2">
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Total Hadiah (Rp)</label>
                <input type="number" name="prize_pool" class="form-control" value="{{ old('prize_pool') }}" min="0">
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Format Turnamen *</label>
            <select name="format" class="form-select">
                <option value="single_elimination">Single Elimination (kalah = gugur)</option>
                <option value="double_elimination">Double Elimination (2x kalah = gugur)</option>
                <option value="round_robin">Round Robin (semua lawan semua)</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success w-100">
            <i class="bi bi-check-circle me-1"></i> Buat Turnamen
        </button>
    </form>
</div>
@endsection