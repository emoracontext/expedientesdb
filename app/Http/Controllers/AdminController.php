<?php

namespace gestorWeb\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

class AdminController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function controlar_privilegios() {
        $tipo = auth()->user()->tipo;
        if ($tipo != 1) {
           return Redirect::to('/expedientesWeb');
        }
    }

    public function cambiar_users() {

        if (auth()->user()->tipo != 1) {
            return $this->controlar_privilegios();
        }

        $query = "select id, name, email, tipo from users";
        $users = DB::select($query);
        $query2 = "select * from tipo_usuario";
        $tipo = DB::select($query2);
        $data = array(
            'users' => $users,
            'tipo' => $tipo,
        );

        return view::make('admin_users')->with($data);
    }

    public function modificar_perfil(Request $request) {

        if (auth()->user()->tipo != 1) {
            return $this->controlar_privilegios();
        }

        $cuenta_p = $request->post('cuenta_a');
        $nombre = $request->post('nombre');
        $priv = $request->post('priv');

        $query = "update users set name = '$nombre', tipo = $priv where email = '$cuenta_p'";

        DB::insert($query);

        return $this->cambiar_users();
    }

    public function modificar_contra(Request $request) {

        if (auth()->user()->tipo != 1) {
            return $this->controlar_privilegios();
        }

        $cuenta = $request->post('cuenta_p');
        $pass = $request->post('pass');

        $opciones = ['cost' => 10,];
        $contra_hash = password_hash("$pass", PASSWORD_BCRYPT, $opciones);

        $query = "update users set password = '$contra_hash' where email = '$cuenta'";

        DB::insert($query);

        return $this->cambiar_users();
    }
    
    public function ventana_insertar() {
        
      
        if (auth()->user()->tipo != 1) {
            return $this->controlar_privilegios();
        }

        $query = "select * from Gestor_Personas where colaborador = 1 or Telefono3 <> '';";
        $users = DB::select($query);
        $query2 = "select * from tipo_usuario";
        $tipo = DB::select($query2);
        $query3 = "select * from Rivera_usuarios";
        $rivera = DB::select($query3);
        $data = array(
            'users' => $users,
            'tipo' => $tipo,
            'rivera' => $rivera
        );

        return view::make('insertar_usuario')->with($data);
    }

    public function insertar_user(Request $request) {
        
        
        if (auth()->user()->tipo != 1) {
            return $this->controlar_privilegios();
        }

        $nombre = $request->post('nombre_i');
        $cuenta = $request->post('cuenta_i');
       // $email = $request->post('email_i');
        $priv = $request->post('priv_i');
       // $colab = $request->post('colab_i');
        $pass = $request->post('pass_i');
        $tel3 = $request->post('tel3_i');

        $opciones = ['cost' => 10,];
        $contra_hash = password_hash("$pass", PASSWORD_BCRYPT, $opciones);
        $token = Str::random(60);

        $query = "insert into users (name, email, password, "
                . "created_at, password_current, tipo, remember_token, cuenta) "
                . "values "
                . "('$nombre', '$cuenta', '$contra_hash', GETDATE (), 0, $priv, '$token', '$tel3')";

        $query;

        DB::insert($query);


//        $query4 = "SELECT TOP (1) [idUsuario]
//  FROM [gestorV].[dbo].[Rivera_usuarios] order by idUsuario desc ";
//        
//        $ult = DB::select($query4);
//        
//        foreach ($ult as $item) {
//                $idUsuario = $item->idUsuario + 1;
//            }
//
//        $query2 = "insert into Rivera_usuarios (idUsuario, nombre, cuenta, contrasena, "
//                . "idPerfil, email, idColaborador) "
//                . "values "
//                . "($idUsuario, '$nombre', '$cuenta', null, 1, '$email', $colab)";
//
//        DB::insert($query2);

         return $this->cambiar_users();
    }

    public function visualizar_logs_users() {

        if (auth()->user()->tipo != 1) {
            return $this->controlar_privilegios();
        }

        $query = "select top (1000) * from Gestor_log_users order by fecha_log desc";

        $logs = DB::select($query);

        $data = array(
            'logs' => $logs
        );

        return view::make('registros.logs_users')->with($data);
    }

    public function busqueda_consulta_users(Request $request) {
        
        if (auth()->user()->tipo != 1) {
            return $this->controlar_privilegios();
        }

        $rivera = $request->post('rivera');
        $users = $request->post('users');
        $ip = $request->post('ip');
        $cuenta = $request->post('cuenta');
        $desde = $request->post('desde');
        $hasta = $request->post('hasta');


        $rivera_query = "";
        $users_query = "";
        $ip_query = "";
        $cuenta_query = "";
        $fecha_query = "";


        $rivera_query = $this->busqueda_numeros($rivera, $rivera_query, 'id_log_rivera');
        $users_query = $this->busqueda_numeros($users, $users_query, 'id_user');
        $ip_query = $this->busqueda_datos($ip, $ip_query, 'ip_log');
        $cuenta_query = $this->busqueda_datos($cuenta, $cuenta_query, 'u.email');
        $fecha_query = $this->busqueda_fechas($desde, $hasta, $fecha_query, 'fecha_log');


        if ($rivera_query == "" && $users_query == "" && $ip_query == "" && $cuenta_query == "" && $fecha_query == "") {
            return $this->visualizar_logs_users();
        } else {
            $query = "select top (1000) "
                    . "l.id_log_rivera, l.id_user, l.ip_log, l.fecha_log "
                    . "from Rivera_usuarios r, Gestor_log_users l left join users u on l.id_user = u.id "
                    . "where r.idUsuario = l.id_log_rivera $rivera_query $users_query $ip_query $cuenta_query $fecha_query"
                    . "order by fecha_log desc";
        }

        $logs = DB::select($query);

        $data = array(
            'logs' => $logs,
        );

        return view::make('registros.logs_users')->with($data);
    }

    public function busqueda_consulta_mov(Request $request) {
        
        if (auth()->user()->tipo != 1) {
            return $this->controlar_privilegios();
        }

        $rivera = $request->post('rivera');
        $users = $request->post('users');
        $ip = $request->post('ip');
        $cuenta = $request->post('cuenta');
        $idExp = $request->post('idExp');
        $desde = $request->post('desde');
        $hasta = $request->post('hasta');


        $rivera_query = "";
        $users_query = "";
        $ip_query = "";
        $idExp_query = "";
        $cuenta_query = "";
        $fecha_query = "";


        $rivera_query = $this->busqueda_numeros($rivera, $rivera_query, 'l.idUsuario');
        $users_query = $this->busqueda_numeros($users, $users_query, 'l.id_users');
        $ip_query = $this->busqueda_datos($ip, $ip_query, 'l.ip');
        $idExp_query = $this->busqueda_numeros($idExp, $idExp_query, 'l.id_expediente');
        $cuenta_query = $this->busqueda_datos($cuenta, $cuenta_query, 'u.email');
        $fecha_query = $this->busqueda_fechas($desde, $hasta, $fecha_query, 'l.fechaHora');


        if ($rivera_query == "" && $users_query == "" && $ip_query == "" && $idExp_query == "" && $cuenta_query == "" && $fecha_query == "") {
            return $this->visualizar_movimientos();
        } else {
            $query = "select top (1000) "
                    . "l.idLog, l.id_users, l.idUsuario, l.ip, l.fechaHora, l.descripcion, l.id_expediente "
                    . "from Rivera_usuarios r, Gestor_log l left join users u on l.id_users = u.id "
                    . "where r.idUsuario = l.idUsuario $rivera_query $users_query $ip_query $idExp_query $cuenta_query $fecha_query"
                    . "order by fechaHora desc";
        }

        $logs = DB::select($query);

        $data = array(
            'logs' => $logs,
        );

        return view::make('registros.movimiento_log')->with($data);
    }

    public function visualizar_movimientos() {

        if (auth()->user()->tipo != 1) {
            return $this->controlar_privilegios();
        }
        $query = "select top (1000) * from Gestor_log ";

        $logs = DB::select($query);

        $data = array(
            'logs' => $logs
        );

        return view::make('registros.movimiento_log')->with($data);
    }

    public function busqueda_datos($elemento, $sentencia, $referencia) {
        if ($elemento != null && $elemento != "") {
            return $sentencia = "and $referencia like '%$elemento%' ";
        }
    }

    public function busqueda_numeros($elemento, $sentencia, $referencia) {
        if ($elemento != null && $elemento != "") {
            return $sentencia = "and $referencia = $elemento ";
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

    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
