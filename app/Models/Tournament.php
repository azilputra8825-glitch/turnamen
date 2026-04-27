<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'game_name',
        'description',
        'start_date',
        'end_date',
        'max_participants',
        'format',
        'status',
        'prize_pool',
    ];

    // Otomatis konversi tipe data
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'prize_pool' => 'decimal:2',
    ];

    // Relasi: turnamen punya banyak peserta
    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'tournament_participant')
                    ->withPivot('status', 'registered_at')
                    ->withTimestamps();
    }

    // Relasi: turnamen punya banyak pertandingan
    public function matches()
    {
        return $this->hasMany(GameMatch::class);
    }

    // Relasi: turnamen punya banyak ranking
    public function rankings()
    {
        return $this->hasMany(Ranking::class)->orderBy('rank');
    }

    // Hitung sisa slot peserta
    public function availableSlots()
    {
        return $this->max_participants - $this->participants()->count();
    }

    // Cek apakah turnamen penuh
    public function isFull()
    {
        return $this->availableSlots() <= 0;
    }
}