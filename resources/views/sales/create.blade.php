@extends('layouts.app')

@section('title', 'Penjualan Baru')

@section('content')
    <h1 class="h4 mb-3">Penjualan Baru</h1>

    <form method="POST" action="{{ route('sales.store') }}" novalidate>
        @csrf

        <div class="card mb-3">
            <div class="card-body">
                <label for="customer_name" class="form-label">Nama pelanggan (opsional)</label>
                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" maxlength="100"
                       class="form-control @error('customer_name') is-invalid @enderror">
                @error('customer_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-semibold">Item Penjualan</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                        <tr><th style="min-width:280px">Produk</th><th style="width:120px">Jumlah</th><th class="text-end" style="width:170px">Perkiraan Subtotal</th><th style="width:60px"></th></tr>
                        </thead>
                        <tbody id="items-body"></tbody>
                    </table>
                </div>

                <button type="button" id="add-row" class="btn btn-outline-success btn-sm">+ Tambah item</button>

                <div class="text-end mt-3">
                    <div class="fs-5">Perkiraan total: <strong id="est-total">Rp 0</strong></div>
                    <div class="text-muted small">Total final dihitung oleh server berdasarkan harga di database.</div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Simpan Penjualan</button>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection

@push('scripts')
<script>
    const products = {{ \Illuminate\Support\Js::from($productOptions) }};
    const oldItems = {{ \Illuminate\Support\Js::from(old('items', [['product_id' => '', 'quantity' => 1]])) }};

    const body = document.getElementById('items-body');
    const estTotal = document.getElementById('est-total');
    let rowIndex = 0;

    function rupiah(n) {
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
    }

    function priceOf(productId) {
        const p = products.find(x => String(x.id) === String(productId));
        return p ? p.price : 0;
    }

    function recalc() {
        let total = 0;
        body.querySelectorAll('tr').forEach(tr => {
            const productId = tr.querySelector('select').value;
            const qty = parseInt(tr.querySelector('input').value, 10) || 0;
            const sub = priceOf(productId) * qty;
            tr.querySelector('.subtotal').textContent = rupiah(sub);
            total += sub;
        });
        estTotal.textContent = rupiah(total);
    }

    function addRow(item) {
        const i = rowIndex++;
        const tr = document.createElement('tr');

        const tdProduct = document.createElement('td');
        const select = document.createElement('select');
        select.name = `items[${i}][product_id]`;
        select.className = 'form-select';
        select.required = true;
        select.add(new Option('-- pilih produk --', ''));
        products.forEach(p => {
            const opt = new Option(p.label, p.id);
            if (String(item.product_id) === String(p.id)) opt.selected = true;
            select.add(opt);
        });
        tdProduct.appendChild(select);

        const tdQty = document.createElement('td');
        const qty = document.createElement('input');
        qty.type = 'number';
        qty.name = `items[${i}][quantity]`;
        qty.className = 'form-control';
        qty.min = 1;
        qty.step = 1;
        qty.required = true;
        qty.value = item.quantity || 1;
        tdQty.appendChild(qty);

        const tdSub = document.createElement('td');
        tdSub.className = 'text-end subtotal';
        tdSub.textContent = 'Rp 0';

        const tdDel = document.createElement('td');
        const del = document.createElement('button');
        del.type = 'button';
        del.className = 'btn btn-outline-danger btn-sm';
        del.textContent = '×';
        del.addEventListener('click', () => { tr.remove(); recalc(); });
        tdDel.appendChild(del);

        [select, qty].forEach(el => el.addEventListener('input', recalc));
        tr.append(tdProduct, tdQty, tdSub, tdDel);
        body.appendChild(tr);
    }

    document.getElementById('add-row').addEventListener('click', () => { addRow({}); recalc(); });
    (Array.isArray(oldItems) ? oldItems : Object.values(oldItems)).forEach(addRow);
    recalc();
</script>
@endpush
