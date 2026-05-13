<?php

namespace App\Http\Controllers;

/**
 * Controlador de Perfil de Usuario
 *
 * Permite a cada usuario autenticado ver y actualizar sus propios datos personales,
 * así como cambiar su contraseña con verificación de la contraseña actual.
 * Registra los cambios en la bitácora del sistema.
 *
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    /**
     * Muestra la vista del perfil del usuario autenticado.
     * Pasa el objeto usuario a la vista para pre-llenar el formulario.
     */
    public function index()
    {
        // Obtener el usuario actualmente autenticado
        $usuario = Auth::user();

        // Retornar la vista con los datos del usuario
        return view('perfil.index', compact('usuario'));
    }

    /**
     * Actualiza los datos personales del usuario (nombre y email).
     * Valida que el email no esté en uso por otro usuario.
     * Registra el cambio en la bitácora.
     *
     * @param Request $request Datos del formulario: nombre_completo, email
     * @return JsonResponse Respuesta JSON con éxito o error
     */
    public function update(Request $request)
    {
        // Obtener el usuario autenticado
        $usuario = Auth::user();

        // Validar los datos del formulario
        $data = $request->validate([
            'nombre_completo' => 'required|string|max:150',
            // El email debe ser único en la tabla usuarios, EXCEPTO para el usuario actual
            // La coma y el ID al final excluyen al usuario actual de la validación de unicidad
            'email' => "required|email|max:100|unique:usuarios,email,{$usuario->id}",
        ], [
            'nombre_completo.required' => 'El nombre es requerido.',
        ]);

        // Actualizar los datos en la base de datos
        $usuario->update($data);

        // Registrar el cambio en la bitácora para auditoría
        Bitacora::registrar($usuario->id, 'ACTUALIZAR PERFIL', 'usuarios', $usuario->id);

        // Retornar respuesta JSON exitosa
        return response()->json(['success' => true, 'message' => 'Perfil actualizado correctamente.']);
    }

    /**
     * Cambia la contraseña del usuario autenticado.
     * Requiere la contraseña actual para verificar identidad.
     * La nueva contraseña se hashea con bcrypt antes de guardar.
     *
     * @param Request $request Datos: password_actual, password_nuevo, password_nuevo_confirmation
     * @return JsonResponse Respuesta JSON con éxito o error
     */
    public function password(Request $request)
    {
        // Validar los campos del formulario de cambio de contraseña
        $request->validate([
            'password_actual' => 'required|string',
            // 'confirmed' verifica que password_nuevo == password_nuevo_confirmation
            'password_nuevo'  => 'required|string|min:6|confirmed',
        ], [
            'password_actual.required' => 'La contraseña actual es requerida.',
            'password_nuevo.required'  => 'La nueva contraseña es requerida.',
            'password_nuevo.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password_nuevo.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Obtener el usuario autenticado
        $usuario = Auth::user();

        // Verificar que la contraseña actual ingresada sea correcta
        // Hash::check compara el texto plano con el hash almacenado en BD
        if (! Hash::check($request->password_actual, $usuario->password)) {
            // Retornar error 422 (Unprocessable Entity) si la contraseña es incorrecta
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta.'
            ], 422);
        }

        // Hashear la nueva contraseña con bcrypt (12 rounds configurados en .env)
        // y guardarla en la base de datos
        $usuario->update(['password' => Hash::make($request->password_nuevo)]);

        // Registrar el cambio de contraseña en la bitácora
        Bitacora::registrar($usuario->id, 'CAMBIAR CONTRASEÑA', 'usuarios', $usuario->id);

        // Retornar respuesta exitosa
        return response()->json(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
    }
}
