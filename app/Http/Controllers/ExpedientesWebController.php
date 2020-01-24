<?php

namespace gestorWeb\Http\Controllers;

use gestorWeb\ExpedientesWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Input;

class ExpedientesWebController extends Controller {

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

    public function index(Request $request) {

        $lib = new LibreriaController();

        if (auth()->user()->password_current == 0) {
            return $lib->controlar_contra();
        }

        $idCuenta = session('cuenta');
        $idColab = session('idColaborador');
        $col = session('colaborador');

        $dia = date('Y-m-d');
        $limite = date("d-m-Y", strtotime($dia . "- 3 month"));

        $select = "SELECT distinct top 1000 EXPE.idExpediente,
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

        $select2 = "SELECT idGestion, descripcionCorta, descripcionLarga
                        FROM Gestor_Gestiones ";


        if (auth()->user()->tipo == 1) {
            $from = " FROM 
         ((((Gestor_expedientes As EXPE LEFT JOIN Gestor_vehiculos As VEH ON 
         (EXPE.idVehiculo = VEH.idVehiculo)
         )   
         LEFT JOIN Gestor_estadosExpediente As ESEX  ON  
         (EXPE.idEstado = ESEX.idEstadoExpediente)
         )    
         LEFT JOIN Gestor_gestiones As GES ON    
         (EXPE.idGestion = GES.idGestion)
		 )
		 LEFT JOIN Gestor_INFTrafico as EXPTRA ON 
		 (EXPE.IdExpediente = EXPTRA.idExpediente)
		 
		 LEFT JOIN Gestor_Personas as per on (expe.idColaborador = per.idPersona)
		 )	 ";

            $colaborador = " where (((EXPE.idEstado = 11) OR (EXPE.idEstado = 12) OR (EXPE.idEstado = 14) OR (EXPE.idEstado = 15))) 
			OR ((EXPE.idEstado = 9) OR (EXPE.idEstado = 10) OR (EXPE.idEstado = 13) OR (EXPE.idEstado = 16)
			OR ((EXPE.idEstado = 17) OR (EXPE.idEstado = 18) OR (EXPE.idEstado = 19)))
        AND PER.Telefono3 = '$idCuenta'
        ORDER BY EXPE.fecha desc";

            $tipos = "";
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
		 or EXPE.idExpediente IN (SELECT idExpediente FROM Gestor_RelExpPer WHERE Telefono3 = '$idCuenta')) and EXPE.fecha >= '$limite' order by fecha desc";

                $tipos = "WHERE 
        descripcionCorta in (SELECT distinct GES.descripcionCorta As descripcionGestion
FROM ((((Gestor_expedientes As EXPE LEFT JOIN Gestor_vehiculos As VEH ON (EXPE.idVehiculo = VEH.idVehiculo)) 
LEFT JOIN Gestor_estadosExpediente As ESEX ON (EXPE.idEstado = ESEX.idEstadoExpediente)) 
LEFT JOIN Gestor_Personas as per on (expe.idColaborador = per.idPersona) 
left join Gestor_INFTrafico as EXPTRA ON (EXPE.IdExpediente = EXPTRA.idExpediente) 
LEFT JOIN Gestor_gestiones As GES ON (EXPE.idGestion = GES.idGestion)) 
LEFT JOIN Gestor_INFTrafico As EINFTRA ON (EXPE.idExpediente = EINFTRA.idExpediente)) 
WHERE (expe.idColaborador = $idColab or EXPE.idExpediente IN (SELECT idExpediente FROM Gestor_RelExpPer WHERE Telefono3 = '$idCuenta')) )";
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

                $colaborador = "where PEREXP.idTipoPersona = 2  and ((EXPE.idEstado = 11) OR (EXPE.idEstado = 12) OR (EXPE.idEstado = 14) OR (EXPE.idEstado = 15) OR (EXPE.idEstado = 9) OR (EXPE.idEstado = 10)
OR (EXPE.idEstado = 13) OR (EXPE.idEstado = 16 OR EXPE.idEstado = 17 OR EXPE.idEstado = 18 OR EXPE.idEstado = 19))
AND (PER.idPersona = $idColab OR expe.idColaborador = $idColab or PER.Telefono3 = '$idCuenta') and EXPE.fecha >= '$limite' order by fechaCambioEstado desc";

