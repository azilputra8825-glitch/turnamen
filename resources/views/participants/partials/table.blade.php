<div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th class="d-none d-sm-table-cell">Username</th>
                <th class="d-none d-md-table-cell">Email</th>
                <th class="d-none d-lg-table-cell">Game ID</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($participants as $p)
            <tr>
                <td class="text-muted small">{{ ($participants->currentPage() - 1) * $participants->perPage() + $loop->iteration }}</td>
                <td>
                    <div class="fw-semibold">{{ $p->name }}</div>
                    <div class="d-sm-none text-muted small"><code>{{ $p->username }}</code></div>
                </td>
                <td class="d-none d-sm-table-cell"><code class="small">{{ $p->username }}</code></td>
                <td class="d-none d-md-table-cell small">{{ $p->email }}</td>
                <td class="d-none d-lg-table-cell small">{{ $p->game_id ?? '-' }}</td>
                <td>
                    <span class="badge {{ $p->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1 flex-wrap">
                        <a href="{{ route('participants.show', $p) }}" class="btn btn-sm btn-info text-white">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('participants.edit', $p) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('participants.destroy', $p) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus peserta ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada peserta.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer">{{ $participants->links() }}</div>
