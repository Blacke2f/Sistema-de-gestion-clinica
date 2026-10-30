<?php

namespace App\Http\Controllers;

/**
 * Controlador de Usuarios del Sistema
 * 
 * Gestiona las cuentas de usuario: creación, edición, eliminación
 * y activación/desactivación. Solo accesible para administradores.
 * Registra todas las acciones en la bitácora del sistema.
 * 
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Bitacora;
use App\Models\PermisoUsuario;
use App\Models\PermisosPorRol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $perPage = in_array($request->get('per_page', 15), [10, 15, 20, 50])
            ? (int) $request->get('per_page', 15)
            : 15;

        $usuarios = Usuario::when($search, function ($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('rol', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios', 'perPage', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username'        => 'required|string|max:50|unique:usuarios,username',
            'email'           => 'required|email|max:100|unique:usuarios,email',
            'password'        => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'nombre_completo' => 'required|string|max:150',
            'rol'             => 'required|in:admin,medico,recepcionista,enfermera,farmaceutico,contador,laboratorista',
            'activo'          => 'boolean',
        ], [
            'username.required'        => 'El usuario es requerido.',
            'username.unique'          => 'El usuario ya existe.',
            'email.required'           => 'El email es requerido.',
            'email.unique'             => 'El email ya está registrado.',
            'password.required'        => 'La contraseña es requerida.',
            'password.min'             => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex'           => 'La contraseña debe tener mayúscula, minúscula y número.',
            'nombre_completo.required' => 'El nombre completo es requerido.',
            'rol.required'             => 'El rol es requerido.',
            'rol.in'                   => 'El rol seleccionado no es válido.',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['activo']   = $request->boolean('activo', true);

        try {
            $usuario = Usuario::create($data);

            // Aplicar permisos por defecto según el rol
            try {
                PermisosPorRol::aplicar($usuario->id, $usuario->rol);
            } catch (\Throwable $e) { /* no crítico */ }

            try {
                Bitacora::registrar(Auth::id(), 'CREAR USUARIO', 'usuarios', $usuario->id,
                    "Creó el usuario '{$usuario->username}' con rol {$usuario->rol_texto}");
            } catch (\Throwable $e) { /* no crítico */ }

            return response()->json([
                'success' => true,
                'message' => "Usuario creado con permisos por defecto para el rol {$usuario->rol_texto}.",
                'id'      => $usuario->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear usuario: ' . $e->getMessage()], 500);
        }
    }

    public function show(Usuario $usuario)
    {
        return response()->json(['success' => true, 'data' => $usuario]);
    }

    public function update(Request $request, Usuario $usuario)
    {
        $data = $request->validate([
            'username'        => "required|string|max:50|unique:usuarios,username,{$usuario->id}",
            'email'           => "required|email|max:100|unique:usuarios,email,{$usuario->id}",
            'nombre_completo' => 'required|string|max:150',
            'rol'             => 'required|in:admin,medico,recepcionista,enfermera,farmaceutico,contador,laboratorista',
            'activo'          => 'boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ], [
                'password.min'   => 'La contraseña debe tener al menos 8 caracteres.',
                'password.regex' => 'La contraseña debe tener mayúscula, minúscula y número.',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $data['activo'] = $request->boolean('activo', $usuario->activo);
        $rolAnterior    = $usuario->rol;

        try {
            $usuario->update($data);

            // Si cambió el rol, re-aplicar permisos por defecto del nuevo rol
            if ($rolAnterior !== $usuario->fresh()->rol && !$usuario->fresh()->isAdmin()) {
                try {
                    PermisosPorRol::aplicar($usuario->id, $usuario->fresh()->rol);
                } catch (\Throwable $e) { /* no crítico */ }
            }

            $detalles = "Editó el usuario '{$usuario->username}'";
            if ($request->filled('password')) $detalles .= ' (contraseña cambiada)';
            if ($rolAnterior !== $data['rol']) $detalles .= " (rol: {$rolAnterior} → {$data['rol']})";

            try {
                Bitacora::registrar(Auth::id(), 'EDITAR USUARIO', 'usuarios', $usuario->id, $detalles);
            } catch (\Throwable $e) { /* no crítico */ }

            return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Usuario $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'No puede eliminarse a sí mismo.'], 403);
        }

        try {
            try {
                Bitacora::registrar(Auth::id(), 'ELIMINAR USUARIO', 'usuarios', $usuario->id, "Eliminó el usuario '{$usuario->username}'");
            } catch (\Throwable $e) { /* bitácora no crítica */ }

            $usuario->delete();
            return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }

    public function toggleEstado(Usuario $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'No puede desactivarse a sí mismo.'], 403);
        }

        $nuevoEstado = ! $usuario->activo;
        $usuario->update(['activo' => $nuevoEstado]);

        $accion      = $nuevoEstado ? 'ACTIVAR USUARIO' : 'DESACTIVAR USUARIO';
        $estadoTexto = $nuevoEstado ? 'activó' : 'desactivó';
        Bitacora::registrar(Auth::id(), $accion, 'usuarios', $usuario->id, "{$estadoTexto} el usuario '{$usuario->username}'");

        return response()->json([
            'success' => true,
            'message' => $nuevoEstado ? 'Usuario activado.' : 'Usuario desactivado.',
            'activo'  => $nuevoEstado,
        ]);
    }

    /**
     * Retorna los permisos actuales del usuario en formato JSON.
     * Se usa para cargar el modal de permisos via AJAX.
     */
    public function editarPermisos(Usuario $usuario)
    {
        // Los admins no necesitan permisos configurados
        if ($usuario->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Los administradores tienen acceso total y no requieren configuración de permisos.',
            ], 422);
        }

        // Cargar permisos existentes del usuario
        $permisos = PermisoUsuario::where('usuario_id', $usuario->id)->get()
            ->keyBy('modulo'); // indexar por nombre de módulo para fácil acceso

        // Construir array con todos los módulos y sus permisos actuales
        $data = [];
        foreach (PermisoUsuario::modulos() as $modulo => $info) {
            $p = $permisos->get($modulo);
            $data[$modulo] = [
                'label'          => $info['label'],
                'icon'           => $info['icon'],
                'color'          => $info['color'],
                'puede_ver'      => $p ? (bool)$p->puede_ver      : false,
                'puede_crear'    => $p ? (bool)$p->puede_crear    : false,
                'puede_editar'   => $p ? (bool)$p->puede_editar   : false,
                'puede_eliminar' => $p ? (bool)$p->puede_eliminar : false,
            ];
        }

        return response()->json([
            'success'  => true,
            'usuario'  => ['id' => $usuario->id, 'nombre' => $usuario->nombre_completo, 'rol' => $usuario->rol],
            'permisos' => $data,
        ]);
    }

    /**
     * Guarda los permisos del usuario enviados desde el modal.
     * Recibe un array de permisos por módulo y los persiste en la BD.
     */
    public function guardarPermisos(Request $request, Usuario $usuario)
    {
        // No se pueden modificar permisos de administradores
        if ($usuario->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No se pueden modificar los permisos de un administrador.',
            ], 422);
        }

        // Guardar todos los permisos usando el método del modelo
        PermisoUsuario::guardarTodos($usuario->id, $request->get('permisos', []));

        // Registrar en bitácora
        try {
            Bitacora::registrar(
                Auth::id(),
                'EDITAR PERMISOS',
                'usuarios',
                $usuario->id,
                "Actualizó los permisos del usuario '{$usuario->username}'"
            );
        } catch (\Throwable $e) { /* bitácora no crítica */ }

        return response()->json([
            'success' => true,
            'message' => "Permisos de {$usuario->nombre_completo} actualizados correctamente.",
        ]);
    }
}
