<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'total' => (float) $this->total,
            'observacoes' => $this->observacoes,
            'cep' => $this->cep,
            'logradouro' => $this->logradouro,
            'bairro' => $this->bairro,
            'cidade' => $this->cidade,
            'uf' => $this->uf,
            'itens' => $this->whenLoaded('itens', function () {
                return $this->itens->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantidade' => $item->quantidade,
                        'preco_unitario' => (float) $item->preco_unitario,
                        'subtotal' => (float) $item->subtotal,
                        'produto' => $item->relationLoaded('produto') && $item->produto
                            ? [
                                'id' => $item->produto->id,
                                'nome' => $item->produto->nome,
                                'preco' => (float) $item->produto->preco,
                            ]
                            : null,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