                $tipos = "WHERE 
        descripcionCorta in (

SELECT distinct GES.descripcionCorta As descripcionGestion
FROM Gestor_Personas AS PER 
INNER JOIN Gestor_RelExpPer AS PEREXP ON PER.idPersona = PEREXP.idPersona 
INNER JOIN Gestor_Expedientes AS EXPE ON PEREXP.IdExpediente = EXPE.IdExpediente 
INNER JOIN Gestor_Gestiones AS GES ON EXPE.idGestion = GES.idGestion 
INNER JOIN Gestor_Vehiculos AS VEH ON EXPE.idVehiculo = VEH.idVehiculo 
INNER JOIN Gestor_EstadosExpediente AS ESEX ON EXPE.idEstado = ESEX.idEstadoExpediente 
INNER JOIN Gestor_INFTrafico as EXPTRA ON EXPE.IdExpediente = EXPTRA.idExpediente 
left JOIN Rivera_usuarios as riv on per.idPersona = riv.idColaborador 
where PEREXP.idTipoPersona = 2 and ((EXPE.idEstado = 11) OR (EXPE.idEstado = 12) OR (EXPE.idEstado = 14) OR (EXPE.idEstado = 15) OR (EXPE.idEstado = 9) OR (EXPE.idEstado = 10) OR (EXPE.idEstado = 13) OR (EXPE.idEstado = 16 OR EXPE.idEstado = 17 OR EXPE.idEstado = 18 OR EXPE.idEstado = 19)) 
AND (PER.idPersona = $idColab OR expe.idColaborador = $idColab or PER.Telefono3 = '$idCuenta'))";
            }
        }

        $query = "$select  $from  $colaborador";

        $expedientes = DB::select($query);

        $query2 = "$select2 $tipos";

        $tipoGestion = DB::select($query2);

        $query3 = "SELECT idEstadoExpediente, descripcion
            FROM Gestor_EstadosExpediente";

        $tipoEstado = DB::select($query3);


        $data = array(
            'expedientes' => $expedientes,
            'tipoGestion' => $tipoGestion,
            'tipoEstado' => $tipoEstado
        );

        return view('expedientes.lista')->with($data);
    }

    public function busqueda(Request $request) {

        $lib = new LibreriaController();

        if (auth()->user()->password_current == 0) {
            return $lib->controlar_contra();
        }

        $idCuenta = session('cuenta');
        $idColab = session('idColaborador');
        $col = session('colaborador');

        $bastidor = $request->post('bastidor');
        $matricula = $request->post('matricula');
        $tipoGes = $request->post('tipoGestion');
        $expediente = $request->post('expediente');
        $desdeFechaExpediente = $request->post('desdeFechaExpediente');
        $hastaFechaExpediente = $request->post('hastaFechaExpediente');
        $tipoEstado = $request->post('tipoEstado');
        $desdeFechaEstado = $request->post('desdeFechaEstado');
        $hastaFechaEstado = $request->post('hastaFechaEstado');
        $nifcomprador = $request->post('nifcomprador');
        $nombrecomprador = $request->post('nombrecomprador');
        $apellido1comprador = $request->post('apellido1comprador');
        $apellido2comprador = $request->post('apellido2comprador');
        $nifvendedor = $request->post('nifvendedor');
        $nombrevendedor = $request->post('nombrevendedor');
        $apellido1vendedor = $request->post('apellido1vendedor');
        $apellido2vendedor = $request->post('apellido2vendedor');


        $bastidor_query = "";
        $matricula_query = "";
        $tipoGestion_query = "";
        $expediente_query = "";
        $fecha_expediente_query = "";
        $tipoEstado_query = "";
        $fecha_estado_query = "";
        $nifcomprador_query = "";
        $nombrecomprador_query = "";
        $apellido1comprador_query = "";
        $apellido2comprador_query = "";
        $nifvendedor_query = "";
        $nombrevendedor_query = "";
        $apellido1vendedor_query = "";
        $apellido2vendedor_query = "";

        $bastidor_query = $lib->busqueda_datos($bastidor, $bastidor_query, 'VEH.bastidor');
        $matricula_query = $lib->busqueda_datos($matricula, $matricula_query, 'VEH.matricula');
        $tipoGestion_query = $lib->busqueda_tipo($tipoGes, $tipoGestion_query, 'ges.idGestion');
        $expediente_query = $lib->busqueda_datos($expediente, $expediente_query, 'EXPE.expediente');
        $fecha_expediente_query = $lib->busqueda_fechas($desdeFechaExpediente, $hastaFechaExpediente, $fecha_expediente_query, 'EXPE.fecha');
        $tipoEstado_query = $lib->busqueda_tipo($tipoEstado, $tipoEstado_query, 'EXPE.idEstado');
        $fecha_estado_query = $lib->busqueda_fechas($desdeFechaEstado, $hastaFechaEstado, $fecha_estado_query, 'EXPE.fechaCambioEstado');
        $nifcomprador_query = $lib->busqueda_datos_personas($nifcomprador, $nifcomprador_query, 'PER.DNICIF', 2);
        $nombrecomprador_query = $lib->busqueda_datos_personas($nombrecomprador, $nombrecomprador_query, 'PER.Nombre', 2);
        $apellido1comprador_query = $lib->busqueda_datos_personas($apellido1comprador, $apellido1comprador_query, 'PER.Apellido1_razon', 2);
        $apellido2comprador_query = $lib->busqueda_datos_personas($apellido2comprador, $apellido2comprador_query, 'PER.Apellido2', 2);
        $nifvendedor_query = $lib->busqueda_datos_personas($nifvendedor, $nifvendedor_query, 'PER.DNICIF', 3);
        $nombrevendedor_query = $lib->busqueda_datos_personas($nombrevendedor, $nombrevendedor_query, 'PER.Nombre', 3);
        $apellido1vendedor_query = $lib->busqueda_datos_personas($apellido1vendedor, $apellido1vendedor_query, 'PER.Apellido1_razon', 3);
        $apellido2vendedor_query = $lib->busqueda_datos_personas($apellido2vendedor, $apellido2vendedor_query, 'PER.Apellido2', 3);

        $select = "SELECT distinct top 1000 EXPE.idExpediente,
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

        $select2 = "SELECT idGestion, descripcionCorta, descripcionLarga
                        FROM Gestor_Gestiones ";


        if (auth()->user()->tipo == 1) {

            $superuser = "";

            $from = "  FROM 
         ((((Gestor_expedientes As EXPE LEFT JOIN Gestor_vehiculos As VEH ON 
         (EXPE.idVehiculo = VEH.idVehiculo)
         )   
         LEFT JOIN Gestor_estadosExpediente As ESEX  ON  
         (EXPE.idEstado = ESEX.idEstadoExpediente)
         )    
         LEFT JOIN Gestor_gestiones As GES ON    
         (EXPE.idGestion = GES.idGestion)
		 )
		 LEFT JOIN Gestor_INFTrafico as EXPTRA ON 
		 (EXPE.IdExpediente = EXPTRA.idExpediente)
		 
		 LEFT JOIN Gestor_Personas as per on (expe.idColaborador = per.idPersona)
		 )	 ";

            $colaborador = " where 1 = 1 ";
            $tipos = "";
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
		 or EXPE.idExpediente IN (SELECT idExpediente FROM Gestor_RelExpPer WHERE Telefono3 = '$idCuenta')) ";

                $tipos = "WHERE 
        descripcionCorta in (SELECT distinct GES.descripcionCorta As descripcionGestion
FROM ((((Gestor_expedientes As EXPE LEFT JOIN Gestor_vehiculos As VEH ON (EXPE.idVehiculo = VEH.idVehiculo)) 
LEFT JOIN Gestor_estadosExpediente As ESEX ON (EXPE.idEstado = ESEX.idEstadoExpediente)) 
LEFT JOIN Gestor_Personas as per on (expe.idColaborador = per.idPersona) 
left join Gestor_INFTrafico as EXPTRA ON (EXPE.IdExpediente = EXPTRA.idExpediente) 
LEFT JOIN Gestor_gestiones As GES ON (EXPE.idGestion = GES.idGestion)) 
LEFT JOIN Gestor_INFTrafico As EINFTRA ON (EXPE.idExpediente = EINFTRA.idExpediente)) 
WHERE (expe.idColaborador = $idColab or EXPE.idExpediente IN (SELECT idExpediente FROM Gestor_RelExpPer WHERE Telefono3 = '$idCuenta')) )";
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

                $colaborador = "where PEREXP.idTipoPersona = 2  and ((EXPE.idEstado = 11) OR (EXPE.idEstado = 12) OR (EXPE.idEstado = 14) OR (EXPE.idEstado = 15) OR (EXPE.idEstado = 9) OR (EXPE.idEstado = 10)
OR (EXPE.idEstado = 13) OR (EXPE.idEstado = 16 OR EXPE.idEstado = 17 OR EXPE.idEstado = 18 OR EXPE.idEstado = 19))
AND (PER.idPersona = $idColab OR expe.idColaborador = $idColab or PER.Telefono3 = '$idCuenta') ";

                $tipos = "WHERE 
        descripcionCorta in (

SELECT distinct GES.descripcionCorta As descripcionGestion
FROM Gestor_Personas AS PER 
INNER JOIN Gestor_RelExpPer AS PEREXP ON PER.idPersona = PEREXP.idPersona 
INNER JOIN Gestor_Expedientes AS EXPE ON PEREXP.IdExpediente = EXPE.IdExpediente 
INNER JOIN Gestor_Gestiones AS GES ON EXPE.idGestion = GES.idGestion 
INNER JOIN Gestor_Vehiculos AS VEH ON EXPE.idVehiculo = VEH.idVehiculo 
INNER JOIN Gestor_EstadosExpediente AS ESEX ON EXPE.idEstado = ESEX.idEstadoExpediente 
INNER JOIN Gestor_INFTrafico as EXPTRA ON EXPE.IdExpediente = EXPTRA.idExpediente 
left JOIN Rivera_usuarios as riv on per.idPersona = riv.idColaborador 
where PEREXP.idTipoPersona = 2 and ((EXPE.idEstado = 11) OR (EXPE.idEstado = 12) OR (EXPE.idEstado = 14) OR (EXPE.idEstado = 15) OR (EXPE.idEstado = 9) OR (EXPE.idEstado = 10) OR (EXPE.idEstado = 13) OR (EXPE.idEstado = 16 OR EXPE.idEstado = 17 OR EXPE.idEstado = 18 OR EXPE.idEstado = 19)) 
AND (PER.idPersona = $idColab OR expe.idColaborador = $idColab or PER.Telefono3 = '$idCuenta'))";
            }
        }

        $query2 = "$select2 $tipos";

        $query = "$select $from $colaborador 
        $bastidor_query
        $matricula_query
        $tipoGestion_query
        $expediente_query
        $fecha_expediente_query
        $tipoEstado_query
        $fecha_estado_query
        $nifcomprador_query
        $nombrecomprador_query
        $apellido1comprador_query
        $apellido2comprador_query
        $nifvendedor_query
        $nombrevendedor_query
        $apellido1vendedor_query
        $apellido2vendedor_query order by fecha desc";

        $expedientes = DB::select($query);

        $tipoGestion = DB::select($query2);

        $query3 = "SELECT idEstadoExpediente, descripcion
            FROM Gestor_EstadosExpediente";

        $tipoEstado = DB::select($query3);

        $data = array(
            'expedientes' => $expedientes,
            'tipoGestion' => $tipoGestion,
            'tipoEstado' => $tipoEstado
        );

        return view('expedientes.lista')->with($data);
    }

    public function dameExpedientes($id = null) {

        $inicio = Input::get("inicio");
        $final = Input::get("final");
        $idColab = Input::get("colaborador");       
        $cuenta = Input::get("cuenta");
        
        $lib = new LibreriaController();
   
        $dia = date('Y-m-d');
        $limite = date("d-m-Y", strtotime($dia . "- 1 month"));

        $select = "SELECT distinct top 750 EXPE.idExpediente, EXPE.expediente, EXPE.idColaborador, "
                . "EXPE.idGestion, EXPE.fecha, EXPE.observaciones, EXPE.idVehiculo, "
                . "EXPE.idEstado, ESEX.descripcion as estado, EXPE.expediente_matricula, "
                . "EXPE.expediente_fechaMatriculacion, EXPE.fechaCambioEstado, ESEX.finalizado, "
                . "GES.descripcionCorta As descripcionGestion, VEH.matricula, VEH.bastidor, "
                . "EXPTRA.fechaPresentacion ";

        $from = " FROM 
         ((((Gestor_expedientes As EXPE LEFT JOIN Gestor_vehiculos As VEH ON 
         (EXPE.idVehiculo = VEH.idVehiculo)
         )   
         LEFT JOIN Gestor_estadosExpediente As ESEX  ON  
         (EXPE.idEstado = ESEX.idEstadoExpediente)
         )    
         LEFT JOIN Gestor_gestiones As GES ON    
         (EXPE.idGestion = GES.idGestion)
		 )
		 LEFT JOIN Gestor_INFTrafico as EXPTRA ON 
		 (EXPE.IdExpediente = EXPTRA.idExpediente)
		 
		 LEFT JOIN Gestor_Personas as per on (expe.idColaborador = per.idPersona)
		 ) ";

        $where = "where 1 = 1 ";

        if (!empty($id)) {

            $where = " $where and expe.idExpediente = $id ";
        } else if (($inicio == null || $inicio == "") && ($final == null || $final == "") && ($idColab == null || $idColab == "") && ($col == null || $col == "") && ($cuenta == null || $cuenta == "")) {

            $where = " $where and EXPE.fecha >= '$limite' ";
        } else {

            $col = $lib->esColaborador($cuenta, $idColab);
            if ($col != null) {

                switch ($col) {
                    case 0:

                        $from = ' FROM
            Gestor_Personas AS PER INNER JOIN 
            Gestor_RelExpPer AS PEREXP ON PER.idPersona = PEREXP.idPersona INNER JOIN            
            Gestor_Expedientes AS EXPE ON PEREXP.IdExpediente = EXPE.IdExpediente INNER JOIN           
            Gestor_Gestiones AS GES ON EXPE.idGestion = GES.idGestion INNER JOIN
            Gestor_Vehiculos AS VEH ON EXPE.idVehiculo = VEH.idVehiculo INNER JOIN 
            Gestor_EstadosExpediente AS ESEX ON EXPE.idEstado = ESEX.idEstadoExpediente INNER JOIN
            Gestor_INFTrafico as EXPTRA ON EXPE.IdExpediente = EXPTRA.idExpediente left JOIN
            Rivera_usuarios as riv on per.idPersona = riv.idColaborador ';

                        $where = " $where and PEREXP.idTipoPersona = 2  and ((EXPE.idEstado = 11) OR (EXPE.idEstado = 12) OR (EXPE.idEstado = 14) OR (EXPE.idEstado = 15) OR (EXPE.idEstado = 9) OR (EXPE.idEstado = 10)
OR (EXPE.idEstado = 13) OR (EXPE.idEstado = 16 OR EXPE.idEstado = 17 OR EXPE.idEstado = 18 OR EXPE.idEstado = 19))
AND (PER.idPersona = $idColab OR expe.idColaborador = $idColab or PER.Telefono3 = '$cuenta') ";

                        break;

                    case 1:

                        $from = ' FROM 
      ((((Gestor_expedientes As EXPE LEFT JOIN Gestor_vehiculos As VEH ON (EXPE.idVehiculo = VEH.idVehiculo)) 
      LEFT JOIN Gestor_estadosExpediente As ESEX  ON (EXPE.idEstado = ESEX.idEstadoExpediente)) 
	  LEFT JOIN Gestor_Personas as per on (expe.idColaborador = per.idPersona)
	  left join Gestor_INFTrafico as EXPTRA ON (EXPE.IdExpediente = EXPTRA.idExpediente)
      LEFT JOIN Gestor_gestiones As GES ON (EXPE.idGestion = GES.idGestion))
      LEFT JOIN Gestor_INFTrafico As EINFTRA ON (EXPE.idExpediente = EINFTRA.idExpediente)) ';

                        $where = " $where and (expe.idColaborador = $idColab
		 or EXPE.idExpediente IN (SELECT idExpediente FROM Gestor_RelExpPer WHERE Telefono3 = '$cuenta')) ";

                        break;

                    default:

                        $from = "FROM ((((Gestor_expedientes As EXPE LEFT JOIN Gestor_vehiculos As VEH ON (EXPE.idVehiculo = VEH.idVehiculo) ) "
                                . "LEFT JOIN Gestor_estadosExpediente As ESEX ON (EXPE.idEstado = ESEX.idEstadoExpediente) ) "
                                . "LEFT JOIN Gestor_gestiones As GES ON (EXPE.idGestion = GES.idGestion) ) "
                                . "LEFT JOIN Gestor_INFTrafico as EXPTRA ON (EXPE.IdExpediente = EXPTRA.idExpediente) "
                                . "LEFT JOIN Gestor_Personas as per on (expe.idColaborador = per.idPersona) ) ";
                        break;
                }
            }

            $lib = new LibreriaController();
            $fecha_expediente_query = '';
            $col_query = '';
            $cuenta_query = '';

            if (($inicio != null || $inicio != "") || ($final != null || $final != "")) {
                $fecha_expediente_query = $lib->busqueda_fechas($inicio, $final, $fecha_expediente_query, 'EXPE.fecha');
            }

//            if ($idColab != null && $idColab != '') {
//                $col_query = $lib->busqueda_numero($idColab, $col_query, 'expe.idColaborador');
//            }
//
//            if ($cuenta != null && $cuenta != '') {
//                $cuenta_query = $lib->busqueda_datos($cuenta, $cuenta_query, 'per.telefono3');
//            }

            $where = "$where $fecha_expediente_query $col_query $cuenta_query";
        }

        $order_by = "order by expe.fecha desc";
        $query = "$select $from $where $order_by";
        

        $exp = DB::select($query);

//        echo $si = count($exp);
//        die;
        $json = json_encode($exp);

        return $json;
    }

}
