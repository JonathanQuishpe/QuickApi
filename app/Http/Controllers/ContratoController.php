<?php

namespace App\Http\Controllers;

use App\Http\Models\Contrato;
use App\Http\Models\ProveedorXCategoria;
use App\Http\Models\Usuarios;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratoController extends Controller
{

    public function guardar(Request $request)
    {

        $horas = str_pad($request->input('hora'), 2, "0", STR_PAD_LEFT);
        $minutos = str_pad($request->input('minutos'), 2, "0", STR_PAD_LEFT);
        $hora = $horas . ':' . $minutos;
        $usuario = Usuarios::find($request->input('usuario'));
        $nombre = $usuario->names . ' ' . $usuario->lastnames;
        $proveedor = Proveedor::find($request->input('proveedor'));
        $contrato = new Contrato();
        $contrato->nombre = $nombre;
        $contrato->direccion = $request->input('direccion');
        $contrato->telefono = $request->input('telefono');
        $contrato->convencional = $request->input('convencional');
        $contrato->descripcion = $request->input('descripcion');
        $contrato->id_categoria = $request->input('categoria');
        $contrato->id_proveedor = $request->input('proveedor');
        $contrato->id_usuario = $request->input('usuario');
        $contrato->fecha = $request->input('fecha');
        $contrato->hora = $hora;
        $contrato->forma_id = $request->input('forma');
        $contrato->estado = $request->input('estado');
        $contrato->referencia = $request->input('referencia');
        $contrato->save();
        $this->mensaje_proveedor($request->input('proveedor'), 'Tiene un contrato nuevo');
        return json_encode($contrato);
    }

    public function lista($id)
    {
        $listado = Contrato::select("categorias.nombre AS nombre_categoria", DB::raw('CONCAT(proveedors.nombres, " ", proveedors.apellidos) AS nombre_proveedor'), "proveedors.id AS id_proveedor", "contratos.id AS id_contrato", "contratos.estado", "contratos.fecha", "contratos.calificacion", "contratos.referencia", "contratos.direccion", "contratos.descripcion", "proveedors.celular", "proveedors.convencional", "contratos.hora", "contratos.nombre", 'usuarios.imagen', 'contratos.comentarios')
            ->join('proveedors', 'proveedors.id', '=', 'contratos.id_proveedor')
            ->join('categorias', 'categorias.id', '=', 'contratos.id_categoria')
            ->join('usuarios', 'usuarios.id_proveedor', '=', 'proveedors.id')
            ->where('contratos.id_usuario', $id)
            ->get();

        return json_encode($listado);
    }

    public function calificar(Request $request)
    {
        $contrato = Contrato::find($request->input('id_contrato'));
        $contrato->calificacion = $request->input('valor');
        $contrato->comentarios = $request->input('comentario');
        $id_proveedor = $contrato->id_proveedor;
        $contrato->save();
        $this->calcular($id_proveedor);
        return json_encode($contrato);
    }

    public function proveedor($id)
    {
        $proveedor = Contrato::select('contratos.*', 'usuarios.imagen')
            ->join('usuarios', 'usuarios.id', '=', 'contratos.id_usuario')
            ->where('contratos.id_proveedor', $id)
            ->get();
        return json_encode($proveedor);
    }

    public function proveedor_transito($id)
    {
        $proveedor = Contrato::where('id_proveedor', $id)
            ->where(function ($query) {
                $query->where('estado', 'Enviado')
                    ->orWhere('estado', 'Aprobado');
            })
            ->get();
        return json_encode($proveedor);
    }
    public function proveedor_finalizado($id)
    {
        $proveedor = Contrato::where('id_proveedor', $id)
            ->where(function ($query) {
                $query->where('estado', 'Finalizado')
                    ->orWhere('estado', 'Rechazado');
            })
            ->get();
        return json_encode($proveedor);
    }

    public function cambiarEstado(Request $request)
    {
        $contrato = Contrato::find($request->input('id_contrato'));
        $contrato->estado = $request->input('estado');
        $contrato->comentarios = $request->input('comentario');
        $contrato->save();
        $message = 'El estado de su servicio ha cambiado a: ' . $request->input('estado');
        $this->mensaje_usuario($contrato->id_usuario, $message);
        return json_encode($contrato);
    }

    public function calcular($id)
    {
        $valor = 0;
        $numero_contratos = Contrato::where('id_proveedor', $id)
            ->where('estado', 'Finalizado')->count();
        $calificacion = Contrato::where('id_proveedor', $id)
            ->where('estado', 'Finalizado')->get();
        foreach ($calificacion as $val) {
            $valor += $val->calificacion;
        }
        $cal = $valor / $numero_contratos;
        $proveedor = Proveedor::find($id);
        $proveedor->calificacion = $cal;
        $proveedor->save();
    }

    public function detalle_servicio($id)
    {
        $servicio = Contrato::select('contratos.*', 'usuarios.imagen')
            ->join('usuarios', 'usuarios.id', '=', 'contratos.id_usuario')
            ->where('contratos.id', $id)
            ->get();
        return json_encode($servicio);
    }

    public function numero_servicios($id)
    {
        $numero = Contrato::whereIn('estado', ['Aprobado', 'Finalizado'])
            ->whereNull('calificacion')
            ->where('id_usuario', $id)
            ->count();

        return json_encode($numero);
    }
    public function numero_contratos($id)
    {
        $numero = Contrato::whereIn('estado', ['Enviado'])
            ->where('id_proveedor', $id)
            ->count();

        return json_encode($numero);
    }

    public function comentarios($id)
    {

        $comentarios = ProveedorXCategoria::select('comentarios', 'fecha')
            ->join('contratos', 'contratos.id_proveedor', '=', 'proveedor_x_categorias.id_proveedor')
            ->where('contratos.estado', 'Finalizado')
            ->where('proveedor_x_categorias.id', $id)
            ->get();
        return json_encode($comentarios);
    }

    public function mensaje_usuario($id, $message)
    {
        $usuario = Usuarios::find($id);
        $this->clouding($usuario->token_web, $message);
        $this->clouding($usuario->token_movil, $message);
    }
    public function mensaje_proveedor($id, $message)
    {
        $usuario = Usuarios::where('id_proveedor', $id)
            ->first();
        $this->clouding($usuario->token_web, $message);
        $this->clouding($usuario->token_movil, $message);
    }

    public function clouding($to, $message)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $body = array(
            "notification" => array(
                "title" => "Notificación de QuickService",
                "body" => $message,
                "icon" => "https://www.qckservice.com/assets/img/icono/logo.png",
            ),
            "to" => $to,
        );

        $fields = json_encode($body);
        $headers = array(
            'Authorization: key=' . "AAAAwuPlY4I:APA91bHKJ1IKCtMq_-Eg0PjCRHFhKiEbXPKHZayX6n7SimJUpcbGCnt4-mCn3auI5sm50qzBdF48_BDQ3C3jWlClJQWSQiu0KS8kuZRVGcT27hsulzFXRsWxG4YpP83UGTPuT-o9aDrU",
            'Content-Type: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);
    }
}
