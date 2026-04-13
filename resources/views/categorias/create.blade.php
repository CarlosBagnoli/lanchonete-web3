
@include('partials.alerts')
@extends('layouts.app')
@section('title', 'Nova Categoria')
@section('content')
    <h2 class="mb-3">Nova Categoria</h2>
    <form action="{{ route('categorias.store') }}" method="POST">
        @csrf
        @include('categorias._form')
    </form>
@endsection
