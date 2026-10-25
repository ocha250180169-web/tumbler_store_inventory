@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Pengguna &amp; Role</h1>
        <a href="{{ route('users.create') }}" class="btn btn-success btn-sm">+ Tambah Pengguna</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr><th>Nama</th><th>Email</th><th>Role</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge {{ $user->isAdmin() ? 'bg-success' : 'bg-secondary' }}">{{ $user->role }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary btn-sm">Ubah</a>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline"
                                      onsubmit="return confirm('Hapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
