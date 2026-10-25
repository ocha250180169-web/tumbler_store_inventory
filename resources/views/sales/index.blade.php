@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Penjualan</h1>
        <a href="{{ route('sales.create') }}" class="btn btn-success btn-sm">+ Penjualan Baru</a>
    </div>

    @include('partials.transaction-filter', [
        'action' => route('sales.index'),
        'statuses' => ['completed', 'cancelled'],
        'placeholder' => 'No. invoice / pelanggan',
    ])

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th>No. Invoice</th><th>Tanggal</th><th>Pelanggan</th><th>Kasir</th>
                    <th>Status</th><th class="text-end">Total</th><th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td>{{ $sale->sale_number }}</td>
                        <td>{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->customer_name ?? '-' }}</td>
                        <td>{{ $sale->user->name }}</td>
                        <td><span class="badge {{ $sale->status === 'completed' ? 'bg-success' : 'bg-secondary' }}">{{ $sale->status }}</span></td>
                        <td class="text-end">@rupiah($sale->total)</td>
                        <td class="text-end"><a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-secondary btn-sm">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada transaksi penjualan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $sales->links() }}</div>
@endsection
