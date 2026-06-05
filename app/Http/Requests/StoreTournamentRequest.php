<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|min:3|max:255',
            'game_name'        => 'required|string|min:2|max:100',
            'description'      => 'nullable|string|max:1000',
            'start_date'       => 'required|date|after_or_equal:today',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'max_participants' => 'required|integer|min:2|max:256',
            'format'           => 'required|in:single_elimination,double_elimination,round_robin',
            'prize_pool'       => 'nullable|numeric|min:0',
            'entry_fee'        => 'nullable|numeric|min:0',
            'qris_image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Nama turnamen wajib diisi.',
            'name.min'                  => 'Nama turnamen minimal 3 karakter.',
            'name.max'                  => 'Nama turnamen maksimal 255 karakter.',
            'game_name.required'        => 'Nama game wajib diisi.',
            'game_name.min'             => 'Nama game minimal 2 karakter.',
            'game_name.max'             => 'Nama game maksimal 100 karakter.',
            'description.max'           => 'Deskripsi maksimal 1000 karakter.',
            'start_date.required'       => 'Tanggal mulai wajib diisi.',
            'start_date.date'           => 'Format tanggal mulai tidak valid.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'end_date.required'         => 'Tanggal selesai wajib diisi.',
            'end_date.date'             => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal'   => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            'max_participants.required' => 'Maksimal peserta wajib diisi.',
            'max_participants.integer'  => 'Maksimal peserta harus berupa angka bulat.',
            'max_participants.min'      => 'Minimal peserta adalah 2 orang.',
            'max_participants.max'      => 'Maksimal peserta tidak boleh lebih dari 256.',
            'format.required'           => 'Format turnamen wajib dipilih.',
            'format.in'                 => 'Format turnamen tidak valid.',
            'prize_pool.numeric'        => 'Total hadiah harus berupa angka.',
            'prize_pool.min'            => 'Total hadiah tidak boleh bernilai negatif.',
            'entry_fee.numeric'         => 'Biaya pendaftaran harus berupa angka.',
            'entry_fee.min'             => 'Biaya pendaftaran tidak boleh bernilai negatif.',
            'qris_image.image'          => 'File QRIS harus berupa gambar.',
            'qris_image.mimes'          => 'Format gambar QRIS yang diperbolehkan: jpeg, png, jpg.',
            'qris_image.max'            => 'Ukuran gambar QRIS maksimal 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'             => 'Nama Turnamen',
            'game_name'        => 'Nama Game',
            'description'      => 'Deskripsi',
            'start_date'       => 'Tanggal Mulai',
            'end_date'         => 'Tanggal Selesai',
            'max_participants' => 'Maksimal Peserta',
            'format'           => 'Format Turnamen',
            'prize_pool'       => 'Total Hadiah',
            'entry_fee'        => 'Biaya Pendaftaran',
            'qris_image'       => 'Gambar QRIS',
        ];
    }
}
