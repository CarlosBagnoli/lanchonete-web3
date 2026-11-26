<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SobreController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AuthController as WebAuthController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ItemPedidoController;
use App\Http\Controllers\CepController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\ProdutoApiController;
use App\Http\Controllers\Api\PedidoApiController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [SobreController::class, 'index'])->name('sobre');
Route::get('/relatorios/categorias.csv', [CategoriaController::class, 'exportCsv'])->name('relatorios.categorias.csv');
Route::get('/contato', [ContatoController::class, 'create'])->name('contato.create');
// Rotas de produtos registradas abaixo dentro do grupo `auth` (evita exposição pública)
Route::get('/register', [WebAuthController::class, 'showRegisterForm'])->name('register.form')->middleware('guest');
Route::post('/register', [WebAuthController::class, 'register'])->name('register')->middleware('guest');

Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login.form')->middleware('guest');
Route::post('/login', [WebAuthController::class, 'login'])->name('login')->middleware('guest');

Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {

Route::post('pedidos/{pedido}/itens-json', [ItemPedidoController::class, 'storeJson'])
    ->name('pedidos.itens.storeJson');

Route::delete('pedidos/{pedido}/itens-json/{itemPedido}', [ItemPedidoController::class, 'destroyJson'])
    ->name('pedidos.itens.destroyJson');
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::resource('produtos', ProdutoController::class);
    Route::resource('pedidos', PedidoController::class);
    Route::get('pedidos/relatorio/dia', [PedidoController::class, 'relatorioDia'])->name('pedidos.relatorio.dia');

    // rotas para itens de um pedido
    Route::post('pedidos/{pedido}/itens', [ItemPedidoController::class, 'store'])->name('pedidos.itens.store');
    Route::put('pedidos/{pedido}/itens/{itemPedido}', [ItemPedidoController::class, 'update'])->name('pedidos.itens.update');
    Route::patch('pedidos/{pedido}/itens/{itemPedido}/quantidade', [ItemPedidoController::class, 'updateQuantity'])->name('pedidos.itens.quantidade.update');
    Route::delete('pedidos/{pedido}/itens/{itemPedido}', [ItemPedidoController::class, 'destroy'])->name('pedidos.itens.destroy');
    Route::post('pedidos/{pedido}/status', [PedidoController::class, 'updateStatus'])->name('pedidos.status.update');
    Route::post('pedidos/{pedido}/endereco', [PedidoController::class, 'updateAddress'])->name('pedidos.endereco.update');

    Route::get('/minha-conta', [WebAuthController::class, 'showAccount'])->name('minha-conta');
    Route::post('/minha-conta', [WebAuthController::class, 'updateAccount'])->name('minha-conta.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/cep/{cep}', [CepController::class, 'show'])->name('cep.show');
});
// Rotas administrativas para gerenciamento de usuários (apenas admin)
Route::middleware(['auth','role:admin'])->prefix('admin')->group(function () {
    Route::get('usuarios', [\App\Http\Controllers\UserController::class, 'index'])->name('admin.usuarios.index');
    Route::post('usuarios/{user}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('admin.usuarios.updateRole');
});

Route::middleware(['auth','role:admin,gerente'])->prefix('admin/gerente')->group(function () {
    Route::post('cep/cache/clear', [CepController::class, 'clearCache'])->name('admin.gerente.cep.cache.clear');
});
Route::middleware(['auth'])->group(function () {
    Route::resource('categorias', CategoriaController::class)
        ->middleware('role:admin');
});


Route::prefix('api')->group(function () {
    Route::post('/login', [ApiAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::apiResource('pedidos', PedidoApiController::class)->only(['index', 'show']);

        Route::get('produtos', [ProdutoApiController::class, 'index']);
        Route::get('produtos/{produto}', [ProdutoApiController::class, 'show']);

        Route::middleware('role:gerente')->group(function () {
            Route::post('produtos', [ProdutoApiController::class, 'store']);
            Route::put('produtos/{produto}', [ProdutoApiController::class, 'update']);
            Route::patch('produtos/{produto}', [ProdutoApiController::class, 'update']);
            Route::delete('produtos/{produto}', [ProdutoApiController::class, 'destroy']);
        });
    });
});