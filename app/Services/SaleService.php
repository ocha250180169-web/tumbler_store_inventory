<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Business logic penjualan.
 * Semua perubahan (header, item, stok) terjadi dalam SATU database transaction:
 * jika ada satu item gagal, seluruh perubahan di-rollback.
 */
class SaleService
{
    /**
     * @param  array<int, array{product_id:int|string, quantity:int|string}>  $items
     */
    public function create(User $user, array $items, ?string $customerName = null): Sale
    {
        $lines = $this->mergeItems($items);

        return DB::transaction(function () use ($user, $lines, $customerName) {
            $sale = Sale::create([
                'user_id' => $user->id,
                'sale_number' => $this->generateNumber(),
                'customer_name' => $customerName,
                'total' => 0,
                'status' => Sale::STATUS_COMPLETED,
                'sold_at' => now(),
            ]);

            $total = 0.0;

            foreach ($lines as $productId => $qty) {
                // lockForUpdate: baris produk dikunci sampai transaksi selesai,
                // sehingga dua penjualan bersamaan tidak bisa membaca stok yang sama (cegah overselling).
                $product = Product::lockForUpdate()->find($productId);

                if (! $product) {
                    throw ValidationException::withMessages([
                        'items' => 'Produk tidak ditemukan atau sudah dihapus.',
                    ]);
                }

                if ($qty < 1) {
                    throw ValidationException::withMessages([
                        'items' => 'Jumlah harus lebih dari 0.',
                    ]);
                }

                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup (tersedia {$product->stock}, diminta {$qty}).",
                    ]);
                }

                // Harga diambil dari database, BUKAN dari input browser.
                $unitPrice = (float) $product->selling_price;
                $subtotal = round($unitPrice * $qty, 2);

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $qty);
                $total += $subtotal;
            }

            $sale->update(['total' => round($total, 2)]);

            return $sale;
        });
    }

    /**
     * Membatalkan penjualan: stok dikembalikan, status menjadi cancelled.
     */
    public function cancel(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            // Kunci header agar pembatalan ganda tidak mengembalikan stok dua kali.
            $sale = Sale::lockForUpdate()->findOrFail($sale->id);

            if ($sale->status !== Sale::STATUS_COMPLETED) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya penjualan berstatus completed yang dapat dibatalkan.',
                ]);
            }

            foreach ($sale->items()->orderBy('product_id')->get() as $item) {
                Product::withTrashed()->lockForUpdate()->find($item->product_id)
                    ?->increment('stock', $item->quantity);
            }

            $sale->update([
                'status' => Sale::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

            return $sale;
        });
    }

    /**
     * Gabungkan baris duplikat (produk sama) dan urutkan berdasarkan product_id
     * agar urutan penguncian konsisten (mengurangi risiko deadlock).
     *
     * @return array<int, int> [product_id => quantity]
     */
    private function mergeItems(array $items): array
    {
        $lines = [];

        foreach ($items as $item) {
            $id = (int) ($item['product_id'] ?? 0);
            $lines[$id] = ($lines[$id] ?? 0) + (int) ($item['quantity'] ?? 0);
        }

        ksort($lines);

        return $lines;
    }

    private function generateNumber(): string
    {
        do {
            $number = 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
        } while (Sale::where('sale_number', $number)->exists());

        return $number;
    }
}
