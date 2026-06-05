<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tournament_id'   => 'required|exists:tournaments,id',
            'participant1_id' => 'required|exists:participants,id',
            'participant2_id' => 'required|exists:participants,id|different:participant1_id',
            'round'           => 'required|integer|min:1',
            'match_number'    => 'required|integer|min:1',
            'scheduled_at'    => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'tournament_id.required'   => 'Turnamen wajib dipilih.',
            'tournament_id.exists'     => 'Turnamen yang dipilih tidak ditemukan.',
            'participant1_id.required' => 'Peserta 1 wajib dipilih.',
            'participant1_id.exists'   => 'Peserta 1 yang dipilih tidak ditemukan.',
            'participant2_id.required' => 'Peserta 2 wajib dipilih.',
            'participant2_id.exists'   => 'Peserta 2 yang dipilih tidak ditemukan.',
            'participant2_id.different'=> 'Peserta 1 dan Peserta 2 tidak boleh sama.',
            'round.required'           => 'Ronde wajib diisi.',
            'round.integer'            => 'Ronde harus berupa angka bulat.',
            'round.min'                => 'Ronde minimal adalah 1.',
            'match_number.required'    => 'Nomor match wajib diisi.',
            'match_number.integer'     => 'Nomor match harus berupa angka bulat.',
            'match_number.min'         => 'Nomor match minimal adalah 1.',
            'scheduled_at.date'        => 'Format jadwal pertandingan tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'tournament_id'   => 'Turnamen',
            'participant1_id' => 'Peserta 1',
            'participant2_id' => 'Peserta 2',
            'round'           => 'Ronde',
            'match_number'    => 'Nomor Match',
            'scheduled_at'    => 'Jadwal Pertandingan',
        ];
    }
}
