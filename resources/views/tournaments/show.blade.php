@extends('layouts.tournament')
@section('title', 'Detail Turnamen')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('tournaments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('rankings.show', $tournament) }}" class="btn btn-info text-white btn-sm">
            <i class="bi bi-bar-chart me-1"></i> Lihat Ranking
        </a>
        <a href="{{ route('tournaments.edit', $tournament) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
    </div>
</div>

{{-- Info Turnamen --}}
<div class="card p-4 mb-3">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h5 class="fw-bold">{{ $tournament->name }}</h5>
            <p class="text-muted mb-1"><i class="bi bi-controller me-1"></i>{{ $tournament->game_name }}</p>
            <p class="text-muted mb-1">
                <i class="bi bi-calendar me-1"></i>
                {{ $tournament->start_date->format('d M Y') }} - {{ $tournament->end_date->format('d M Y') }}
            </p>
            <p class="text-muted mb-0">
                <i class="bi bi-people me-1"></i>
                {{ $tournament->participants->count() }}/{{ $tournament->max_participants }} Peserta
                &nbsp;|&nbsp;
                <i class="bi bi-trophy me-1"></i>
                {{ ucfirst(str_replace('_', ' ', $tournament->format)) }}
            </p>
        </div>
        <span class="badge fs-6
            {{ $tournament->status === 'upcoming' ? 'bg-info' :
               ($tournament->status === 'ongoing' ? 'bg-success' : 'bg-secondary') }}">
            {{ ucfirst($tournament->status) }}
        </span>
    </div>
    @if($tournament->prize_pool)
    <div class="mt-2 text-success fw-semibold">
        <i class="bi bi-cash me-1"></i>Prize Pool: Rp {{ number_format($tournament->prize_pool, 0, ',', '.') }}
    </div>
    @endif
</div>

<div class="row g-3">
    {{-- Daftar Peserta --}}
    <div class="col-md-5">
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-people me-2"></i>Peserta Terdaftar</h6>

            @if(!$tournament->isFull())
            <form action="{{ route('tournaments.register', $tournament) }}" method="POST" class="mb-3">
                @csrf
                <div class="input-group">
                    <select name="participant_id" class="form-select form-select-sm">
                        <option value="">-- Pilih Peserta --</option>
                        @foreach($availableParticipants as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->username }})</option>
                        @endforeach
                    </select>
                    <button class="btn btn-success btn-sm">
                        <i class="bi bi-plus"></i> Daftarkan
                    </button>
                </div>
            </form>
            @else
            <div class="alert alert-warning py-2 small">Turnamen sudah penuh!</div>
            @endif

            @forelse($tournament->participants as $p)
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <span><i class="bi bi-person-circle me-1"></i>{{ $p->name }}</span>
                <form action="{{ route('tournaments.removeParticipant', [$tournament, $p]) }}"
                      method="POST" onsubmit="return confirm('Hapus peserta ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger py-0">
                        <i class="bi bi-x"></i>
                    </button>
                </form>
            </div>
            @empty
            <p class="text-muted small mb-0">Belum ada peserta terdaftar.</p>
            @endforelse
        </div>
    </div>

    {{-- Jadwal Pertandingan --}}
    <div class="col-md-7">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-controller me-2"></i>Jadwal Pertandingan</h6>
                <a href="{{ route('matches.create') }}?tournament_id={{ $tournament->id }}"
                   class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Tambah Match
                </a>
            </div>

            @forelse($tournament->matches->sortBy('round') as $match)
            <div class="card border mb-2 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Ronde {{ $match->round }} - Match #{{ $match->match_number }}</small>
                        <div class="fw-semibold">
                            {{ $match->participant1->name }}
                            <span class="text-muted mx-2">vs</span>
                            {{ $match->participant2->name }}
                        </div>
                        @if($match->status === 'completed')
                        <small class="text-success">
                            <i class="bi bi-trophy me-1"></i>Pemenang: {{ $match->winner->name }}
                            ({{ $match->score_participant1 }} - {{ $match->score_participant2 }})
                        </small>
                        @endif
                    </div>
                    <div class="d-flex gap-1">
                        <span class="badge {{ $match->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ ucfirst($match->status) }}
                        </span>
                        @if($match->status !== 'completed')
                        <a href="{{ route('matches.result.form', $match) }}"
                           class="btn btn-sm btn-success py-0">
                            <i class="bi bi-pencil-square"></i> Input Hasil
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <p class="text-muted small mb-0">Belum ada pertandingan dijadwalkan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection