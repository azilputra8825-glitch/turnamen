<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|min:2|max:255',
            'username' => 'required|string|min:3|max:50|unique:participants|regex:/^[a-zA-Z0-9_]+$/',
            'email'    => 'required|email|unique:participants',
            'phone'    => 'nullable|string|max:20|regex:/^(\+62|0)[0-9]{8,13}$/',
            'game_id'  => 'nullable|string|max:100',
            'status'   => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'name.min'          => 'Nama minimal 2 karakter.',
            'name.max'          => 'Nama maksimal 255 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.min'      => 'Username minimal 3 karakter.',
            'username.max'      => 'Username maksimal 50 karakter.',
            'username.unique'   => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'username.regex'    => 'Username hanya boleh berisi huruf, angka, dan underscore (_).',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid (contoh: nama@email.com).',
            'email.unique'      => 'Email ini sudah terdaftar, silakan gunakan email lain.',
            'phone.max'         => 'Nomor HP maksimal 20 karakter.',
            'phone.regex'       => 'Format nomor HP tidak valid (contoh: 08123456789 atau +628123456789).',
            'game_id.max'       => 'Game ID maksimal 100 karakter.',
            'status.required'   => 'Status wajib dipilih.',
            'status.in'         => 'Status hanya boleh: active atau inactive.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'Nama Lengkap',
            'username' => 'Username',
            'email'    => 'Email',
            'phone'    => 'No. HP',
            'game_id'  => 'Game ID',
            'status'   => 'Status',
        ];
    }
}
