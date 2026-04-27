@extends('layouts.tournament')
@section('title', 'Ranking')
@section('content')
<div class="mb-3">
    <a href="{{ route('tournaments.show', $tournament) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali ke Turnamen
    </a>
</div>
<div class="card p-4">
    <h5 class="fw-bold mb-1"><i class="bi bi-bar-chart me-2"></i>Ranking Turnamen</h5>
    <p class="text-muted mb-4">{{ $tournament->name }}</p>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Rank</th><th>Peserta</th><th>Main</th>
                    <th>Menang</th><th>Kalah</th><th>Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rankings as $r)
                <tr class="{{ $r->rank <= 3 ? 'table-warning' : '' }}">
                    <td>
                        @if($r->rank == 1) 🥇
                        @elseif($r->rank == 2) 🥈
                        @elseif($r->rank == 3) 🥉
                        @else {{ $r->rank }}
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $r->participant->name }}</td>
                    <td>{{ $r->matches_played }}</td>
                    <td class="text-success fw-bold">{{ $r->wins }}</td>
                    <td class="text-danger">{{ $r->losses }}</td>
                    <td><span class="badge bg-primary fs-6">{{ $r->points }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data ranking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection