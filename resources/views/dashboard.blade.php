@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="h4 mb-1">Dashboard</h1>
    @unless (auth()->user()->isAdmin())
        <p class="text-muted small">Angka penjualan &amp; transaksi menampilkan transaksi milik Anda.</p>
    @endunless

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <div class="text-muted small">Total Produk</div>
                    <div class="fs-3 fw-bold" id="stat-products">{{ $stats['total_products'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <div class="text-muted small">Penjualan Hari Ini</div>
                    <div class="fs-3 fw-bold">@rupiah($stats['sales_today'])</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <div class="text-muted small">Penjualan Bulan Ini</div>
                    <div class="fs-3 fw-bold">@rupiah($stats['sales_month'])</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <div class="text-muted small">Transaksi Penjualan</div>
                    <div class="fs-3 fw-bold">{{ $stats['sales_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <div class="text-muted small">Transaksi Pembelian</div>
                    <div class="fs-3 fw-bold">{{ $stats['purchase_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card h-100 {{ $stats['low_stock_count'] > 0 ? 'border-danger' : 'border-success' }}">
                <div class="card-body">
                    <div class="text-muted small">Produk Stok Rendah</div>
                    <div class="fs-3 fw-bold {{ $stats['low_stock_count'] > 0 ? 'text-danger' : '' }}">{{ $stats['low_stock_count'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <span class="fw-semibold">Stok Rendah</span>
                    <a href="{{ route('products.low-stock') }}" class="small">Lihat semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light">
                        <tr><th>Produk</th><th class="text-end">Stok</th><th class="text-end">Min</th></tr>
                        </thead>
                        <tbody>
                        @forelse ($lowStockProducts as $product)
                            <tr>
                                <td>
                                    <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                                    <div class="text-muted small">{{ $product->sku }}</div>
                                </td>
                                <td class="text-end text-danger fw-bold">{{ $product->stock }}</td>
                                <td class="text-end">{{ $product->minimum_stock }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Semua stok aman.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <span class="fw-semibold">Penjualan Terbaru</span>
                    <a href="{{ route('sales.index') }}" class="small">Lihat semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light">
                        <tr><th>No. Invoice</th><th>Status</th><th class="text-end">Total</th></tr>
                        </thead>
                        <tbody>
                        @forelse ($latestSales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}">{{ $sale->sale_number }}</a>
                                    <div class="text-muted small">{{ $sale->sold_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $sale->status === 'completed' ? 'bg-success' : 'bg-secondary' }}">{{ $sale->status }}</span>
                                </td>
                                <td class="text-end">@rupiah($sale->total)</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada penjualan.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
