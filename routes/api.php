<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
return $request->user();
}); */
Route::resource('user', 'UsuariosController');
Route::resource('category', 'CategoriaController');
Route::resource('proveedor', 'ProveedorController');
Route::post('login', 'UsuariosController@login');
Route::post('foto', 'UsuariosController@foto');
Route::post('administracion/login', 'AdministracionController@login');
Route::post('token/movil', 'UsuariosController@guardar_token');
Route::post('token/web', 'UsuariosController@guardar_token_web');
Route::post('user/reestablecer', 'UsuariosController@reestablecer');
Route::post('contrato', 'ContratoController@guardar');
Route::get('proveedor-contrato/{id}', 'ProveedorController@proveedorContrato');
Route::get('lista', 'ProveedorController@lista');
Route::get('pendientes', 'ProveedorController@pendientes');
Route::get('pendientes_datos/{id}', 'ProveedorController@pendientes_datos');
Route::get('listado-contrato/{id}', 'ContratoController@lista');
Route::post('calificar', 'ContratoController@calificar');
Route::get('proveedor-estado/{id}', 'ContratoController@proveedor');
Route::get('configuraciones', 'AdministracionController@configuraciones');
Route::post('configuraciones', 'AdministracionController@actualizar');
Route::get('proveedor-transito/{id}', 'ContratoController@proveedor_transito');
Route::get('proveedor-finalizado/{id}', 'ContratoController@proveedor_finalizado');
Route::post('cambiar-estado', 'ContratoController@cambiarEstado');
Route::get('prueba/{id}', 'ContratoController@calcular');
Route::get('datos/{id}', 'UsuariosController@datos');
Route::get('datos-proveedor/{id}', 'ProveedorController@datos');
Route::get('datos-contrato/{id}', 'ProveedorController@datosContrato');
Route::post('userUpdate', 'UsuariosController@update');
Route::post('proveedor/actualizar', 'ProveedorController@actualizar');
Route::post('proveedor/guardar', 'ProveedorController@guardar');
Route::post('proveedor/activar', 'ProveedorController@activar_proveedor');
Route::post('categoria/guardar', 'CategoriaController@guardar');
Route::post('categoria/actualizar', 'CategoriaController@actualizar');
Route::get('categoria/borrar/{id}', 'CategoriaController@borrar');
Route::get('proveedor/borrar/{id}', 'ProveedorController@borrar');
Route::get('proveedor/activar/{id}', 'ProveedorController@activar');
Route::get('usuario/cuentas', 'UsuariosController@cuenta');
Route::get('usuario/cuentas-asignar', 'UsuariosController@cuentaLibres');
Route::get('cuentas', 'ProveedorController@proveedorDisponible');
Route::get('cuentas/asignar/{id}/{pro}', 'UsuariosController@asignar');
Route::get('detalle_servicio/{id}', 'ContratoController@detalle_servicio');
Route::get('listado_categorias/{id}', 'ProveedorXCategoriaController@listado_categorias');
Route::get('item/{id}', 'ProveedorXCategoriaController@item');
Route::get('numero/servicios/{id}', 'ContratoController@numero_servicios');
Route::get('numero/contratos/{id}', 'ContratoController@numero_contratos');
Route::get('comentarios/{id}', 'ContratoController@comentarios');
Route::get('busqueda/{string}', 'CategoriaController@busqueda');
Route::get('categoria/contar/{id}', 'ProveedorXCategoriaController@proveedores');
//mail
Route::get('proveedor/mail', 'ProveedorController@email');
//Route::get('notificaciones', 'ContratoController@mensaje');
