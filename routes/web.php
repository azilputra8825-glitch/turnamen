<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\RankingController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        $stats = [
            'total_participants'  => \App\Models\Participant::count(),
            'total_tournaments'   => \App\Models\Tournament::count(),
            'ongoing_tournaments' => \App\Models\Tournament::where('status', 'ongoing')->count(),
            'total_matches'       => \App\Models\GameMatch::count(),
        ];
        return view('dashboard', compact('stats'));
    })->name('dashboard');

    // Peserta
    Route::resource('participants', ParticipantController::class);

    // Turnamen
    Route::resource('tournaments', TournamentController::class);
    Route::post('tournaments/{tournament}/register', [TournamentController::class, 'registerParticipant'])->name('tournaments.register');
    Route::delete('tournaments/{tournament}/participants/{participant}', [TournamentController::class, 'removeParticipant'])->name('tournaments.removeParticipant');

    // Pertandingan
    Route::resource('matches', MatchController::class);
    Route::get('matches/{match}/result', function(\App\Models\GameMatch $match) {
        $match->load('participant1', 'participant2');
        return view('matches.result', compact('match'));
    })->name('matches.result.form');
    Route::post('matches/{match}/result', [MatchController::class, 'inputResult'])->name('matches.result');

    // Ranking
    Route::get('tournaments/{tournament}/rankings', [RankingController::class, 'show'])->name('rankings.show');

    // Ambil peserta by turnamen (untuk dropdown)
    Route::get('tournaments/{tournament}/participants-list', [MatchController::class, 'getParticipants'])->name('tournaments.participantsList');

});