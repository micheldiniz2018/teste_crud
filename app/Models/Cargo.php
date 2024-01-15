<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cargo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'descricao',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];


    public function users(){
        return $this->hasMany('App\Models\User', 'cargo_id', 'id');
    }
}
