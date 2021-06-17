<?php

namespace App\Http\Controllers;

use App\Http\Models\ProveedorXCategoria;
use App\Http\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UsuariosController extends Controller
{
    public function index()
    {
        $user = Usuarios::get();
        echo (json_encode($user));
    }

    public function datos($id)
    {
        $usuario = Usuarios::find($id);
        $usuario->pass = base64_decode($usuario->pass);
        return json_encode($usuario);
    }
    public function store(Request $request)
    {
        $correo = Usuarios::where('email', $request->input('email'))
            ->get();
        if (count($correo) > 0) {
            return json_encode('fail');
        } else {
            $user = new Usuarios();
            $user->names = $request->input('names');
            $user->lastnames = $request->input('lastnames');
            $user->email = $request->input('email');
            $user->user = $request->input('user');
            $user->pass = base64_encode($request->input('password'));
            $user->save();
            return json_encode($user);
        }

    }

    public function update(Request $request)
    {
        /*$image = $request->file('imagen');
        $path = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('perfil'), $path);*/
        $user = Usuarios::find($request->input('id'));

        /*$old = public_path() . '/perfil/' . $user->imagen;
        if (file_exists($old)) {
        unlink($old);
        }*/

        $user->names = $request->input('names');
        $user->lastnames = $request->input('lastnames');
        //$user->email = $request->input('email');
        $user->user = $request->input('user');
        $user->pass = base64_encode($request->input('password'));
        //$user->imagen = $path;
        $user->save();
        return json_encode($user);
    }

    public function destroy($id)
    {
        $movie = Usuarios::find($id);
        $movie->delete();
    }

    public function show($id)
    {
        $user = Usuarios::find($id);
        $user->pass = base64_decode($user->pass);
        return json_encode($user);
    }

    public function login(Request $request)
    {
        $user = Usuarios::select('user', 'id', 'id_rol', 'id_proveedor')
            ->where('email', $request->input('email'))
            ->where('pass', base64_encode($request->input('password')))
            ->get();
        return json_encode($user);

    }

    public function cuenta()
    {
        $user = Usuarios::all();
        return json_encode($user);
    }
    public function cuentaLibres()
    {
        $user = Usuarios::where('id_proveedor', '=', 0)
            ->get();
        return json_encode($user);
    }
    public function asignar($id, $pro)
    {
        $categoria = new ProveedorXCategoria();
        $categoria->id_proveedor = $pro;
        $categoria->id_categoria = $id;
        $categoria->alias = 'S/N';
        $categoria->descripcion = 'S/N';
        $categoria->celular = 'S/N';
        $categoria->hora_min = '00:00';
        $categoria->hora_max = '00:00';
        $categoria->precio = '0$-0$';
        $categoria->banco = 'S/N';
        $categoria->cuenta = 'S/N';
        $categoria->save();
        return json_encode($categoria);
        /*$user = Usuarios::find($id);
    $user->id_rol = 2;
    $user->id_proveedor = $pro;
    $user->save();
    echo json_encode($user);*/
    }

    public function foto(Request $request)
    {
        $image = $request->file('imagen');
        $path = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('perfil'), $path);
        $user = Usuarios::find($request->input('id'));

        $old = public_path() . '/perfil/' . $user->imagen;
        if (file_exists($old)) {
            unlink($old);
        }

        $user->imagen = $path;
        $user->save();
        $response = '';
        if ($user) {
            $response = 'ok';
        } else {
            $response = 'error';
        }

        return json_encode($response);
    }

    public function guardar_token(Request $request)
    {
        $user = Usuarios::find($request->input('id'));
        $user->token_movil = $request->input('token');
        $user->save();

        return json_encode($user);
    }
    public function guardar_token_web(Request $request)
    {
        $user = Usuarios::find($request->input('id'));
        $user->token_web = $request->input('token');
        $user->save();

        return json_encode($user);
    }

    public function reestablecer(Request $request)
    {
        $correo = $request->input('email');
        $usuario = Usuarios::where('email', $correo)
            ->first();
        $response = array();
        if ($usuario) {
            //enviar correo
            $this->email($usuario);
            $response = array(
                'status' => 'success',
                'message' => 'La contraseña ha sido enviada al correo ingresado.',
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'El correo ingresado no se encuentra registrado en el sistema.',
            );
        }

        return json_encode($response);
    }

    public function email($usuario)
    {
        $correos = [$usuario->email];
        $datos['user'] = $usuario;
        Mail::send('email.reset', $datos, function ($message) use ($correos) {
            $message->from('info@qckservice.com', 'Notificación QuickService');
            $message->to($correos)->subject('Notificación QuickService - Recordatorio de Contraseña');
        });
    }
}
