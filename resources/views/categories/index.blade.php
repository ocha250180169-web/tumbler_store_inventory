@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Kategori</h1>
        <a href="{{ route('categories.create') }}" class="btn btn-success btn-sm">+ Tambah Kategori</a>
    </div>

    <form method="GET" action="{{ route('categories.index') }}" class="row g-2 mb-3">
        <div class="col-sm-6 col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kategori" class="form-control form-control-sm">
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-success btn-sm">Cari</button>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr><th>Nama</th><th class="text-end">Jumlah Produk</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td class="text-end">{{ $category->products_count }}</td>
                        <td class="text-end">
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary btn-sm">Ubah</a>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}" class="d-inline"
                                  onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $categories->links() }}</div>
@endsection
