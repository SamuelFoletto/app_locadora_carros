<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Http\Request;
use App\Repositories\ClienteRepository;

class ClienteController extends Controller
{
    public  function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    public function index(Request $request)
    {
        $clienteRepository = new ClienteRepository($this->cliente);

        if ($request->has('filtro')) {
            $clienteRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $clienteRepository->selectAtributos($request->atributos);

        }

        return response()->json($clienteRepository->getResultado());
    }


    public function store(Request $request)
    {
        $request->validate($this->cliente->regras());

        $cliente = $this->cliente->create([
            'nome' => $request->nome
        ]);

        return response()->json($cliente, 201);
    }

    public function show($id)
    {
        $cliente = $this->cliente->find($id);

        if ($cliente === null){
            return response()->json(['erro' => 'Carro pesquisado não existe'], 404);
        }

        return response()->json($cliente);
    }


    public function update(Request $request, $id)
    {
        $cliente = $this->cliente->find($id);


        if ($cliente === null){
            return response()->json(['erro' => 'Alteração não realizada. Marca não existe'], 404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();

            foreach ($cliente->regras() as $input => $regra){

                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        } else {

            $request->validate($cliente->regras());

        }


        $cliente->fill($request->all());
        $cliente->save();

        return response()->json($cliente, 200);
    }


    public function destroy($id)
    {
        $cliente = $this->cliente->find($id);

        if ($cliente === null){
            return response()->json(['erro' => 'Alteração não realizada. Marca não existe'], 404);
        }


        $cliente->delete();
        return ['msg' => 'Cliente deletado com sucesso!'];
    }
}
