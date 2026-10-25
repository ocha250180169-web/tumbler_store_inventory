@extends('layouts.app')

@section('title', 'Pemasok')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Pemasok</h1>
        <a href="{{ route('suppliers.create') }}" class="btn btn-success btn-sm">+ Tambah Pemasok</a>
    </div>

    <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2 mb-3">
        <div class="col-sm-6 col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama pemasok" class="form-control form-control-sm">
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-success btn-sm">Cari</button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr><th>Nama</th><th>Telepon</th><th>Email</th><th class="text-end">Pembelian</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->phone ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td class="text-end">{{ $supplier->purchases_count }}</td>
                        <td class="text-end">
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-primary btn-sm">Ubah</a>
                            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="d-inline"
                                  onsubmit="return confirm('Hapus pemasok ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pemasok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $suppliers->links() }}</div>
@endsection
