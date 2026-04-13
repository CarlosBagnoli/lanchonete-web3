{{-- resources/views/partials/alerts.blade.php --}}

@if (session('sucesso'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('sucesso') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

@if (session('erro'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('erro') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-warning" role="alert">
        <strong>Atenção:</strong> verifique os campos do formulário.
    </div>
@endif