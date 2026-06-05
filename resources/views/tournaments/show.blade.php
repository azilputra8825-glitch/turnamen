@extends('layouts.tournament')
@section('title', 'Detail Turnamen')
@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2 page-header">
    <a href="{{ route('tournaments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <div class="d-flex gap-2 flex-wrap btn-group-actions">
        <a href="{{ route('rankings.show', $tournament) }}" class="btn btn-info text-white btn-sm">
            <i class="bi bi-bar-chart me-1"></i> Ranking
        </a>
        <a href="{{ route('tournaments.bracket', $tournament) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-diagram-3 me-1"></i> Bracket
        </a>

        @if(auth()->user()->isAdmin())
        <a href="{{ route('tournaments.export', $tournament) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Ekspor Peserta (CSV)
        </a>
        @if($tournament->participants->count() >= 2)
        <form action="{{ route('tournaments.generateBracket', $tournament) }}" method="POST"
              onsubmit="return confirm('{{ $tournament->matches->count() > 0 ? '⚠️ Match yang belum selesai akan dihapus dan bracket akan di-reset!\n\nLanjutkan?' : 'Generate bracket untuk ' . $tournament->participants->count() . ' peserta?\n\nPeserta akan diacak secara random.' }}')">
            @csrf
            <button class="btn btn-success btn-sm fw-semibold">
                <i class="bi bi-shuffle me-1"></i>
                {{ $tournament->matches->count() > 0 ? 'Re-Generate' : 'Generate Bracket' }}
            </button>
        </form>
        @else
        <button class="btn btn-success btn-sm" disabled title="Minimal 2 peserta">
            <i class="bi bi-shuffle me-1"></i> Generate Bracket
        </button>
        @endif

        <a href="{{ route('tournaments.edit', $tournament) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        @endif
    </div>
</div>

{{-- Info Turnamen --}}
<div class="card p-3 p-md-4 mb-3">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1">{{ $tournament->name }}</h5>
            <p class="text-muted mb-1 small"><i class="bi bi-controller me-1"></i>{{ $tournament->game_name }}</p>
            <p class="text-muted mb-1 small">
                <i class="bi bi-calendar me-1"></i>
                {{ $tournament->start_date->format('d M Y') }} - {{ $tournament->end_date->format('d M Y') }}
            </p>
            <p class="text-muted mb-0 small">
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
    <div class="mt-2 d-flex gap-3 flex-wrap">
        @if($tournament->prize_pool)
        <span class="text-success fw-semibold small">
            <i class="bi bi-cash-stack me-1"></i>Prize Pool: Rp {{ number_format($tournament->prize_pool, 0, ',', '.') }}
        </span>
        @endif
        <span class="text-primary fw-semibold small">
            <i class="bi bi-ticket-perforated me-1"></i>Biaya Pendaftaran:
            @if($tournament->entry_fee > 0)
                Rp {{ number_format($tournament->entry_fee, 0, ',', '.') }}
            @else
                <span class="badge bg-success">Gratis</span>
            @endif
        </span>
    </div>
</div>

