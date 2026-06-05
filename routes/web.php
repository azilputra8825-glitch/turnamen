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

        $isAdmin = auth()->user()->isAdmin();
        $tournamentStatusData = [];
        $gamePopularityData = [];
        $monthlyTrendData = [];

        if ($isAdmin) {
            // 1. Data Status Turnamen
            $tournaments = \App\Models\Tournament::select('status', \DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();
            $statusLabels = ['upcoming' => 'Upcoming', 'ongoing' => 'Ongoing', 'completed' => 'Completed'];
            $tournamentStatusData = [
                'labels' => [],
                'values' => [],
            ];
            foreach ($statusLabels as $key => $label) {
                $count = $tournaments->where('status', $key)->first()?->total ?? 0;
                $tournamentStatusData['labels'][] = $label;
                $tournamentStatusData['values'][] = $count;
            }

            // 2. Data Game Terpopuler
            $games = \App\Models\Tournament::select('game_name', \DB::raw('count(*) as total'))
                ->groupBy('game_name')
                ->orderBy('total', 'desc')
                ->take(5)
                ->get();
            $gamePopularityData = [
                'labels' => $games->pluck('game_name')->toArray(),
                'values' => $games->pluck('total')->toArray(),
            ];

            // 3. Tren Bulanan Pendaftaran & Pendapatan (6 bulan terakhir)
            $monthlyRaw = \DB::table('tournament_participant')
                ->select(
                    \DB::raw("DATE_FORMAT(registered_at, '%Y-%m') as month"),
                    \DB::raw("COUNT(*) as registrations"),
                    \DB::raw("SUM(CASE WHEN payment_status = 'verified' THEN amount_paid ELSE 0 END) as revenue")
                )
                ->where('registered_at', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            $monthlyTrendData = [
                'labels' => [],
                'registrations' => [],
                'revenue' => [],
            ];
            
            $monthsMap = [
                'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun',
                'Jul' => 'Jul', 'Aug' => 'Agt', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'
            ];

            for ($i = 5; $i >= 0; $i--) {
                $monthObj = now()->subMonths($i);
                $monthKey = $monthObj->format('Y-m');
                $shortMonth = $monthObj->format('M');
                $indonesianMonth = isset($monthsMap[$shortMonth]) ? $monthsMap[$shortMonth] : $shortMonth;
                $monthName = $indonesianMonth . ' ' . $monthObj->format('Y');
                
                $match = $monthlyRaw->firstWhere('month', $monthKey);
                
                $monthlyTrendData['labels'][] = $monthName;
                $monthlyTrendData['registrations'][] = $match ? (int)$match->registrations : 0;
                $monthlyTrendData['revenue'][] = $match ? (float)$match->revenue : 0.0;
            }
        }

        return view('dashboard', compact('stats', 'tournamentStatusData', 'gamePopularityData', 'monthlyTrendData', 'isAdmin'));
    })->name('dashboard');

    // ── ADMIN ONLY - rute SPESIFIK (statis) harus didaftarkan SEBELUM wildcard ──
    Route::middleware('admin')->group(function () {

        // Peserta CRUD (rute statis dulu)
        Route::get('participants/create',             [ParticipantController::class, 'create'])->name('participants.create');
        Route::post('participants',                   [ParticipantController::class, 'store'])->name('participants.store');
        Route::get('participants/{participant}/edit', [ParticipantController::class, 'edit'])->name('participants.edit');
        Route::put('participants/{participant}',      [ParticipantController::class, 'update'])->name('participants.update');
        Route::delete('participants/{participant}',   [ParticipantController::class, 'destroy'])->name('participants.destroy');

        // Turnamen CRUD (rute statis dulu)
        Route::get('tournaments/create',             [TournamentController::class, 'create'])->name('tournaments.create');
        Route::post('tournaments',                   [TournamentController::class, 'store'])->name('tournaments.store');
        Route::get('tournaments/{tournament}/edit',  [TournamentController::class, 'edit'])->name('tournaments.edit');
        Route::put('tournaments/{tournament}',       [TournamentController::class, 'update'])->name('tournaments.update');
        Route::delete('tournaments/{tournament}',    [TournamentController::class, 'destroy'])->name('tournaments.destroy');
        Route::get('tournaments/{tournament}/export', [TournamentController::class, 'exportParticipants'])->name('tournaments.export');
        Route::post('tournaments/{tournament}/register', [TournamentController::class, 'registerParticipant'])->name('tournaments.register');
        Route::delete('tournaments/{tournament}/participants/{participant}', [TournamentController::class, 'removeParticipant'])->name('tournaments.removeParticipant');
        Route::post('tournaments/{tournament}/participants/{participant}/verify-payment', [TournamentController::class, 'verifyPayment'])->name('tournaments.verifyPayment');
        Route::post('tournaments/{tournament}/generate-bracket', [TournamentController::class, 'generateBracket'])->name('tournaments.generateBracket');

        // Matches CRUD (rute statis dulu)
        Route::get('matches/create',           [MatchController::class, 'create'])->name('matches.create');
        Route::post('matches',                 [MatchController::class, 'store'])->name('matches.store');
        Route::get('matches/{match}/edit',     [MatchController::class, 'edit'])->name('matches.edit');
        Route::put('matches/{match}',          [MatchController::class, 'update'])->name('matches.update');
        Route::delete('matches/{match}',       [MatchController::class, 'destroy'])->name('matches.destroy');

        // Input hasil
        Route::get('matches/{match}/result', function (\App\Models\GameMatch $match) {
            $match->load('participant1', 'participant2');
            return view('matches.result', compact('match'));
        })->name('matches.result.form');
        Route::post('matches/{match}/result',       [MatchController::class, 'inputResult'])->name('matches.result');
        Route::post('matches/{match}/update-score', [MatchController::class, 'updateScore'])->name('matches.updateScore');

        // Save winner bracket
        Route::post('tournaments/{tournament}/bracket/save-winner', [MatchController::class, 'saveWinner'])
             ->name('tournaments.bracket.saveWinner');
    });

    // ── READ-ONLY (semua user yang login) - wildcard di SINI, setelah rute statis admin ──

    // Peserta (index & show - wildcard)
    Route::get('participants',               [ParticipantController::class, 'index'])->name('participants.index');
    Route::get('participants/{participant}', [ParticipantController::class, 'show'])->name('participants.show');

    // Turnamen (index & show - wildcard)
    Route::get('tournaments',              [TournamentController::class, 'index'])->name('tournaments.index');
    Route::get('tournaments/{tournament}', [TournamentController::class, 'show'])->name('tournaments.show');

    // Bracket & Ranking (read-only)
    Route::get('tournaments/{tournament}/bracket',  [MatchController::class, 'bracket'])->name('tournaments.bracket');
    Route::get('tournaments/{tournament}/rankings', [RankingController::class, 'show'])->name('rankings.show');

    // Matches (index & show - wildcard)
    Route::get('matches',         [MatchController::class, 'index'])->name('matches.index');
    Route::get('matches/{match}', [MatchController::class, 'show'])->name('matches.show');

    // Ambil peserta by turnamen (untuk dropdown)
    Route::get('tournaments/{tournament}/participants-list', [MatchController::class, 'getParticipants'])
         ->name('tournaments.participantsList');

    // Pendaftaran mandiri & Unggah bukti transfer (untuk viewer/user yang login)
    Route::post('tournaments/{tournament}/join', [TournamentController::class, 'joinTournament'])->name('tournaments.join');
    Route::post('tournaments/{tournament}/pay',  [TournamentController::class, 'uploadPaymentProof'])->name('tournaments.pay');
    Route::get('tournaments/{tournament}/receipt', [TournamentController::class, 'paymentReceipt'])->name('tournaments.receipt');
});