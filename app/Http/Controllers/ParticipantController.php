<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Http\Requests\StoreParticipantRequest;
use App\Http\Requests\UpdateParticipantRequest;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    // Tampilkan semua peserta
    public function index(Request $request)
    {
        $query = Participant::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('game_id', 'like', "%{$search}%");
            });
        }

        $participants = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('participants.partials.table', compact('participants'))->render();
        }

        return view('participants.index', compact('participants'));
    }

    // Tampilkan form tambah peserta
    public function create()
    {
        return view('participants.create');
    }

    // Simpan peserta baru
    public function store(StoreParticipantRequest $request)
    {
        Participant::create($request->validated());

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
    public function update(UpdateParticipantRequest $request, Participant $participant)
    {
        $participant->update($request->validated());

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