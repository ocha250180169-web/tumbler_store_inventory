<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-master') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama pemasok wajib diisi.',
            'phone.max' => 'Nomor telepon maksimal 30 karakter.',
            'email.email' => 'Format email tidak valid.',
        ];
    }
}
