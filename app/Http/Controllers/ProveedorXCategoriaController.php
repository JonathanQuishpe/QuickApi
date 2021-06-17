<?php

namespace App\Http\Controllers;

use App\Http\Models\ProveedorXCategoria;

class ProveedorXCategoriaController extends Controller
{

    public function listado_categorias($id)
    {
        $categorias = ProveedorXCategoria::select('proveedor_x_categorias.id', 'categorias.nombre')
            ->join('categorias', 'categorias.id', '=', 'proveedor_x_categorias.id_categoria')
            ->where('proveedor_x_categorias.id_proveedor', $id)
            ->get();

        return json_encode($categorias);
    }

    public function item($id)
    {
        $categoria = ProveedorXCategoria::where('id', $id)
            ->get();

        return json_encode($categoria);
    }

    public function proveedores($id)
    {
        $categoria = ProveedorXCategoria::where('id_categoria', $id)
            ->count();

        return json_encode($categoria);
    }

}
