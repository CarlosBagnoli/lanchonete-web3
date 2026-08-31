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

    <div class="card border-primary shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-uppercase text-muted">Carrinho</small>
                    <div class="fw-bold"><span id="miniCartItemCount">0 itens</span></div>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Total</small>
                    <strong id="miniCartTotal">R$ 0,00</strong>
                </div>
            </div>
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
                            <button class="btn btn-primary" type="submit" id="btnAddItem">
                                <span class="btn-text">Adicionar</span>
                                <span class="spinner-border spinner-border-sm d-none ms-1" role="status" aria-hidden="true"></span>
                            </button>
                        </form>

                        <script>
                          window.PW3 = {
                            pedidoId: {{ $pedido->id }},
                            urlAdd: "{{ route('pedidos.itens.storeJson', $pedido) }}",
                            urlDelBase: "{{ url('pedidos/'.$pedido->id.'/itens-json') }}",
                            urlQtyBase: "{{ url('pedidos/'.$pedido->id.'/itens') }}"
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
                              <td class="text-end">
                                <input
                                    type="number"
                                    class="form-control form-control-sm qty-input"
                                    value="{{ $item->quantidade }}"
                                    min="1"
                                    max="999"
                                    data-item-id="{{ $item->id }}"
                                    data-qty-route="{{ route('pedidos.itens.quantidade.update', ['pedido' => $pedido, 'itemPedido' => $item]) }}"
                                    style="width: 80px; margin-left: auto;"
                                >
                              </td>
                              <td class="text-end">R$ {{ number_format($item->preco_unitario,2,',','.') }}</td>
                              <td class="text-end" data-subtotal="{{ $item->id }}">R$ {{ number_format($item->subtotal,2,',','.') }}</td>
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
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
}

function moneyBR(value) {
    return (Number(value) || 0).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
}

function normalizeValidationMessage(message) {
    const translations = {
        'The product id field is required.': 'Selecione um produto.',
        'The quantity field is required.': 'Informe a quantidade.',
        'The quantity field must be an integer.': 'Quantidade deve ser um número inteiro.',
        'The quantity field must be at least 1.': 'Quantidade deve ser no mínimo 1.',
        'The quantity field may not be greater than 999.': 'Quantidade deve ser no máximo 999.',
        'The selected product id is invalid.': 'Produto selecionado é inválido.',
        'The selected produto id is invalid.': 'Produto selecionado é inválido.',
        'The produto id field is required.': 'Selecione um produto.'
    };

    return translations[message] || message;
}

function getFriendlyErrorMessage(resp, data) {
    if (resp.status === 422 && data && data.errors) {
        const labels = {
            produto_id: 'Produto',
            quantidade: 'Quantidade',
            product_id: 'Produto',
            quantity: 'Quantidade'
        };

        const parts = Object.entries(data.errors).map(([field, messages]) => {
            const label = labels[field] || field;
            const text = (messages || []).map((message) => normalizeValidationMessage(message)).join(' ');
            return `${label}: ${text}`;
        });

        return parts.join('\n');
    }

    if (data && data.message) {
        return data.message;
    }

    return 'Ocorreu um erro inesperado.';
}

function showToast(title, body) {
    const el = document.getElementById('pw3Toast');
    if (!el) {
        alert(title + '\n' + body);
        return;
    }

    const titleEl = document.getElementById('pw3ToastTitle');
    const bodyEl = document.getElementById('pw3ToastBody');
    if (titleEl) titleEl.textContent = title;
    if (bodyEl) bodyEl.textContent = body;

    const toast = bootstrap.Toast.getOrCreateInstance(el, { delay: 3000 });
    toast.show();
}

function upsertRow(item) {
    const tbody = document.getElementById('itensBody');
    if (!tbody) return;

    let row = document.getElementById('item-' + item.id);
    const qtyRoute = `${window.PW3.urlQtyBase}/${item.id}/quantidade`;
    const html = `
        <tr id="item-${item.id}" data-item-id="${item.id}">
            <td>${item.produto.nome}</td>
            <td class="text-end">
                <input
                    type="number"
                    class="form-control form-control-sm qty-input"
                    value="${item.quantidade}"
                    min="1"
                    max="999"
                    data-item-id="${item.id}"
                    data-qty-route="${qtyRoute}"
                    style="width: 80px; margin-left: auto;"
                >
            </td>
            <td class="text-end">${moneyBR(item.preco_unitario)}</td>
            <td class="text-end" data-subtotal="${item.id}">${moneyBR(item.subtotal)}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-danger" data-remove="${item.id}">Remover</button>
            </td>
        </tr>`;

    if (row) {
        row.outerHTML = html;
    } else {
        tbody.insertAdjacentHTML('beforeend', html);
    }

    const newInput = document.querySelector('#item-' + item.id + ' .qty-input');
    if (newInput) {
        attachQtyInput(newInput);
    }
}

function removeRow(itemId) {
    const row = document.getElementById('item-' + itemId);
    if (row) row.remove();
}

