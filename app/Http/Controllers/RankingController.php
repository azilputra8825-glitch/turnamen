<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Models\Tournament;

class RankingController extends Controller
{
    // Tampilkan ranking berdasarkan turnamen
    public function show(Tournament $tournament)
    {
        $rankings = Ranking::where('tournament_id', $tournament->id)
                           ->with('participant')
                           ->orderBy('rank')
                           ->get();

        return view('rankings.show', compact('tournament', 'rankings'));
    }
}