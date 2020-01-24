<?php

namespace gestorWeb\Http\Controllers;

use gestorWeb\Gestiones;
use Illuminate\Http\Request;

class GestorGestionesController extends Controller {

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
                echo "<h1>Error 404, el token no es válido</h1>";
                die;
            case 4:
                break;
        }
    }

    public function index() {
        $gestiones = Gestiones::all();
        $json = json_encode($gestiones);
        return $json;
    }

}
