@extends('layouts.app')

@section('title', 'Categorias')

@section('content')
    @include('partials.alerts')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Categorias</h2>
        <a href="{{ route('categorias.create') }}" class="btn btn-primary">
            Nova Categoria
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Ativa</th>
                        <th class="text-end px-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorias as $categoria)
                        <tr>
                            <td class="align-middle px-3">{{ $categoria->nome }}</td>
                            <td class="align-middle">
                                <span class="badge {{ $categoria->ativa ? 'bg-success' : 'bg-danger' }}">
                                    {{ $categoria->ativa ? 'Sim' : 'Não' }}
                                </span>
                            </td>
                            <td class="text-end px-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('categorias.edit', $categoria->id) }}" 
                                       class="btn btn-sm btn-outline-secondary">
                                        Editar
                                    </a>

                                    <form action="{{ route('categorias.destroy', $categoria->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Tem certeza? Isso excluirá todos os produtos desta categoria!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">Nenhuma categoria encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection