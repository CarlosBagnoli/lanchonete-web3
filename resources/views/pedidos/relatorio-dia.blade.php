@extends('layouts.app')
@section('title', 'Relatório do Dia')
@section('content')
    @include('partials.alerts')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Relatório do Dia</h2>
        <a class="btn btn-outline-secondary" href="{{ route('pedidos.index') }}">Voltar</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Atendente</th>
                        <th>Status</th>
                        <th>Itens</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidosDoDia as $pedido)
                        <tr>
                            <td>#{{ $pedido->id }}</td>
                            <td>{{ $pedido->user->name ?? '—' }}</td>
                            <td>{{ $pedido->status }}</td>
                            <td>
                                @if($pedido->itens->isNotEmpty())
                                    @foreach($pedido->itens as $item)
                                        <div>{{ $item->produto->nome ?? 'Produto' }} x{{ $item->quantidade }}</div>
                                    @endforeach
                                @else
                                    <span class="text-muted">Nenhum item</span>
                                @endif
                            </td>
                            <td class="text-end">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted p-4">Nenhum pedido registrado hoje.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
