@extends('layouts.tournament') 
@section('title', 'Dashboard')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h4 class="fw-bold"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
    <span class="text-muted">Selamat datang, {{ auth()->user()->name }}!</span>
</div>

<!-- Statistik -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-primary p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-muted small">Total Peserta</div>
                    <div class="fs-2 fw-bold text-primary">{{ $stats['total_participants'] }}</div>
                </div>
                <i class="bi bi-people-fill fs-1 text-primary opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-success p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-muted small">Total Turnamen</div>
                    <div class="fs-2 fw-bold text-success">{{ $stats['total_tournaments'] }}</div>
                </div>
                <i class="bi bi-trophy-fill fs-1 text-success opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-warning p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-muted small">Turnamen Aktif</div>
                    <div class="fs-2 fw-bold text-warning">{{ $stats['ongoing_tournaments'] }}</div>
                </div>
                <i class="bi bi-lightning-fill fs-1 text-warning opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-info p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-muted small">Total Match</div>
                    <div class="fs-2 fw-bold text-info">{{ $stats['total_matches'] }}</div>
                </div>
                <i class="bi bi-controller fs-1 text-info opacity-25"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card p-4">
    <h6 class="fw-bold mb-3">Aksi Cepat</h6>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('participants.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah Peserta
        </a>
        <a href="{{ route('tournaments.create') }}" class="btn btn-success">
            <i class="bi bi-trophy me-1"></i> Buat Turnamen
        </a>
        <a href="{{ route('matches.create') }}" class="btn btn-info text-white">
            <i class="bi bi-controller me-1"></i> Jadwalkan Match
        </a>
    </div>
</div>
@endsection