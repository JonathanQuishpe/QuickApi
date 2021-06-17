<?php

namespace App\Http\Controllers;

use App\Http\Models\Administracion;
use App\Http\Models\Configuraciones;
use Illuminate\Http\Request;

class AdministracionController extends Controller
{
    public function login(Request $request)
    {
        $user = Administracion::where('usuario', $request->input('usuario'))
            ->where('pass', $request->input('pass'))
            ->get();
        return json_encode($user);
    }

    public function configuraciones()
    {
        $configuraciones = Configuraciones::where('id', 1)
            ->get();
        return json_encode($configuraciones);

    }

    public function actualizar(Request $request)
    {
        $configuracion = Configuraciones::find(1);
        if ($request->has('app_android')) {
            $configuracion->app_android = $request->input('app_android');
        }
        if ($request->has('app_store')) {
            $configuracion->app_android = $request->input('app_store');
        }
        if ($request->has('titulo')) {
            $configuracion->titulo = $request->input('titulo');
        }
        if ($request->has('sub_titulo_1')) {
            $configuracion->sub_titulo_1 = $request->input('sub_titulo_1');
        }
        if ($request->has('sub_titulo_2')) {
            $configuracion->sub_titulo_2 = $request->input('sub_titulo_2');
        }
        if ($request->has('parrafo')) {
            $configuracion->parrafo = $request->input('parrafo');
        }
        $configuracion->save();
        return json_encode($configuracion);
    }
}
