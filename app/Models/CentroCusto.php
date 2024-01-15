<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CentroCusto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'descricao',
        'departamento_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];

    public function users(){
        return $this->hasMany('App\Models\User', 'centro_custo_id', 'id');
    }

    public function departamentos(){
        return $this->hasMany('App\Models\Departamento', 'id', 'departamento_id');
    }
}
