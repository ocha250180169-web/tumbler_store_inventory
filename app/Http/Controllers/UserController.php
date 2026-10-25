<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create', ['user' => new User(['role' => User::ROLE_STAFF])]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::create($request->validated()); // password di-hash otomatis oleh cast 'hashed'

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']); // password kosong = tidak diubah
        }

        // Admin tidak boleh menurunkan role dirinya sendiri (mencegah tidak ada admin tersisa).
        if ($user->id === $request->user()->id) {
            $data['role'] = $user->role;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->sales()->exists() || $user->purchases()->exists()) {
            return back()->with('error', 'Pengguna tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