{{-- Section Aksi Pengguna --}}
@auth
    @if(auth()->user()->isAdmin())
        {{-- PANEL VERIFIKASI PEMBAYARAN (ADMIN ONLY) --}}
        @php
            $pendingPayments = $tournament->participants->filter(function($p) {
                return $p->pivot->payment_status === 'pending';
            });
        @endphp

        @if($pendingPayments->count() > 0)
        <div class="card border-warning mb-4 p-3 p-md-4 shadow-sm">
            <h5 class="fw-bold text-warning-emphasis mb-3">
                <i class="bi bi-shield-exclamation me-2 text-warning"></i>Verifikasi Pembayaran ({{ $pendingPayments->count() }})
            </h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Peserta</th>
                            <th class="d-none d-sm-table-cell">Kontak</th>
                            <th>Nominal</th>
                            <th class="d-none d-md-table-cell">Waktu Daftar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingPayments as $p)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $p->name }}</div>
                                <div class="text-muted" style="font-size: .75rem;">@<span>{{ $p->username }}</span></div>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <div><i class="bi bi-phone text-secondary me-1"></i>{{ $p->phone ?? '-' }}</div>
                                <div class="text-muted" style="font-size: .75rem;"><i class="bi bi-controller text-secondary me-1"></i>{{ $p->game_id ?? '-' }}</div>
                            </td>
                            <td class="fw-bold text-success">
                                Rp {{ number_format($p->pivot->amount_paid, 0, ',', '.') }}
                            </td>
                            <td class="text-muted d-none d-md-table-cell">
                                {{ \Carbon\Carbon::parse($p->pivot->registered_at)->format('d M Y H:i') }}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalVerify{{ $p->id }}">
                                    <i class="bi bi-eye me-1"></i> Periksa
                                </button>

                                {{-- Modal Verifikasi --}}
                                <div class="modal fade" id="modalVerify{{ $p->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content text-start">
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-patch-check text-success me-2"></i>Verifikasi - {{ $p->name }}
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                @if($p->pivot->payment_proof)
                                                    <div class="mb-3 bg-dark-subtle p-2 rounded">
                                                        <small class="text-muted d-block mb-1">Bukti Transfer:</small>
                                                        <a href="{{ asset('storage/' . $p->pivot->payment_proof) }}" target="_blank" title="Klik untuk memperbesar">
                                                            <img src="{{ asset('storage/' . $p->pivot->payment_proof) }}" class="img-fluid rounded border shadow-sm" style="max-height: 300px; object-fit: contain;" alt="Bukti Transfer">
                                                        </a>
                                                        <span class="d-block text-muted small mt-1"><i class="bi bi-zoom-in me-1"></i>Klik untuk memperbesar</span>
                                                    </div>
                                                @else
                                                    <div class="alert alert-danger mb-3">Gambar bukti transfer tidak ditemukan!</div>
                                                @endif

                                                <div class="bg-light p-3 rounded text-start mb-3 small border">
                                                    <div class="row">
                                                        <div class="col-6 mb-2"><strong>No. HP:</strong> {{ $p->phone }}</div>
                                                        <div class="col-6 mb-2"><strong>Game ID:</strong> {{ $p->game_id }}</div>
                                                        <div class="col-12 border-top pt-2"><strong>Nominal:</strong> <span class="text-success fw-bold">Rp {{ number_format($p->pivot->amount_paid, 0, ',', '.') }}</span></div>
                                                    </div>
                                                </div>

                                                <div class="border-top pt-3">
                                                    <form action="{{ route('tournaments.verifyPayment', [$tournament, $p]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="action" value="approve">
                                                        <button class="btn btn-success w-100 mb-3 fw-semibold">
                                                            <i class="bi bi-check-circle me-1"></i>Setujui Pendaftaran
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('tournaments.verifyPayment', [$tournament, $p]) }}" method="POST" class="text-start p-3 border rounded bg-danger-subtle border-danger-subtle">
                                                        @csrf
                                                        <input type="hidden" name="action" value="reject">
                                                        <label class="form-label text-danger-emphasis small fw-bold mb-1">
                                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Tolak Pendaftaran
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="text" name="rejection_reason" class="form-control form-control-sm" placeholder="Alasan penolakan..." required>
                                                            <button class="btn btn-danger btn-sm">
                                                                <i class="bi bi-x-circle me-1"></i>Tolak
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @else
        {{-- VIEW PADA PESERTA (VIEWER) --}}
        @php
            $userParticipant = auth()->user()->participant;
            $isRegistered = false;
            $userPivot = null;
            if ($userParticipant) {
                $userPivot = $tournament->participants->where('id', $userParticipant->id)->first();
                if ($userPivot) {
                    $isRegistered = true;
                }
            }
        @endphp

        @if(!$isRegistered)
            @if($tournament->status === 'upcoming')
                @if(!$tournament->isFull())
                <div class="card border-primary p-3 p-md-4 mb-4 shadow-sm">
                    <h5 class="fw-bold text-primary mb-2"><i class="bi bi-ticket-perforated-fill me-2"></i>Pendaftaran Turnamen</h5>
                    <p class="text-muted small">Silakan daftarkan diri Anda sebagai peserta turnamen ini.</p>

                    <form action="{{ route('tournaments.join', $tournament) }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <label for="join_name" class="form-label fw-semibold small">Nama Lengkap</label>
                                <input type="text" id="join_name" class="form-control form-control-sm bg-light" value="{{ auth()->user()->name }}" readonly>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="join_email" class="form-label fw-semibold small">Email</label>
                                <input type="email" id="join_email" class="form-control form-control-sm bg-light" value="{{ auth()->user()->email }}" readonly>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="phone" class="form-label fw-semibold small">Nomor HP <span class="text-danger">*</span></label>
                                <input type="text" id="phone" name="phone" class="form-control form-control-sm @error('phone') is-invalid @enderror" value="{{ old('phone', $userParticipant->phone ?? '') }}" placeholder="Contoh: 08123456789" required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="game_id" class="form-label fw-semibold small">Game ID / In-Game Name <span class="text-danger">*</span></label>
                                <input type="text" id="game_id" name="game_id" class="form-control form-control-sm @error('game_id') is-invalid @enderror" value="{{ old('game_id', $userParticipant->game_id ?? '') }}" placeholder="Contoh: Budi#ML123" required>
                                @error('game_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top flex-wrap gap-2">
                            <span class="fw-semibold small">
                                Biaya Pendaftaran:
                                @if($tournament->entry_fee > 0)
                                    <span class="text-success fs-6">Rp {{ number_format($tournament->entry_fee, 0, ',', '.') }}</span>
                                @else
                                    <span class="badge bg-success px-2 py-1">Gratis (Free Entry)</span>
                                @endif
                            </span>
                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="bi bi-person-plus-fill me-1"></i> Daftar Sekarang
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <div class="alert alert-warning p-3 mb-4"><i class="bi bi-exclamation-triangle-fill me-2"></i>Pendaftaran ditutup karena kuota peserta sudah penuh!</div>
                @endif
            @else
            <div class="alert alert-secondary p-3 mb-4"><i class="bi bi-info-circle-fill me-2"></i>Pendaftaran ditutup karena turnamen ini sudah/telah berjalan (Status: {{ ucfirst($tournament->status) }}).</div>
            @endif
        @else
            {{-- Detail Status Pendaftaran & Pembayaran --}}
            <div class="card p-3 p-md-4 mb-4 border-info shadow-sm">
                <h5 class="fw-bold text-info mb-3"><i class="bi bi-info-circle-fill me-2"></i>Status Pendaftaran Anda</h5>

                <div class="row align-items-center mb-3 g-2">
                    <div class="col-12 col-sm-6">
                        <div class="text-muted small">Status Keikutsertaan:</div>
                        <div class="fw-bold mt-1">
                            @if($userPivot->pivot->status === 'confirmed')
                                <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Confirmed</span>
                            @elseif($userPivot->pivot->status === 'registered')
                                <span class="text-warning"><i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi</span>
                            @else
                                <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Disqualified</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="text-muted small">Status Pembayaran:</div>
                        <div class="fw-bold mt-1">
                            @if($userPivot->pivot->payment_status === 'verified')
                                <span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i>Lunas (Verified)</span>
                            @elseif($userPivot->pivot->payment_status === 'pending')
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Menunggu Verifikasi</span>
                            @elseif($userPivot->pivot->payment_status === 'rejected')
                                <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i>Ditolak</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-wallet2 me-1"></i>Belum Bayar</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($userPivot->pivot->payment_status === 'unpaid' || $userPivot->pivot->payment_status === 'rejected')
                    @if($userPivot->pivot->payment_status === 'rejected')
                        <div class="alert alert-danger py-2 small mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Bukti transfer ditolak:</strong> "{{ $userPivot->pivot->rejection_reason }}"
                        </div>
                    @endif

                    <div class="bg-light p-3 rounded mb-3 small border">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-credit-card me-1"></i>Instruksi Pembayaran:</h6>
                        <p class="mb-1 text-muted">Silakan transfer biaya pendaftaran sebesar:</p>
                        <div class="fs-4 fw-bold text-success mb-2">Rp {{ number_format($tournament->entry_fee, 0, ',', '.') }}</div>
                        <hr class="my-2">

                        @if($tournament->qris_image)
                        <div class="text-center my-3">
                            <p class="mb-2 fw-semibold text-dark small"><i class="bi bi-qr-code me-1"></i>Scan QRIS untuk Membayar:</p>
                            <img src="{{ asset('storage/' . $tournament->qris_image) }}"
                                 alt="QRIS {{ $tournament->name }}"
                                 class="img-fluid rounded border shadow-sm"
                                 style="max-width: 200px; max-height: 200px; object-fit: contain;">
                            <div class="text-muted small mt-1"><i class="bi bi-phone me-1"></i>Scan dengan aplikasi m-banking</div>
                        </div>
                        <hr class="my-2">
                        @endif

                        <p class="mb-1"><i class="bi bi-bank text-secondary me-1"></i>Bank: <strong>Bank Mandiri</strong></p>
                        <p class="mb-1"><i class="bi bi-credit-card-2-front text-secondary me-1"></i>No. Rekening: <strong>123-456-7890-12</strong></p>
                        <p class="mb-0"><i class="bi bi-person text-secondary me-1"></i>Atas Nama: <strong>Tournament Organizer</strong></p>
                    </div>

                    <form action="{{ route('tournaments.pay', $tournament) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="payment_proof" class="form-label fw-semibold small">Unggah Bukti Transfer <span class="text-danger">*</span></label>
                            <input type="file" id="payment_proof" name="payment_proof" class="form-control form-control-sm @error('payment_proof') is-invalid @enderror" accept="image/*" required>
                            @error('payment_proof')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text small text-muted">Format JPG, JPEG, PNG. Maksimal 2MB.</div>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100 py-2">
                            <i class="bi bi-cloud-upload me-1"></i> Kirim Bukti Pembayaran
                        </button>
                    </form>
                @elseif($userPivot->pivot->payment_status === 'pending')
                    <div class="alert alert-warning py-3 mb-0 small">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                            <div>
                                <strong>Bukti transfer terkirim!</strong> Pembayaran Anda sedang diperiksa Admin.
                            </div>
                        </div>
                        @if($userPivot->pivot->payment_proof)
                            <div class="mt-3 border-top pt-2">
                                <a class="text-decoration-none small text-warning" data-bs-toggle="collapse" href="#collapseProof" role="button">
                                    <i class="bi bi-image me-1"></i>Lihat Bukti yang Anda Unggah
                                </a>
                                <div class="collapse mt-2" id="collapseProof">
                                    <img src="{{ asset('storage/' . $userPivot->pivot->payment_proof) }}" class="img-fluid rounded border shadow-sm" style="max-height: 200px;" alt="Bukti Transfer">
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-success py-3 mb-0 small">
                        <i class="bi bi-patch-check-fill me-2 fs-5 align-middle text-success"></i>
                        <strong>Selamat!</strong> Pendaftaran Anda telah terkonfirmasi.
                        <div class="mt-3">
                            <a href="{{ route('tournaments.receipt', $tournament) }}" target="_blank"
                               class="btn btn-outline-success btn-sm fw-semibold">
                                <i class="bi bi-receipt me-1"></i> Lihat & Cetak Kuitansi
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @endif
@endauth

{{-- Daftar Peserta & Jadwal --}}
<div class="row g-3">
    {{-- Daftar Peserta --}}
    <div class="col-12 col-md-5">
        <div class="card p-3 p-md-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-people me-2"></i>Peserta Terdaftar</h6>

            @if(auth()->user()->isAdmin())
                @if(!$tournament->isFull())
                <form action="{{ route('tournaments.register', $tournament) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="input-group input-group-sm">
                        <select name="participant_id" class="form-select">
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
            @endif

            @forelse($tournament->participants as $p)
            <div class="d-flex justify-content-between align-items-center border-bottom py-2 flex-wrap gap-1">
                <span style="min-width:0;flex:1;">
                    <i class="bi bi-person-circle me-1"></i>
                    <span class="text-truncate">{{ $p->name }}</span>

                    @if($p->pivot->status === 'confirmed')
                        <span class="badge bg-success-subtle text-success py-0 px-1 ms-1" style="font-size:.62rem;">Confirmed</span>
                    @elseif($p->pivot->status === 'registered')
                        <span class="badge bg-warning-subtle text-warning-emphasis py-0 px-1 ms-1" style="font-size:.62rem;">Registered</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger py-0 px-1 ms-1" style="font-size:.62rem;">Disqualified</span>
                    @endif

                    @if($tournament->entry_fee > 0)
                        @if($p->pivot->payment_status === 'verified')
                            <span class="badge bg-success text-white py-0 px-1 ms-1" style="font-size:.58rem;">Lunas</span>
                        @elseif($p->pivot->payment_status === 'pending')
                            <span class="badge bg-warning text-dark py-0 px-1 ms-1" style="font-size:.58rem;">Pending</span>
                        @elseif($p->pivot->payment_status === 'rejected')
                            <span class="badge bg-danger text-white py-0 px-1 ms-1" style="font-size:.58rem;">Ditolak</span>
                        @else
                            <span class="badge bg-secondary text-white py-0 px-1 ms-1" style="font-size:.58rem;">Belum Bayar</span>
                        @endif
                    @endif
                </span>

                @if(auth()->user()->isAdmin())
                <form action="{{ route('tournaments.removeParticipant', [$tournament, $p]) }}"
                      method="POST" onsubmit="return confirm('Hapus peserta ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger py-0 flex-shrink-0">
                        <i class="bi bi-x"></i>
                    </button>
                </form>
                @endif
            </div>
            @empty
            <p class="text-muted small mb-0">Belum ada peserta terdaftar.</p>
            @endforelse
        </div>
    </div>

    {{-- Jadwal Pertandingan --}}
    <div class="col-12 col-md-7">
        <div class="card p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-controller me-2"></i>Jadwal Pertandingan</h6>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('matches.create') }}?tournament_id={{ $tournament->id }}"
                   class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Tambah Match
                </a>
                @endif
            </div>

            @forelse($tournament->matches->sortBy('round') as $match)
            <div class="card border mb-2 p-2 p-sm-3">
                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                    <div style="min-width:0;flex:1;">
                        <small class="text-muted">Ronde {{ $match->round }} - Match #{{ $match->match_number }}</small>
                        <div class="fw-semibold small">
                            {!! $match->participant1->name ?? '<em class="text-muted fw-normal">TBD</em>' !!}
                            <span class="text-muted mx-1">vs</span>
                            {!! $match->participant2->name ?? '<em class="text-muted fw-normal">TBD</em>' !!}
                        </div>
                        @if($match->status === 'completed' && $match->winner)
                        <small class="text-success">
                            <i class="bi bi-trophy me-1"></i>{{ $match->winner->name }}
                            ({{ $match->score_participant1 }} - {{ $match->score_participant2 }})
                        </small>
                        @endif
                    </div>
                    <div class="d-flex gap-1 flex-shrink-0">
                        <span class="badge {{ $match->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ ucfirst($match->status) }}
                        </span>
                        @if($match->status !== 'completed' && auth()->user()->isAdmin())
                        <a href="{{ route('matches.result.form', $match) }}"
                           class="btn btn-sm btn-success py-0 px-2">
                            <i class="bi bi-pencil-square"></i>
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