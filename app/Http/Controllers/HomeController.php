<?php

namespace gestorWeb\Http\Controllers;

use Auth;
use Hash;
use DB;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	if (auth()->user()->password_current == 0) {                 
        return $this->controlar_contra();
        }
        return Redirect::to('/expedientesWeb');
    }

	 public function cambiar_contra() {
        return view('cambiar_contra');
    }

    public function controlar_contra() {
        $pass = auth()->user()->password_current;
        if ($pass == 0) {
            return view('cambiar_contra');
        }
    }

    public function verificar_pass(Request $request) {

        $now = $request->post('now');
        $new = $request->post('new');
        $newrep = $request->post('newrep');

        $pass_actual = auth()->user()->password;
        $id_usuario = auth()->user()->id;

        if (Hash::check($now, $pass_actual) && $new == $newrep) {
            //dd('Write here your update password code');
            $opciones = ['cost' => 10,];
            $contra_hash = password_hash("$new", PASSWORD_BCRYPT, $opciones);

            $query = "update users set password = '$contra_hash', password_current = 1 "
                    . "where id = $id_usuario";

            DB::insert($query);

            Auth::logout();
            if (session_status() == PHP_SESSION_ACTIVE) {
                Session::flush();
            }
            
            return Redirect::to('/login');
            
        } else {
            $data = array(
                'mensaje' => "La contraseña que ha puesto no encaja, vuelva a intentarlo",
                'check' => false,
            );

            return view::make('informe_pass')->with($data);
        }
    }
}
