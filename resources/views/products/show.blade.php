@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">{{ $product->name }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
            @can('manage-master')
                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm">Ubah</a>
            @endcan
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">SKU</dt><dd class="col-sm-9"><code>{{ $product->sku }}</code></dd>
                <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ $product->category->name }}</dd>
                <dt class="col-sm-3">Harga Beli</dt><dd class="col-sm-9">@rupiah($product->purchase_price)</dd>
                <dt class="col-sm-3">Harga Jual</dt><dd class="col-sm-9">@rupiah($product->selling_price)</dd>
                <dt class="col-sm-3">Stok</dt>
                <dd class="col-sm-9">
                    <span class="badge {{ $product->isLowStock() ? 'bg-danger' : 'bg-success' }}">{{ $product->stock }}</span>
                    @if ($product->isLowStock())
                        <span class="text-danger small ms-1">Di bawah / sama dengan stok minimum</span>
                    @endif
                </dd>
                <dt class="col-sm-3">Stok Minimum</dt><dd class="col-sm-9">{{ $product->minimum_stock }}</dd>
            </dl>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Penjualan Terakhir</div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>Invoice</th><th class="text-end">Qty</th><th class="text-end">Harga</th></tr></thead>
                        <tbody>
                        @forelse ($saleItems as $item)
                            <tr>
                                <td>
                                    @can('view-transaction', $item->sale)
                                        <a href="{{ route('sales.show', $item->sale) }}">{{ $item->sale->sale_number }}</a>
                                    @else
                                        {{ $item->sale->sale_number }}
                                    @endcan
                                </td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">@rupiah($item->unit_price)</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada penjualan.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Pembelian Terakhir</div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>No. Pembelian</th><th class="text-end">Qty</th><th class="text-end">Harga</th></tr></thead>
                        <tbody>
                        @forelse ($purchaseItems as $item)
                            <tr>
                                <td>
                                    @can('view-transaction', $item->purchase)
                                        <a href="{{ route('purchases.show', $item->purchase) }}">{{ $item->purchase->purchase_number }}</a>
                                    @else
                                        {{ $item->purchase->purchase_number }}
                                    @endcan
                                </td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">@rupiah($item->unit_cost)</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada pembelian.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
