<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PedidoResource;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PedidoApiController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'status' => ['nullable', 'string', Rule::in(Pedido::STATUSES)],
        ]);

        $query = Pedido::with('itens.produto')->orderByDesc('created_at');

        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }

        $pedidos = $query->paginate(10)->withQueryString();

        return PedidoResource::collection($pedidos);
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('itens.produto');

        return new PedidoResource($pedido);
    }
}
