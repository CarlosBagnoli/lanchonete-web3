@extends('layouts.app')

@section('title', 'Funcionários')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Funcionários</h1>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->role }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.usuarios.updateRole', $usuario) }}" class="d-inline">
                                    @csrf
                                    <select name="role" class="form-select form-select-sm d-inline w-auto">
                                        <option value="atendente" {{ $usuario->role === 'atendente' ? 'selected' : '' }}>Atendente</option>
                                        <option value="admin" {{ $usuario->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary ms-2">Salvar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $usuarios->links() }}
        </div>
    </div>
</div>
@endsection
