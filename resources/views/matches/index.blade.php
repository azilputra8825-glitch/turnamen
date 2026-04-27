@extends('layouts.tournament')
@section('title', 'Daftar Pertandingan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold"><i class="bi bi-controller me-2"></i>Daftar Pertandingan</h4>
    <a href="{{ route('matches.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Jadwalkan Match
    </a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Turnamen</th><th>Peserta</th>
                    <th>Ronde</th><th>Jadwal</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matches as $m)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $m->tournament->name }}</td>
                    <td>
                        {{ $m->participant1->name }}
                        <span class="text-muted">vs</span>
                        {{ $m->participant2->name }}
                    </td>
                    <td>Ronde {{ $m->round }}</td>
                    <td>{{ $m->scheduled_at ? $m->scheduled_at->format('d M Y H:i') : '-' }}</td>
                    <td>
                        <span class="badge {{ $m->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ ucfirst($m->status) }}
                        </span>
                    </td>
                    <td>
                        @if($m->status !== 'completed')
                        <a href="{{ route('matches.result.form', $m) }}" class="btn btn-sm btn-success">
                            <i class="bi bi-pencil-square"></i> Input Hasil
                        </a>
                        @else
                        <span class="text-success small">
                            <i class="bi bi-trophy"></i> {{ $m->winner->name }}
                        </span>
                        @endif
                        <form action="{{ route('matches.destroy', $m) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus pertandingan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pertandingan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $matches->links() }}</div>
</div>
@endsection