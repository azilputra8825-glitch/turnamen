<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\GameMatch;

class Participant extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'user_id',
        'name',
        'username',
        'email',
        'phone',
        'game_id',
        'status',
    ];

    // Relasi: profil ini terhubung dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: peserta bisa ikut banyak turnamen
    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_participant')
                    ->withPivot('status', 'registered_at', 'payment_status', 'payment_proof', 'amount_paid', 'rejection_reason')
                    ->withTimestamps();
    }

    // Relasi: peserta bisa punya banyak ranking
    public function rankings()
    {
        return $this->hasMany(Ranking::class);
    }

    // Relasi: semua pertandingan peserta ini
    public function matches()
    {
        return GameMatch::where('participant1_id', $this->id)
                    ->orWhere('participant2_id', $this->id);
    }
}