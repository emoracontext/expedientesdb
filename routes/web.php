<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false]);	

Route::get('/', function () {
    if (auth()) {
       return Redirect::to('/expedientesWeb');
    } else {
    return view('auth.login');
    }
});
Route::resource('/solicitar_acceso', 'AccesoController');
Route::post('pedir_acceso', 'AccesoController@enviar');

Route::resource('/estadosexpediente', 'EstadosExpedienteController');
Route::resource('/gestiones', 'GestorGestionesController');
Route::resource('/expedientesWeb','ExpedientesWebController');
Route::resource('/expedienteDP', 'ExpedientesWebDPController');
Route::resource('imagenesExp','ImagenesExpController');
Route::resource('/usuariosWeb','UsuariosWebController');
Route::resource('/observacionesExp','ObservacionesExpController');
Route::resource('/expedientesFicha','ExpedientesFichaController');

Route::post('busqueda_movimiento', 'AdminController@busqueda_consulta_mov');
Route::get('/consulta_movimientos', 'AdminController@visualizar_movimientos');
Route::get('/consulta_users', 'AdminController@visualizar_logs_users');
Route::post('busqueda_users', 'AdminController@busqueda_consulta_users');

Route::get('/modificar_pass', 'HomeController@cambiar_contra');
Route::post('verificar_pass', 'HomeController@verificar_pass');
Route::post('modificar_perfil', 'AdminController@modificar_perfil');
Route::post('modificar_contra', 'AdminController@modificar_contra');
Route::get('/admin_users', 'AdminController@cambiar_users');
Route::get('/insertar_usuario', 'AdminController@ventana_insertar');
Route::post('insertar_usuario', 'AdminController@insertar_user');

Route::post('busqueda', 'ExpedientesWebController@busqueda');

Route::get('/visualizar/{opcion}/{crypt}',
        [   'as' => 'imagen',
            'uses' =>'ExpedientesFichaController@visualizar',
            'middleware' => 'auth']);

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/pwd/{clave}', function ($clave) {
	
	return Hash::make($clave);
});

// Rutas de json
Route::get('/expedientes/{id?}', 'ExpedientesWebController@dameExpedientes');
Route::get('/expedientes-stats/transferencias', 'EstadisticaController@recogerTransferencia');
Route::get('/expedientes-stats/matriculaciones', 'EstadisticaController@recogerMatriculacion');
Route::get('/expedientes-stats/duplicados', 'EstadisticaController@recogerDuplicado');

Route::get('/exp', 'ExpedientesApiController@index');
