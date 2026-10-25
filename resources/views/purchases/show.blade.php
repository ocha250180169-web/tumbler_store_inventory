@extends('layouts.app')

@section('title', 'Detail ' . $purchase->purchase_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Pembelian {{ $purchase->purchase_number }}</h1>
        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9"><span class="badge {{ $purchase->status === 'confirmed' ? 'bg-success' : 'bg-secondary' }}">{{ $purchase->status }}</span></dd>
                <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9">{{ $purchase->purchased_at->format('d/m/Y H:i') }}</dd>
                <dt class="col-sm-3">Pemasok</dt><dd class="col-sm-9">{{ $purchase->supplier->name }}</dd>
                <dt class="col-sm-3">Dicatat oleh</dt><dd class="col-sm-9">{{ $purchase->user->name }}</dd>
                @if ($purchase->cancelled_at)
                    <dt class="col-sm-3">Dibatalkan</dt><dd class="col-sm-9">{{ $purchase->cancelled_at->format('d/m/Y H:i') }}</dd>
                @endif
            </dl>
        </div>
    </div>

    <div class="card mb-3">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                <tr><th>Produk</th><th class="text-end">Harga Beli</th><th class="text-end">Qty</th><th class="text-end">Subtotal</th></tr>
                </thead>
                <tbody>
                @foreach ($purchase->items as $item)
                    <tr>
                        <td>{{ $item->product->name }} <span class="text-muted small">({{ $item->product->sku }})</span></td>
                        <td class="text-end">@rupiah($item->unit_cost)</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">@rupiah($item->subtotal)</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="table-light">
                    <th colspan="3" class="text-end">Total</th>
                    <th class="text-end">@rupiah($purchase->total)</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @can('cancel-transaction')
        @if ($purchase->status === 'confirmed')
            <form method="POST" action="{{ route('purchases.cancel', $purchase) }}"
                  onsubmit="return confirm('Batalkan pembelian ini? Stok akan dikurangi kembali.')">
                @csrf
                <button class="btn btn-outline-danger">Batalkan Pembelian</button>
            </form>
        @endif
    @endcan
@endsection
