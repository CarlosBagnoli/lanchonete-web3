<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    // Listar todos os produtos (Index)
    public function index()
    {
        // Usamos o 'with' para carregar a categoria e evitar o problema de N+1 consultas
        $produtos = Produto::with('categoria')->orderBy('nome')->paginate(10);
        return view('produtos.index', compact('produtos'));
    }

    // Mostrar formulário de criação (Create)
    public function create()
    {
        $categorias = Categoria::where('ativa', true)->get();
        return view('produtos.create', compact('categorias'));
    }

    // Salvar no banco (Store)
    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome'         => 'required|min:3|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'preco'        => 'required|numeric|min:0',
            'descricao'    => 'nullable|max:500',
            'estoque'      => 'required|integer|min:0',
        ]);

        Produto::create($dados);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    // Mostrar formulário de edição (Edit)
    public function edit(Produto $produto)
    {
        $categorias = Categoria::where('ativa', true)->get();
        return view('produtos.edit', compact('produto', 'categorias'));
    }

    // Atualizar no banco (Update)
    public function update(Request $request, Produto $produto)
    {
        $dados = $request->validate([
            'nome'         => 'required|min:3|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'preco'        => 'required|numeric|min:0',
            'descricao'    => 'nullable|max:500',
            'estoque'      => 'required|integer|min:0',
        ]);

        $produto->update($dados);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    // Deletar do banco (Destroy)
    public function destroy(Produto $produto)
    {
        $produto->delete();

        return redirect()->route('produtos.index')
            ->with('success', 'Produto removido com sucesso!');
    }
}