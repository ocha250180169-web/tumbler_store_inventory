@extends('layouts.app')

@section('title', 'Ubah Pemasok')

@section('content')
    <h1 class="h4 mb-3">Ubah Pemasok</h1>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" novalidate>
            @csrf
            @method('PUT')
            @include('suppliers._form')
        </form>
    </div></div>
@endsection
