@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Produk</h1>
        @can('manage-master')
            <a href="{{ route('products.create') }}" class="btn btn-success btn-sm">+ Tambah Produk</a>
        @endcan
    </div>

    <form method="GET" action="{{ route('products.index') }}" class="card card-body mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label for="q" class="form-label small mb-1">Cari SKU / nama</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-4">
                <label for="category_id" class="form-label small mb-1">Kategori</label>
                <select id="category_id" name="category_id" class="form-select form-select-sm">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-success btn-sm">Filter</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th>SKU</th><th>Nama</th><th>Kategori</th>
                    <th class="text-end">Harga Jual</th><th class="text-end">Stok</th><th class="text-end">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td><code>{{ $product->sku }}</code></td>
                        <td><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></td>
                        <td>{{ $product->category->name }}</td>
                        <td class="text-end">@rupiah($product->selling_price)</td>
                        <td class="text-end">
                            <span class="badge {{ $product->isLowStock() ? 'bg-danger' : 'bg-success' }}">{{ $product->stock }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary btn-sm">Detail</a>
                            @can('manage-master')
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary btn-sm">Ubah</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline"
                                      onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Produk tidak ditemukan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $products->links() }}</div>
@endsection
