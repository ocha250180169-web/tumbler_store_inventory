<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id', 'sale_number', 'customer_name',
        'total', 'status', 'sold_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'sold_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    // Penjualan dibuat oleh satu user (FK user_id ada di tabel sales)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Satu penjualan punya banyak item (FK sale_id ada di tabel sale_items)
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->when(! $user->isAdmin(), fn (Builder $q) => $q->where('user_id', $user->id));
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeFilter(Builder $query, array $f): Builder
    {
        return $query
            ->when($f['q'] ?? null, function (Builder $q, string $v) {
                $q->where(function (Builder $w) use ($v) {
                    $w->where('sale_number', 'like', "%{$v}%")
                      ->orWhere('customer_name', 'like', "%{$v}%");
                });
            })
            ->when($f['status'] ?? null, fn (Builder $q, string $v) => $q->where('status', $v))
            ->when($f['date_from'] ?? null, fn (Builder $q, string $v) => $q->whereDate('sold_at', '>=', $v))
            ->when($f['date_to'] ?? null, fn (Builder $q, string $v) => $q->whereDate('sold_at', '<=', $v));
    }
}
