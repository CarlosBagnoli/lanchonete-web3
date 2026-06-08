<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SobreController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [SobreController::class, 'index'])->name('sobre');
Route::get('/relatorios/categorias.csv', [CategoriaController::class, 'exportCsv'])->name('relatorios.categorias.csv');
Route::resource('categorias', CategoriaController::class);
Route::get('/contato', [ContatoController::class, 'create'])->name('contato.create');
Route::resource('pedidos', \App\Http\Controllers\PedidoController::class);
Route ::resource('produtos', ProdutoController::class);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('guest');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::resource('produtos', ProdutoController::class);
});
Route::middleware(['auth'])->group(function () {
    Route::resource('categorias', CategoriaController::class)
        ->middleware(function ($request, $next) {
            abort_unless(auth()->user()->role === 'gerente', 403);
            return $next($request);
        });
});