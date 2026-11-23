<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Produto;
use Illuminate\Http\Request;

class ItemPedidoController extends Controller
{
    protected function assertPedidoAberto(Pedido $pedido): void
    {
        if ($pedido->isClosed()) {
            abort(403, 'Pedido fechado não pode receber alterações de itens.');
        }
    }

    public function store(Request $request, Pedido $pedido)
    {
        $this->assertPedidoAberto($pedido);

        $dados = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1|max:99',
        ]);

        $produto = Produto::findOrFail($dados['produto_id']);

        $preco = $produto->preco;
        $subtotal = $preco * $dados['quantidade'];

        // Se já existir item do produto no pedido, atualiza a quantidade (opcional)
        $item = ItemPedido::where('pedido_id', $pedido->id)
            ->where('produto_id', $produto->id)
            ->first();

        if ($item) {
            $item->quantidade += $dados['quantidade'];
            $item->preco_unitario = $preco;
            $item->subtotal = $item->quantidade * $preco;
            $item->save();
        } else {
            ItemPedido::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $produto->id,
                'quantidade' => $dados['quantidade'],
                'preco_unitario' => $preco,
                'subtotal' => $subtotal,
            ]);
        }

        // Recalcular total
        $pedido->total = ItemPedido::where('pedido_id', $pedido->id)->sum('subtotal');
        $pedido->save();

        return redirect()->route('pedidos.edit', $pedido)
            ->with('sucesso', 'Item adicionado ao pedido!');
    }

    public function destroy(Pedido $pedido, ItemPedido $itemPedido)
    {
        $this->assertPedidoAberto($pedido);

        // Garante que o item pertence ao pedido
        abort_unless($itemPedido->pedido_id === $pedido->id, 404);

        $itemPedido->delete();

        $pedido->total = ItemPedido::where('pedido_id', $pedido->id)->sum('subtotal');
        $pedido->save();

        return redirect()->route('pedidos.edit', $pedido)
            ->with('sucesso', 'Item removido!');
    }

    public function update(Request $request, Pedido $pedido, ItemPedido $itemPedido)
    {
        $this->assertPedidoAberto($pedido);

        abort_unless($itemPedido->pedido_id === $pedido->id, 404);

        $dados = $request->validate([
            'quantidade' => 'required|integer|min:1|max:999',
        ]);

        $itemPedido->quantidade = $dados['quantidade'];
        $itemPedido->subtotal = $itemPedido->preco_unitario * $itemPedido->quantidade;
        $itemPedido->save();

        // Recalcula total do pedido
        $pedido->total = ItemPedido::where('pedido_id', $pedido->id)->sum('subtotal');
        $pedido->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Quantidade atualizada com sucesso.',
                'pedido' => [
                    'id' => $pedido->id,
                    'total' => (float) $pedido->total,
                ],
                'item' => [
                    'id' => $itemPedido->id,
                    'quantidade' => (int) $itemPedido->quantidade,
                    'preco_unitario' => (float) $itemPedido->preco_unitario,
                    'subtotal' => (float) $itemPedido->subtotal,
                ],
            ]);
        }

        return redirect()->route('pedidos.edit', $pedido)->with('sucesso', 'Quantidade atualizada com sucesso.');
    }

    public function updateQuantity(Request $request, Pedido $pedido, ItemPedido $itemPedido)
    {
        return $this->update($request, $pedido, $itemPedido);
    }

    public function storeJson(Request $request, Pedido $pedido)
    {
        $this->assertPedidoAberto($pedido);

        $dados = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1|max:99',
        ]);

        $produto = Produto::findOrFail($dados['produto_id']);

        $preco = $produto->preco;

        $item = ItemPedido::where('pedido_id', $pedido->id)
            ->where('produto_id', $produto->id)
            ->first();

        if ($item) {
            $item->quantidade += $dados['quantidade'];
            $item->preco_unitario = $preco;
            $item->subtotal = $item->quantidade * $preco;
            $item->save();
        } else {
            $item = ItemPedido::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $produto->id,
                'quantidade' => $dados['quantidade'],
                'preco_unitario' => $preco,
                'subtotal' => $dados['quantidade'] * $preco,
            ]);
        }

        $pedido->total = ItemPedido::where('pedido_id', $pedido->id)->sum('subtotal');
        $pedido->save();

        $item->load('produto');

        return response()->json([
            'message' => 'Item adicionado com sucesso.',
            'pedido' => [
                'id' => $pedido->id,
                'total' => (float) $pedido->total,
            ],
            'item' => [
                'id' => $item->id,
                'produto' => [
                    'id' => $item->produto->id,
                    'nome' => $item->produto->nome,
                ],
                'quantidade' => (int) $item->quantidade,
                'preco_unitario' => (float) $item->preco_unitario,
                'subtotal' => (float) $item->subtotal,
            ]
        ], 200);
    }

    public function destroyJson(Pedido $pedido, ItemPedido $itemPedido)
    {
        $this->assertPedidoAberto($pedido);

        abort_unless($itemPedido->pedido_id === $pedido->id, 404);

        $itemPedido->delete();

        $pedido->total = ItemPedido::where('pedido_id', $pedido->id)->sum('subtotal');
        $pedido->save();

        return response()->json([
            'message' => 'Item removido com sucesso.',
            'pedido' => [
                'id' => $pedido->id,
                'total' => (float) $pedido->total,
            ],
            'removed_item_id' => (int) $itemPedido->id,
        ], 200);
    }
}
