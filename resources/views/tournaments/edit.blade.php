@extends('layouts.tournament')
@section('title', 'Edit Turnamen')
@section('content')
<div class="mb-3">
    <a href="{{ route('tournaments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="card p-4" style="max-width:650px">
    <h5 class="fw-bold mb-4"><i class="bi bi-pencil me-2"></i>Edit Turnamen</h5>
    <form action="{{ route('tournaments.update', $tournament) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Turnamen *</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $tournament->name) }}">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Game *</label>
            <input type="text" name="game_name" class="form-control"
                   value="{{ old('game_name', $tournament->game_name) }}">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $tournament->description) }}</textarea>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label fw-semibold">Tanggal Mulai *</label>
                <input type="date" name="start_date" class="form-control"
                       value="{{ old('start_date', $tournament->start_date->format('Y-m-d')) }}">
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Tanggal Selesai *</label>
                <input type="date" name="end_date" class="form-control"
                       value="{{ old('end_date', $tournament->end_date->format('Y-m-d')) }}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label fw-semibold">Maks Peserta *</label>
                <input type="number" name="max_participants" class="form-control"
                       value="{{ old('max_participants', $tournament->max_participants) }}">
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Total Hadiah (Rp)</label>
                <input type="number" name="prize_pool" class="form-control"
                       value="{{ old('prize_pool', $tournament->prize_pool) }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Format *</label>
            <select name="format" class="form-select">
                <option value="single_elimination" {{ $tournament->format === 'single_elimination' ? 'selected' : '' }}>Single Elimination</option>
                <option value="double_elimination" {{ $tournament->format === 'double_elimination' ? 'selected' : '' }}>Double Elimination</option>
                <option value="round_robin" {{ $tournament->format === 'round_robin' ? 'selected' : '' }}>Round Robin</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Status *</label>
            <select name="status" class="form-select">
                <option value="upcoming" {{ $tournament->status === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="ongoing" {{ $tournament->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ $tournament->status === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-warning w-100">
            <i class="bi bi-check-circle me-1"></i> Update Turnamen
        </button>
    </form>
</div>
@endsection