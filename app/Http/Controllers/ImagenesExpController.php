<?php

namespace gestorWeb\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImagenesExpController extends Controller {

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

        $query = "
		SELECT
			idImagen,
			nombreArchivo,
			descripcionArchivo,
			rutaImagen,
			idExpediente,
			fecha
		FROM
			Gestor_ImagenesExpediente
		WHERE IdExpediente =$idExpediente
        ";

        $result = DB::select($query);

        $json = json_encode($result);

        return $json;
    }

}
