@extends('layouts.app')

@section('title', 'Stok Rendah')

@section('content')
    <h1 class="h4 mb-1">Produk Stok Rendah</h1>
    <p class="text-muted small">Produk dengan stok sama dengan atau di bawah stok minimum. Segera lakukan pembelian.</p>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr><th>SKU</th><th>Nama</th><th>Kategori</th><th class="text-end">Stok</th><th class="text-end">Minimum</th></tr>
                </thead>
                <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td><code>{{ $product->sku }}</code></td>
                        <td><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></td>
                        <td>{{ $product->category->name }}</td>
                        <td class="text-end"><span class="badge bg-danger">{{ $product->stock }}</span></td>
                        <td class="text-end">{{ $product->minimum_stock }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Semua stok aman.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $products->links() }}</div>
@endsection
