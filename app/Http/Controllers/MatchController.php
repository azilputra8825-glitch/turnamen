<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\Tournament;
use App\Models\Ranking;
use App\Http\Requests\StoreMatchRequest;
use App\Http\Requests\InputResultRequest;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        $query = GameMatch::with(['tournament', 'participant1', 'participant2', 'winner']);

        if ($request->filled('tournament_id')) {
            $query->where('tournament_id', $request->tournament_id);
        }

        $matches = $query->latest()->paginate(15);
        $tournaments = Tournament::orderBy('name')->get();

        return view('matches.index', compact('matches', 'tournaments'));
    }

    public function create()
    {
        $tournaments = Tournament::where('status', '!=', 'completed')->get();
        return view('matches.create', compact('tournaments'));
    }

    // Ambil peserta berdasarkan turnamen (untuk dropdown dinamis)
    public function getParticipants(Tournament $tournament)
    {
        $participants = $tournament->participants()
                                ->wherePivot('status', 'confirmed')
                                ->get();
        return response()->json($participants);
    }

    public function store(StoreMatchRequest $request)
    {
        $validated           = $request->validated();
        $validated['status'] = 'scheduled';
        GameMatch::create($validated);

        return redirect()->route('tournaments.show', $request->tournament_id)
                         ->with('success', 'Pertandingan berhasil dijadwalkan!');
    }

    public function show(GameMatch $match)
    {
        $match->load('tournament', 'participant1', 'participant2', 'winner');
        return view('matches.show', compact('match'));
    }

    public function edit(GameMatch $match)
    {
        $match->load('tournament', 'participant1', 'participant2');
        return view('matches.edit', compact('match'));
    }

    // ⭐ Fitur utama: Input hasil pertandingan
    public function inputResult(InputResultRequest $request, GameMatch $match)
    {
        $match->update([
            'score_participant1' => $request->score_participant1,
            'score_participant2' => $request->score_participant2,
            'winner_id'          => $request->winner_id,
            'played_at'          => now(),
            'status'             => 'completed',
            'notes'              => $request->notes,
        ]);

        // Update ranking kedua peserta
        $this->updateRanking($match, $request->winner_id);

        return redirect()->route('tournaments.show', $match->tournament_id)
                         ->with('success', 'Hasil pertandingan berhasil diinput!');
    }

    // Update ranking setelah pertandingan selesai
    private function updateRanking(GameMatch $match, $winnerId)
    {
        $loserId = ($winnerId == $match->participant1_id)
                   ? $match->participant2_id
                   : $match->participant1_id;

        // Update ranking pemenang: +3 poin, +1 menang
        $winnerRanking = Ranking::where('tournament_id', $match->tournament_id)
                                ->where('participant_id', $winnerId)
                                ->first();
        if ($winnerRanking) {
            $winnerRanking->increment('wins');
            $winnerRanking->increment('points', 3);
            $winnerRanking->increment('matches_played');
        }

        // Update ranking yang kalah: +0 poin, +1 kalah
        $loserRanking = Ranking::where('tournament_id', $match->tournament_id)
                               ->where('participant_id', $loserId)
                               ->first();
        if ($loserRanking) {
            $loserRanking->increment('losses');
            $loserRanking->increment('matches_played');
        }

        // Hitung ulang urutan ranking berdasarkan poin
        $this->recalculateRanks($match->tournament_id);
    }

    // Hitung ulang posisi rank semua peserta dalam turnamen
    private function recalculateRanks($tournamentId)
    {
        $rankings = Ranking::where('tournament_id', $tournamentId)
                           ->orderByDesc('points')
                           ->orderByDesc('wins')
                           ->get();

        foreach ($rankings as $index => $ranking) {
            $ranking->update(['rank' => $index + 1]);
        }
    }

    // ── Auto-advance pemenang ke match babak berikutnya ──────────────────
    private function advanceWinnerToNextRound(GameMatch $match, $winnerId)
    {
        $nextRound       = $match->round + 1;
        $nextMatchNumber = (int) ceil($match->match_number / 2);
        $isFirstSlot     = ($match->match_number % 2 === 1);

        // Cek apakah ada sibling feeder di round yang sama
        $siblingMatchNumber = $isFirstSlot
            ? $match->match_number + 1
            : $match->match_number - 1;

        $siblingExists = GameMatch::where('tournament_id', $match->tournament_id)
                                  ->where('round', $match->round)
                                  ->where('match_number', $siblingMatchNumber)
                                  ->exists();

        // Cari match babak berikutnya yang sudah ada
        $nextMatch = GameMatch::where('tournament_id', $match->tournament_id)
                              ->where('round', $nextRound)
                              ->where('match_number', $nextMatchNumber)
                              ->first();

        if ($nextMatch) {
            // Match sudah ada → update slot yang sesuai
            if ($isFirstSlot) {
                $nextMatch->update(['participant1_id' => $winnerId]);
            } else {
                $nextMatch->update(['participant2_id' => $winnerId]);
            }
        } elseif ($siblingExists) {
            // Buat match baru HANYA jika ada sibling (ada lawan yang akan datang)
            GameMatch::create([
                'tournament_id'   => $match->tournament_id,
                'participant1_id' => $isFirstSlot ? $winnerId : null,
                'participant2_id' => $isFirstSlot ? null : $winnerId,
                'round'           => $nextRound,
                'match_number'    => $nextMatchNumber,
                'status'          => 'scheduled',
            ]);
        }
        // Jika tidak ada sibling DAN tidak ada next match → ini juara, tidak buat match baru
    }

    // ── Auto-resolve match TBD yang tidak akan pernah terisi ─────────────
    private function autoResolveTBDMatches(Tournament $tournament)
    {
        // ── 1. Hitung max round yang sah berdasarkan jumlah peserta ─────
        $participantCount = $tournament->participants()
                                       ->wherePivot('status', 'confirmed')
                                       ->count();
        $maxRound = $participantCount > 1
            ? (int) ceil(log($participantCount, 2))
            : 1;

        // ── 2. Hapus "ghost rounds" di atas maxRound (sisa bug lama) ────
        GameMatch::where('tournament_id', $tournament->id)
                  ->where('round', '>', $maxRound)
                  ->delete();

        // ── 3. Resolve TBD matches yang sah (dalam maxRound) ────────────
        for ($pass = 0; $pass < $maxRound; $pass++) {
            $pendingMatches = GameMatch::where('tournament_id', $tournament->id)
                                       ->where('status', '!=', 'completed')
                                       ->where(function ($q) {
                                           $q->whereNull('participant1_id')
                                             ->orWhereNull('participant2_id');
                                       })
                                       ->orderBy('round')
                                       ->orderBy('match_number')
                                       ->get();

            foreach ($pendingMatches as $match) {
                $hasP1 = $match->participant1_id !== null;
                $hasP2 = $match->participant2_id !== null;
                if (!$hasP1 && !$hasP2) continue;

                // Tentukan feeder match yang akan mengisi slot kosong
                $prevRound         = $match->round - 1;
                $emptySlot         = !$hasP1 ? 'participant1' : 'participant2';
                $feederMatchNumber = ($emptySlot === 'participant1')
                    ? ($match->match_number * 2 - 1)
                    : ($match->match_number * 2);

                // Feeder ada jika prevRound ≥ 1 dan match-nya ada di DB
                $feederExists = ($prevRound >= 1) && GameMatch::where('tournament_id', $tournament->id)
                                                              ->where('round', $prevRound)
                                                              ->where('match_number', $feederMatchNumber)
                                                              ->exists();

                if (!$feederExists) {
                    // Tidak ada feeder → lawan tidak akan pernah datang → auto-complete
                    $autoWinnerId = $match->participant1_id ?? $match->participant2_id;

                    $match->update([
                        'winner_id'          => $autoWinnerId,
                        'score_participant1'  => $hasP1 ? 1 : 0,
                        'score_participant2'  => $hasP2 ? 1 : 0,
                        'played_at'          => now(),
                        'status'             => 'completed',
                        'notes'              => 'Auto-advance: lawan tidak ada (bye)',
                    ]);

                    // Update ranking
                    $winnerRanking = Ranking::where('tournament_id', $tournament->id)
                                            ->where('participant_id', $autoWinnerId)
                                            ->first();
                    if ($winnerRanking) {
                        $winnerRanking->increment('wins');
                        $winnerRanking->increment('points', 3);
                        $winnerRanking->increment('matches_played');
                    }
                    $this->recalculateRanks($tournament->id);

                    // Advance ke next round (tidak akan buat match baru jika tidak ada sibling)
                    $this->advanceWinnerToNextRound($match, $autoWinnerId);
                }
            }
        }

        // ── 4. Auto-complete tournament jika semua match selesai ─────────
        $total     = GameMatch::where('tournament_id', $tournament->id)->count();
        $completed = GameMatch::where('tournament_id', $tournament->id)
                              ->where('status', 'completed')->count();
        if ($total > 0 && $total === $completed) {
            $tournament->update(['status' => 'completed']);
        }
    }

    public function destroy(GameMatch $match)
    {
        $match->delete();
        return redirect()->route('tournaments.show', $match->tournament_id)
                         ->with('success', 'Pertandingan berhasil dihapus!');
    }

    public function bracket(Tournament $tournament)
    {
        // Bersihkan ghost rounds & resolve TBD yang sah
        $this->autoResolveTBDMatches($tournament);

        $matches = GameMatch::with(['participant1', 'participant2', 'winner'])
                            ->where('tournament_id', $tournament->id)
                            ->orderBy('round')
                            ->orderBy('match_number')
                            ->get();

        return view('matches.bracket', compact('tournament', 'matches'));
    }

    public function saveWinner(Request $request, Tournament $tournament)
    {
        $request->validate([
            'match_id'           => 'required|exists:matches,id',
            'winner_id'          => 'required|exists:participants,id',
            'score_participant1' => 'nullable|integer|min:0',
            'score_participant2' => 'nullable|integer|min:0',
        ]);

        $match = GameMatch::where('id', $request->match_id)
                          ->where('tournament_id', $tournament->id)
                          ->firstOrFail();

        if (!in_array($request->winner_id, [$match->participant1_id, $match->participant2_id])) {
            return response()->json(['success' => false, 'message' => 'Pemenang tidak valid.'], 422);
        }

        $match->update([
            'winner_id'          => $request->winner_id,
            'score_participant1' => $request->score_participant1 ?? 0,
            'score_participant2' => $request->score_participant2 ?? 0,
            'played_at'          => now(),
            'status'             => 'completed',
        ]);

        $this->updateRanking($match, $request->winner_id);

        // ── Auto-complete tournament jika semua match selesai ────────────
        $totalMatches     = GameMatch::where('tournament_id', $tournament->id)->count();
        $completedMatches = GameMatch::where('tournament_id', $tournament->id)
                                     ->where('status', 'completed')->count();
        if ($totalMatches > 0 && $totalMatches === $completedMatches) {
            $tournament->update(['status' => 'completed']);
        }

        return response()->json(['success' => true, 'message' => 'Juara berhasil disimpan!']);
    }

    // ── Update skor dari bracket (AJAX) ──────────────────────────────────
    public function updateScore(Request $request, GameMatch $match)
    {
        $allowedWinners = array_filter([
            $match->participant1_id,
            $match->participant2_id,
        ]);

        $request->validate([
            'score_participant1' => 'required|integer|min:0',
            'score_participant2' => 'required|integer|min:0',
            'winner_id'          => 'required|in:' . implode(',', $allowedWinners),
        ]);

        $match->update([
            'score_participant1' => $request->score_participant1,
            'score_participant2' => $request->score_participant2,
            'winner_id'          => $request->winner_id,
            'played_at'          => now(),
            'status'             => 'completed',
        ]);

        $this->updateRanking($match, $request->winner_id);

        // Auto-advance pemenang ke match babak berikutnya
        $this->advanceWinnerToNextRound($match, $request->winner_id);

        // Auto-complete tournament jika semua match selesai
        $tournament       = $match->tournament;
        $totalMatches     = GameMatch::where('tournament_id', $tournament->id)->count();
        $completedMatches = GameMatch::where('tournament_id', $tournament->id)
                                     ->where('status', 'completed')->count();
        if ($totalMatches > 0 && $totalMatches === $completedMatches) {
            $tournament->update(['status' => 'completed']);
        }

        // Load updated match list untuk response (agar bracket bisa re-render dengan match baru)
        $allMatches = GameMatch::with(['participant1', 'participant2', 'winner'])
                               ->where('tournament_id', $match->tournament_id)
                               ->orderBy('round')
                               ->orderBy('match_number')
                               ->get();

        return response()->json([
            'success'  => true,
            'message'  => 'Skor berhasil disimpan! Pemenang maju ke babak berikutnya.',
            'matches'  => $allMatches,
        ]);
    }
}