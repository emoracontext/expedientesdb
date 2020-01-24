<?php

namespace gestorWeb\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;

class ExpedientesFichaController extends Controller {

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function controlar_contra() {
        $pass = auth()->user()->password_current;
        if ($pass == 0) {
            return view('cambiar_contra');
        }
    }

    public function __invoke(Request $request) {
        
    }

    function visualizar($opcion, $crypt) {

        $password = "password";
        $crypt = str_replace("*", "/", $crypt);
        $decrypted_string = openssl_decrypt($crypt, "AES-128-ECB", $password);

        $partes = explode("/", $decrypted_string);
        $idExpediente = $partes[0];
        $folder = $partes[1];
        $fichero = $partes[2];
        $idCuenta = session('cuenta');
        $idColab = session('idColaborador');
        $col = session('colaborador');

        $select = "SELECT top 1000 EXPE.idExpediente,
            EXPE.expediente,
            EXPE.idColaborador,
            EXPE.idGestion,
            EXPE.fecha,
            EXPE.observaciones,
            EXPE.idVehiculo,
            EXPE.idEstado,
            ESEX.descripcion as estado,
            EXPE.expediente_matricula,
            EXPE.expediente_fechaMatriculacion,
            EXPE.fechaCambioEstado, 
            ESEX.finalizado, 
            GES.descripcionCorta As descripcionGestion, 
            VEH.matricula,
            VEH.bastidor,
            EXPTRA.fechaPresentacion ";

        if (auth()->user()->tipo == 1) {
            $from = " FROM Gestor_Personas AS PER INNER JOIN 
Gestor_RelExpPer AS PEREXP ON PER.idPersona = PEREXP.idPersona right JOIN 
Gestor_Expedientes AS EXPE ON PEREXP.IdExpediente = EXPE.IdExpediente INNER JOIN 
Gestor_Gestiones AS GES ON EXPE.idGestion = GES.idGestion left JOIN 
Gestor_Vehiculos AS VEH ON EXPE.idVehiculo = VEH.idVehiculo left JOIN
Gestor_EstadosExpediente AS ESEX ON EXPE.idEstado = ESEX.idEstadoExpediente left JOIN
Gestor_INFTrafico as EXPTRA ON EXPE.IdExpediente = EXPTRA.idExpediente left JOIN 
Rivera_usuarios as riv on per.idPersona = riv.idColaborador ";

            $colaborador = " where EXPE.idExpediente = $idExpediente";
        } else {

            if ($col == 1) {

                $from = ' FROM 
      ((((Gestor_expedientes As EXPE LEFT JOIN Gestor_vehiculos As VEH ON (EXPE.idVehiculo = VEH.idVehiculo)) 
      LEFT JOIN Gestor_estadosExpediente As ESEX  ON (EXPE.idEstado = ESEX.idEstadoExpediente)) 
	  LEFT JOIN Gestor_Personas as per on (expe.idColaborador = per.idPersona)
	  left join Gestor_INFTrafico as EXPTRA ON (EXPE.IdExpediente = EXPTRA.idExpediente)
      LEFT JOIN Gestor_gestiones As GES ON (EXPE.idGestion = GES.idGestion))
      LEFT JOIN Gestor_INFTrafico As EINFTRA ON (EXPE.idExpediente = EINFTRA.idExpediente)) ';

                $colaborador = " WHERE  (expe.idColaborador = $idColab
		 or EXPE.idExpediente IN (SELECT idExpediente FROM Gestor_RelExpPer WHERE Telefono3 = '$idCuenta')) and EXPE.idExpediente = $idExpediente";
            } else {

                $from = ' FROM
            Gestor_Personas AS PER INNER JOIN 
            Gestor_RelExpPer AS PEREXP ON PER.idPersona = PEREXP.idPersona INNER JOIN            
            Gestor_Expedientes AS EXPE ON PEREXP.IdExpediente = EXPE.IdExpediente INNER JOIN           
            Gestor_Gestiones AS GES ON EXPE.idGestion = GES.idGestion INNER JOIN
            Gestor_Vehiculos AS VEH ON EXPE.idVehiculo = VEH.idVehiculo INNER JOIN 
            Gestor_EstadosExpediente AS ESEX ON EXPE.idEstado = ESEX.idEstadoExpediente INNER JOIN
            Gestor_INFTrafico as EXPTRA ON EXPE.IdExpediente = EXPTRA.idExpediente left JOIN
            Rivera_usuarios as riv on per.idPersona = riv.idColaborador ';

                $colaborador = "where (PER.idPersona = $idColab OR expe.idColaborador = $idColab or PER.Telefono3 = '$idCuenta') and EXPE.idExpediente = $idExpediente";
            }
        }

