@extends('layouts.app')
@section('title', 'Editar Pedido')
@section('content')
    @include('partials.alerts')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Pedido #{{ $pedido->id }}</h2>
        <div class="d-flex gap-2">
            @auth
                @if(in_array(auth()->user()->role, ['admin','atendente']))
                    @if($pedido->nextStatus())
                        <form method="POST" action="{{ route('pedidos.status.update', $pedido) }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ $pedido->nextStatus() }}">
                            <button class="btn btn-success">{{ ucfirst($pedido->nextStatus()) }}</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('pedidos.destroy', $pedido) }}" onsubmit="return confirm('Deseja excluir este pedido?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger">Excluir Pedido</button>
                    </form>
                @endif
            @endauth
            <a class="btn btn-outline-secondary" href="{{ route('pedidos.index') }}">Voltar</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-2">Resumo do pedido</h6>
                    <p class="mb-1"><strong>Status:</strong> {{ $pedido->status }}</p>
                    <p class="mb-1"><strong>Observações:</strong> {{ $pedido->observacoes ?? '—' }}</p>
                    <p class="mb-0"><strong>Total atual:</strong> R$ {{ number_format($pedido->total,2,',','.') }}</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bold">Adicionar item</h5>
                    @if($pedido->canEdit())
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
                    @else
                        <div class="alert alert-warning mb-0">Pedido fechado. Não é possível adicionar ou remover itens.</div>
                    @endif
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
                                    <td class="text-end">
                                        @if($pedido->canEdit())
                                            <form method="POST" action="{{ route('pedidos.itens.update', [$pedido, $item]) }}" class="d-inline d-flex justify-content-end align-items-center">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" name="quantidade" value="{{ $item->quantidade }}" min="1" max="999" class="form-control form-control-sm me-2" style="width:80px;">
                                                <button class="btn btn-sm btn-outline-primary">Atualizar</button>
                                            </form>
                                        @else
                                            <span class="text-muted">{{ $item->quantidade }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">R$ {{ number_format($item->preco_unitario,2,',','.') }}</td>
                                    <td class="text-end">R$ {{ number_format($item->subtotal,2,',','.') }}</td>
                                    <td class="text-end">
                                        @if($pedido->canEdit())
                                            <form method="POST" action="{{ route('pedidos.itens.destroy', [$pedido, $item]) }}" class="d-inline"
                                                  onsubmit="return confirm('Remover este item?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Remover</button>
                                            </form>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
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
