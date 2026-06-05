<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Participant;
use App\Models\GameMatch;
use App\Http\Requests\StoreTournamentRequest;
use App\Http\Requests\UpdateTournamentRequest;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index(Request $request)
    {
        $query = Tournament::withCount('participants')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('game_name', 'like', "%{$search}%");
            });
        }

        $tournaments = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('tournaments.partials.grid', compact('tournaments'))->render();
        }

        return view('tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        return view('tournaments.create');
    }

    public function store(StoreTournamentRequest $request)
    {
        $validated           = $request->validated();
        $validated['status'] = 'upcoming';

        // Upload gambar QRIS jika ada
        if ($request->hasFile('qris_image')) {
            $validated['qris_image'] = $request->file('qris_image')
                ->store('qris', 'public');
        } else {
            unset($validated['qris_image']);
        }

        Tournament::create($validated);

        return redirect()->route('tournaments.index')
                         ->with('success', 'Turnamen berhasil dibuat!');
    }

    public function show(Tournament $tournament)
    {
        $tournament->load('participants', 'matches.participant1', 'matches.participant2', 'matches.winner', 'rankings.participant');
        
        // Ambil peserta yang belum terdaftar di turnamen ini
        $registeredIds = $tournament->participants->pluck('id');
        $availableParticipants = Participant::where('status', 'active')
                                            ->whereNotIn('id', $registeredIds)
                                            ->get();

        return view('tournaments.show', compact('tournament', 'availableParticipants'));
    }

    public function edit(Tournament $tournament)
    {
        return view('tournaments.edit', compact('tournament'));
    }

    public function update(UpdateTournamentRequest $request, Tournament $tournament)
    {
        $validated = $request->validated();

        // Upload gambar QRIS baru jika ada
        if ($request->hasFile('qris_image')) {
            // Hapus gambar QRIS lama
            if ($tournament->qris_image) {
                \Storage::disk('public')->delete($tournament->qris_image);
            }
            $validated['qris_image'] = $request->file('qris_image')
                ->store('qris', 'public');
        } else {
            unset($validated['qris_image']);
        }

        $tournament->update($validated);

        return redirect()->route('tournaments.index')
                         ->with('success', 'Turnamen berhasil diperbarui!');
    }

    public function destroy(Tournament $tournament)
    {
        $tournament->delete();
        return redirect()->route('tournaments.index')
                         ->with('success', 'Turnamen berhasil dihapus!');
    }

    // Daftarkan peserta ke turnamen
    public function registerParticipant(Request $request, Tournament $tournament)
    {
        $request->validate([
            'participant_id' => 'required|exists:participants,id',
        ]);

        // Cek apakah turnamen sudah penuh
        if ($tournament->isFull()) {
            return back()->with('error', 'Turnamen sudah penuh!');
        }

        // Cek apakah sudah terdaftar
        $alreadyRegistered = $tournament->participants()
                                        ->where('participant_id', $request->participant_id)
                                        ->exists();
        if ($alreadyRegistered) {
            return back()->with('error', 'Peserta sudah terdaftar di turnamen ini!');
        }

        // Daftarkan peserta
        $tournament->participants()->attach($request->participant_id, [
            'status'        => 'confirmed',
            'registered_at' => now(),
        ]);

        // Buat entry ranking awal
        \App\Models\Ranking::firstOrCreate([
            'tournament_id'  => $tournament->id,
            'participant_id' => $request->participant_id,
        ]);

        return back()->with('success', 'Peserta berhasil didaftarkan!');
    }

    // Hapus peserta dari turnamen
    public function removeParticipant(Tournament $tournament, Participant $participant)
    {
        $tournament->participants()->detach($participant->id);
        return back()->with('success', 'Peserta berhasil dihapus dari turnamen!');
    }

    // ── Auto-Generate Bracket (Round 1 only) ────────────────────────────
    public function generateBracket(Request $request, Tournament $tournament)
    {
        $participants = $tournament->participants()
                                   ->wherePivot('status', 'confirmed')
                                   ->get();

        $count = $participants->count();

        if ($count < 2) {
            return back()->with('error', 'Minimal 2 peserta diperlukan untuk membuat bracket!');
        }

        // Hapus semua match lama yang belum selesai (reset bracket)
        GameMatch::where('tournament_id', $tournament->id)
                 ->where('status', '!=', 'completed')
                 ->delete();

        // Acak urutan peserta (seeding random)
        $seeded = $participants->shuffle()->values();

        // Hitung jumlah slot (next power of 2)
        $slots = (int) pow(2, ceil(log($count, 2)));
        while ($seeded->count() < $slots) {
            $seeded->push(null); // pad dengan bye
        }

        $matchNumber = 1;

        // ── Hanya buat match Round 1 ─────────────────────────────────────
        for ($i = 0; $i < $slots; $i += 2) {
            $p1 = $seeded[$i];
            $p2 = $seeded[$i + 1];

            // Dua bye berhadapan → skip
            if ($p1 === null && $p2 === null) continue;

            if ($p1 !== null && $p2 !== null) {
                // Match normal
                GameMatch::create([
                    'tournament_id'   => $tournament->id,
                    'participant1_id' => $p1->id,
                    'participant2_id' => $p2->id,
                    'round'           => 1,
                    'match_number'    => $matchNumber++,
                    'status'          => 'scheduled',
                ]);
            }
            // Jika salah satu bye → peserta langsung lolos tanpa match (tidak dibuat record)
        }

        $created = $matchNumber - 1;

        // Set status turnamen jadi ongoing
        $tournament->update(['status' => 'ongoing']);

        return back()->with('success',
            "Bracket Round 1 berhasil di-generate! {$count} peserta → {$created} match dibuat.");
    }

    // ── FITUR PENDAFTARAN MANDIRI PESERTA ─────────────────────────────
    public function joinTournament(Request $request, Tournament $tournament)
    {
        $request->validate([
            'phone'   => ['required', 'string', 'max:20', 'regex:/^(\+62|0)[0-9]{8,13}$/'],
            'game_id' => ['required', 'string', 'max:100'],
        ], [
            'phone.required'   => 'Nomor HP wajib diisi.',
            'phone.regex'      => 'Format nomor HP tidak valid (contoh: 08123456789 atau +628123456789).',
            'game_id.required' => 'Game ID wajib diisi.',
        ]);

        $user = auth()->user();

        // Buat username dari email
        $username = strstr($user->email, '@', true);
        $username = preg_replace('/[^a-zA-Z0-9_]/', '', $username);
        if (strlen($username) < 3) {
            $username = 'user_' . $user->id;
        }

        // Pastikan username unik di tabel participants
        $original = $username;
        $counter = 1;
        while (Participant::where('username', $username)->where('user_id', '!=', $user->id)->exists()) {
            $username = $original . $counter;
            $counter++;
        }

        // Dapatkan atau buat Participant yang terhubung dengan User ini
        $participant = Participant::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name'     => $user->name,
                'username' => $username,
                'email'    => $user->email,
                'phone'    => $request->phone,
                'game_id'  => $request->game_id,
                'status'   => 'active',
            ]
        );

        // Cek apakah turnamen sudah penuh
        if ($tournament->isFull()) {
            return back()->with('error', 'Turnamen sudah penuh!');
        }

        // Cek apakah sudah terdaftar
        $alreadyRegistered = $tournament->participants()
                                        ->where('participant_id', $participant->id)
                                        ->exists();
        if ($alreadyRegistered) {
            return back()->with('error', 'Anda sudah terdaftar di turnamen ini!');
        }

        // Daftarkan peserta
        // Jika turnamen gratis (entry_fee = 0), langsung set status = 'confirmed' dan payment_status = 'verified'
        $isFree = $tournament->entry_fee <= 0;
        
        $tournament->participants()->attach($participant->id, [
            'status'         => $isFree ? 'confirmed' : 'registered',
            'payment_status' => $isFree ? 'verified' : 'unpaid',
            'registered_at'  => now(),
        ]);

        // Buat entry ranking awal
        \App\Models\Ranking::firstOrCreate([
            'tournament_id'  => $tournament->id,
            'participant_id' => $participant->id,
        ]);

        $message = $isFree 
            ? 'Pendaftaran berhasil! Turnamen ini gratis, Anda langsung terdaftar sebagai peserta.'
            : 'Pendaftaran berhasil! Silakan lakukan pembayaran untuk menyelesaikan proses.';

        return back()->with('success', $message);
    }

    // ── FITUR UNGGAH BUKTI PEMBAYARAN ──────────────────────────────────
    public function uploadPaymentProof(Request $request, Tournament $tournament)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'payment_proof.required' => 'Bukti pembayaran wajib diunggah.',
            'payment_proof.image'    => 'File harus berupa gambar.',
            'payment_proof.mimes'    => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif.',
            'payment_proof.max'      => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $user = auth()->user();
        $participant = $user->participant;

        if (!$participant) {
            return back()->with('error', 'Profil peserta tidak ditemukan.');
        }

        // Cek apakah memang terdaftar di turnamen ini
        $pivot = $tournament->participants()->where('participant_id', $participant->id)->first();
        if (!$pivot) {
            return back()->with('error', 'Anda belum terdaftar di turnamen ini.');
        }

        // Upload file
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $filename = time() . '_' . $tournament->id . '_' . $participant->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payment_proofs', $filename, 'public');

            // Update pivot table
            $tournament->participants()->updateExistingPivot($participant->id, [
                'payment_status' => 'pending',
                'payment_proof'  => $path,
                'amount_paid'    => $tournament->entry_fee,
                'rejection_reason' => null, // reset rejection reason
            ]);

            return back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi admin.');
        }

        return back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }

    // ── VERIFIKASI PEMBAYARAN OLEH ADMIN ───────────────────────────────
    public function verifyPayment(Request $request, Tournament $tournament, Participant $participant)
    {
        $request->validate([
            'action'           => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:255',
        ], [
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi jika pembayaran ditolak.',
            'rejection_reason.max'         => 'Alasan penolakan maksimal 255 karakter.',
        ]);

        // Cek apakah peserta terdaftar di turnamen ini
        $isRegistered = $tournament->participants()->where('participant_id', $participant->id)->exists();
        if (!$isRegistered) {
            return back()->with('error', 'Peserta tidak terdaftar di turnamen ini.');
        }

        if ($request->action === 'approve') {
            // Setujui pembayaran
            $tournament->participants()->updateExistingPivot($participant->id, [
                'status'         => 'confirmed',
                'payment_status' => 'verified',
                'rejection_reason' => null,
            ]);

            // Buat entry ranking awal jika belum ada
            \App\Models\Ranking::firstOrCreate([
                'tournament_id'  => $tournament->id,
                'participant_id' => $participant->id,
            ]);

            return back()->with('success', 'Pembayaran berhasil diverifikasi dan pendaftaran peserta terkonfirmasi!');
        } else {
            // Tolak pembayaran
            $tournament->participants()->updateExistingPivot($participant->id, [
                'payment_status'   => 'rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);

            return back()->with('success', 'Pembayaran ditolak. Alasan penolakan telah dikirim ke peserta.');
        }
    }

    // ── KUITANSI PEMBAYARAN (untuk viewer setelah verified) ────────────
    public function paymentReceipt(Tournament $tournament)
    {
        $user        = auth()->user();
        $participant = $user->participant;

        if (!$participant) {
            abort(403, 'Anda belum memiliki profil peserta.');
        }

        $pivot = $tournament->participants()
                            ->where('participant_id', $participant->id)
                            ->first();

        if (!$pivot || $pivot->pivot->payment_status !== 'verified') {
            abort(403, 'Pembayaran belum terverifikasi atau Anda belum terdaftar.');
        }

        return view('tournaments.receipt', compact('tournament', 'participant', 'pivot'));
    }

    // Export data peserta ke CSV
    public function exportParticipants(Tournament $tournament)
    {
        $tournament->load('participants');
        $participants = $tournament->participants;

        $fileName = 'peserta_' . \Illuminate\Support\Str::slug($tournament->name) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'No.',
            'Nama Lengkap', 
            'Username', 
            'Email', 
            'Nomor HP', 
            'Game ID', 
            'Status Keikutsertaan', 
            'Status Pembayaran', 
            'Biaya Pendaftaran', 
            'Tanggal Registrasi'
        ];

        $callback = function() use($tournament, $participants, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Tambahkan Header Informasi Turnamen yang Rapi
            fputcsv($file, ['LAPORAN DATA PESERTA TURNAMEN'], ';');
            fputcsv($file, ['Nama Turnamen:', $tournament->name], ';');
            fputcsv($file, ['Game:', $tournament->game_name], ';');
            fputcsv($file, ['Format Turnamen:', ucfirst(str_replace('_', ' ', $tournament->format))], ';');
            fputcsv($file, ['Tanggal Cetak:', date('d-m-Y H:i')], ';');
            fputcsv($file, [], ';'); // Baris kosong untuk jarak
            
            fputcsv($file, $columns, ';');

            $no = 1;
            foreach ($participants as $p) {
                // Map status keikutsertaan ke Bahasa Indonesia
                $statusMap = [
                    'registered'   => 'Terdaftar (Pending)',
                    'confirmed'    => 'Terkonfirmasi (Aktif)',
                    'disqualified' => 'Diskualifikasi'
                ];
                $statusIndo = $statusMap[$p->pivot->status] ?? ucfirst($p->pivot->status);

                // Map status pembayaran ke Bahasa Indonesia
                $paymentMap = [
                    'unpaid'   => 'Belum Bayar',
                    'pending'  => 'Menunggu Verifikasi',
                    'verified' => 'Lunas (Terverifikasi)',
                    'rejected' => 'Ditolak'
                ];
                $paymentIndo = $paymentMap[$p->pivot->payment_status] ?? ucfirst($p->pivot->payment_status);

                // Format rupiah
                $entryFeeFormatted = 'Rp ' . number_format($p->pivot->amount_paid ?? $tournament->entry_fee ?? 0, 0, ',', '.');

                // Format tanggal registrasi
                $regDateFormatted = $p->pivot->registered_at 
                    ? \Carbon\Carbon::parse($p->pivot->registered_at)->format('d-m-Y H:i')
                    : '-';

                fputcsv($file, [
                    $no++,
                    $p->name,
                    '@' . $p->username,
                    $p->email,
                    $p->phone ?? '-',
                    $p->game_id ?? '-',
                    $statusIndo,
                    $paymentIndo,
                    $entryFeeFormatted,
                    $regDateFormatted
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}