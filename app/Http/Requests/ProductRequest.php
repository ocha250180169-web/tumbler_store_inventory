<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-master') ?? false;
    }

    public function rules(): array
    {
        return [
            'sku' => [
                'required', 'string', 'max:50',
                // ignore() agar SKU produk yang sedang diedit tidak dianggap duplikat dirinya sendiri
                Rule::unique('products', 'sku')->ignore($this->route('product')),
            ],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'selling_price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'minimum_stock' => ['required', 'integer', 'min:0', 'max:1000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'SKU wajib diisi.',
            'sku.unique' => 'SKU sudah dipakai produk lain.',
            'sku.max' => 'SKU maksimal 50 karakter.',
            'name.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Pilih kategori produk.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'purchase_price.min' => 'Harga beli tidak boleh negatif.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.numeric' => 'Harga jual harus berupa angka.',
            'selling_price.min' => 'Harga jual tidak boleh negatif.',
            'minimum_stock.required' => 'Stok minimum wajib diisi.',
            'minimum_stock.integer' => 'Stok minimum harus berupa bilangan bulat.',
            'minimum_stock.min' => 'Stok minimum tidak boleh negatif.',
        ];
    }
}
