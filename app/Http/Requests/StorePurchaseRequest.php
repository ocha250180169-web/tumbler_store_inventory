<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-transactions') ?? false;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => [
                'required', 'integer',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0', 'max:999999999'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Pilih pemasok terlebih dahulu.',
            'supplier_id.exists' => 'Pemasok yang dipilih tidak valid.',
            'items.required' => 'Tambahkan minimal satu item pembelian.',
            'items.min' => 'Tambahkan minimal satu item pembelian.',
            'items.*.product_id.required' => 'Pilih produk pada setiap baris item.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak valid.',
            'items.*.quantity.required' => 'Jumlah wajib diisi pada setiap baris item.',
            'items.*.quantity.integer' => 'Jumlah harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Jumlah harus lebih dari 0.',
            'items.*.unit_cost.required' => 'Harga beli wajib diisi pada setiap baris item.',
            'items.*.unit_cost.numeric' => 'Harga beli harus berupa angka.',
            'items.*.unit_cost.min' => 'Harga beli tidak boleh negatif.',
        ];
    }
}
