<?php

namespace App\Http\Controllers;

use App\Http\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categoria = Categoria::where('estado', '=', 'activo')
            ->get();
        return json_encode($categoria);
    }

    public function guardar(Request $resquest)
    {
        $categoria = new Categoria();
        $categoria->nombre = $resquest->input('nombre');
        $categoria->descripcion = $resquest->input('descripcion');
        $categoria->estado = 'activo';
        $categoria->save();
        return json_encode($categoria);
    }
    public function actualizar(Request $resquest)
    {
        $categoria = Categoria::find($resquest->input('id'));
        $categoria->nombre = $resquest->input('nombre');
        $categoria->descripcion = $resquest->input('descripcion');
        $categoria->save();
        return json_encode($categoria);
    }

    public function borrar($id)
    {
        $categoria = Categoria::find($id);
        $categoria->estado = 'inactivo';
        $categoria->save();

        return json_encode($categoria);
    }

    public function busqueda($string)
    {
        $categoria = Categoria::where('nombre', 'like', $string . '%')
            ->where('estado', 'activo')
            ->get();
        return json_encode($categoria);
    }
}
