<?php

namespace App\Http\Controllers;

use App\Http\Models\ProveedorXCategoria;
use App\Http\Models\Usuarios;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProveedorController extends Controller
{

    public function proveedorContrato($id)
    {
        $proveedores = Proveedor::select('proveedors.nombres AS pnombre', 'proveedors.apellidos AS papellido', 'proveedors.id AS id')
            ->join('categorias', 'categorias.id', '=', 'proveedors.id_categoria')
            ->where('proveedors.id_categoria', '=', $id)
            ->where('proveedors.estado', '=', 'activo')
            ->get();
        return json_encode($proveedores);
    }

    public function datos($id)
    {
        $proveedor = Proveedor::find($id);
        return json_encode($proveedor);
    }

    public function lista()
    {
        $proveedores = Proveedor::where('id_categoria', '>', 0)->get();
        return json_encode($proveedores);
    }

    public function show($id)
    {
        if ($id == 'all') {
            $proveedores = ProveedorXCategoria::select('proveedor_x_categorias.alias', 'proveedor_x_categorias.descripcion', 'proveedors.calificacion', 'proveedor_x_categorias.imagen', 'categorias.nombre', 'proveedor_x_categorias.id', 'proveedors.ciudad', 'proveedors.sector', 'proveedors.barrio')
                ->join('proveedors', 'proveedors.id', '=', 'proveedor_x_categorias.id_proveedor')
                ->join('categorias', 'categorias.id', '=', 'proveedor_x_categorias.id_categoria')
                ->where('proveedors.estado', '=', 'activo')
                ->where('proveedor_x_categorias.alias', '!=', 'S/N')
                ->get();
        } else {
            $proveedores = ProveedorXCategoria::select('proveedor_x_categorias.alias', 'proveedor_x_categorias.descripcion', 'proveedors.calificacion', 'proveedor_x_categorias.imagen', 'categorias.nombre', 'proveedor_x_categorias.id', 'proveedors.ciudad', 'proveedors.sector', 'proveedors.barrio')
                ->join('proveedors', 'proveedors.id', '=', 'proveedor_x_categorias.id_proveedor')
                ->join('categorias', 'categorias.id', '=', 'proveedor_x_categorias.id_categoria')
                ->where('proveedor_x_categorias.id_categoria', '=', $id)
                ->where('proveedors.estado', '=', 'activo')
                ->where('proveedor_x_categorias.alias', '!=', 'S/N')
                ->get();
        }
        /*$proveedores = Proveedor::select('proveedors.nombres AS pnombre', 'proveedors.apellidos AS papellido', 'proveedors.imagen', 'proveedors.descripcion', 'categorias.nombre', 'proveedors.celular', 'proveedors.calificacion', 'proveedors.id')
        ->join('categorias', 'categorias.id', '=', 'proveedors.id_categoria')
        ->where('proveedors.id_categoria', '=', $id)
        ->where('proveedors.estado', '=', 'activo')
        ->get();*/
        return json_encode($proveedores);
    }

    public function guardar(Request $request)
    {
        $correo = Usuarios::where('email', $request->input('email'))
            ->get();
        if (count($correo) > 0) {
            return json_encode('fail');
        } else {
            $user = new Usuarios();
            $user->names = $request->input('nombres');
            $user->lastnames = $request->input('apellidos');
            $user->email = $request->input('email');
            $user->user = $request->input('nombres');
            $user->pass = base64_encode('ABC123');

            $image = $request->file('imagen');
            $path = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('hojas_vida'), $path);
            $proveedor = new Proveedor();
            $proveedor->nombres = $request->input('nombres');
            $proveedor->apellidos = $request->input('apellidos');
            $proveedor->direccion = $request->input('direccion');
            $proveedor->ciudad = $request->input('ciudad');
            $proveedor->sector = $request->input('sector');
            $proveedor->barrio = $request->input('barrio');
            $proveedor->celular = $request->input('celular');
            $proveedor->convencional = $request->input('convencional');
            $proveedor->descripcion = $request->input('descripcion');
            $proveedor->imagen = $path;
            $proveedor->id_categoria = $request->input('id_categoria');
            $proveedor->estado = 'inactivo';
            $proveedor->save();

            $user->id_proveedor = $proveedor->id;
            $user->save();
            return json_encode($proveedor);
        }
    }

    public function actualizar(Request $request)
    {
        $image = $request->file('imagen');
        $path = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('fondos'), $path);

        $proveedor = ProveedorXCategoria::find($request->input('id'));
        $proveedor->alias = $request->input('nombres');
        $proveedor->descripcion = $request->input('descripcion');
        $proveedor->celular = $request->input('celular');
        $proveedor->convencional = $request->input('convencional');
        $proveedor->hora_min = $request->input('h_in');
        $proveedor->hora_max = $request->input('h_out');
        $proveedor->precio_min = $request->input('precio_min');
        $proveedor->precio_max = $request->input('precio_max');
        $proveedor->precio = $request->input('precio_min') . '$-' . $request->input('precio_max') . '$';
        $proveedor->banco = $request->input('banco');
        $proveedor->cuenta = $request->input('cuenta');
        $proveedor->imagen = $path;
        $proveedor->save();
        /*$image = $request->file('imagen');
        $path = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('fondos'), $path);

        $proveedor = Proveedor::find($request->input('id'));
        $proveedor->nombres = $request->input('nombres');
        $proveedor->apellidos = $request->input('apellidos');
        $proveedor->direccion = $request->input('direccion');
        $proveedor->celular = $request->input('celular');
        $proveedor->descripcion = $request->input('descripcion');
        $proveedor->imagen = $path;
        $proveedor->save();*/
        return json_encode($proveedor);
    }

    public function borrar($id)
    {
        $proveedor = Proveedor::find($id);
        $proveedor->estado = 'inactivo';
        $proveedor->save();
        return json_encode($proveedor);
    }

    public function activar($id)
    {
        $proveedor = Proveedor::find($id);
        $proveedor->estado = 'activo';
        $proveedor->save();
        echo json_encode($proveedor);
    }

    public function proveedorDisponible()
    {
        $proveedor = Proveedor::select('proveedors.id', 'proveedors.nombres', 'proveedors.apellidos')
            ->join('proveedor_x_categorias', 'proveedor_x_categorias.id_proveedor', '=', 'proveedors.id')
            ->groupBy('proveedors.id', 'proveedors.nombres', 'proveedors.apellidos')
            ->get();
        /*$proveedor = DB::select('SELECT * FROM
        proveedors
        WHERE id NOT IN (SELECT id_proveedor FROM usuarios
        WHERE id_proveedor > 0)');*/
        ///$this->email();
        return json_encode($proveedor);
    }

    public function datosContrato($id)
    {
        $proveedor = ProveedorXCategoria::select('proveedors.calificacion', 'proveedors.direccion', 'proveedors.nombres', 'proveedors.apellidos', 'proveedor_x_categorias.*')
            ->join('proveedors', 'proveedors.id', '=', 'proveedor_x_categorias.id_proveedor')
            ->where('proveedor_x_categorias.id', $id)
            ->get();
        /* $proveedor = Proveedor::where('id', '=', $id)
        ->get();*/
        return json_encode($proveedor);
    }

    public function pendientes()
    {
        $proveedor = Proveedor::where('aprobado', 'NO')->get();
        return json_encode($proveedor);
    }

    public function pendientes_datos($id)
    {
        $proveedor = Proveedor::select('proveedors.*', 'categorias.nombre as categoria')
            ->leftjoin('categorias', 'categorias.id', '=', 'proveedors.id_categoria')
            ->where('proveedors.id', $id)
            ->get();
        return json_encode($proveedor);
    }

    public function activar_proveedor(Request $request)
    {
        $proveedor = Proveedor::find($request->input('id_proveedor'));
        $proveedor->id_categoria = $request->input('id_categoria');
        $proveedor->estado = 'activo';
        $proveedor->aprobado = 'SI';
        $proveedor->save();

        $categoria = new ProveedorXCategoria();
        $categoria->id_proveedor = $proveedor->id;
        $categoria->id_categoria = $proveedor->id_categoria;
        $categoria->alias = 'S/N';
        $categoria->descripcion = 'S/N';
        $categoria->celular = 'S/N';
        $categoria->hora_min = '00:00';
        $categoria->hora_max = '00:00';
        $categoria->precio = '0$-0$';
        $categoria->banco = 'S/N';
        $categoria->cuenta = 'S/N';
        $categoria->save();

        Usuarios::where('id_proveedor', $proveedor->id)
            ->update(['id_rol' => 2]);

        //enviar correo
        $usuario = Usuarios::where('id_proveedor', $proveedor->id)
            ->first();
        $this->email($usuario);

        return json_encode($proveedor);
    }

    public function email($usuario)
    {
        $correos = [$usuario->email];
        $datos['pass'] = base64_decode($usuario->pass);
        Mail::send('email.email', $datos, function ($message) use ($correos) {
            $message->from('info@qckservice.com', 'Notificación QuickService');
            $message->to($correos)->subject('Notificación QuickService - Perfil Proveedor');
        });
    }

}
