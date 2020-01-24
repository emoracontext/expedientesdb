<?php

namespace gestorWeb\Http\Controllers\Auth;

use gestorWeb\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Redirect;
use gestorWeb\UsuariosWeb;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

   protected function authenticated($request, $user) {

        $id_user = auth()->user()->id;
        $ip = $this->get_client_ip();

        $usuario = UsuariosWeb::where('cuenta', $user->cuenta)->first();



        if (!$usuario == null) {

            // dd($usuario);
            session(['idUsuario' => $usuario->idUsuario]);
            session(['idColaborador' => $usuario->idColaborador]);
            session(['nombre' => $usuario->nombre]);
            session(['cuenta' => $usuario->cuenta]);
            session(['ip' => $ip]);
            session(['idPerfil' => $usuario->idPerfil]);

            $queryCol = "Select colaborador from Gestor_Personas where idPersona = $usuario->idColaborador";
            $res = DB::select($queryCol);
            foreach ($res as $i) {
                session(['colaborador' => $i->colaborador]);
            }           
            
            $query = "insert into Gestor_log_users (id_user, id_log_rivera, fecha_log, ip_log)"
                    . " values ($id_user, $usuario->idUsuario, GETDATE (), '$ip')";

            DB::insert($query);
        } else {

            $query = "insert into Gestor_log_users (id_user, id_log_rivera, fecha_log, ip_log)"
                    . " values ($id_user, null, GETDATE(), '$ip')";

            DB::insert($query);

            Auth::logout();
            if (session_status() == PHP_SESSION_ACTIVE) {
                Session::flush();
            }
            return Redirect::to('/login');
        }
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

}
