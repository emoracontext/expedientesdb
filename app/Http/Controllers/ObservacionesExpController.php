<?php

namespace gestorWeb\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObservacionesExpController extends Controller {

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

    public function show($idExpediente) {

        // $idExpediente = 105914 ;

        $query = "
        SELECT OBS.idObservacionesExpediente,
        	OBS.idExpediente,
        	OBS.idUsuario,
        	OBS.fechaHora,
        	OBS.observacion,
        	OBS.web,
        	OBS.publicado,
        	USU.usuario
        FROM Gestor_observacionesExpediente As OBS LEFT JOIN Gestor_usuarios As USU ON (OBS.idUsuario = USU.idUsuario)
        WHERE idExpediente = $idExpediente
        ";

        $result = DB::select($query);

        $json = json_encode($result);

        return $json;
    }

}
