<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\MarcaRepository;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }


    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);

        if($request->has('atributos_marca')){
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        } else{
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if ($request->has('filtro')) {
            $modeloRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $modeloRepository->selectAtributos($request->atributos);

        }

        return response()->json($modeloRepository->getResultado());

    }


    public function store(Request $request)
    {
        $request->validate($this->modelo->regras());


        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');


        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' =>  $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs

        ]);

        return response()->json($modelo, 201);
    }


    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id);

        if ($modelo === null){
            return response()->json(['erro' => 'Modelo não existe'], 404);
        }

        return ($modelo);
    }


    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);


        if ($modelo === null){
            return response()->json(['erro' => 'Alteração não realizada. Modelo não existe'], 404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();

            foreach ($modelo->regras() as $input => $regra){

                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        } else {

            $request->validate($modelo->regras());

        }

        if($request->file('imagem')){

            Storage::disk('public')->delete($modelo->imagem);
        }


        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');



        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;
        $modelo->save();


        return response()->json($modelo, 200);
    }


    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null){
            return response()->json(['erro' => 'Alteração não realizada. Modelo não existe'], 404);
        }

        Storage::disk('public')->delete($modelo->imagem);


        $modelo->delete();
        return ['msg' => 'Marca deletada com sucesso!'];
    }
}
