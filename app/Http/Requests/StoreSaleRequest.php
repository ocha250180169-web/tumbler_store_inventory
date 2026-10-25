<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-transactions') ?? false;
    }

    public function rules(): array
    {
        // Perhatikan: tidak ada field harga/total. Keduanya dihitung server dari database.
        return [
            'customer_name' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => [
                'required', 'integer',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Tambahkan minimal satu item penjualan.',
            'items.min' => 'Tambahkan minimal satu item penjualan.',
            'items.*.product_id.required' => 'Pilih produk pada setiap baris item.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak valid.',
            'items.*.quantity.required' => 'Jumlah wajib diisi pada setiap baris item.',
            'items.*.quantity.integer' => 'Jumlah harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Jumlah harus lebih dari 0.',
            'customer_name.max' => 'Nama pelanggan maksimal 100 karakter.',
        ];
    }
}
