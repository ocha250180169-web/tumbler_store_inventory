@extends('layouts.app')

@section('title', 'Pembelian Baru')

@section('content')
    <h1 class="h4 mb-3">Pembelian Baru (Stock-in)</h1>

    <form method="POST" action="{{ route('purchases.store') }}" novalidate>
        @csrf

        <div class="card mb-3">
            <div class="card-body">
                <label for="supplier_id" class="form-label">Pemasok</label>
                <select id="supplier_id" name="supplier_id" required
                        class="form-select @error('supplier_id') is-invalid @enderror">
                    <option value="">-- pilih pemasok --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected((string) old('supplier_id') === (string) $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-semibold">Item Pembelian</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                        <tr>
                            <th style="min-width:260px">Produk</th>
                            <th style="width:110px">Jumlah</th>
                            <th style="width:160px">Harga Beli / unit</th>
                            <th class="text-end" style="width:160px">Perkiraan Subtotal</th>
                            <th style="width:60px"></th>
                        </tr>
                        </thead>
                        <tbody id="items-body"></tbody>
                    </table>
                </div>

                <button type="button" id="add-row" class="btn btn-outline-success btn-sm">+ Tambah item</button>

                <div class="text-end mt-3">
                    <div class="fs-5">Perkiraan total: <strong id="est-total">Rp 0</strong></div>
                    <div class="text-muted small">Total final dihitung oleh server.</div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Simpan Pembelian</button>
        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection

@push('scripts')
<script>
    const products = {{ \Illuminate\Support\Js::from($productOptions) }};
    const oldItems = {{ \Illuminate\Support\Js::from(old('items', [['product_id' => '', 'quantity' => 1, 'unit_cost' => '']])) }};

    const body = document.getElementById('items-body');
    const estTotal = document.getElementById('est-total');
    let rowIndex = 0;

    function rupiah(n) {
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
    }

    function recalc() {
        let total = 0;
        body.querySelectorAll('tr').forEach(tr => {
            const qty = parseInt(tr.querySelector('.qty').value, 10) || 0;
            const cost = parseFloat(tr.querySelector('.cost').value) || 0;
            const sub = qty * cost;
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
        qty.className = 'form-control qty';
        qty.min = 1;
        qty.step = 1;
        qty.required = true;
        qty.value = item.quantity || 1;
        tdQty.appendChild(qty);

        const tdCost = document.createElement('td');
        const cost = document.createElement('input');
        cost.type = 'number';
        cost.name = `items[${i}][unit_cost]`;
        cost.className = 'form-control cost';
        cost.min = 0;
        cost.step = '0.01';
        cost.required = true;
        cost.value = item.unit_cost ?? '';
        tdCost.appendChild(cost);

        // Saat produk dipilih, isi harga beli default dari data produk (tetap bisa diubah).
        select.addEventListener('change', () => {
            const p = products.find(x => String(x.id) === String(select.value));
            if (p) cost.value = p.cost;
            recalc();
        });

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

        [qty, cost].forEach(el => el.addEventListener('input', recalc));
        tr.append(tdProduct, tdQty, tdCost, tdSub, tdDel);
        body.appendChild(tr);
    }

    document.getElementById('add-row').addEventListener('click', () => { addRow({}); recalc(); });
    (Array.isArray(oldItems) ? oldItems : Object.values(oldItems)).forEach(addRow);
    recalc();
</script>
@endpush
