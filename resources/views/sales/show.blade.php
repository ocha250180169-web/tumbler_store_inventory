@extends('layouts.app')

@section('title', 'Detail ' . $sale->sale_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Penjualan {{ $sale->sale_number }}</h1>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9"><span class="badge {{ $sale->status === 'completed' ? 'bg-success' : 'bg-secondary' }}">{{ $sale->status }}</span></dd>
                <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9">{{ $sale->sold_at->format('d/m/Y H:i') }}</dd>
                <dt class="col-sm-3">Kasir</dt><dd class="col-sm-9">{{ $sale->user->name }}</dd>
                <dt class="col-sm-3">Pelanggan</dt><dd class="col-sm-9">{{ $sale->customer_name ?? '-' }}</dd>
                @if ($sale->cancelled_at)
                    <dt class="col-sm-3">Dibatalkan</dt><dd class="col-sm-9">{{ $sale->cancelled_at->format('d/m/Y H:i') }}</dd>
                @endif
            </dl>
        </div>
    </div>

    <div class="card mb-3">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                <tr><th>Produk</th><th class="text-end">Harga</th><th class="text-end">Qty</th><th class="text-end">Subtotal</th></tr>
                </thead>
                <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td>{{ $item->product->name }} <span class="text-muted small">({{ $item->product->sku }})</span></td>
                        <td class="text-end">@rupiah($item->unit_price)</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">@rupiah($item->subtotal)</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="table-light">
                    <th colspan="3" class="text-end">Total</th>
                    <th class="text-end">@rupiah($sale->total)</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @can('cancel-transaction')
        @if ($sale->status === 'completed')
            <form method="POST" action="{{ route('sales.cancel', $sale) }}"
                  onsubmit="return confirm('Batalkan penjualan ini? Stok akan dikembalikan.')">
                @csrf
                <button class="btn btn-outline-danger">Batalkan Penjualan</button>
            </form>
        @endif
    @endcan
@endsection
