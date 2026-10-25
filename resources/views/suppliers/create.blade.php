@extends('layouts.app')

@section('title', 'Tambah Pemasok')

@section('content')
    <h1 class="h4 mb-3">Tambah Pemasok</h1>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('suppliers.store') }}" novalidate>
            @csrf
            @include('suppliers._form')
        </form>
    </div></div>
@endsection
