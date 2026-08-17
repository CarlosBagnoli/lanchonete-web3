@extends('layouts.app')

@section('content')
    @include('partials.alerts')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listagem de Produtos</h2>
        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('produtos.create') }}" class="btn btn-success">Novo Produto</a>
            @endif
        @endauth
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('produtos.index') }}" class="row g-2 align-items-center">
                <div class="col-md">
                    <select name="categoria_id" class="form-select">
                        <option value="">Todas as Categorias</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary">Filtrar</button>
                    <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>
                        <a href="{{ route('produtos.index', array_merge(request()->query(), ['sort' => 'nome', 'dir' => $sort === 'nome' && $dir === 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                            Nome
                            @if($sort === 'nome')
                                <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('produtos.index', array_merge(request()->query(), ['sort' => 'preco', 'dir' => $sort === 'preco' && $dir === 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                            Preço
                            @if($sort === 'preco')
                                <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('produtos.index', array_merge(request()->query(), ['sort' => 'categoria_id', 'dir' => $sort === 'categoria_id' && $dir === 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                            Categoria
                            @if($sort === 'categoria_id')
                                <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Imagem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produtos as $produto)
                <tr>
                    <td>{{ $produto->nome }}</td>
                    <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                    <td>{{ $produto->categoria->nome }}</td>
                    <td>
                        {{-- AQUI FOI ALTERADO PARA USAR ASSET() --}}
                        @if($produto->imagem)
                            <img src="{{ asset('imagens/produtos/' . $produto->imagem) }}" alt="Imagem de {{ $produto->nome }}" width="80" class="img-thumbnail rounded">
                        @else
                            <span class="badge bg-secondary">Sem imagem</span>
                        @endif
                    </td>
                    <td>
                        @auth
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-sm btn-primary">Editar</a>
                                <form method="POST" action="{{ route('produtos.destroy', $produto) }}" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                            @else
                                <span class="text-muted small">Sem permissões</span>
                            @endif
                        @else
                            <span class="text-muted small">Login necessário</span>
                        @endauth
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $produtos->links() }}
    </div>
@endsection
