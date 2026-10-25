@extends('layouts.app')

@section('title', 'Ubah Produk')

@section('content')
    <h1 class="h4 mb-3">Ubah Produk</h1>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}" novalidate>
            @csrf
            @method('PUT')
            @include('products._form')
        </form>
    </div></div>
@endsection
