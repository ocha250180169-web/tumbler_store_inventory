@extends('layouts.app')

@section('title', 'Ubah Kategori')

@section('content')
    <h1 class="h4 mb-3">Ubah Kategori</h1>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('categories.update', $category) }}" novalidate>
            @csrf
            @method('PUT')
            @include('categories._form')
        </form>
    </div></div>
@endsection
