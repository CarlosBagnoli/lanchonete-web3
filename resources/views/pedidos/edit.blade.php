@extends('layouts.app')
@section('title', 'Editar Pedido')
@section('content')
    @include('partials.alerts')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Pedido #{{ $pedido->id }}</h2>
        <a class="btn btn-outline-secondary" href="{{ route('pedidos.index') }}">Voltar</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bold">Adicionar item</h5>
                    <form method="POST" action="{{ route('pedidos.itens.store', $pedido) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Produto</label>
                            <select name="produto_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach($produtos as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->nome }} (R$ {{ number_format($prod->preco,2,',','.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade</label>
                            <input type="number" name="quantidade" class="form-control" value="1" min="1" max="99" required>
                        </div>
                        <button class="btn btn-primary">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bold">Itens do pedido</h5>

                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-end">Qtd</th>
                                <th class="text-end">Unit.</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pedido->itens as $item)
                                <tr>
                                    <td>{{ $item->produto->nome ?? '—' }}</td>
                                    <td class="text-end">{{ $item->quantidade }}</td>
                                    <td class="text-end">R$ {{ number_format($item->preco_unitario,2,',','.') }}</td>
                                    <td class="text-end">R$ {{ number_format($item->subtotal,2,',','.') }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('pedidos.itens.destroy', [$pedido, $item]) }}" class="d-inline"
                                              onsubmit="return confirm('Remover este item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Remover</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted p-3">Nenhum item ainda.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end">
                        <div class="fw-bold">Total: R$ {{ number_format($pedido->total,2,',','.') }}</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
