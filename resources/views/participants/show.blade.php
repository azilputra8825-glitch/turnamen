@extends('layouts.tournament')
@section('title', 'Detail Peserta')
@section('content')
<div class="mb-3">
    <a href="{{ route('participants.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card p-4 text-center">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                 style="width:80px;height:80px;font-size:2rem">
                {{ strtoupper(substr($participant->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold">{{ $participant->name }}</h5>
            <code>{{ $participant->username }}</code>
            <span class="badge {{ $participant->status === 'active' ? 'bg-success' : 'bg-secondary' }} mt-2">
                {{ ucfirst($participant->status) }}
            </span>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3">Informasi Peserta</h6>
            <table class="table table-borderless mb-0">
                <tr><th width="30%">Email</th><td>{{ $participant->email }}</td></tr>
                <tr><th>No. HP</th><td>{{ $participant->phone ?? '-' }}</td></tr>
                <tr><th>Game ID</th><td>{{ $participant->game_id ?? '-' }}</td></tr>
                <tr><th>Terdaftar</th><td>{{ $participant->created_at->format('d M Y') }}</td></tr>
            </table>
        </div>
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Turnamen yang Diikuti</h6>
            @forelse($participant->tournaments as $t)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <span>{{ $t->name }}</span>
                    <span class="badge bg-info">{{ $t->game_name }}</span>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ikut turnamen apapun.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection