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
                        <form id="formAddItem">
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
                            <button class="btn btn-primary" type="submit">Adicionar</button>
                        </form>

                        <script>
                          window.PW3 = {
                            pedidoId: {{ $pedido->id }},
                            urlAdd: "{{ route('pedidos.itens.storeJson', $pedido) }}",
                            urlDelBase: "{{ url('pedidos/'.$pedido->id.'/itens-json') }}"
                          };
                        </script>
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
                        <tbody id="itensBody">
                          @foreach($pedido->itens as $item)
                            <tr id="item-{{ $item->id }}">
                              <td>{{ $item->produto->nome }}</td>
                              <td class="text-end">{{ $item->quantidade }}</td>
                              <td class="text-end">R$ {{ number_format($item->preco_unitario,2,',','.') }}</td>
                              <td class="text-end">R$ {{ number_format($item->subtotal,2,',','.') }}</td>
                              <td class="text-end">
                                <button class="btn btn-sm btn-outline-danger" data-remove="{{ $item->id }}">Remover</button>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end">
                        <div class="fw-bold">Total: <span id="pedidoTotal">R$ {{ number_format($pedido->total,2,',','.') }}</span></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

function moneyBR(value) {
    return (value ?? 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function showToast(title, body) {
    const el = document.getElementById('pw3Toast');
    if (!el) {
        // fallback simple alert
        alert(title + '\n' + body);
        return;
    }
    document.getElementById('pw3ToastTitle').textContent = title;
    document.getElementById('pw3ToastBody').textContent = body;
    const toast = bootstrap.Toast.getOrCreateInstance(el, { delay: 2500 });
    toast.show();
}

function upsertRow(item) {
    const tbody = document.getElementById('itensBody');
    let row = document.getElementById('item-' + item.id);

    const html = `
        <tr id="item-${item.id}">
            <td>${item.produto.nome}</td>
            <td class="text-end">${item.quantidade}</td>
            <td class="text-end">${moneyBR(item.preco_unitario)}</td>
            <td class="text-end">${moneyBR(item.subtotal)}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-danger" data-remove="${item.id}">Remover</button>
            </td>
        </tr>`;

    if (row) {
        row.outerHTML = html;
    } else {
        tbody.insertAdjacentHTML('beforeend', html);
    }
}

function removeRow(itemId) {
    const row = document.getElementById('item-' + itemId);
    if (row) row.remove();
}

function setTotal(total) {
    document.getElementById('pedidoTotal').textContent = moneyBR(total);
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formAddItem');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const fd = new FormData(form);

            try {
                const resp = await fetch(window.PW3.urlAdd, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json'
                    },
                    body: fd
                });

                const ct = resp.headers.get('content-type') || '';
                let data = null;
                if (ct.includes('application/json')) {
                    data = await resp.json();
                } else {
                    const text = await resp.text();
                    console.error('Non-JSON response on add', resp.status, text);
                    showToast('Erro', 'Erro no servidor. Veja console para detalhes.');
                    return;
                }

                if (!resp.ok) {
                    const msg = data.message || 'Erro ao adicionar item.';
                    showToast('Erro', msg);
                    console.error('Add failed', resp.status, data);
                    return;
                }

                upsertRow(data.item);
                setTotal(data.pedido.total);
                showToast('Sucesso', data.message);

                // reset rápido
                form.quantidade.value = 1;
                form.produto_id.value = '';

            } catch (err) {
                console.error(err);
                showToast('Erro', 'Falha de conexão. Verifique servidor e console.');
            }
        });
    }

    const tbody = document.getElementById('itensBody');
    if (tbody) {
        tbody.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-remove]');
            if (!btn) return;

            const itemId = btn.getAttribute('data-remove');
            if (!confirm('Remover este item?')) return;

            try {
                const url = `${window.PW3.urlDelBase}/${itemId}`;
                console.log('PW3 delete', { url, itemId });
                const resp = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json'
                    }
                });

                const ct = resp.headers.get('content-type') || '';
                let data = null;
                if (ct.includes('application/json')) {
                    data = await resp.json();
                } else {
                    const text = await resp.text();
                    console.error('Non-JSON response on delete', resp.status, text);
                    showToast('Erro', `Erro no servidor (status ${resp.status}). Veja console.`);
                    return;
                }

                console.log('Delete response', resp.status, data);
                if (!resp.ok) {
                    showToast('Erro', data.message || `Erro ao remover (status ${resp.status}).`);
                    console.error('Delete failed', resp.status, data);
                    return;
                }

                removeRow(data.removed_item_id);
                setTotal(data.pedido.total);
                showToast('Sucesso', data.message);

            } catch (err) {
                console.error(err);
                showToast('Erro', 'Falha de conexão.');
            }
        });
    }
});
</script>
@endpush
