{{-- Variabel: $product (Product, bisa baru), $categories --}}
<div class="row g-3">
    <div class="col-md-4">
        <label for="sku" class="form-label">SKU</label>
        <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" maxlength="50" required
               class="form-control @error('sku') is-invalid @enderror">
        @error('sku')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-8">
        <label for="name" class="form-label">Nama Produk</label>
        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" maxlength="150" required
               class="form-control @error('name') is-invalid @enderror">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="category_id" class="form-label">Kategori</label>
        <select id="category_id" name="category_id" required
                class="form-select @error('category_id') is-invalid @enderror">
            <option value="">-- pilih kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="minimum_stock" class="form-label">Stok Minimum</label>
        <input type="number" id="minimum_stock" name="minimum_stock" min="0" step="1" required
               value="{{ old('minimum_stock', $product->minimum_stock ?? 0) }}"
               class="form-control @error('minimum_stock') is-invalid @enderror">
        @error('minimum_stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="purchase_price" class="form-label">Harga Beli (Rp)</label>
        <input type="number" id="purchase_price" name="purchase_price" min="0" step="0.01" required
               value="{{ old('purchase_price', $product->purchase_price) }}"
               class="form-control @error('purchase_price') is-invalid @enderror">
        @error('purchase_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="selling_price" class="form-label">Harga Jual (Rp)</label>
        <input type="number" id="selling_price" name="selling_price" min="0" step="0.01" required
               value="{{ old('selling_price', $product->selling_price) }}"
               class="form-control @error('selling_price') is-invalid @enderror">
        @error('selling_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if ($product->exists)
        <div class="col-12">
            <div class="alert alert-info mb-0">
                Stok saat ini: <strong>{{ $product->stock }}</strong>.
                Stok tidak dapat diubah dari form ini; gunakan transaksi Pembelian / Penjualan.
            </div>
        </div>
    @else
        <div class="col-12">
            <div class="alert alert-info mb-0">Stok awal produk baru adalah 0. Tambahkan stok lewat menu Pembelian.</div>
        </div>
    @endif
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
