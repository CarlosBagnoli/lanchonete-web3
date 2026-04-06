<?php
@extends('layouts.app')
@section('title', 'Contato')
@section('content')
    @include('partials.alerts')

    <h2 class="mb-3">Fale conosco</h2>

    <form action="#" method="GET" class="card p-3">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" id="nome" name="nome" class="form-control" value="{{ old('nome') }}" maxlength="100" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" maxlength="150" required>
        </div>

        <div class="mb-3">
            <label for="mensagem" class="form-label">Mensagem</label>
            <textarea id="mensagem" name="mensagem" class="form-control" rows="6" maxlength="2000" required>{{ old('mensagem') }}</textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Enviar</button>
            <a href="{{ route('home') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
@endsection