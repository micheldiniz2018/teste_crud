<?php

namespace App\Http\Controllers;

use App\Models\CentroCusto;
use Illuminate\Http\Request;

class CentroCustoController extends Controller
{

    public function getCentroCusto()
    {
        $cargos = CentroCusto::all();

        return $cargos;
    }
    public function criar(Request $request)
    {
        $campos = $request->only(['centro_custo']);

        if(empty($campos['centro_custo'])){
            return response()->json([
                'status' => 'error',
                'message' => 'O campo centro de custo está vazio.',
                'error' => [],
                'response' => []
            ], 422);
        }

        $cargo = new CentroCusto();
        $cargo->descricao = $campos['centro_custo'];
        $cargo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Centro de Custo Incluido.',
            'error' => [],
            'response' => []
        ], 201);
    }

    public function deletar(Request $request){
        $campos = $request->only(['id']);

        $cargo = CentroCusto::find($campos['id']);
        $cargo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Centro de Custo Excluído.',
            'error' => [],
            'response' => []
        ], 201);
    }

    public function atualizar(Request $request){
        $campos = $request->only(['id','valor']);

        $cargo = CentroCusto::find($campos['id']);
        $cargo->descricao = $campos['valor'];
        $cargo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Centro de Custo Alterado.',
            'error' => [],
            'response' => []
        ], 201);
    }
}