function syncMiniCart() {
    const qtyInputs = document.querySelectorAll('#itensBody .qty-input');
    let totalItens = 0;

    qtyInputs.forEach((input) => {
        const value = Number(input.value || 0);
        if (Number.isFinite(value) && value > 0) {
            totalItens += value;
        }
    });

    const countLabel = document.getElementById('miniCartItemCount');
    if (countLabel) {
        countLabel.textContent = `${totalItens} item${totalItens === 1 ? '' : 's'}`;
    }

    const totalLabel = document.getElementById('miniCartTotal');
    if (totalLabel) {
        const totalText = document.getElementById('pedidoTotal')?.textContent || 'R$ 0,00';
        totalLabel.textContent = totalText;
    }
}

function setTotal(total) {
    const totalEl = document.getElementById('pedidoTotal');
    if (totalEl) {
        totalEl.textContent = moneyBR(total);
    }
    syncMiniCart();
}

function updateRowTotals(itemId, quantidade, subtotal) {
    const row = document.getElementById('item-' + itemId);
    if (!row) return;

    const qtyInput = row.querySelector('.qty-input');
    if (qtyInput) {
        qtyInput.value = quantidade;
    }

    const subtotalCell = row.querySelector('[data-subtotal="' + itemId + '"]');
    if (subtotalCell) {
        subtotalCell.textContent = moneyBR(subtotal);
    }
}

function setAddLoading(isLoading) {
    const button = document.getElementById('btnAddItem');
    if (!button) return;

    const text = button.querySelector('.btn-text');
    const spinner = button.querySelector('.spinner-border');

    button.disabled = isLoading;
    button.setAttribute('aria-busy', isLoading ? 'true' : 'false');

    if (text) {
        text.textContent = isLoading ? 'Adicionando...' : 'Adicionar';
    }

    if (spinner) {
        spinner.classList.toggle('d-none', !isLoading);
    }
}

const qtyTimers = new Map();

function attachQtyInput(input) {
    if (!input || input.dataset.qtyBound === 'true') {
        return;
    }

    input.dataset.qtyBound = 'true';

    input.addEventListener('input', function () {
        const itemId = this.dataset.itemId;
        const route = this.dataset.qtyRoute;
        const value = Number(this.value);

        if (!route || !Number.isInteger(value) || value < 1 || value > 999) {
            return;
        }

        const existingTimer = qtyTimers.get(itemId);
        if (existingTimer) {
            clearTimeout(existingTimer);
        }

        const timer = setTimeout(async function () {
            const quantidade = Number(this.value);
            this.disabled = true;

            try {
                const resp = await fetch(route, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ quantidade })
                });

                const ct = resp.headers.get('content-type') || '';
                let data = null;
                if (ct.includes('application/json')) {
                    data = await resp.json();
                } else {
                    const text = await resp.text();
                    console.error('Non-JSON response on quantity update', resp.status, text);
                    showToast('Erro', 'Erro ao atualizar quantidade.');
                    return;
                }

                if (!resp.ok) {
                    const msg = getFriendlyErrorMessage(resp, data);
                    showToast('Dados inválidos', msg);
                    return;
                }

                if (data.item) {
                    updateRowTotals(data.item.id, data.item.quantidade, data.item.subtotal);
                }

                if (data.pedido) {
                    setTotal(data.pedido.total);
                }

                syncMiniCart();
                showToast('Tudo certo', data.message || 'Quantidade atualizada com sucesso.');
            } catch (err) {
                console.error(err);
                showToast('Erro', 'Falha de conexão ao atualizar quantidade.');
            } finally {
                this.disabled = false;
            }
        }.bind(this), 400);

        qtyTimers.set(itemId, timer);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    syncMiniCart();

    document.querySelectorAll('.qty-input').forEach((input) => {
        attachQtyInput(input);
    });

    const form = document.getElementById('formAddItem');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const button = document.getElementById('btnAddItem');
            if (!button || button.disabled) return;

            setAddLoading(true);
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
                    const msg = getFriendlyErrorMessage(resp, data);
                    showToast('Dados inválidos', msg);
                    console.error('Add failed', resp.status, data);
                    return;
                }

                upsertRow(data.item);
                setTotal(data.pedido.total);

                const produtoIdField = form.querySelector('[name="produto_id"]');
                const quantidadeField = form.querySelector('[name="quantidade"]');
                if (quantidadeField) quantidadeField.value = 1;
                if (produtoIdField) produtoIdField.value = '';

            } catch (err) {
                console.error(err);
                showToast('Erro', 'Falha de conexão. Verifique servidor e console.');
            } finally {
                setAddLoading(false);
            }
        });
    }

    const tbody = document.getElementById('itensBody');
    if (tbody) {
        tbody.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-remove]');
            if (!btn) return;

            const itemId = btn.getAttribute('data-remove');
            btn.disabled = true;
            btn.textContent = 'Removendo...';

            try {
                const url = `${window.PW3.urlDelBase}/${itemId}`;
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

                if (!resp.ok) {
                    showToast('Erro', getFriendlyErrorMessage(resp, data));
                    console.error('Delete failed', resp.status, data);
                    return;
                }

                removeRow(data.removed_item_id);
                setTotal(data.pedido.total);
                syncMiniCart();
                showToast('Item removido', data.message || 'Item removido com sucesso.');
            } catch (err) {
                console.error(err);
                showToast('Erro', 'Falha de conexão.');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Remover';
            }
        });
    }
});
</script>
@endpush
