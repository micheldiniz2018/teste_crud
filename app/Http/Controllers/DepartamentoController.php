<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;

class DepartamentoController extends Controller
{
    public function getDepartamento()
    {
        $retorno = collect();
        $departamentos = Departamento::with('centro_custo')->get();

        foreach ($departamentos as $departamento) {
            $retorno->push([
                'descricao' => $departamento->descricao,
                'id' => $departamento->id,
                'centro_custo' => $departamento->centro_custo->descricao,
                'id_centro_custo' => $departamento->centro_custo->id
            ]);
        }

        return $retorno;
    }

    public function criar(Request $request)
    {
        $campos = $request->only(['id_centro_custo', 'departamento']);

        if (empty($campos['departamento'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'O campo departamento está vazio.',
                'error' => [],
                'response' => []
            ], 422);
        }

        if (empty($campos['id_centro_custo'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Selecione um centro de custo.',
                'error' => [],
                'response' => []
            ], 422);
        }


        $cargo = new Departamento();
        $cargo->descricao = $campos['departamento'];
        $cargo->centro_custo_id = $campos['id_centro_custo'];
        $cargo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Departamento Incluido.',
            'error' => [],
            'response' => []
        ], 201);
    }

    public function deletar(Request $request)
    {
        $campos = $request->only(['id']);

        $cargo = Departamento::find($campos['id']);
        $cargo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Departamento Excluído.',
            'error' => [],
            'response' => []
        ], 201);
    }

    public function atualizar(Request $request)
    {
        $campos = $request->only(['id', 'valor','id_centro_custo']);

        $cargo = Departamento::find($campos['id']);
        $cargo->descricao = $campos['valor'];
        $cargo->centro_custo_id = $campos['id_centro_custo'];
        $cargo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Departamento Alterado.',
            'error' => [],
            'response' => []
        ], 201);
    }

    public function filtroDepartamento(Request $request)
    {
        $campo = $request->only(['id_centro_custo']);

        $departamentos = Departamento::where('centro_custo_id',$campo['id_centro_custo'])->get();

        return $departamentos;
    }
}
