@extends('layouts.app')

@section('title', 'Editar Produto')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Produto: {{ $produto->nome }}</h1>
        <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
    </div>

    {{-- Usando o seu componente de alertas para manter o padrão --}}
    @include('partials.alerts')

    <form action="{{ route('produtos.update', $produto->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" 
                   class="form-control @error('nome') is-invalid @enderror" 
                   value="{{ old('nome', $produto->nome) }}" required>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" rows="4" 
                      class="form-control @error('descricao') is-invalid @enderror">{{ old('descricao', $produto->descricao) }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="preco" class="form-label">Preço (R$)</label>
                <input type="number" name="preco" id="preco" class="form-control @error('preco') is-invalid @enderror" 
                       step="0.01" value="{{ old('preco', $produto->preco) }}" required>
            </div>
            
            <div class="col-md-4 mb-3">
                <label for="estoque" class="form-label">Estoque</label>
                <input type="number" name="estoque" id="estoque" class="form-control @error('estoque') is-invalid @enderror" 
                       value="{{ old('estoque', $produto->estoque) }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="categoria_id" class="form-label">Categoria</label>
                <select name="categoria_id" id="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
                    <option value="">Selecione...</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" 
                            {{ old('categoria_id', $produto->categoria_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-4 card p-3 bg-light">
            <label for="imagem" class="form-label fw-bold">Imagem do Produto</label>
            <input type="file" name="imagem" id="imagem" class="form-control @error('imagem') is-invalid @enderror">
            <small class="text-muted">Deixe em branco para manter a imagem atual.</small>
            
            @if($produto->imagem)
                <div class="mt-3">
                    <p class="mb-1 small text-secondary">Imagem atual:</p>
                    <img src="{{ asset('storage/' . $produto->imagem) }}" 
                         alt="{{ $produto->nome }}" 
                         class="img-thumbnail shadow-sm" 
                         style="max-width: 150px; height: auto;">
                </div>
            @endif
        </div>

        <div class="d-flex gap-2 border-top pt-3">
            <button type="submit" class="btn btn-primary px-4">Salvar Alterações</button>
            <a href="{{ route('produtos.index') }}" class="btn btn-light border">Cancelar</a>
        </div>
    </form>
</div>
@endsection