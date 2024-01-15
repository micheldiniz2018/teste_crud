<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Departamento;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserCriarRequest;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function criar(UserCriarRequest $request)
    {
        $campos = $request->only(['nome', 'sobre_nome', 'idade', 'cpf', 'email', 'departamento_id', 'cargo_id']);

        $cargo = new User();
        $cargo->name = $campos['nome'];
        $cargo->sobrenome = $campos['sobre_nome'];
        $cargo->idade = $campos['idade'];
        $cargo->cpf = (integer)preg_replace('/[^0-9]/is', '', $campos['cpf']);
        $cargo->email = $campos['email'];
        $cargo->departamento_id = $campos['departamento_id'];
        $cargo->cargo_id = $campos['cargo_id'];
        $cargo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Usuário Incluido.',
            'error' => [],
            'response' => []
        ], 201);
    }

    public function getUser(Request $request)
    {
        $campos = $request->only(['id']);

        $users = User::with(['cargo', 'departamento']);

        if (!empty($campos['id'])) {
            $users->where('departamento_id', $campos['id']);
        }

        $user = $users->get();
        $retorno = collect();

        foreach ($user as $dado) {
            $retorno->push([
                'id' => $dado->id,
                'name' => $dado->name . ' ' . $dado->sobrenome,
                'idade' => $dado->idade,
                'cpf' => $this->mascara($dado->cpf, '###.###.###-##'),
                'email' => $dado->email,
                'departamento' => $dado->departamento->descricao,
                'cargo' => $dado->cargo->descricao,
                'centro_custo' => $dado->departamento->centro_custo->descricao,
                'cargo_id' => $dado->cargo->id,
                'departamento_id' => $dado->departamento->id
            ]);
        }

        return $retorno;
    }

    public function atualizarModal(Request $request)
    {
        $campos = $request->only(['id']);

        $users = User::with(['cargo', 'departamento']);

        $user = $users->find($campos['id']);
        $retorno = collect();

        $retorno->push([
            'id' => $user->id,
            'name' => $user->name,
            'sobrenome' => $user->sobrenome,
            'idade' => $user->idade,
            'cpf' => $this->mascara($user->cpf, '###.###.###-##'),
            'email' => $user->email,
            'departamento' => $user->departamento->descricao,
            'cargo' => $user->cargo->descricao,
            'centro_custo' => $user->departamento->centro_custo->descricao,
            'cargo_id' => $user->cargo->id,
            'departamento_id' => $user->departamento->id
        ]);


        return $retorno;
    }

    public function atualizar(UserCriarRequest $request)
    {
        $campos = $request->only(['id', 'nome', 'sobre_nome', 'idade', 'cpf', 'email', 'departamento_id', 'cargo_id']);

        $cargo = User::findOrFail($campos['id']);
        $cargo->name = $campos['nome'];
        $cargo->sobrenome = $campos['sobre_nome'];
        $cargo->idade = $campos['idade'];
        $cargo->cpf = (integer)preg_replace('/[^0-9]/is', '', $campos['cpf']);
        $cargo->email = $campos['email'];
        $cargo->departamento_id = $campos['departamento_id'];
        $cargo->cargo_id = $campos['cargo_id'];
        $cargo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Dados Atualizado.',
            'error' => [],
            'response' => []
        ], 201);
    }

    public function deletar(Request $request)
    {
        $campos = $request->only(['id']);

        $cargo = User::find($campos['id']);
        $cargo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Usuário Excluído.',
            'error' => [],
            'response' => []
        ], 201);
    }

    private function mascara($val, $mask)
    {
        $mascara = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) $mascara .= $val[$k++];
            } else {
                if (isset($mask[$i])) $mascara .= $mask[$i];
            }
        }
        return $mascara;
    }

    public function importarArquivo(Request $request)
    {
        $arquivo = $request->file('file_csv');

        if(empty($arquivo)){
            return response()->json([
                "status" => 'error',
                "message" => 'Nenhum arquivo selecionado.',
                "error" => [],
                "response" => []
            ], 422);
        }

        $stream = fopen($arquivo->getPathName(), 'r');

        $usuarios = [];
        $extensao = $arquivo->getClientOriginalExtension();
        $pular_primeira_linha = 0;

        if ($extensao === 'csv') {
            while (($data = fgetcsv($stream, 0)) !== false) {
                if (!isset($usuarios[$data[0]])) {
                    if($pular_primeira_linha !== 0){
                        $linha = explode(';',$data[0]);
                        $usuarios[] = $linha;

                    }else{
                        $pular_primeira_linha = 1;
                    }
                }
            }

            foreach ($usuarios as $usuario){
                $search_deparetamento = strtolower(trim($usuario[5]));
                $departamento = Departamento::where(DB::raw("LOWER(descricao)"),'like', $search_deparetamento)->first();
                $search_cargo = str_replace(' ','',strtolower(trim($usuario[6])));
                $cargo = Cargo::where(DB::raw("replace(LOWER(descricao),' ','')"),'like', $search_cargo)->first();

                if(empty($departamento)){
                    return response()->json([
                        "status" => 'error',
                        "message" => 'Departamento do usuário '.$usuario[0].' não encontrado.',
                        "error" => [],
                        "response" => []
                    ], 422);
                }

                if(empty($cargo)){
                    return response()->json([
                        "status" => 'error',
                        "message" => 'Cargo do usuário '.$usuario[0].' não encontrado.',
                        "error" => [],
                        "response" => []
                    ], 422);
                }

                $user = new User();
                $user->name = $usuario[0];
                $user->sobrenome = mb_convert_encoding($usuario[1],'UTF-8');
                $user->idade = $usuario[2];
                $user->cpf = (integer)preg_replace('/[^0-9]/is', '', $usuario[3]);
                $user->email = $usuario[4];
                $user->departamento_id = $departamento->id;
                $user->cargo_id = $cargo->id;
                $user->save();

                return response()->json([
                    "status" => 'success',
                    "message" => 'Usuário Inseridos.',
                    "error" => [],
                    "response" => []
                ], 201);
            }


        } else {
            return response()->json([
                "status" => 'error',
                "message" => 'Formato de Arquivo inválido.',
                "error" => [],
                "response" => []
            ], 422);
        }
        dd($arquivo);

    }
}
