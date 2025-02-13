<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $fillable = [
        'nome',
        'imagem'
    ];


    public function regras()
    {
        return [
            'nome' => 'required|unique:marcas,nome,' . $this->id . '|min:3',
            'imagem' => 'required|file|mimes:png'
        ];
    }

    public function feedback(){
        return [
            'required' => 'O campo ::attribute é obrigatório',
            'nome.unique' => 'Marca já cadastrada',
            'imagem.file' => 'Imagem deve ser uma imagem',
            'imagem.mimes' => 'Imagem deve ser png'
        ];
    }

    public function modelos(){
        return $this->hasMany(Modelo::class);
    }
}
