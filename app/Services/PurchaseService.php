<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Business logic pembelian / stock-in.
 */
class PurchaseService
{
    /**
     * @param  array<int, array{product_id:int|string, quantity:int|string, unit_cost:int|float|string}>  $items
     */
    public function create(User $user, int $supplierId, array $items): Purchase
    {
        // Urutkan per product_id supaya urutan penguncian konsisten.
        usort($items, fn ($a, $b) => (int) $a['product_id'] <=> (int) $b['product_id']);

        return DB::transaction(function () use ($user, $supplierId, $items) {
            $purchase = Purchase::create([
                'supplier_id' => $supplierId,
                'user_id' => $user->id,
                'purchase_number' => $this->generateNumber(),
                'total' => 0,
                'status' => Purchase::STATUS_CONFIRMED,
                'purchased_at' => now(),
            ]);

            $total = 0.0;

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->find((int) $item['product_id']);
                $qty = (int) $item['quantity'];
                $unitCost = (float) $item['unit_cost'];

                if (! $product) {
                    throw ValidationException::withMessages([
                        'items' => 'Produk tidak ditemukan atau sudah dihapus.',
                    ]);
                }

                if ($qty < 1 || $unitCost < 0) {
                    throw ValidationException::withMessages([
                        'items' => 'Jumlah harus lebih dari 0 dan harga tidak boleh negatif.',
                    ]);
                }

                // Subtotal dihitung server, bukan dipercaya dari browser.
                $subtotal = round($unitCost * $qty, 2);

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                $product->increment('stock', $qty);
                $total += $subtotal;
            }

            $purchase->update(['total' => round($total, 2)]);

            return $purchase;
        });
    }

    /**
     * Membatalkan pembelian: stok dikurangi kembali.
     * Ditolak bila stok saat ini tidak cukup (barang sudah terjual) agar stok tidak negatif.
     */
    public function cancel(Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($purchase) {
            $purchase = Purchase::lockForUpdate()->findOrFail($purchase->id);

            if ($purchase->status !== Purchase::STATUS_CONFIRMED) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya pembelian berstatus confirmed yang dapat dibatalkan.',
                ]);
            }

            foreach ($purchase->items()->orderBy('product_id')->get() as $item) {
                $product = Product::withTrashed()->lockForUpdate()->find($item->product_id);

                if (! $product || $product->stock < $item->quantity) {
                    $name = $product?->name ?? 'produk';
                    throw ValidationException::withMessages([
                        'status' => "Pembelian tidak dapat dibatalkan: stok {$name} saat ini kurang dari jumlah yang dibeli (sebagian sudah terjual).",
                    ]);
                }

                $product->decrement('stock', $item->quantity);
            }

            $purchase->update([
                'status' => Purchase::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

            return $purchase;
        });
    }

    private function generateNumber(): string
    {
        do {
            $number = 'PUR-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
        } while (Purchase::where('purchase_number', $number)->exists());

        return $number;
    }
}
