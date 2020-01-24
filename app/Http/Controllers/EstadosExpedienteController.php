<?php

namespace gestorWeb\Http\Controllers;

use Illuminate\Http\Request;
use gestorWeb\EstadosExpediente;
use App;

class EstadosExpedienteController extends Controller {

    public function __construct() {

        $lib = new LibreriaController();
        $acceder = $lib->compruebaAcceso();

        switch ($acceder) {
            case 1:
                $this->middleware('auth');
                break;
            case 2:
                $this->middleware('auth');
                break;
            case 3:
                return response()->json(['message' => 'Not Found!'], 404);
                echo "<h1>Error 404, el token no es válido</h1>";
                die;
            case 4:
                break;
        }
    }

    public function index() {

        $estados = EstadosExpediente::all();  // metemos los registros en una variable
        //  return view('projects.index', ['projects' => $projects ]);  // pasamos la variable a la vista
        $json = json_encode($estados);
        return $json;
    }


}
