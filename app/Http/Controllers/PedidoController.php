<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with('user')
            ->orderByDesc('id')
            ->paginate(10);

        return view('pedidos.index', compact('pedidos'));
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

    // update e destroy podem ser evoluídos nas atividades
}
