<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdutoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'nome' => $this->nome,
        'descricao' => $this->descricao,
        'preco' => (float) $this->preco,
        'estoque' => (int) $this->estoque,
        'ativo' => (bool) $this->ativo,
        'imagem' => $this->imagem,
        'categoria' => $this->whenLoaded('categoria', function () {
            return [
                'id' => $this->categoria->id,
                'nome' => $this->categoria->nome,
            ];
        }),
        'created_at' => $this->created_at?->toISOString(),
        'updated_at' => $this->updated_at?->toISOString(),
    ];
}
}
