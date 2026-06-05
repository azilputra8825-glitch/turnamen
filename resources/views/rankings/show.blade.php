@extends('layouts.tournament')
@section('title', 'Ranking – ' . $tournament->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 page-header">
    <div>
        <a href="{{ route('tournaments.show', $tournament) }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Turnamen
        </a>
        <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Ranking Turnamen</h5>
        <small class="text-muted">{{ $tournament->name }}</small>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:60px">Rank</th>
                    <th>Peserta</th>
                    <th class="text-center d-none d-sm-table-cell" style="width:70px">Main</th>
                    <th class="text-center" style="width:70px">Menang</th>
                    <th class="text-center d-none d-sm-table-cell" style="width:70px">Kalah</th>
                    <th class="text-center" style="width:80px">Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rankings as $r)
                <tr class="{{ $r->rank <= 3 ? 'table-warning' : '' }}">
                    <td class="text-center fw-bold fs-5">
                        @if($r->rank == 1) 🥇
                        @elseif($r->rank == 2) 🥈
                        @elseif($r->rank == 3) 🥉
                        @else <span class="text-muted">{{ $r->rank }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $r->participant->name }}</div>
                        {{-- Show stats inline on xs --}}
                        <div class="d-sm-none small text-muted mt-1">
                            {{ $r->matches_played }} main &middot;
                            <span class="text-success fw-bold">{{ $r->wins }} menang</span> &middot;
                            <span class="text-danger">{{ $r->losses }} kalah</span>
                        </div>
                    </td>
                    <td class="text-center d-none d-sm-table-cell text-muted">{{ $r->matches_played }}</td>
                    <td class="text-center text-success fw-bold">{{ $r->wins }}</td>
                    <td class="text-center text-danger d-none d-sm-table-cell">{{ $r->losses }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary px-2 py-1">{{ $r->points }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-bar-chart fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada data ranking.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection