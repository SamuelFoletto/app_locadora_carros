<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    public  function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca);

        if($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else{
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if ($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $marcaRepository->selectAtributos($request->atributos);

        }

        return response()->json($marcaRepository->getResultadoPaginado(3));

    }



    public function store(Request $request)
    {
        $request->validate($this->marca->regras(), $this->marca->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca, 201);
    }


    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);

        if ($marca === null){
            return response()->json(['erro' => 'Marca pesquisada não existe'], 404);
        }

        return ($marca);
    }


    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);


        if ($marca === null){
            return response()->json(['erro' => 'Alteração não realizada. Marca não existe'], 404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();

            foreach ($marca->regras() as $input => $regra){

                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $marca->feedback());
        } else {

            $request->validate($marca->regras(), $marca->feedback());

        }

            if($request->file('imagem')){

                Storage::disk('public')->delete($marca->imagem);
            }


        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');


        $marca->fill($request->all());
        $marca->imagem = $imagem_urn;
        $marca->save();

        return response()->json($marca, 200);
    }


    public function destroy($id)
    {
        $marca = $this->marca->find($id);

        if ($marca === null){
            return response()->json(['erro' => 'Alteração não realizada. Marca não existe'], 404);
        }

        Storage::disk('public')->delete($marca->imagem);


        $marca->delete();
        return ['msg' => 'Marca deletada com sucesso!'];
    }
}
