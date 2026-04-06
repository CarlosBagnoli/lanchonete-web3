@extends('layouts.app')

@section('title', 'Cadastrar Produto')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Novo Produto</h1>
        <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
    </div>

    {{-- Usando o seu componente de alertas --}}
    @include('partials.alerts')

    <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="nome" class="form-label">Nome do Produto</label>
                <input type="text" name="nome" id="nome" 
                       class="form-control @error('nome') is-invalid @enderror"
                       value="{{ old('nome') }}" placeholder="Ex: Teclado Mecânico" required>
                @error('nome')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="categoria_id" class="form-label">Categoria</label>
                <select name="categoria_id" id="categoria_id" 
                        class="form-select @error('categoria_id') is-invalid @enderror" required>
                    <option value="">Selecione...</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nome }}
                        </option>
                    @endforeach
                </select>
                @error('categoria_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" rows="3"
                      class="form-control @error('descricao') is-invalid @enderror" 
                      placeholder="Detalhes sobre o produto...">{{ old('descricao') }}</textarea>
            @error('descricao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="preco" class="form-label">Preço (R$)</label>
                <input type="number" name="preco" id="preco" step="0.01" min="0"
                       class="form-control @error('preco') is-invalid @enderror"
                       value="{{ old('preco') }}" placeholder="0,00" required>
                @error('preco')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="estoque" class="form-label">Estoque Inicial</label>
                <input type="number" name="estoque" id="estoque" min="0"
                       class="form-control @error('estoque') is-invalid @enderror"
                       value="{{ old('estoque', 0) }}">
                @error('estoque')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                    {{-- Lembre-se de tratar o 'ativo' no controller se necessário --}}
                    <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1"
                           {{ old('ativo', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="ativo">Produto Ativo</label>
                </div>
            </div>
        </div>

        <div class="mb-4 card p-3 bg-light">
            <label for="imagem" class="form-label fw-bold">Imagem do Produto</label>
            <input type="file" name="imagem" id="imagem" accept="image/*"
                   class="form-control @error('imagem') is-invalid @enderror">
            <div class="form-text">Formatos aceitos: JPG, PNG ou WEBP (Máx. 2MB).</div>
            @error('imagem')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2 border-top pt-3">
            <button type="submit" class="btn btn-primary px-5">Cadastrar</button>
            <a href="{{ route('produtos.index') }}" class="btn btn-light border">Cancelar</a>
        </div>
    </form>
</div>
@endsection