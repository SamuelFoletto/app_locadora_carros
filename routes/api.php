<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware('jwt.auth')->group(function (){
    Route::apiResource('cliente', App\Http\Controllers\ClienteController::class);
    Route::apiResource('carro', App\Http\Controllers\CarroController::class);
    Route::apiResource('locacao', App\Http\Controllers\LocacaoController::class);
    Route::apiResource('marca', App\Http\Controllers\MarcaController::class);
    Route::apiResource('modelo', App\Http\Controllers\ModeloController::class);
    Route::post('me', [App\Http\Controllers\AuthController::class, 'me']);
    Route::post('refresh', [App\Http\Controllers\AuthController::class, 'refresh']);
    Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);
});

Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);


