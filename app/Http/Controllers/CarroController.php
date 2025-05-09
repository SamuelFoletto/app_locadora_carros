<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;


class CarroController extends Controller
{
    public  function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }

    public function index( Request $request )
    {
        $carroRepository = new CarroRepository($this->carro);

        if($request->has('atributos_modelo')){
            $atributos_modelo = 'modelo:id,'.$request->atributos_modelo;
            $carroRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        } else{
            $carroRepository->selectAtributosRegistrosRelacionados('modelo');
        }

        if ($request->has('filtro')) {
            $carroRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $carroRepository->selectAtributos($request->atributos);

        }

        return response()->json($carroRepository->getResultado());
    }


    public function store(Request $request)
    {
        $request->validate($this->carro->regras());


        $carro = $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);

        return response()->json($carro, 201);
    }


    public function show($id)
    {
        $carro = $this->carro->with('modelo')->find($id);

        if ($carro === null){
            return response()->json(['erro' => 'Carro pesquisado não existe'], 404);
        }

        return ($carro);
    }


    public function update(Request $request, $id)
    {
        $carro = $this->carro->find($id);


        if ($carro === null){
            return response()->json(['erro' => 'Alteração não realizada. Marca não existe'], 404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();

            foreach ($carro->regras() as $input => $regra){

                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        } else {

            $request->validate($carro->regras());

        }


        $carro->fill($request->all());
        $carro->save();

        return response()->json($carro, 200);
    }

    public function destroy($id)
    {
        $carro = $this->carro->find($id);

        if ($carro === null){
            return response()->json(['erro' => 'Alteração não realizada. Marca não existe'], 404);
        }


        $carro->delete();
        return ['msg' => 'Carro deletado com sucesso!'];
    }
}
