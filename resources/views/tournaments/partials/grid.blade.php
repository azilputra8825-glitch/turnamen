<div class="row g-3">
    @forelse($tournaments as $t)
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="card p-3 p-md-4 h-100 shadow-sm border-0 stat-card">
            <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                <h6 class="fw-bold mb-0 text-dark" style="word-break:break-word;">{{ $t->name }}</h6>
                <span class="badge flex-shrink-0
                    {{ $t->status === 'upcoming' ? 'bg-info text-white' :
                       ($t->status === 'ongoing' ? 'bg-success text-white' : 'bg-secondary text-white') }}">
                    {{ ucfirst($t->status) }}
                </span>
            </div>
            <p class="text-muted small mb-1">
                <i class="bi bi-controller me-1 text-primary"></i>{{ $t->game_name }}
                &nbsp;|&nbsp;
                <i class="bi bi-people me-1 text-success"></i>{{ $t->participants_count }}/{{ $t->max_participants }} Peserta
            </p>
            <p class="text-muted small mb-3">
                <i class="bi bi-calendar me-1 text-warning"></i>
                {{ \Carbon\Carbon::parse($t->start_date)->format('d M Y') }} -
                {{ \Carbon\Carbon::parse($t->end_date)->format('d M Y') }}
            </p>
            <div class="d-flex gap-2 flex-wrap mt-auto">
                <a href="{{ route('tournaments.show', $t) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye me-1"></i> Detail
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('tournaments.edit', $t) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <form action="{{ route('tournaments.destroy', $t) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Hapus turnamen ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                </form>
                @endif
                <a href="{{ route('rankings.show', $t) }}" class="btn btn-sm btn-info text-white">
                    <i class="bi bi-bar-chart me-1"></i> Ranking
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card p-5 text-center text-muted border-0 shadow-sm">
            <i class="bi bi-trophy fs-1 mb-2 text-warning opacity-50"></i>
            <p>Belum ada turnamen.</p>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('tournaments.create') }}" class="btn btn-success mx-auto" style="width:fit-content">
                Buat Turnamen
            </a>
            @endif
        </div>
    </div>
    @endforelse
</div>
<div class="mt-3">{{ $tournaments->links() }}</div>
