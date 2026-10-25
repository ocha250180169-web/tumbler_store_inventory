{{-- Variabel: $category (Category, bisa baru) --}}
<div class="mb-3">
    <label for="name" class="form-label">Nama Kategori</label>
    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" maxlength="100" required
           class="form-control @error('name') is-invalid @enderror">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<button type="submit" class="btn btn-success">Simpan</button>
<a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Batal</a>
