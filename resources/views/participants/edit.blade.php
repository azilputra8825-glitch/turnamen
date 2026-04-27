@extends('layouts.tournament')
@section('title', 'Edit Peserta')
@section('content')
<div class="mb-3">
    <a href="{{ route('participants.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="card p-4" style="max-width:600px">
    <h5 class="fw-bold mb-4"><i class="bi bi-pencil me-2"></i>Edit Peserta</h5>
    <form action="{{ route('participants.update', $participant) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap *</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $participant->name) }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Username *</label>
            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username', $participant->username) }}">
            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Email *</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $participant->email) }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">No. HP</label>
            <input type="text" name="phone" class="form-control"
                   value="{{ old('phone', $participant->phone) }}">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Game ID</label>
            <input type="text" name="game_id" class="form-control"
                   value="{{ old('game_id', $participant->game_id) }}">
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Status *</label>
            <select name="status" class="form-select">
                <option value="active" {{ $participant->status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $participant->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-warning w-100">
            <i class="bi bi-check-circle me-1"></i> Update Peserta
        </button>
    </form>
</div>
@endsection