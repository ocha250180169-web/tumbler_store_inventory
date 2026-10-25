@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <h1 class="h4 mb-3">Tambah Kategori</h1>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('categories.store') }}" novalidate>
            @csrf
            @include('categories._form')
        </form>
    </div></div>
@endsection
