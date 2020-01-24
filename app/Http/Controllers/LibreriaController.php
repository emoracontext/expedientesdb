<?php

namespace gestorWeb\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Input;

class LibreriaController extends Controller {

    public function compruebaAcceso() {

        $ip = $this->get_client_ip();
        $acceder = 0;
        $estalakey = 0;
        if ($ip == '127.0.0.1' || $ip == '83.44.92.239' || $ip == '83.37.138.109') {
        //   if ( $ip == '83.37.138.109') {
            $acceder = 99;
            foreach (getallheaders() as $name => $value) {
                if ($name == "api-key") {
                    $estalakey++;
                    //  echo " ----------- ESTOY RECOGIENDO EL HOST --------- ";
                    if ($value === "056476eff841ae09d1435531d0e74df4") {
                        $acceder = 4;
                    } else {
                        $acceder = 3;
                    }
                }
            }
        } else {
            $acceder = 1;
        }

        if ($estalakey == 0) {
            $acceder = 2;
        }
        return $acceder;
    }

    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }

    function recogerEstadistica($gestion = null, $estado1 = null, $estado2 = null, $estado3 = null, $cuenta = null, $colaborador = null, $fecha = null) {

        $where = "";
        $where1 = "";
        $where2 = "";
        $where3 = "";


        if ($cuenta != null && $colaborador != null) {

            $col = $this->esColaborador($cuenta, $colaborador);

            if ($col == 0) {

                $where = " and expe.idExpediente in (SELECT distinct EXPE.idExpediente FROM Gestor_Personas AS PER 
                            INNER JOIN Gestor_RelExpPer AS PEREXP ON PER.idPersona = PEREXP.idPersona 
                            INNER JOIN Gestor_Expedientes AS EXPE ON PEREXP.IdExpediente = EXPE.IdExpediente 
                            INNER JOIN Gestor_Gestiones AS GES ON EXPE.idGestion = GES.idGestion 
                            INNER JOIN Gestor_Vehiculos AS VEH ON EXPE.idVehiculo = VEH.idVehiculo 
                            INNER JOIN Gestor_EstadosExpediente AS ESEX ON EXPE.idEstado = ESEX.idEstadoExpediente
                            INNER JOIN Gestor_INFTrafico as EXPTRA ON EXPE.IdExpediente = EXPTRA.idExpediente 
                            left JOIN Rivera_usuarios as riv on per.idPersona = riv.idColaborador 
                            where 1 = 1 $fecha and PEREXP.idTipoPersona = 2  AND (PER.idPersona = $colaborador OR expe.idColaborador = $colaborador or PER.Telefono3 = '$cuenta'))";
            } else if ($col == 1) {

                $where1 = $this->elegirTipoColaborador($gestion, $estado1, $cuenta, $colaborador, $fecha);
                $where2 = $this->elegirTipoColaborador($gestion, $estado2, $cuenta, $colaborador, $fecha);
                $where3 = $this->elegirTipoColaborador($gestion, $estado3, $cuenta, $colaborador, $fecha);
            }
        }

        $query = "select count(expe.IdExpediente) as Expedientes,  es.descripcion
                    from Gestor_Expedientes as expe
                    inner join Gestor_EstadosExpediente as es on expe.idEstado = es.idEstadoExpediente
                    inner join Gestor_Gestiones as ges on expe.idGestion = ges.idGestion

                    where GES.descripcionCorta like '%$gestion%' and es.descripcion like '%$estado1%' $where $where1 

                    group by es.descripcion

                    UNION

                    select count(expe.IdExpediente) as Expedientes,  es.descripcion
                    from Gestor_Expedientes as expe
                    inner join Gestor_EstadosExpediente as es on expe.idEstado = es.idEstadoExpediente
                    inner join Gestor_Gestiones as ges on expe.idGestion = ges.idGestion
                    where GES.descripcionCorta like '%$gestion%' and es.descripcion like '$estado2' $where $where2 

                    group by es.descripcion

                    UNION

                    select count(expe.IdExpediente) as Expedientes,  es.descripcion
                    from Gestor_Expedientes as expe
                    inner join Gestor_EstadosExpediente as es on expe.idEstado = es.idEstadoExpediente
                    inner join Gestor_Gestiones as ges on expe.idGestion = ges.idGestion
                    where GES.descripcionCorta like '%$gestion%' and es.descripcion like '$estado3' $where3

                    group by es.descripcion";

        $resultado = DB::select($query);
        return $resultado;
    }

    function esColaborador($cuenta = null, $colaborador = null) {

        $col = 3;

        $query2 = "select per.colaborador 
from Rivera_usuarios as riv 
inner join Gestor_Personas as per on riv.cuenta = per.telefono3 
where telefono3 like '$cuenta' and per.idPersona = $colaborador";

        $esColaborador = DB::select($query2);

        foreach ($esColaborador as $item) {
            $col = $item->colaborador;
            break;
        }

        return $col;
    }

    function elegirTipoColaborador($gestion = null, $estado = null, $cuenta = null, $colaborador = null, $fecha = null) {

        $where = " and expe.idExpediente in (SELECT EXPE.idExpediente
                    FROM ((((Gestor_expedientes As EXPE LEFT JOIN Gestor_vehiculos As VEH ON (EXPE.idVehiculo = VEH.idVehiculo)) 
                    LEFT JOIN Gestor_estadosExpediente As ESEX ON (EXPE.idEstado = ESEX.idEstadoExpediente)) 
                    LEFT JOIN Gestor_Personas as per on (expe.idColaborador = per.idPersona) 
                    left join Gestor_INFTrafico as EXPTRA ON (EXPE.IdExpediente = EXPTRA.idExpediente) 
                    LEFT JOIN Gestor_gestiones As GES ON (EXPE.idGestion = GES.idGestion)) 
                    LEFT JOIN Gestor_INFTrafico As EINFTRA ON (EXPE.idExpediente = EINFTRA.idExpediente)) 
                    where 1 = 1 $fecha 
                    and (expe.idColaborador = $colaborador or 
                    EXPE.idExpediente IN (SELECT rel.idExpediente FROM Gestor_RelExpPer as rel
                    left join Gestor_Expedientes as expe on rel.IdExpediente = expe.IdExpediente
                    left join Gestor_Gestiones as ges on expe.idGestion = ges.idGestion
                    left join Gestor_EstadosExpediente as es on expe.idEstado = es.idEstadoExpediente
                    WHERE rel.Telefono3 = '$cuenta' and GES.descripcionCorta like '%$gestion%' and es.descripcion like '%$estado%' $fecha ))) ";

        return $where;
    }

    function seleccionarEstadistica($gestion = null, $estado1 = null, $estado2 = null, $estado3 = null, $columna_fecha = null) {
        $colaborador = Input::get("colaborador");
        $cuenta = Input::get("cuenta");
        $inicio = Input::get("inicio");
        $final = Input::get("final");
        $fecha_expediente_query = "";

        $lib = new LibreriaController();

        if (($inicio != null || $inicio != "") || ($final != null || $final != "")) {
            $fecha_expediente_query = $lib->busqueda_fechas($inicio, $final, $fecha_expediente_query, $columna_fecha);
        }

        $tran = $lib->recogerEstadistica($gestion, $estado1, $estado2, $estado3, $cuenta, $colaborador, $fecha_expediente_query);

        $resultado = json_encode($tran);
        return $resultado;
    }

    public function controlar_contra() {
        $pass = auth()->user()->password_current;
        if ($pass == 0) {
            return view('cambiar_contra');
        }
    }

    public function busqueda_datos($elemento, $sentencia, $referencia) {
        if ($elemento != null && $elemento != "") {
            return $sentencia = "and $referencia like '%$elemento%' ";
        }
    }

    public function busqueda_datos_personas($elemento, $sentencia, $referencia, $cov) {

        if ($elemento != null && $elemento != "") {
            return $sentencia = "AND EXPE.idExpediente IN (SELECT PER.idExpediente FROM Gestor_relExpPer As PER WHERE PER.idTipoPersona = $cov AND $referencia LIKE '%$elemento%')";
        }
    }

    public function busqueda_tipo($elemento, $sentencia, $referencia) {
        if ($elemento != null && $elemento != "") {
            if ($elemento != "todos") {
                return $sentencia = "and $referencia = '$elemento' ";
            }
        }
    }

    public function busqueda_fechas($desde, $hasta, $sentencia, $referencia) {

        if (($desde != null && $desde != "") ||
                ($hasta != null && $hasta != "")) {

            if (($desde != null && $desde != "") &&
                    ($hasta == null || $hasta == "")) {
                return $sentencia = "and $referencia >= CONVERT(SMALLDATETIME,'$desde',101) ";
            } else if (($desde != null && $desde != "") &&
                    ($hasta != null && $hasta != "")) {
                return $sentencia = "and $referencia >= CONVERT(SMALLDATETIME,'$desde',101) and"
                        . " $referencia <= CONVERT(SMALLDATETIME,'$hasta',101) ";
            } else {
                return $sentencia = "and $referencia <= CONVERT(SMALLDATETIME,'$hasta',101) ";
            }
        }
    }

    public function busqueda_numero($num, $sentencia, $referencia) {
        if ($num != null && $num != '') {
            return $sentencia = "and $referencia = $num ";
        }
    }

}
