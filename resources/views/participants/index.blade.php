@extends('layouts.tournament')
@section('title', 'Daftar Peserta')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Manajemen Peserta</h4>
    <a href="{{ route('participants.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Peserta
    </a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Nama</th><th>Username</th>
                    <th>Email</th><th>Game ID</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->name }}</td>
                    <td><code>{{ $p->username }}</code></td>
                    <td>{{ $p->email }}</td>
                    <td>{{ $p->game_id ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $p->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('participants.show', $p) }}" class="btn btn-sm btn-info text-white">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('participants.edit', $p) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('participants.destroy', $p) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus peserta ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada peserta.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $participants->links() }}</div>
</div>
@endsection