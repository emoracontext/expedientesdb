<?php

namespace gestorWeb\Http\Controllers;

use gestorWeb\ExpedientesWeb;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpedientesWebDPController extends Controller
{
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
    
    public function index($idExpediente)
    {
        
        $query = "
         SELECT  
             REP.idRelExpPer,    
             REP.idExpediente,   
             REP.idPersona, 
             REP.nombre, 
             REP.apellido1_razon, 
             REP.apellido2, 
             REP.DNICIF, 
             REP.telefono3,
             REP.idTipoPersona
          FROM
             Gestor_relExpPer As REP 
          WHERE
            idExpediente = $idExpediente 
        ";

        $result = DB::select($query);
                
        $json = json_encode($result);

        return $json;

    }

    public function show( $expedientesWeb)
    {

        $query = "
         SELECT  
             REP.idRelExpPer,    
             REP.idExpediente,   
             REP.idPersona, 
             REP.nombre, 
             REP.apellido1_razon, 
             REP.apellido2, 
             REP.DNICIF, 
             REP.telefono3,
             REP.idTipoPersona
          FROM
             Gestor_relExpPer As REP 
          WHERE
            idExpediente = $expedientesWeb 
        ";

        $result = DB::select($query);
        
        $json = json_encode($result);

        return $json;
    }
 
}
