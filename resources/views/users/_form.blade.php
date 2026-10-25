{{-- Variabel: $user (User, bisa baru) --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Nama</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="100" required
               class="form-control @error('name') is-invalid @enderror">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" maxlength="150" required
               class="form-control @error('email') is-invalid @enderror">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="role" class="form-label">Role</label>
        <select id="role" name="role" required class="form-select @error('role') is-invalid @enderror"
                @disabled($user->exists && $user->id === auth()->id())>
            <option value="staff" @selected(old('role', $user->role) === 'staff')>Staff</option>
            <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin / Owner</option>
        </select>
        @if ($user->exists && $user->id === auth()->id())
            {{-- select yang disabled tidak terkirim, jadi role dikirim lewat hidden input --}}
            <input type="hidden" name="role" value="{{ $user->role }}">
            <div class="form-text">Role akun Anda sendiri tidak dapat diubah.</div>
        @endif
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="password" class="form-label">Password {{ $user->exists ? '(kosongkan jika tidak diubah)' : '' }}</label>
        <input type="password" id="password" name="password" autocomplete="new-password"
               class="form-control @error('password') is-invalid @enderror" {{ $user->exists ? '' : 'required' }}>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
               class="form-control">
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
