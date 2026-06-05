<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran – {{ $tournament->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f8;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        /* ── Tombol aksi (hanya tampil di layar) ── */
        .action-bar {
            display: flex;
            gap: .75rem;
            margin-bottom: 1.5rem;
            width: 100%;
            max-width: 680px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .55rem 1.25rem;
            border: none;
            border-radius: 8px;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: filter .15s;
        }
        .btn:hover { filter: brightness(.9); }
        .btn-primary   { background:#2563eb; color:#fff; }
        .btn-secondary { background:#e5e7eb; color:#374151; }

        /* ── Kartu Kuitansi ── */
        .receipt {
            width: 100%;
            max-width: 680px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10);
            overflow: hidden;
        }

        /* Header */
        .receipt-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            color: #fff;
            padding: 2rem 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }
        .receipt-header h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: .25rem; }
        .receipt-header p  { font-size: .85rem; opacity: .8; }
        .badge-verified {
            background: #22c55e;
            color: #fff;
            font-size: .75rem;
            font-weight: 700;
            padding: .3rem .75rem;
            border-radius: 999px;
            letter-spacing: .04em;
            white-space: nowrap;
        }

        /* Body */
        .receipt-body { padding: 2rem 2.5rem; }

        /* Nomor kuitansi */
        .receipt-no {
            font-size: .8rem;
            color: #6b7280;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px dashed #e5e7eb;
        }
        .receipt-no strong { color: #111; font-size: .875rem; }

        /* Baris info */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem 2rem;
            margin-bottom: 1.5rem;
        }
        .info-item label {
            display: block;
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #9ca3af;
            margin-bottom: .2rem;
        }
        .info-item .value {
            font-size: .925rem;
            font-weight: 600;
            color: #111827;
        }
        .info-item .value.amount {
            font-size: 1.35rem;
            font-weight: 700;
            color: #16a34a;
        }

        /* Divider */
        .divider { border: none; border-top: 1px solid #f3f4f6; margin: 1.5rem 0; }

        /* Bukti transfer */
        .proof-section { margin-bottom: 1.5rem; }
        .proof-section h3 { font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; margin-bottom: .75rem; }
        .proof-section img { max-width: 200px; max-height: 200px; object-fit: contain; border-radius: 8px; border: 1px solid #e5e7eb; }

        /* Footer */
        .receipt-footer {
            background: #f9fafb;
            border-top: 1px solid #f3f4f6;
            padding: 1.25rem 2.5rem;
            font-size: .78rem;
            color: #9ca3af;
            text-align: center;
            line-height: 1.6;
        }

        /* Watermark verified */
        .watermark {
            text-align: center;
            padding: .75rem 0 0;
        }
        .watermark svg { width: 48px; height: 48px; color: #22c55e; }
        .watermark p { font-size: .8rem; font-weight: 600; color: #16a34a; margin-top: .25rem; }

        /* ── Print Styles ── */
        @media print {
            body { background: #fff; padding: 0; }
            .action-bar { display: none !important; }
            .receipt {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }
        }

        /* ── Mobile Responsive ── */
        @media (max-width: 600px) {
            .receipt-header {
                padding: 1.25rem 1.25rem;
                flex-direction: column;
                gap: .6rem;
            }
            .receipt-body { padding: 1.25rem; }
            .receipt-footer { padding: 1rem 1.25rem; }
            .info-grid { grid-template-columns: 1fr; gap: .6rem; }
            .info-item[style*="span 2"] { grid-column: span 1; }
            .action-bar .btn { flex: 1 1 auto; justify-content: center; }
        }
    </style>
</head>
<body>

    {{-- Tombol aksi (tidak muncul saat cetak) --}}
    <div class="action-bar">
        <button class="btn btn-primary" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak / Simpan PDF
        </button>
        <a href="{{ route('tournaments.show', $tournament) }}" class="btn btn-secondary">
            ← Kembali ke Turnamen
        </a>
    </div>

    {{-- Kartu Kuitansi --}}
    <div class="receipt">

        {{-- Header --}}
        <div class="receipt-header">
            <div>
                <h1>Kuitansi Pembayaran</h1>
                <p>{{ $tournament->name }} &bull; {{ $tournament->game_name }}</p>
            </div>
            <span class="badge-verified">✓ LUNAS</span>
        </div>

        {{-- Body --}}
        <div class="receipt-body">

            {{-- No. Kuitansi --}}
            <div class="receipt-no">
                No. Kuitansi: <strong>TRN-{{ str_pad($tournament->id, 4, '0', STR_PAD_LEFT) }}-PST-{{ str_pad($participant->id, 5, '0', STR_PAD_LEFT) }}</strong>
                &bull; Diterbitkan: <strong>{{ \Carbon\Carbon::now()->format('d F Y, H:i') }} WIB</strong>
            </div>

            {{-- Grid info --}}
            <div class="info-grid">
                <div class="info-item">
                    <label>Nama Peserta</label>
                    <div class="value">{{ $participant->name }}</div>
                </div>
                <div class="info-item">
                    <label>Username</label>
                    <div class="value">@{{ $participant->username }}</div>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <div class="value">{{ $participant->email }}</div>
                </div>
                <div class="info-item">
                    <label>No. HP</label>
                    <div class="value">{{ $participant->phone ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <label>Game ID</label>
                    <div class="value">{{ $participant->game_id ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <label>Tanggal Daftar</label>
                    <div class="value">
                        {{ \Carbon\Carbon::parse($pivot->pivot->registered_at)->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>

            <hr class="divider">

            <div class="info-grid">
                <div class="info-item">
                    <label>Turnamen</label>
                    <div class="value">{{ $tournament->name }}</div>
                </div>
                <div class="info-item">
                    <label>Game</label>
                    <div class="value">{{ $tournament->game_name }}</div>
                </div>
                <div class="info-item">
                    <label>Periode Turnamen</label>
                    <div class="value">{{ $tournament->start_date->format('d M Y') }} – {{ $tournament->end_date->format('d M Y') }}</div>
                </div>
                <div class="info-item">
                    <label>Status Pembayaran</label>
                    <div class="value" style="color:#16a34a;">✓ Verified</div>
                </div>
                <div class="info-item" style="grid-column: span 2">
                    <label>Jumlah Dibayar</label>
                    <div class="value amount">Rp {{ number_format($pivot->pivot->amount_paid, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Bukti Transfer --}}
            @if($pivot->pivot->payment_proof)
            <hr class="divider">
            <div class="proof-section">
                <h3>Bukti Transfer</h3>
                <img src="{{ asset('storage/' . $pivot->pivot->payment_proof) }}" alt="Bukti Transfer">
            </div>
            @endif

            {{-- Watermark --}}
            <div class="watermark">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" style="color:#22c55e;">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                </svg>
                <p>Pembayaran Telah Dikonfirmasi oleh Admin</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="receipt-footer">
            Kuitansi ini diterbitkan secara digital oleh sistem turnamen.<br>
            Simpan kuitansi ini sebagai bukti pendaftaran resmi Anda di <strong>{{ $tournament->name }}</strong>.<br>
            Semoga sukses dalam pertandingan! 🏆
        </div>
    </div>

</body>
</html>
