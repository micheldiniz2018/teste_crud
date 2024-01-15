<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserCriarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'nome' => [
                'required',
                'max:50'
            ],
            'sobre_nome' => [
                'required',
                'max:100'
            ],
            'idade' => [
                'required',
            ],
            'cpf' => [
                'required',
                function($attribute, $value, $fail) {
                    $value = preg_replace( '/[^0-9]/is', '', $value );

                    if (strlen($value) != 11) {
                        return $fail('CPF inválido');
                    }

                    if (preg_match('/(\d)\1{10}/', $value)) {
                        return $fail('CPF inválido');
                    }

                    for ($t = 9; $t < 11; $t++) {
                        for ($d = 0, $c = 0; $c < $t; $c++) {
                            $d += $value[$c] * (($t + 1) - $c);
                        }
                        $d = ((10 * $d) % 11) % 10;
                        if ($value[$c] != $d) {
                            return $fail('CPF inválido');
                        }
                    }
                }
            ],
            'email' => [
                'required',
                'email'
            ],
            'departamento_id' => [
                'required',
            ],
        ];
    }

    public function messages(){
        return [
            'nome.required' => __('validation.required', ['attribute' => 'Nome']),
            'nome.max' => __('validation.max', ['attribute' => 'Nome']),
            'sobre_nome.required' => __('validation.required', ['attribute' => 'Sobrenome']),
            'sobre_nome.max' => __('validation.max', ['attribute' => 'Sobrenome']),
            'idade.required' => __('validation.date_format', ['attribute' => 'Idade']),
            'cpf.required' => __('validation.required', ['attribute' => 'CPF']),
            'email.required'  => __('validation.required', ['attribute' => 'E-mail']),
            'departamento_id.required'  => __('validation.required', ['attribute' => 'Departamento']),
        ];
    }

    protected function failedValidation(Validator $validator) {
        $errors = (new ValidationException($validator))->errors();
        foreach ($errors as $key => $value) {
            $errors[$key] = implode("<br>", $value);
        }
        $error = [
            'status' => 'error',
            'message' => '',
            'error' => $errors,
            'response' => []
        ];
        throw new HttpResponseException(response()->json($error, 422));
    }
}
