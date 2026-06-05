@extends('layouts.tournament')
@section('title', 'Detail Peserta')
@section('content')
<div class="mb-3">
    <a href="{{ route('participants.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="row g-3">
    {{-- Kartu Profil --}}
    <div class="col-12 col-md-4">
        <div class="card p-3 p-md-4 text-center h-100">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                 style="width:72px;height:72px;font-size:1.8rem;flex-shrink:0;">
                {{ strtoupper(substr($participant->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold mb-1">{{ $participant->name }}</h5>
            <code class="small">{{ $participant->username }}</code>
            <div class="mt-2">
                <span class="badge {{ $participant->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($participant->status) }}
                </span>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="mt-3 pt-3 border-top">
                <a href="{{ route('participants.edit', $participant) }}" class="btn btn-warning btn-sm w-100">
                    <i class="bi bi-pencil me-1"></i> Edit Peserta
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Detail Info --}}
    <div class="col-12 col-md-8">
        <div class="card p-3 p-md-4 mb-3">
            <h6 class="fw-bold mb-3">Informasi Peserta</h6>
            <dl class="row mb-0" style="row-gap:.4rem;">
                <dt class="col-5 col-sm-4 text-muted small fw-semibold">Email</dt>
                <dd class="col-7 col-sm-8 mb-0 small text-break">{{ $participant->email }}</dd>

                <dt class="col-5 col-sm-4 text-muted small fw-semibold">No. HP</dt>
                <dd class="col-7 col-sm-8 mb-0 small">{{ $participant->phone ?? '-' }}</dd>

                <dt class="col-5 col-sm-4 text-muted small fw-semibold">Game ID</dt>
                <dd class="col-7 col-sm-8 mb-0 small">{{ $participant->game_id ?? '-' }}</dd>

                <dt class="col-5 col-sm-4 text-muted small fw-semibold">Terdaftar</dt>
                <dd class="col-7 col-sm-8 mb-0 small">{{ $participant->created_at->format('d M Y') }}</dd>
            </dl>
        </div>

        <div class="card p-3 p-md-4">
            <h6 class="fw-bold mb-3">Turnamen yang Diikuti</h6>
            @forelse($participant->tournaments as $t)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2 flex-wrap gap-1">
                    <span class="small fw-semibold">{{ $t->name }}</span>
                    <span class="badge bg-info text-dark flex-shrink-0">{{ $t->game_name }}</span>
                </div>
            @empty
                <p class="text-muted mb-0 small">Belum ikut turnamen apapun.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection