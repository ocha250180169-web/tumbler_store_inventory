{{-- Variabel: $supplier (Supplier, bisa baru) --}}
<div class="mb-3">
    <label for="name" class="form-label">Nama Pemasok</label>
    <input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}" maxlength="150" required
           class="form-control @error('name') is-invalid @enderror">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="phone" class="form-label">Telepon</label>
    <input type="text" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}" maxlength="30"
           class="form-control @error('phone') is-invalid @enderror">
    @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email', $supplier->email) }}" maxlength="150"
           class="form-control @error('email') is-invalid @enderror">
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<button type="submit" class="btn btn-success">Simpan</button>
<a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Batal</a>
