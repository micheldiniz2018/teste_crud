<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cargo;

class CargoGerenciarController extends Controller
{
    public function getCargos()
    {
        $cargos = Cargo::all();

        return $cargos;
    }
    public function criar(Request $request)
    {
        $campos = $request->only(['cargo']);

        if(empty($campos['cargo'])){
            return response()->json([
                'status' => 'error',
                'message' => 'O campo cargo está vazio.',
                'error' => [],
                'response' => []
            ], 422);
        }

        $cargo = new Cargo();
        $cargo->descricao = $campos['cargo'];
        $cargo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cargo Incluido.',
            'error' => [],
            'response' => []
        ], 201);
    }

    public function deletar(Request $request){
        $campos = $request->only(['id']);

        $cargo = Cargo::find($campos['id']);
        $cargo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cargo Excluído.',
            'error' => [],
            'response' => []
        ], 201);
    }

    public function atualizar(Request $request){
        $campos = $request->only(['id','valor']);

        $cargo = Cargo::find($campos['id']);
        $cargo->descricao = $campos['valor'];
        $cargo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cargo Alterado.',
            'error' => [],
            'response' => []
        ], 201);
    }
}
