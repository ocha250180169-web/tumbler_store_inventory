<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    // 'stock' sengaja TIDAK di-fillable: stok hanya berubah lewat transaksi (service).
    protected $fillable = [
        'category_id', 'sku', 'name',
        'purchase_price', 'selling_price', 'minimum_stock',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock' => 'integer',
            'minimum_stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }

    public function isUsedInTransactions(): bool
    {
        return $this->saleItems()->exists() || $this->purchaseItems()->exists();
    }

    /** Produk dengan stok <= stok minimum. */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'minimum_stock');
    }

    /** Pencarian berdasarkan SKU atau nama (memakai parameter binding, aman dari SQL injection). */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function (Builder $q, string $term) {
            $q->where(function (Builder $w) use ($term) {
                $w->where('sku', 'like', "%{$term}%")
                  ->orWhere('name', 'like', "%{$term}%");
            });
        });
    }
}
