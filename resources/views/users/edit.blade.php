@extends('layouts.app')

@section('title', 'Ubah Pengguna')

@section('content')
    <h1 class="h4 mb-3">Ubah Pengguna</h1>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('users.update', $user) }}" novalidate>
            @csrf
            @method('PUT')
            @include('users._form')
        </form>
    </div></div>
@endsection
