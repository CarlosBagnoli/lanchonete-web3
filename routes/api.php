<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdutoApiController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('produtos', ProdutoApiController::class);
});
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('produtos', ProdutoApiController::class);
});