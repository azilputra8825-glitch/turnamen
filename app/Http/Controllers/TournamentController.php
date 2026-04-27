<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Participant;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::withCount('participants')->latest()->paginate(10);
        return view('tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        return view('tournaments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'game_name'        => 'required|string|max:100',
            'description'      => 'nullable|string',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'max_participants' => 'required|integer|min:2',
            'format'           => 'required|in:single_elimination,double_elimination,round_robin',
            'prize_pool'       => 'nullable|numeric|min:0',
        ]);

        $validated['status'] = 'upcoming';
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

    public function update(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'game_name'        => 'required|string|max:100',
            'description'      => 'nullable|string',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'max_participants' => 'required|integer|min:2',
            'format'           => 'required|in:single_elimination,double_elimination,round_robin',
            'status'           => 'required|in:upcoming,ongoing,completed',
            'prize_pool'       => 'nullable|numeric|min:0',
        ]);

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
}