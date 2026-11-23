@extends('layouts.app')

@section('title', 'Categorias')

@section('content')

    @include('partials.alerts')

    <div class="row align-items-center g-3 mb-3">
        <div class="col-auto">
            <h2 class="mb-0">Categorias</h2>
        </div>

        <div class="col-md">
            <form method="GET" action="{{ route('categorias.index') }}" class="row g-2 align-items-center">
                <div class="col-sm">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por nome...">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary w-100" type="submit">Buscar</button>
                </div>
                <div class="col-auto">
                    <a class="btn btn-outline-secondary w-100" href="{{ route('categorias.index') }}">Limpar</a>
                </div>
            </form>
        </div>

        <div class="col-auto">
            <a class="btn btn-primary" href="{{ route('categorias.create') }}">Nova Categoria</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
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

                                    <form method="POST" action="{{ route('categorias.destroy', $categoria->id) }}" class="d-inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Excluir</button>
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

    <div class="mt-3">
        {{ $categorias->links() }}
    </div>
@endsection