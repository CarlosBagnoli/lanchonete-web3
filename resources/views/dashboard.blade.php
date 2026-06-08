@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Dashboard</h1>
    <p>Olá, {{ auth()->user()->name }}! Você está autenticado(a).</p>
</div>
@endsection