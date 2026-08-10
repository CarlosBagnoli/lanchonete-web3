@extends('layouts.app')
@section('title', 'Novo Pedido')
@section('content')
    @include('partials.alerts')

    <h2 class="mb-3">Iniciar Pedido</h2>

    <form method="POST" action="{{ route('pedidos.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Observações (opcional)</label>
            <textarea name="observacoes" class="form-control" rows="3">{{ old('observacoes') }}</textarea>
        </div>
        <button class="btn btn-primary">Criar e adicionar itens</button>
        <a class="btn btn-outline-secondary" href="{{ route('pedidos.index') }}">Cancelar</a>
    </form>
@endsection
