<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    // Tampilkan semua peserta
    public function index()
    {
        $participants = Participant::latest()->paginate(10);
        return view('participants.index', compact('participants'));
    }

    // Tampilkan form tambah peserta
    public function create()
    {
        return view('participants.create');
    }

    // Simpan peserta baru
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:participants',
            'email'    => 'required|email|unique:participants',
            'phone'    => 'nullable|string|max:20',
            'game_id'  => 'nullable|string|max:100',
            'status'   => 'required|in:active,inactive',
        ]);

        Participant::create($validated);

        return redirect()->route('participants.index')
                         ->with('success', 'Peserta berhasil ditambahkan!');
    }

    // Tampilkan detail 1 peserta
    public function show(Participant $participant)
    {
        // Ambil juga turnamen dan ranking peserta ini
        $participant->load('tournaments', 'rankings.tournament');
        return view('participants.show', compact('participant'));
    }

    // Tampilkan form edit peserta
    public function edit(Participant $participant)
    {
        return view('participants.edit', compact('participant'));
    }

    // Update data peserta
    public function update(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:participants,username,' . $participant->id,
            'email'    => 'required|email|unique:participants,email,' . $participant->id,
            'phone'    => 'nullable|string|max:20',
            'game_id'  => 'nullable|string|max:100',
            'status'   => 'required|in:active,inactive',
        ]);

        $participant->update($validated);

        return redirect()->route('participants.index')
                         ->with('success', 'Data peserta berhasil diperbarui!');
    }

    // Hapus peserta
    public function destroy(Participant $participant)
    {
        $participant->delete();
        return redirect()->route('participants.index')
                         ->with('success', 'Peserta berhasil dihapus!');
    }
}