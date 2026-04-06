@php $isEdit = isset($produto); @endphp

<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Nome do Produto</label>
        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" 
               value="{{ old('nome', $produto->nome ?? '') }}" required>
        @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Categoria</label>
        <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
            <option value="">Selecione...</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}" 
                    {{ (old('categoria_id', $produto->categoria_id ?? '') == $cat->id) ? 'selected' : '' }}>
                    {{ $cat->nome }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Preço (R$)</label>
        <input type="number" step="0.01" name="preco" class="form-control @error('preco') is-invalid @enderror" 
               value="{{ old('preco', $produto->preco ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Estoque Atual</label>
        <input type="number" name="estoque" class="form-control @error('estoque') is-invalid @enderror" 
               value="{{ old('estoque', $produto->estoque ?? 0) }}" required>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Descrição</label>
    <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $produto->descricao ?? '') }}</textarea>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Atualizar Produto' : 'Salvar Produto' }}</button>
    <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>