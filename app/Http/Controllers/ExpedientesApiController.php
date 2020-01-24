<?php

namespace gestorWeb\Http\Controllers;


use gestorWeb\expedientes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Input;
use gestorWeb\Http\Controllers\LibreriaController;

use gestorWeb\CustomClass\apiauth;





class ExpedientesApiController extends Controller
{
   
   public function __construct() {

       /* $lib = new LibreriaController();
        $acceder = $lib->compruebaAcceso();
        * 
        */
       
       $apiauth = new \gestorWeb\CustomClass\apiauth();
       $ip = $apiauth->get_client_ip();
       if ( $apiauth->ip_autorizada($ip) ) {
           info('Acceso API desde '.  $ip);
           return; // autorizado, devolvemos el control
       } else {
          // abort(403, 'No autorizado');
          ;
       }
    }
    
   public function index(Request $request) {
       
       // estos valores han de ser pasados en el request por el usuario
       // ?inicio={valor}&final={valor}&colaborador={valor}&cuenta={valor}
       // todos son opcionales
        $inicio = Input::get("inicio");
        $final = Input::get("final");
        $idColab = Input::get("colaborador");       
        $cuenta = Input::get("cuenta");
        
        // tomamos la fecha actual y calculamos la fecha de ahora menos 1 mes
        $dia = date('Y-m-d');
        $limite = date("d-m-Y", strtotime($dia . "- 1 month"));
        
        echo "inicio $inicio , final $final , colaborador $idColab , cuenta $cuenta \n<br>";
        echo "dia $dia limite $limite \n";
        
        
   }

 
}
