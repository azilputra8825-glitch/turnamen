<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GameMatch extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'matches';

    protected $fillable = [
        'tournament_id',
        'participant1_id',
        'participant2_id',
        'winner_id',
        'round',
        'match_number',
        'scheduled_at',
        'played_at',
        'score_participant1',
        'score_participant2',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'played_at'    => 'datetime',
    ];

    // Relasi ke turnamen
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    // Relasi ke peserta 1
    public function participant1()
    {
        return $this->belongsTo(Participant::class, 'participant1_id');
    }

    // Relasi ke peserta 2
    public function participant2()
    {
        return $this->belongsTo(Participant::class, 'participant2_id');
    }

    // Relasi ke pemenang
    public function winner()
    {
        return $this->belongsTo(Participant::class, 'winner_id');
    }

    // Cek apakah pertandingan sudah selesai
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}