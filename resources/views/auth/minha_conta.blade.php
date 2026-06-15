@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Minha Conta</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('minha-conta.update') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Nome</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
        </div>

        <button class="btn btn-primary mt-3">Salvar</button>
    </form>
</div>
@endsection
