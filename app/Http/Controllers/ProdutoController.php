<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\File; 

class ProdutoController extends Controller
{
    public function __construct()
    {
        // Garante que apenas usuários com role 'admin' podem criar/editar/excluir produtos
        $this->middleware('role:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    // Listar todos os produtos (Index)
    public function index(Request $request)
    {
        // Usamos o 'with' para carregar a categoria e evitar o problema de N+1 consultas
        $query = Produto::with('categoria');
        
        // Filtrar por categoria se fornecida
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        
        // Validar e aplicar ordenação
        $sortAllowed = ['nome', 'preco', 'categoria_id', 'estoque'];
        $dirAllowed = ['asc', 'desc'];
        
        $sort = in_array($request->query('sort'), $sortAllowed) ? $request->query('sort') : 'nome';
        $dir = in_array($request->query('dir'), $dirAllowed) ? $request->query('dir') : 'asc';
        
        $query->orderBy($sort, $dir);
        
        $produtos = $query->paginate(10)->withQueryString();
        $categorias = Categoria::where('ativa', true)->get();
        
        return view('produtos.index', compact('produtos', 'categorias', 'sort', 'dir'));
    }

    // Mostrar formulário de criação (Create)
    public function create()
    {
        $categorias = Categoria::where('ativa', true)->get();
        return view('produtos.create', compact('categorias'));
    }

    // Salvar no banco (Store)
        // Salvar no banco (Store)
    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome'         => 'required|min:3|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'preco'        => 'required|numeric|min:0',
            'descricao'    => 'nullable|max:500',
            'estoque'      => 'required|integer|min:0',
            'imagem'       => 'nullable|image|max:2048',
        ]);

        
        $dados['ativo'] = $request->has('ativo') ? 1 : 0;

       
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $nomeImagem = time() . '_' . uniqid() . '.' . $request->imagem->extension();
            $request->imagem->move(public_path('imagens/produtos'), $nomeImagem);
            $dados['imagem'] = $nomeImagem;
        }

        Produto::create($dados);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto cadastrado com sucesso!');
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
            'imagem'       => 'nullable|image|max:2048',
        ]);

        // TRATAMENTO DO CHECKBOX 'ATIVO'
        $dados['ativo'] = $request->has('ativo') ? 1 : 0;

        // Tratar upload de nova imagem: remover antiga e salvar a nova na pasta public
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            
            // Remover imagem antiga física da pasta public, se existir
            if ($produto->imagem) {
                $caminhoAntigo = public_path('imagens/produtos/' . $produto->imagem);
                if (\Illuminate\Support\Facades\File::exists($caminhoAntigo)) {
                    \Illuminate\Support\Facades\File::delete($caminhoAntigo);
                }
            }

            // Faz o upload da nova imagem
            $nomeImagem = time() . '_' . uniqid() . '.' . $request->imagem->extension();
            $request->imagem->move(public_path('imagens/produtos'), $nomeImagem);
            $dados['imagem'] = $nomeImagem;
        }

        $produto->update($dados);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }


    // Mostrar formulário de edição (Edit)
    public function edit(Produto $produto)
    {
        $categorias = Categoria::where('ativa', true)->get();
        return view('produtos.edit', compact('produto', 'categorias'));
    }



    // Deletar do banco (Destroy)
    public function destroy(Produto $produto)
    {
        // Remover imagem associada fisicamente da pasta public
        if ($produto->imagem) {
            $caminhoImagem = public_path('imagens/produtos/' . $produto->imagem);
            if (File::exists($caminhoImagem)) {
                File::delete($caminhoImagem);
            }
        }

        $produto->delete();

        return redirect()->route('produtos.index')
            ->with('success', 'Produto removido com sucesso!');
    }
}
