<?php

namespace App\Http\Controllers;

/**
 * Controlador de Mensajes Internos
 *
 * Gestiona la mensajería interna entre usuarios del sistema.
 * Cada usuario puede ver sus mensajes recibidos y enviados.
 * Los administradores pueden enviar mensajes masivos a grupos de usuarios.
 * Al enviar un mensaje, el destinatario recibe una notificación automática.
 *
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Mensaje;
use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MensajesController extends Controller
{
    /**
     * Muestra la bandeja de mensajes del usuario autenticado.
     *
     * Carga por separado los mensajes recibidos y enviados.
     * También carga la lista de usuarios activos para el selector del formulario.
     * Los mensajes no leídos se muestran resaltados en la vista.
     */
    public function index()
    {
        // Obtener el usuario autenticado
        $usuario = Auth::user();

        // Mensajes recibidos por este usuario, con datos del remitente
        $mensajesRecibidos = Mensaje::with('remitente')
            ->where('destinatario_id', $usuario->id)
            ->latest()
            ->get();

        // Mensajes enviados por este usuario, con datos del destinatario
        $mensajesEnviados = Mensaje::with('destinatario')
            ->where('remitente_id', $usuario->id)
            ->latest()
            ->get();

        // Lista de usuarios activos para el selector "Enviar a:"
        // Excluye al usuario actual (no puede enviarse mensajes a sí mismo)
        $usuarios = Usuario::where('id', '!=', $usuario->id)->activos()->get();

        return view('mensajes.index', compact('mensajesRecibidos', 'mensajesEnviados', 'usuarios'));
    }

    /**
     * Envía un mensaje individual a un usuario específico.
     *
     * Crea el mensaje en la BD y envía una notificación al destinatario.
     * La notificación está en try/catch para que si falla no afecte el envío.
     *
     * @param Request $request Datos: destinatario_id, asunto, contenido
     * @return JsonResponse { success, message, id }
     */
    public function store(Request $request)
    {
        // Validar los campos del formulario
        $request->validate([
            'destinatario_id' => 'required|exists:usuarios,id', // debe existir en BD
            'asunto'          => 'required|string|max:200',
            'contenido'       => 'required|string',
        ], [
            'destinatario_id.required' => 'Seleccione un destinatario.',
            'asunto.required'          => 'El asunto es requerido.',
            'contenido.required'       => 'El mensaje es requerido.',
        ]);

        try {
            // Crear el mensaje con el remitente = usuario autenticado
            $mensaje = Mensaje::create([
                'remitente_id'    => Auth::id(),
                'destinatario_id' => $request->destinatario_id,
                'asunto'          => $request->asunto,
                'contenido'       => $request->contenido,
                // leido = false por defecto (definido en la migración)
            ]);

            // Notificar al destinatario sobre el nuevo mensaje
            // Si la notificación falla, el mensaje ya fue guardado — no es crítico
            try {
                Notificacion::notificarUsuario(
                    usuarioId: $request->destinatario_id,
                    tipo:      'mensaje_nuevo',
                    titulo:    'Nuevo Mensaje',
                    mensaje:   Auth::user()->nombre_completo . ' te envió: ' . $request->asunto,
                    icono:     'bxs-envelope',
                    color:     'info',
                    url:       '/mensajes'
                );
            } catch (\Throwable $e) { /* notificación no crítica */ }

            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado correctamente.',
                'id'      => $mensaje->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al enviar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Envía un mensaje masivo a un grupo de usuarios.
     * Solo disponible para administradores.
     *
     * Grupos disponibles: todos, medicos, recepcionistas, admins.
     * Excluye al administrador que envía el mensaje.
     * Marca los mensajes con es_masivo=true para identificarlos.
     *
     * @param Request $request Datos: asunto, contenido, destinatarios (grupo)
     * @return JsonResponse { success, message, enviados }
     */
    public function storeMasivo(Request $request)
    {
        // Verificar que el usuario sea administrador
        if (! Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Sin permiso para esta acción.'], 403);
        }

        $request->validate([
            'asunto'        => 'required|string|max:200',
            'contenido'     => 'required|string',
            'destinatarios' => 'required|string|in:todos,medicos,recepcionistas,admins',
        ]);

        // Construir la consulta base: usuarios activos excepto el admin que envía
        $query = Usuario::activos()->where('id', '!=', Auth::id());

        // Filtrar por rol si no es "todos"
        if ($request->destinatarios !== 'todos') {
            // Mapear el nombre del grupo al valor del campo 'rol' en la BD
            $rolMap = [
                'medicos'          => 'medico',
                'recepcionistas'   => 'recepcionista',
                'admins'           => 'admin',
            ];
            $query->where('rol', $rolMap[$request->destinatarios]);
        }

        $destinatarios = $query->get();

        // Verificar que haya al menos un destinatario
        if ($destinatarios->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No hay destinatarios disponibles.'], 422);
        }

        // Crear un mensaje para cada destinatario
        $enviados = 0;
        foreach ($destinatarios as $dest) {
            Mensaje::create([
                'remitente_id'    => Auth::id(),
                'destinatario_id' => $dest->id,
                'asunto'          => $request->asunto,
                'contenido'       => $request->contenido,
                'es_masivo'       => true, // marcar como mensaje masivo
            ]);
            $enviados++;
        }

        return response()->json([
            'success'  => true,
            'message'  => "Mensaje enviado a {$enviados} usuario(s) correctamente.",
            'enviados' => $enviados,
        ]);
    }

    /**
     * Muestra el contenido de un mensaje y lo marca como leído.
     *
     * Al abrir un mensaje, automáticamente se marca como leído (leido=true).
     * Esto actualiza el badge de mensajes no leídos en la navbar.
     *
     * @param Mensaje $mensaje Mensaje a mostrar (Route Model Binding)
     * @return JsonResponse { success, data }
     */
    public function show(Mensaje $mensaje)
    {
        // Marcar como leído al abrirlo
        $mensaje->update(['leido' => true]);

        // Cargar datos del remitente para mostrar en el modal
        $mensaje->load('remitente');

        return response()->json(['success' => true, 'data' => $mensaje]);
    }

    /**
     * Marca un mensaje como leído sin abrirlo.
     * Se puede usar para marcar mensajes como leídos desde la lista.
     *
     * @param Mensaje $mensaje Mensaje a marcar (Route Model Binding)
     * @return JsonResponse { success, message }
     */
    public function markAsRead(Mensaje $mensaje)
    {
        $mensaje->update(['leido' => true]);
        return response()->json(['success' => true, 'message' => 'Mensaje marcado como leído.']);
    }

    /**
     * Elimina un mensaje del sistema.
     *
     * @param Mensaje $mensaje Mensaje a eliminar (Route Model Binding)
     * @return JsonResponse { success, message }
     */
    public function destroy(Mensaje $mensaje)
    {
        $mensaje->delete();
        return response()->json(['success' => true, 'message' => 'Mensaje eliminado.']);
    }
}
