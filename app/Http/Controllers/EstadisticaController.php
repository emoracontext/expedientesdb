<?php

namespace gestorWeb\Http\Controllers;

use Illuminate\Http\Request;
use Input;

class EstadisticaController extends Controller {
    

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
        //
    }

    function recogerTransferencia() {
               
        $lib = new LibreriaController();
        
        return $lib->seleccionarEstadistica('TRAN', 'INICIAL', 'EN TRAFICO', 'TERMINADO', 'EXPE.FECHA');
    }

    function recogerMatriculacion() {
        
        $lib = new LibreriaController();
        
        return $lib->seleccionarEstadistica('MATR', 'INICIAL', 'EN TRAFICO', 'TERMINADO', 'EXPE.FECHA');
    }

    function recogerDuplicado() {
        
        $lib = new LibreriaController();
        
        return $lib->seleccionarEstadistica('DUPL', 'INICIAL', 'EN TRAFICO', 'TERMINADO', 'EXPE.FECHA');
    }

}
