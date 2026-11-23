@extends('layouts.app')
@section('title', 'Início')
@section('content')
    <div class="p-5 mb-4 bg-light rounded-4 border shadow-sm">
        <div class="container-fluid py-3">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill">Lanchonete Web3</span>
                    <h1 class="display-5 fw-bold mb-3">{{ $titulo }}</h1>
                    <p class="fs-5 text-secondary mb-4">
                        Este é o início do sistema da lanchonete. Em breve, você terá catálogo, pedidos, relatórios e muito mais.
                    </p>
                    <a href="{{ route('produtos.index') }}" class="btn btn-primary btn-lg">Ver cardápio</a>
                </div>
                <div class="col-lg-5 text-center">
                    <div class="bg-white rounded-4 p-4 shadow-sm border">
                        <div class="display-1 mb-2">🍔</div>
                        <div class="fw-bold fs-4">Pedido rápido</div>
                        <div class="text-secondary mt-2">Gestão simples e eficiente para sua cozinha.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection