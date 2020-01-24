<?php

namespace gestorWeb\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use DB;

class AccesoController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('solicitar_acceso');
    }

    public function enviar(Request $request) {

        $nombre = $request->post('nombre');
        $empresa = $request->post('empresa');
        $tel = $request->post('tel');
        $mail = $request->post('mail');
        $mensaje = $request->post('mensaje');

        $query = "insert into solicitud_acceso values "
                . "('$nombre', '$empresa', '$tel', '$mail', '$mensaje')";
        DB::insert($query);

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
        $headers .= "From: <$mail>" . "\r\n";
        $headers .= "Cc: $mail" . "\r\n";
        //dfernandez@alrescate.com es a donde se envia
        mail("dfernandez@alrescate.com", "Solicitud de acceso - $nombre de $empresa ($tel)", "$mensaje", $headers);

        return Redirect::to('/');
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