        $query = "$select $from $colaborador";

        $result = DB::select($query);

        $filas = 0;
        foreach ($result as $item) {
            $filas++;
        }

        if ($filas != null || $filas != 0) {
            $rutaDef = storage_path("ImagenesExpediente/$folder/$fichero");

            if ($opcion == 0) {
                return response()->file($rutaDef);
            } else if ($opcion == 1) {

                $user = auth()->user()->id; //idUser

                $idUsuario = session('idUsuario');
                $ip = session('ip');
                $descripcion = "El usuario $user ha descargado $fichero ";



                $query = "insert into Gestor_log (fechaHora, idUsuario, descripcion, "
                        . "id_users, ip, id_expediente) values(GETDATE (),'$idUsuario','$descripcion',$user, '$ip', $idExpediente )  ";

                DB::insert($query);

                return Response::download($rutaDef, $partes[2]);
            }
        } ELSE {
            echo "NO TIENES PERMISOS PARA VER EL FICHERO";
        }
    }

    function cargaObservaciones($idExpediente) {
//        $query = "
//            SELECT OBS.idObservacionesExpediente,
//                OBS.idExpediente,
//                OBS.idUsuario,
//                OBS.fechaHora,
//                OBS.observacion,
//                OBS.web,
//                OBS.publicado,
//                USU.usuario
//            FROM Gestor_observacionesExpediente As OBS LEFT JOIN Gestor_usuarios As USU ON (OBS.idUsuario = USU.idUsuario)
//            WHERE idExpediente = $idExpediente
//            ";

        $query = "SELECT * FROM Gestor_ObservacionesWEB where idExpediente = $idExpediente";
        $result = DB::select($query);

        return $result;
    }

    function cargaExpediente($idExpediente) {

        $query = "
            SELECT EXPE.idExpediente,
                EXPE.expediente,
                EXPE.idColaborador,
                EXPE.idGestion,
                EXPE.fecha,
                EXPE.observaciones,
                EXPE.idVehiculo,
                EXPE.idEstado,
                EXPE.expediente_matricula,
                EXPE.expediente_fechaMatriculacion,
                EXPE.fechaCambioEstado, 
                ESEX.descripcion As descripcionEstado,
                ESEX.finalizado,GES.descripcionCorta As descripcionGestion, 
                VEH.matricula,
                VEH.bastidor,
                EXPTRA.fechaPresentacion 
            FROM
                Gestor_Expedientes AS EXPE left JOIN
                Gestor_Gestiones AS GES ON EXPE.idGestion = GES.idGestion left JOIN
                Gestor_Vehiculos AS VEH ON EXPE.idVehiculo = VEH.idVehiculo right JOIN 
                Gestor_EstadosExpediente AS ESEX ON EXPE.idEstado = ESEX.idEstadoExpediente left JOIN
                Gestor_INFTrafico as EXPTRA ON EXPE.IdExpediente = EXPTRA.idExpediente 
            WHERE EXPE.idExpediente = $idExpediente
            ";

        $expediente = DB::select($query);

        return $expediente;
    }

    function cargaPersonas($idExpediente) {

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

        $personas = DB::select($query);

        return $personas;
    }

    function cargaImagenes($idExpediente) {
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

        $imagenes = DB::select($query);

        return $imagenes;
    }

    public function show($idExpediente) {

        if (auth()->user()->password_current == 0) {
            return $this->controlar_contra();
        }

        $idCuenta = session('cuenta');
        $idColab = session('idColaborador');
        $col = session('colaborador');

        $select = "SELECT top 1000 EXPE.idExpediente,
            EXPE.expediente,
            EXPE.idColaborador,
            EXPE.idGestion,
            EXPE.fecha,
            EXPE.observaciones,
            EXPE.idVehiculo,
            EXPE.idEstado,
            ESEX.descripcion as estado,
            EXPE.expediente_matricula,
            EXPE.expediente_fechaMatriculacion,
            EXPE.fechaCambioEstado, 
            ESEX.finalizado, 
            GES.descripcionCorta As descripcionGestion, 
            VEH.matricula,
            VEH.bastidor,
            EXPTRA.fechaPresentacion ";

        if (auth()->user()->tipo == 1) {
            $from = " FROM Gestor_Personas AS PER INNER JOIN 
Gestor_RelExpPer AS PEREXP ON PER.idPersona = PEREXP.idPersona right JOIN 
Gestor_Expedientes AS EXPE ON PEREXP.IdExpediente = EXPE.IdExpediente INNER JOIN 
Gestor_Gestiones AS GES ON EXPE.idGestion = GES.idGestion left JOIN 
Gestor_Vehiculos AS VEH ON EXPE.idVehiculo = VEH.idVehiculo left JOIN
Gestor_EstadosExpediente AS ESEX ON EXPE.idEstado = ESEX.idEstadoExpediente left JOIN
Gestor_INFTrafico as EXPTRA ON EXPE.IdExpediente = EXPTRA.idExpediente left JOIN 
Rivera_usuarios as riv on per.idPersona = riv.idColaborador  ";

            $colaborador = " where EXPE.idExpediente = $idExpediente";
        } else {

            if ($col == 1) {

                $from = ' FROM 
      ((((Gestor_expedientes As EXPE LEFT JOIN Gestor_vehiculos As VEH ON (EXPE.idVehiculo = VEH.idVehiculo)) 
      LEFT JOIN Gestor_estadosExpediente As ESEX  ON (EXPE.idEstado = ESEX.idEstadoExpediente)) 
	  LEFT JOIN Gestor_Personas as per on (expe.idColaborador = per.idPersona)
	  left join Gestor_INFTrafico as EXPTRA ON (EXPE.IdExpediente = EXPTRA.idExpediente)
      LEFT JOIN Gestor_gestiones As GES ON (EXPE.idGestion = GES.idGestion))
      LEFT JOIN Gestor_INFTrafico As EINFTRA ON (EXPE.idExpediente = EINFTRA.idExpediente)) ';

                $colaborador = " WHERE  (expe.idColaborador = $idColab
		 or EXPE.idExpediente IN (SELECT idExpediente FROM Gestor_RelExpPer WHERE Telefono3 = '$idCuenta')) and EXPE.idExpediente = $idExpediente";
            } else {

                $from = ' FROM
            Gestor_Personas AS PER INNER JOIN 
            Gestor_RelExpPer AS PEREXP ON PER.idPersona = PEREXP.idPersona INNER JOIN            
            Gestor_Expedientes AS EXPE ON PEREXP.IdExpediente = EXPE.IdExpediente INNER JOIN           
            Gestor_Gestiones AS GES ON EXPE.idGestion = GES.idGestion INNER JOIN
            Gestor_Vehiculos AS VEH ON EXPE.idVehiculo = VEH.idVehiculo INNER JOIN 
            Gestor_EstadosExpediente AS ESEX ON EXPE.idEstado = ESEX.idEstadoExpediente INNER JOIN
            Gestor_INFTrafico as EXPTRA ON EXPE.IdExpediente = EXPTRA.idExpediente left JOIN
            Rivera_usuarios as riv on per.idPersona = riv.idColaborador ';

                $colaborador = "where (PER.idPersona = $idColab OR expe.idColaborador = $idColab or PER.Telefono3 = '$idCuenta') and EXPE.idExpediente = $idExpediente";
            }
        }

        $query = "$select $from $colaborador";

        $result = DB::select($query);

        $filas = 0;
        foreach ($result as $item) {
            $filas++;
        }

        if ($filas != null || $filas != 0) {

            $expediente = $this->cargaExpediente($idExpediente);
            $observaciones = $this->cargaObservaciones($idExpediente);
            $personas = $this->cargaPersonas($idExpediente);
            $imagenes = $this->cargaImagenes($idExpediente);

            return view('expedientes.ficha', ['expediente' => $expediente,
                'observaciones' => $observaciones,
                'personas' => $personas,
                'imagenes' => $imagenes]);
        } else {
            return 'La ficha de dicho expediente no se puede mostrar porque no contiene documentos o no tiene permisos para verlo';
        }
    }

}
