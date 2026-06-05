<?php

namespace App\Http\Requests;

use App\Models\GameMatch;
use Illuminate\Foundation\Http\FormRequest;

class InputResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var GameMatch $match */
        $match = $this->route('match');

        return [
            'score_participant1' => 'required|integer|min:0|max:9999',
            'score_participant2' => 'required|integer|min:0|max:9999',
            'winner_id'          => 'required|in:' . $match->participant1_id . ',' . $match->participant2_id,
            'notes'              => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'score_participant1.required' => 'Skor peserta 1 wajib diisi.',
            'score_participant1.integer'  => 'Skor peserta 1 harus berupa angka bulat.',
            'score_participant1.min'      => 'Skor peserta 1 tidak boleh bernilai negatif.',
            'score_participant1.max'      => 'Skor peserta 1 terlalu besar (maks: 9999).',
            'score_participant2.required' => 'Skor peserta 2 wajib diisi.',
            'score_participant2.integer'  => 'Skor peserta 2 harus berupa angka bulat.',
            'score_participant2.min'      => 'Skor peserta 2 tidak boleh bernilai negatif.',
            'score_participant2.max'      => 'Skor peserta 2 terlalu besar (maks: 9999).',
            'winner_id.required'          => 'Pemenang wajib dipilih.',
            'winner_id.in'                => 'Pemenang yang dipilih tidak valid — harus salah satu peserta match ini.',
            'notes.max'                   => 'Catatan maksimal 500 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'score_participant1' => 'Skor Peserta 1',
            'score_participant2' => 'Skor Peserta 2',
            'winner_id'          => 'Pemenang',
            'notes'              => 'Catatan',
        ];
    }
}
