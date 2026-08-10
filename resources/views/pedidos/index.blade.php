@extends('layouts.app')
@section('title', 'Pedidos')
@section('content')
    @include('partials.alerts')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Pedidos</h2>
        <a class="btn btn-primary" href="{{ route('pedidos.create') }}">Novo Pedido</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Atendente</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidos as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->user->name ?? '—' }}</td>
                            <td>{{ $p->status }}</td>
                            <td class="text-end">R$ {{ number_format($p->total, 2, ',', '.') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('pedidos.edit', $p) }}">Abrir</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center p-4 text-muted">Nenhum pedido.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $pedidos->links() }}</div>
@endsection
