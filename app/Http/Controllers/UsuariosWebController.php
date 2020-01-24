<?php

namespace gestorWeb\Http\Controllers;

use Illuminate\Http\Request;
use gestorWeb\UsuariosWeb;

class UsuariosWebController extends Controller
{
    public function show($cuenta) {

    	$result = UsuariosWeb::where('cuenta','like',$cuenta)->get();  // metemos los registros en una variable
      //  return view('projects.index', ['projects' => $projects ]);  // pasamos la variable a la vista
    	// $result = UsuariosWeb::all();
    	return $result;

    
    }
}
