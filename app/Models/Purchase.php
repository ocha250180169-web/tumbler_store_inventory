<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    // 'total' & 'status' diisi oleh service, bukan dari input browser.
    protected $fillable = [
        'supplier_id', 'user_id', 'purchase_number',
        'total', 'status', 'purchased_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'purchased_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Satu header pembelian punya banyak item
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /** Admin melihat semua transaksi; staff hanya miliknya sendiri. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->when(! $user->isAdmin(), fn (Builder $q) => $q->where('user_id', $user->id));
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeFilter(Builder $query, array $f): Builder
    {
        return $query
            ->when($f['q'] ?? null, fn (Builder $q, string $v) => $q->where('purchase_number', 'like', "%{$v}%"))
            ->when($f['status'] ?? null, fn (Builder $q, string $v) => $q->where('status', $v))
            ->when($f['date_from'] ?? null, fn (Builder $q, string $v) => $q->whereDate('purchased_at', '>=', $v))
            ->when($f['date_to'] ?? null, fn (Builder $q, string $v) => $q->whereDate('purchased_at', '<=', $v));
    }
}
