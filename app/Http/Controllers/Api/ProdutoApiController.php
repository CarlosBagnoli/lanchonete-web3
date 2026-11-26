<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdutoResource;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoApiController extends Controller
{
    private function authorizeGerente(Request $request): ?\Illuminate\Http\JsonResponse
    {
        if ($request->user()?->role !== 'gerente') {
            return response()->json([
                'message' => 'Apenas usuários gerente podem alterar produtos.',
                'data' => null,
                'errors' => [],
            ], 403);
        }

        return null;
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'q' => 'nullable|string|max:80',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
            'preco_min' => 'nullable|numeric|min:0',
            'preco_max' => 'nullable|numeric|min:0',
            'ativo' => 'nullable|boolean',
            'sort' => 'nullable|string|in:nome,preco,created_at',
            'dir' => 'nullable|string|in:asc,desc',
        ]);

        $query = Produto::query()->with('categoria');

        if (!empty($data['q'])) {
            $query->where('nome', 'like', '%' . $data['q'] . '%');
        }

        if (isset($data['categoria_id'])) {
            $query->where('categoria_id', $data['categoria_id']);
        }

        if (isset($data['preco_min'])) {
            $query->where('preco', '>=', $data['preco_min']);
        }

        if (isset($data['preco_max'])) {
            $query->where('preco', '<=', $data['preco_max']);
        }

        if (isset($data['ativo'])) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        $sort = $data['sort'] ?? 'nome';
        $dir = $data['dir'] ?? 'asc';

        $produtos = $query->orderBy($sort, $dir)->paginate(10)->withQueryString();

        return ProdutoResource::collection($produtos);
    }

    public function show(Produto $produto)
    {
        $produto->load('categoria');
        return new ProdutoResource($produto);
    }

    public function store(Request $request)
    {
        if ($forbidden = $this->authorizeGerente($request)) {
            return $forbidden;
        }

        $data = $request->validate([
            'nome' => 'required|string|max:120',
            'preco' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'descricao' => 'nullable|string|max:500',
            'estoque' => 'sometimes|integer|min:0',
            'ativo' => 'sometimes|boolean',
        ]);

        $produto = Produto::create($data);
        $produto->load('categoria');

        return response()->json([
            'message' => 'Produto criado com sucesso.',
            'data' => (new ProdutoResource($produto))->resolve($request),
        ], 201);
    }

    public function update(Request $request, Produto $produto)
    {
        if ($forbidden = $this->authorizeGerente($request)) {
            return $forbidden;
        }

        $data = $request->validate([
            'nome' => 'sometimes|required|string|max:120',
            'preco' => 'sometimes|required|numeric|min:0',
            'categoria_id' => 'sometimes|required|exists:categorias,id',
            'descricao' => 'sometimes|nullable|string|max:500',
            'estoque' => 'sometimes|integer|min:0',
            'ativo' => 'sometimes|boolean',
        ]);

        $produto->update($data);
        $produto->load('categoria');

        return response()->json([
            'message' => 'Produto atualizado com sucesso.',
            'data' => (new ProdutoResource($produto))->resolve($request),
        ]);
    }

    public function destroy(Request $request, Produto $produto)
    {
        if ($forbidden = $this->authorizeGerente($request)) {
            return $forbidden;
        }

        $produto->delete();

        return response()->json([
            'message' => 'Produto excluído com sucesso.',
            'data' => null,
        ], 200);
    }
}