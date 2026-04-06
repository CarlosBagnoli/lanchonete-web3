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
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produtos as $p)
                <tr>
                    <td><strong>{{ $p->nome }}</strong></td>
                    <td><span class="badge bg-info text-dark">{{ $p->categoria->nome }}</span></td>
                    <td>R$ {{ number_format($p->preco, 2, ',', '.') }}</td>
                    <td>{{ $p->estoque }} unidades</td>
                    <td class="text-center">
                        <a href="{{ route('produtos.edit', $p->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                        
                        <form action="{{ route('produtos.destroy', $p->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir produto?')">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Nenhum produto cadastrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $produtos->links() }}
    </div>
@endsection