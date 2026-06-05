@extends('layouts.tournament')
@section('title', 'Daftar Pertandingan')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 mt-1 flex-wrap gap-2 page-header">
    <h4 class="fw-bold mb-0"><i class="bi bi-controller me-2 text-primary"></i>Daftar Pertandingan</h4>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('matches.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm px-3 py-2">
        <i class="bi bi-plus-circle"></i> Jadwalkan Match
    </a>
    @endif
</div>

{{-- Filter Form --}}
<div class="card p-3 mb-4 shadow-sm border-0">
    <form action="{{ route('matches.index') }}" method="GET">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-sm-6 col-md-4">
                <label for="tournament_id" class="form-label small fw-semibold text-muted mb-1">Filter Berdasarkan Turnamen:</label>
                <select name="tournament_id" id="tournament_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Turnamen --</option>
                    @foreach($tournaments as $t)
                        <option value="{{ $t->id }}" {{ request('tournament_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->name }} ({{ $t->game_name }})
                        </option>
                    @endforeach
                </select>
            </div>
            @if(request('tournament_id'))
            <div class="col-12 col-sm-auto align-self-end">
                <a href="{{ route('matches.index') }}" class="btn btn-outline-secondary btn-sm py-1 px-3">
                    <i class="bi bi-x-circle me-1"></i> Reset Filter
                </a>
            </div>
            @endif
        </div>
    </form>
</div>

@php
    $groupedMatches = $matches->groupBy('tournament_id');
@endphp

@forelse($groupedMatches as $tournamentId => $tournamentMatches)
    @php
        $firstMatch = $tournamentMatches->first();
        $tournamentName = $firstMatch->tournament->name;
        $gameName = $firstMatch->tournament->game_name;
    @endphp

    <div class="mb-5">
        {{-- Header Turnamen --}}
        <div class="d-flex align-items-center border-bottom pb-2 mb-3 gap-2 flex-wrap">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-trophy text-warning me-1"></i>{{ $tournamentName }}</h5>
            <span class="badge bg-primary-subtle text-primary rounded-pill small" style="font-size: 0.72rem;">{{ $gameName }}</span>
        </div>

        <div class="row g-3">
            @foreach($tournamentMatches as $m)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden">
                    {{-- Top Accent Border berdasarkan status --}}
                    <div class="position-absolute top-0 start-0 end-0" style="height: 4px; background: {{ $m->status === 'completed' ? '#10b981' : '#f59e0b' }}"></div>
                    
                    <div class="card-body p-4 pt-4">
                        {{-- Ronde & Status --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-1 small" style="font-size:0.7rem;">Ronde {{ $m->round }}</span>
                            <span class="badge {{ $m->status === 'completed' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' }} px-2 py-1 rounded-2" style="font-size: 0.72rem;">
                                <i class="bi {{ $m->status === 'completed' ? 'bi-check-circle-fill' : 'bi-clock-history' }} me-1"></i>{{ ucfirst($m->status) }}
                            </span>
                        </div>

                        {{-- Esports-style Versus Display --}}
                        <div class="bg-light rounded-3 p-3 mb-3 border">
                            <div class="d-flex align-items-center justify-content-between">
                                
                                {{-- Peserta 1 --}}
                                <div class="text-center w-40 d-flex flex-column align-items-center" style="flex:1; min-width:0;">
                                    <div class="avatar-circle bg-primary-subtle text-primary fw-bold mb-2 shadow-xs d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 50%;">
                                        {{ $m->participant1 ? strtoupper(substr($m->participant1->name, 0, 2)) : '?' }}
                                    </div>
                                    <span class="fw-semibold small text-truncate w-100 text-dark" title="{{ $m->participant1->name ?? 'TBD' }}">
                                        {{ $m->participant1->name ?? 'TBD' }}
                                    </span>
                                    @if($m->status === 'completed')
                                        <div class="fs-4 fw-bold mt-1 text-dark">{{ $m->score_participant1 }}</div>
                                    @endif
                                </div>

                                {{-- VS Divider --}}
                                <div class="text-center px-2 d-flex flex-column align-items-center justify-content-center" style="flex-shrink:0;">
                                    <span class="badge bg-danger-subtle text-danger rounded-circle p-2 fw-bold" style="width: 28px; height: 28px; font-size: 0.65rem; display: flex; align-items: center; justify-content-center; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">VS</span>
                                </div>

                                {{-- Peserta 2 --}}
                                <div class="text-center w-40 d-flex flex-column align-items-center" style="flex:1; min-width:0;">
                                    <div class="avatar-circle bg-success-subtle text-success fw-bold mb-2 shadow-xs d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 50%;">
                                        {{ $m->participant2 ? strtoupper(substr($m->participant2->name, 0, 2)) : '?' }}
                                    </div>
                                    <span class="fw-semibold small text-truncate w-100 text-dark" title="{{ $m->participant2->name ?? 'TBD' }}">
                                        {{ $m->participant2->name ?? 'TBD' }}
                                    </span>
                                    @if($m->status === 'completed')
                                        <div class="fs-4 fw-bold mt-1 text-dark">{{ $m->score_participant2 }}</div>
                                    @endif
                                </div>

                            </div>
                        </div>

                        {{-- Winner Banner --}}
                        @if($m->status === 'completed' && $m->winner)
                        <div class="alert alert-success border-0 py-2 px-3 mb-3 d-flex align-items-center gap-2 small">
                            <i class="bi bi-trophy-fill text-success fs-5"></i>
                            <div class="text-truncate">
                                Pemenang: <strong class="text-dark">{{ $m->winner->name }}</strong>
                            </div>
                        </div>
                        @endif

                        {{-- Jadwal Info --}}
                        <div class="text-muted small mb-0 d-flex align-items-center gap-1">
                            <i class="bi bi-calendar-event"></i>
                            <span>Jadwal: {{ $m->scheduled_at ? $m->scheduled_at->format('d M Y H:i') : '-' }}</span>
                        </div>
                    </div>

                    {{-- Footer Action Buttons --}}
                    @if(auth()->user()->isAdmin())
                    <div class="card-footer bg-white border-top-0 px-4 pb-4 pt-0 d-flex justify-content-end align-items-center">
                        <div class="d-flex gap-1.5">
                            @if($m->status !== 'completed')
                            <a href="{{ route('matches.result.form', $m) }}" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1 py-1 px-2.5" title="Input Skor">
                                <i class="bi bi-pencil-square"></i> Skor
                            </a>
                            @endif
                            <form action="{{ route('matches.destroy', $m) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pertandingan ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 py-1 px-2.5" title="Hapus Match">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="card p-5 text-center text-muted border-0 shadow-sm">
        <i class="bi bi-controller fs-1 text-secondary opacity-30 mb-3"></i>
        <h5 class="fw-semibold text-dark">Belum Ada Pertandingan</h5>
        <p class="small text-muted mb-0">Pertandingan yang dijadwalkan akan muncul di sini.</p>
    </div>
@endforelse

@if($matches->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $matches->appends(request()->query())->links() }}
</div>
@endif

@endsection