@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
    <h1 class="h4 mb-3">Tambah Pengguna</h1>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('users.store') }}" novalidate>
            @csrf
            @include('users._form')
        </form>
    </div></div>
@endsection
