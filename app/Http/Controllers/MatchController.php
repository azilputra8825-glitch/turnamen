<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\Tournament;
use App\Models\Ranking;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        $matches = GameMatch::with(['tournament', 'participant1', 'participant2', 'winner'])
                            ->latest()->paginate(10);
        return view('matches.index', compact('matches'));
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tournament_id'    => 'required|exists:tournaments,id',
            'participant1_id'  => 'required|exists:participants,id',
            'participant2_id'  => 'required|exists:participants,id|different:participant1_id',
            'round'            => 'required|integer|min:1',
            'match_number'     => 'required|integer|min:1',
            'scheduled_at'     => 'nullable|date',
        ]);

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
    public function inputResult(Request $request, GameMatch $match)
    {
        $request->validate([
            'score_participant1' => 'required|integer|min:0',
            'score_participant2' => 'required|integer|min:0',
            'winner_id'          => 'required|in:' . $match->participant1_id . ',' . $match->participant2_id,
            'notes'              => 'nullable|string|max:500',
        ]);

        // Tentukan pemenang berdasarkan skor
        $winnerId = $request->winner_id;

        // Update data pertandingan
        $match->update([
            'score_participant1' => $request->score_participant1,
            'score_participant2' => $request->score_participant2,
            'winner_id'          => $winnerId,
            'played_at'          => now(),
            'status'             => 'completed',
            'notes'              => $request->notes,
        ]);

        // Update ranking kedua peserta
        $this->updateRanking($match, $winnerId);

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

    public function destroy(GameMatch $match)
    {
        $match->delete();
        return redirect()->route('tournaments.show', $match->tournament_id)
                         ->with('success', 'Pertandingan berhasil dihapus!');
    }
}