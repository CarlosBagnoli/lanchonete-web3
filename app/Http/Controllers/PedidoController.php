<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with(['user', 'itens.produto'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('pedidos.index', compact('pedidos'));
    }

    public function relatorioDia()
    {
        $pedidosDoDia = Pedido::with(['user', 'itens.produto'])
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->get();

        return view('pedidos.relatorio-dia', compact('pedidosDoDia'));
    }

    public function create()
    {
        return view('pedidos.create');
    }

    public function store(Request $request)
    {
        $pedido = Pedido::create([
            'user_id' => auth()->id(),
            'status' => 'aberto',
            'total' => 0,
            'observacoes' => $request->input('observacoes'),
        ]);

        return redirect()->route('pedidos.edit', $pedido)
            ->with('sucesso', 'Pedido iniciado! Agora adicione itens.');
    }

    public function edit(Pedido $pedido)
    {
        $pedido->load('itens.produto');
        $produtos = \App\Models\Produto::orderBy('nome')->get();

        return view('pedidos.edit', compact('pedido', 'produtos'));
    }

    public function updateAddress(Request $request, Pedido $pedido)
    {
        $dados = $request->validate([
            'cep' => ['nullable', 'string'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'uf' => ['nullable', 'string', 'size:2'],
        ]);

        $cepLimpo = $dados['cep'] ? preg_replace('/\D/', '', $dados['cep']) : null;

        if ($cepLimpo !== null && ! preg_match('/^\d{8}$/', $cepLimpo)) {
            return response()->json([
                'message' => 'CEP inválido. Informe 8 números válidos.',
                'errors' => ['cep' => ['CEP deve conter exatamente 8 números válidos.']],
            ], 422);
        }

        $pedido->fill([
            'cep' => $cepLimpo,
            'logradouro' => $dados['logradouro'] ?? null,
            'bairro' => $dados['bairro'] ?? null,
            'cidade' => $dados['cidade'] ?? null,
            'uf' => $dados['uf'] ?? null,
        ]);

        $pedido->save();

        return response()->json([
            'message' => 'Endereço do pedido salvo com sucesso.',
            'pedido' => [
                'id' => $pedido->id,
                'cep' => $pedido->cep,
                'logradouro' => $pedido->logradouro,
                'bairro' => $pedido->bairro,
                'cidade' => $pedido->cidade,
                'uf' => $pedido->uf,
            ],
        ]);
    }

    public function updateStatus(Request $request, Pedido $pedido)
    {
        $dados = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', Pedido::STATUSES)],
        ]);

        if ($pedido->isClosed()) {
            abort(403, 'Pedido fechado não pode ter status alterado.');
        }

        $statusAtual = $pedido->status;
        $proximoStatus = $pedido->nextStatus();

        if ($dados['status'] !== $proximoStatus) {
            abort(422, 'Status inválido para este pedido. A sequência permitida é: ' . implode(' → ', Pedido::STATUSES));
        }

        $pedido->status = $dados['status'];
        $pedido->save();

        return redirect()->route('pedidos.edit', $pedido)
            ->with('sucesso', 'Status atualizado para "' . $dados['status'] . '".');
    }

    // Deletar pedido (aprovado para admin e atendente)
    public function destroy(Pedido $pedido)
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, ['admin', 'atendente'])) {
            abort(403);
        }

        // remover itens primeiro
        $pedido->itens()->delete();
        $pedido->delete();

        return redirect()->route('pedidos.index')
            ->with('sucesso', 'Pedido excluído com sucesso.');
    }
}
