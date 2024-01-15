<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CargoGerenciarController;
use App\Http\Controllers\CentroCustoController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/cargo')->group(function (){
    Route::get('/listar', [CargoGerenciarController::class, 'getCargos'])->name('cargos.listar');
    Route::post('/criar', [CargoGerenciarController::class, 'criar'])->name('cargos.criar');
    Route::post('/deletar', [CargoGerenciarController::class, 'deletar'])->name('cargos.deletar');
    Route::post('/atualizar', [CargoGerenciarController::class, 'atualizar'])->name('cargos.atualizar');
});

Route::prefix('/centro_custo')->group(function (){
    Route::get('/listar', [CentroCustoController::class, 'getCentroCusto'])->name('centro_custo.listar');
    Route::post('/criar', [CentroCustoController::class, 'criar'])->name('centro_custo.criar');
    Route::post('/deletar', [CentroCustoController::class, 'deletar'])->name('centro_custo.deletar');
    Route::post('/atualizar', [CentroCustoController::class, 'atualizar'])->name('centro_custo.atualizar');
});

Route::prefix('/departamento')->group(function (){
    Route::get('/listar', [DepartamentoController::class, 'getDepartamento'])->name('departamento.listar');
    Route::post('/criar', [DepartamentoController::class, 'criar'])->name('departamento.criar');
    Route::post('/deletar', [DepartamentoController::class, 'deletar'])->name('departamento.deletar');
    Route::post('/atualizar', [DepartamentoController::class, 'atualizar'])->name('departamento.atualizar');
    Route::post('/filtro', [DepartamentoController::class, 'filtroDepartamento'])->name('departamento.filtro');
});


Route::prefix('/user')->group(function (){
    Route::post('/listar', [UserController::class, 'getUser'])->name('user.listar');
    Route::post('/criar', [UserController::class, 'criar'])->name('user.criar');
    Route::post('/deletar', [UserController::class, 'deletar'])->name('user.deletar');
    Route::post('/atualizarModal', [UserController::class, 'atualizarModal'])->name('user.atualizarModal');
    Route::post('/atualizar', [UserController::class, 'atualizar'])->name('user.atualizar');
    Route::post('/importar', [UserController::class, 'importarArquivo'])->name('user.importar');
});
