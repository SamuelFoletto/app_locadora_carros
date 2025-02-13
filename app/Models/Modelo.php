<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $fillable = [
        'marca_id',
        'nome',
        'imagem',
        'numero_portas',
        'lugares',
        'air_bag',
        'abs'
    ];


    public function regras()
    {
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:modelos,nome,' . $this->id . '|min:3',
            'imagem' => 'required|file|mimes:png, jpeg, jpg',
            'numero_portas' => 'integer|digits_between:1,5|required',
            'lugares' => 'integer|digits_between:1,20|required',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean'
        ];
    }

    public function marca(){
        return $this->belongsTo(Marca::class);
    }
}
