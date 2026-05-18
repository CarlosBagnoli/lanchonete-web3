<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SobreController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\ProdutoController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [SobreController::class, 'index'])->name('sobre');
Route::get('/relatorios/categorias.csv', [CategoriaController::class, 'exportCsv'])->name('relatorios.categorias.csv');
Route::resource('categorias', CategoriaController::class);
Route::get('/contato', [ContatoController::class, 'create'])->name('contato.create');
Route::resource('pedidos', \App\Http\Controllers\PedidoController::class);
Route ::resource('produtos', ProdutoController::class);