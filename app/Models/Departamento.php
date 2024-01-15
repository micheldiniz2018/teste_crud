<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'descricao',
        'centro_custo_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];

    public function users(){
        return $this->hasMany('App\Models\User', 'departamento_id', 'id');
    }

    public function centro_custo(){
        return $this->hasOne('App\Models\CentroCusto', 'id', 'centro_custo_id');
    }
}
