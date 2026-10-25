@extends('layouts.app')

@section('title', 'Pembelian')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Pembelian / Stock-in</h1>
        <a href="{{ route('purchases.create') }}" class="btn btn-success btn-sm">+ Pembelian Baru</a>
    </div>

    @include('partials.transaction-filter', [
        'action' => route('purchases.index'),
        'statuses' => ['confirmed', 'cancelled'],
        'placeholder' => 'No. pembelian',
    ])

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th>No. Pembelian</th><th>Tanggal</th><th>Pemasok</th><th>Dicatat Oleh</th>
                    <th>Status</th><th class="text-end">Total</th><th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->purchase_number }}</td>
                        <td>{{ $purchase->purchased_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $purchase->supplier->name }}</td>
                        <td>{{ $purchase->user->name }}</td>
                        <td><span class="badge {{ $purchase->status === 'confirmed' ? 'bg-success' : 'bg-secondary' }}">{{ $purchase->status }}</span></td>
                        <td class="text-end">@rupiah($purchase->total)</td>
                        <td class="text-end"><a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-secondary btn-sm">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada transaksi pembelian.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $purchases->links() }}</div>
@endsection
