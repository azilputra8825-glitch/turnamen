<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ranking extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'participant_id',
        'rank',
        'wins',
        'losses',
        'points',
        'matches_played',
    ];

    // Relasi ke turnamen
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    // Relasi ke peserta
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}