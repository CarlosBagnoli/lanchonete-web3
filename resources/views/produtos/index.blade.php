@extends('layouts.app')

@section('content')
    @include('partials.alerts')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listagem de Produtos</h2>
        <a href="{{ route('produtos.create') }}" class="btn btn-success">Novo Produto</a>
    </div>

    <div class="card shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Categoria</th>
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
                        @if($produto->imagem)
                            <img src="{{ Storage::url($produto->imagem) }}" alt="Imagem de {{ $produto->nome }}" width="100">
                        @else
                            Sem imagem
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-sm btn-primary">Editar</a>
                        <form action="{{ route('produtos.destroy', $produto) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</button>
                        </form>
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