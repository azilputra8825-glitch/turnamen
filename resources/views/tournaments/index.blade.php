@extends('layouts.tournament')
@section('title', 'Daftar Turnamen')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold"><i class="bi bi-trophy me-2"></i>Manajemen Turnamen</h4>
    <a href="{{ route('tournaments.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle me-1"></i> Buat Turnamen
    </a>
</div>
<div class="row g-3">
    @forelse($tournaments as $t)
    <div class="col-md-6">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="fw-bold mb-0">{{ $t->name }}</h6>
                <span class="badge
                    {{ $t->status === 'upcoming' ? 'bg-info' :
                       ($t->status === 'ongoing' ? 'bg-success' : 'bg-secondary') }}">
                    {{ ucfirst($t->status) }}
                </span>
            </div>
            <p class="text-muted small mb-2">
                <i class="bi bi-controller me-1"></i>{{ $t->game_name }}
                &nbsp;|&nbsp;
                <i class="bi bi-people me-1"></i>{{ $t->participants_count }}/{{ $t->max_participants }} Peserta
            </p>
            <p class="text-muted small mb-3">
                <i class="bi bi-calendar me-1"></i>
                {{ \Carbon\Carbon::parse($t->start_date)->format('d M Y') }} -
                {{ \Carbon\Carbon::parse($t->end_date)->format('d M Y') }}
            </p>
            <div class="d-flex gap-2">
                <a href="{{ route('tournaments.show', $t) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye me-1"></i> Detail
                </a>
                <a href="{{ route('tournaments.edit', $t) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('rankings.show', $t) }}" class="btn btn-sm btn-info text-white">
                    <i class="bi bi-bar-chart me-1"></i> Ranking
                </a>
                <form action="{{ route('tournaments.destroy', $t) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Hapus turnamen ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card p-5 text-center text-muted">
            <i class="bi bi-trophy fs-1 mb-2"></i>
            <p>Belum ada turnamen. Buat turnamen pertama!</p>
            <a href="{{ route('tournaments.create') }}" class="btn btn-success mx-auto" style="width:fit-content">
                Buat Turnamen
            </a>
        </div>
    </div>
    @endforelse
</div>
<div class="mt-3">{{ $tournaments->links() }}</div>
@endsection