@php
    $isEdit = isset($categoria);
@endphp

<div class="mb-3">
    <label for="nome" class="form-label">Nome</label>
    <input type="text" 
           class="form-control @error('nome') is-invalid @enderror" 
           id="nome" 
           name="nome" 
           value="{{ old('nome', $categoria->nome ?? '') }}" 
           maxlength="100" 
           required>
    @error('nome')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="descricao" class="form-label">Descrição</label>
    <textarea class="form-control @error('descricao') is-invalid @enderror" 
              id="descricao" 
              name="descricao" 
              rows="3" 
              maxlength="500">{{ old('descricao', $categoria->descricao ?? '') }}</textarea>
    @error('descricao')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-check form-switch mb-3">
    {{-- Campo hidden para garantir que o valor '0' seja enviado se o checkbox estiver desmarcado --}}
    <input type="hidden" name="ativa" value="0">
    <input class="form-check-input" 
           type="checkbox" 
           role="switch" 
           id="ativa" 
           name="ativa" 
           value="1"
           {{ old('ativa', $categoria->ativa ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="ativa">Ativa</label>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'Atualizar' : 'Salvar' }}
    </button>
    <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
</div>