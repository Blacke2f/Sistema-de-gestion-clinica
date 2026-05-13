<?php

namespace App\Http\Controllers;

/**
 * Controlador de Notificaciones
 * 
 * Gestiona las notificaciones en tiempo real del sistema.
 * Provee endpoints para el dropdown de notificaciones y el polling de badges.
 * 
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionesController extends Controller
{
    /** Devuelve las últimas 10 notificaciones no leídas (para el dropdown) */
    public function index()
    {
        $notificaciones = Notificacion::where('usuario_id', Auth::id())
            ->where('leida', false)
            ->latest()
            ->limit(10)
            ->get();

        $total = Notificacion::where('usuario_id', Auth::id())
            ->where('leida', false)
            ->count();

        return response()->json([
            'success'        => true,
            'notificaciones' => $notificaciones,
            'total'          => $total,
        ]);
    }

    /** Marcar una notificación como leída */
    public function markAsRead(Notificacion $notificacion)
    {
        $notificacion->update(['leida' => true]);
        return response()->json(['success' => true]);
    }

    /** Marcar todas como leídas */
    public function markAllRead()
    {
        Notificacion::where('usuario_id', Auth::id())
            ->where('leida', false)
            ->update(['leida' => true]);

        return response()->json(['success' => true, 'message' => 'Todas marcadas como leídas.']);
    }

    /** Conteo rápido para el badge (polling) */
    public function count()
    {
        $notif   = Notificacion::where('usuario_id', Auth::id())->where('leida', false)->count();
        $mensajes = Auth::user()->mensajesNoLeidos();

        return response()->json([
            'success'     => true,
            'notificaciones' => $notif,
            'mensajes'    => $mensajes,
        ]);
    }
}
