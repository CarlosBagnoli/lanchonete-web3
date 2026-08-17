<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SobreController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ItemPedidoController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [SobreController::class, 'index'])->name('sobre');
Route::get('/relatorios/categorias.csv', [CategoriaController::class, 'exportCsv'])->name('relatorios.categorias.csv');
Route::get('/contato', [ContatoController::class, 'create'])->name('contato.create');
// Rotas de produtos registradas abaixo dentro do grupo `auth` (evita exposição pública)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('guest');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::resource('produtos', ProdutoController::class);
    Route::resource('pedidos', PedidoController::class);

    // rotas para itens de um pedido
    Route::post('pedidos/{pedido}/itens', [ItemPedidoController::class, 'store'])->name('pedidos.itens.store');
    Route::put('pedidos/{pedido}/itens/{itemPedido}', [ItemPedidoController::class, 'update'])->name('pedidos.itens.update');
    Route::delete('pedidos/{pedido}/itens/{itemPedido}', [ItemPedidoController::class, 'destroy'])->name('pedidos.itens.destroy');

    Route::get('/minha-conta', [AuthController::class, 'showAccount'])->name('minha-conta');
    Route::post('/minha-conta', [AuthController::class, 'updateAccount'])->name('minha-conta.update');
});
// Rotas administrativas para gerenciamento de usuários (apenas admin)
Route::middleware(['auth','role:admin'])->prefix('admin')->group(function () {
    Route::get('usuarios', [\App\Http\Controllers\UserController::class, 'index'])->name('admin.usuarios.index');
    Route::post('usuarios/{user}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('admin.usuarios.updateRole');
});
Route::middleware(['auth'])->group(function () {
    Route::resource('categorias', CategoriaController::class)
        ->middleware('role:admin');
});